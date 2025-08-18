1) Создали облако с базовыми настройками через ансибл, чтобы можно было подключиться через докер к нашему проекту
2) натсроили ssl который с самоподписным сертом все равно не работает. нужно использовать let's encrypt но для него нужен готовый домен. есть 3 или 4 варианта установки ssl, самый геморный это самоподписной, потом он предустановлен в каком-то сервере аля нджинкс (чатгпт в помощь), encrypt и еще какой-то. 
3) после создания облака и конфигурации ансибль для настройки его стало понятно что нужно создавать сразу кластер, посколбку кластер из коробки в облаке дает свои преимущества и намного проще настраивается чем если делать это в ручную. конфигурация тераформ переписана.
4) установить doctl
5) запускаем кластер с помощью терраформ. запускаем хэлм чарт для создания сервака

vchizhov@vchizhov:~$ helm repo add ingress-nginx https://kubernetes.github.io/ingress-nginx
"ingress-nginx" has been added to your repositories
vchizhov@vchizhov:~$ helm repo update
Hang tight while we grab the latest from your chart repositories...
...Successfully got an update from the "ingress-nginx" chart repository
Update Complete. ⎈Happy Helming!⎈
vchizhov@vchizhov:~$ helm install ingress-nginx ingress-nginx/ingress-nginx
NAME: ingress-nginx
LAST DEPLOYED: Tue May 13 11:44:46 2025
NAMESPACE: default
STATUS: deployed
REVISION: 1
TEST SUITE: None
NOTES:
The ingress-nginx controller has been installed.
It may take a few minutes for the load balancer IP to be available.
You can watch the status by running 'kubectl get service --namespace default ingress-nginx-controller --output wide --watch'

An example Ingress that makes use of the controller:
  apiVersion: networking.k8s.io/v1
  kind: Ingress
  metadata:
    name: example
    namespace: foo
  spec:
    ingressClassName: nginx
    rules:
      - host: www.example.com
        http:
          paths:
            - pathType: Prefix
              backend:
                service:
                  name: exampleService
                  port:
                    number: 80
              path: /
    # This section is only required if TLS is to be enabled for the Ingress
    tls:
      - hosts:
        - www.example.com
        secretName: example-tls

If TLS is enabled for the Ingress, a Secret containing the certificate and key must also be provided:

  apiVersion: v1
  kind: Secret
  metadata:
    name: example-tls
    namespace: foo
  data:
    tls.crt: <base64 encoded cert>
    tls.key: <base64 encoded key>
  type: kubernetes.io/tls
vchizhov@vchizhov:~$ kubectl get nodes
NAME                 STATUS   ROLES    AGE   VERSION
default-pool-t7hpy   Ready    <none>   43m   v1.32.2
vchizhov@vchizhov:~$ kubectl get pods
NAME                                        READY   STATUS    RESTARTS   AGE
ingress-nginx-controller-6885cfc548-dn6kx   1/1     Running   0          61s

Создается load balancer когда мы создали Ingress Controller с помощью хелм

6) добавили репу в гитлаб
7) создаем ранер в кластере
helm repo add gitlab https://charts.gitlab.io

helm install --namespace default gitlab-runner -f ./runners/values.yaml gitlab/gitlab-runner

8) Создание Container Registry, добавил настройку в терраформ

9) скачал репу с проектом, добавил туда сиай файл с готовым пайплайном для гитлаб регистри (идет в коробке с  гитлабом)

10) заменил dind на kaniko поскольку для сборки dind нужны привелигорванные права, и это  
 дает уязвимость контейнерам. канико не использует dockerd демон как dind а просто читает Dockerfile, интерпретирует его и собирает слои вручную, сохраняет результат в образ и пушит в регистри

 получил аксес токен для гитлаб
 получил адрес контейнер регистри
 Project → Deploy → Container Registry, нажав CLI Commands 

11)  создал секрет со своими данными через кубер
kubectl create secret docker-registry gitlab-credentials --docker-server=<REGISTRY_SERVER> --docker-username=<GITLAB_USER> --docker-password=<ACCESS_TOKEN> --docker-email=<GITLAB_USER_EMAIL> -n finenomore

посмотреть результат 
kubectl get secrets gitlab-credentials
kubectl get secret gitlab-credentials -o yaml
kubectl get secret gitlab-credentials -o jsonpath="{.data.\.dockerconfigjson}" | base64 --decode

12) установил и настроил gitlab агент для деплоя (GitLab CI/CD workflow)
в проекте Infrastructure → Kubernetes clusters и нажмите Connect a cluster.

создать новый получить токен и команду для установки агента в кластер
helm repo add gitlab https://charts.gitlab.io
helm repo update
helm upgrade --install finenomore-gitlab-agent gitlab/gitlab-agent \
    --namespace gitlab-agent \
    --create-namespace \
    --set image.tag=v15.3.0 \
    --set config.token=<YOUR_AGENT_ACCESS_TOKEN> \
    --set config.kasAddress=wss://<YOUR_GITLAB_NAME>.gitlab.com/-/kubernetes-agent/

запустил ее

helm upgrade --install finenomore-gitlab-agent gitlab/gitlab-agent \
    --namespace gitlab-agent-finenomore-gitlab-agent \
    --create-namespace \
    --set config.token=<YOUR_AGENT_ACCESS_TOKEN> \
    --set config.kasAddress=wss://kas.gitlab.com


проверить после установки сторедж класс 
kubectl get storageclass


13) изменил сиай файл добавил деплой в мой кубер (смотри комиты) и запустил пайплайн.

14) агент создал вручную и добавить актуальный токен агента в скрипт

15) запустил пайплайн алгоритм которого такой: сначала я билдю наш проект в гитлаб сторедж с помощью канеко, потом с помощью хелма деплою его в кластер 

16) получил айпи для подключения (этот же айпи можно найти в облаке лоад балансер)
kubectl get ingress -n finenomore

база не взлетела, ошибка pod has unbound immediate PersistentVolumeClaims

изменил хелм, указал неверно сторедж.

17) начал проверку пайплайна на уязвимости
sql инъекция
XSS-уязвимость

https://owasp.org/www-project-top-ten/


-------------------------------------------------------------

1)  связать doctl c компьютером

2)  выполнить терраформ эплай (не забыть прокинуть аксесс токен, если требуется) терраформ должен будет создать кластер

3)  сохранить у себя конфиг куба чтобы управлять им с хоста (провалиться в настройки кластера, там будет инструкция)

4)  устанавливаем ингесс контролер для получения внешнего айпи



