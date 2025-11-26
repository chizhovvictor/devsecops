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

5) создаем неймспейс finenomore и устанавливаем туда наш проект с помощью хелм чарта

проект имеет бд, которая цепляет память из стореджа кубера. при создании нашего кластера автоматически создается объект storageclass с названием do-block-storage. он указывается в нашем чарте прилы, и с помощью pvc наш сторедж баундится с бд
в конфигурации хедма прилы есть 2 темплейта которые и отвечают за развертывание

6) добваили репозиторий через испорт проекта и выбрали для кубера куреб экзекутор

7) устанавливаем раннер

8) протестировали сборку билд 

образ пушится в gitlab container registry
изначално создавать что-то в гитлаб регистри не надо, образ сам просто запушится в это хранилище, которое создано по умолчанию, для это в пайплайне указываются предопределенные переменные 

9) Настраиваем развёртывание FineNoMore
Создайем Personal Access Token.



Получить адрес GitLab Container Registry
docker login registry.gitlab.com

Добавьте секрет gitlab-credentials в namespace finenomore.

В GitLab-проекте FineNoMore подключите кластер Kubernetes

Создайте новый агент с названием finenomore-gitlab-agent и нажмите Register

Появится окно, где нужно будет сохранить Agent access token, и команда для установки агента 

Добавление в пайплайн задачи для развёртывания приложения

на этом развертывание инфраструктуры заканчивается
-------------------------------------------------------------------------
Создание DevSecops пайплайна

1) pre-commit - проверка секретов при добавлении кода в репу
Для этого нужно настроить хуки pre-commit. Это специальные приложения, которые устанавливаются на локальных машинах. Когда разработчик начинает выполнять коммит, приложение проверяет качество и безопасность кода. От результата проверки зависит, будет коммит выполнен и зафиксирован в системе контроля версий или нет.

Инструменты
Существует множество опенсорсных инструментов, с помощью которых можно настроить pre-commit-проверки:
Gitleaks
git-secrets
Trivy Secret Scanning
Whispers
git-all-secrets
detect-secrets
gittyleaks



2) pre-build - различные проверки исходного кода после попадания в систему контроля версий (White Box Testing)
Secret Detection обнаруживает незашифрованную конфиденциальную информацию в исходном коде.
Static Application Security Testing (SAST) проводит статический анализ качества и безопасности исходного кода.
Software Composition Analysis (SCA) анализирует безопасность компонентов и зависимостей приложения.

Secret Detection - встроенный в гитлаб сиайсиди analyzer с gitleaks

Все найденные уязвимости содержатся в массиве vulnerabilities. 

SAST - линтеры на проверку синтаксиса програмного и IaC кода. Измеряют качество кода с помощью специальных метрик.
Основная задача SAST-сканеров — тестирование безопасности. Они обнаруживают распространённые уязвимости, например, из списка OWASP Top Ten. Что важно: SAST-сканеры находят не только саму уязвимость, но и фрагмент кода, из-за которого она появилась.
Примеры инструментов для SAST
Бесплатное решение:
GitLab SAST
Есть бесплатная базовая версия:
semgrep
SonarQube
Лицензионные решения:
Checkmarx SAST
Российские решения:
Solar AppScreener
PT Application Inspector
PVS-Studio

Существует два типа SCA:
Source SCA анализирует исходный код, а точнее зависимости приложения, определённые в исходном коде. 
Binary SCA анализирует бинарные артефакты: Docker-образы, RPM-пакеты, JAR/WAR/EAR-архивы. Его часто называют Container Scanning.

Вот некоторые SCA-анализаторы, которые выполняют Source SCA:
Опенсорсное решение
Trivy
Доступно только в GitLab Ultimate-версии
GitLab Dependency Scanning
Отечественные решения
Profiscope
CodeScoring
Solar appScreener

Все найденные уязвимости содержатся в массиве vulnerabilities. 


3) post-build проверяют безопасность артефактов. Эта фаза начинается после того, как в CI-пайплайне из исходного кода будут собраны артефакты для распространения и запуска приложения: Docker-образ, RPM-пакет или JAR-архив.
С помощью post-build-проверок анализируют бинарные артефакты и отыскивают в них возможные уязвимости.

Binary Software Composition Analysis (SCA)

Несколько примеров популярных анализаторов Binary SCA:
Бесплатное решение
GitLab Container Scanning
Опенсорс-решения
Trivy
Clair
Grype
Quay
JFrog Xray
Docker Hub
Docker Registry со встроенными анализаторами
Harbor

Все найденные уязвимости содержатся в массиве vulnerabilities. 


4) test-time. DAST, IAST и OAST

Метод чёрного ящика — это изучение приложения без знания внутренних механизмов работы. Его делают через пользовательский интерфейс, не обращаясь к исходному программному коду. Классическими примерами этой техники являются DAST и OAST. Вы разберёте их подробнее в следующих разделах.

Метод серого ящика — тестирование программного обеспечения, которое предполагает комбинацию подходов White Box и Black Box. Такой метод предполагает динамическое тестирование безопасности приложения с использованием данных о его внутренней работе. Эти данные получают с помощью специализированных сенсоров, встроенных в приложение. Пример этой техники — IAST

DAST (Dynamic Application Security Testing) — динамическое тестирование безопасности приложений. DAST-сканеры работают автоматически и проверяют приложения, имитируя внешние атаки через уязвимости.

IAST (Interactive Application Security Testing) — интерактивное тестирование безопасности приложений методом серого ящика (Gray Box Testing)

Чтобы собрать все плюсы и исключить минусы этих двух техник, придумали гибридный механизм IAST, объединивший SAST и DAST

Основной составляющей любого IAST-сканера являются сенсоры или агенты — софтверные библиотеки, встраиваемые в исходный код приложения. Сенсоры или агенты используют информацию об исходном коде приложения и наблюдают за исполнением ручных или автоматизированных интерактивных тестов.

OAST (Out-of-band Application Security Testing) — техника разработана компанией PortSwigger и является расширением DAST.

Примеры DAST-инструментов
GitLab DAST — доступен только в Ultimate-версии
OWASP Zed Attack Proxy (ZAP) — опенсорсное решение, которое в том числе используется в GitLab DAST
Acunetix
Fortify WebInspect
HCL Security AppScan
Synopsys Managed DAST
Tenable.io (Web App Scanning)
Veracode Dynamic Analysis
WhiteHat Sentinel Dynamic
Примеры IAST-инструментов
Synopsys Seeker
Fortify WebInspect
Checkmarx CxIAST
Contrast Assess
Positive Technologies Application Inspector
Примеры OAST-инструментов
Burp Suite
OWASP ZAP с OAST-плагином и OAST-сервисами BOAST, TukTuk и interactsh

5) post-deploy
Мониторинг безопасности приложения
Ещё один способ обеспечить безопасность — мониторинг самого приложения.
RASP (Runtime Application Self-Protection) — технология, которая в реальном времени обнаруживает и блокирует атаки на приложения. Она добавляет функцию защиты в среду исполнения, что даёт приложению возможность самозащиты (self-protection) в автоматическом режиме.

Web Application Firewall (WAF) — межсетевой экран для веб-приложений. Это инструмент для фильтрации трафика. Он работает на прикладном уровне и защищает веб-приложения, анализируя трафик HTTP/HTTPS.

Инструменты для мониторинга безопасности Docker-образов:
Snyk Container
Docker Hub
RASP-инструменты:
OpenText
DataDog
OpenRASP — опенсорсное решение, разработанное Baidu
Fastly
Jscrambler
Imperva

-----------------------------------------------------------------------------
Security Dashboards
Триажирование (триажинг, от английского и французского triage) — процесс определения наиболее критичных проблем безопасности, которые можно устранить с максимальной вероятностью.

Security Dashboard — это специализированный дашборд, который отображает консолидированную информацию о безопасности.

Инструменты
SonarQube — это проприетарный инструмент для статического анализа кода.
SonarQube включает в себя встроенные дашборды, которые позволяют оценить качество и безопасность исходного кода всего проекта или продукта

SonarQube можно использовать только для визуализации результатов статического анализа. Динамический анализ он делать не умеет.

GitLab Security Center — это центр управления безопасностью разрабатываемых приложений, встроенный в GitLab. В отличие от SonarQube, он позволяет оценить безопасность разрабатываемых приложений комплексно благодаря нативной интеграции со встроенными в GitLab сканерами безопасности, такими как GitLab Secret Detection, GitLab SAST, GitLab Container Scanning, GitLab Dependency Scanning, GitLab DAST.

DefectDojo — это Open Source Security Dashboard, который помогает эффективнее управлять безопасностью приложений. Он интегрируется со многими DevSecOps-анализаторами и различными системами управления задачами и генерации отчётов и позволяет настраивать метрики безопасности.


после того, как развернули в кубере наш DefectDojo (смотри инструкцию и не забывай подчищать ключи если вдруг не встало) нужно включить режим дедупликации 
Configuration → System Settings

Создайте Product Type. Для этого в меню слева выберите Add Product Type

Создайте Product - Add Product

Name: finenomore
Description: dev-sec-ops finenomore
Product Type: выберите dev-sec-ops
SLA Configuration: Default
Enable Simple Risk Acceptance: yes (для более простого принятия рисков)

Получите апи токен API v2 Key


Создать переменные в нашем репозитории 
DEFECTDOJO_URL	https://defectdojo.<IP>.sslip.io/api/v2	Variable	No
DEFECTDOJO_PRODUCTID	1	Variable	No
DEFECTDOJO_TOKEN	replace_with_your_gitlab-ci_user’s_api_token	Variable	Yes

Изменил пайплайн на выгрузку артефактов и пуш их в наш дашбоард

