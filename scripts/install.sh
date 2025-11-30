
# 1. связать doctl c компьютером
echo "Save kubeconfig for devsecops cluster"
doctl kubernetes cluster kubeconfig save devsecops


# 2. установить ингесс контролер для получения внешнего айпи
echo "Add helm repo for ingress-nginx"
helm repo add ingress-nginx https://kubernetes.github.io/ingress-nginx

echo "Update helm repo"
helm repo update

echo "Install ingress-nginx"
helm install ingress-nginx ingress-nginx/ingress-nginx

# 3.  развертывание приложения finenomore
echo "Create namespace for finenomore"
kubectl create namespace finenomore

# 5. создание секрета для доступа к gitlab container registry (это пункт 3)
echo "Create secret for gitlab credentials"
kubectl create secret docker-registry gitlab-credentials --docker-server=registry.gitlab.com --docker-username=chizhovvictor --docker-password=<ACCESS TOKEN> --docker-email=test@test.com -n finenomore

#3-1 установка argo cd для деплоя приложения finenomore
echo "Create namespace for argocd"
kubectl create namespace argocd

echo "Install argocd"
helm repo add argo https://argoproj.github.io/argo-helm
helm repo update
helm install argocd argo/argo-cd --namespace argocd

echo "Get argocd initial admin password"
kubectl get secret argocd-initial-admin-secret -n argocd -o jsonpath="{.data.password}" | base64 -d

kubectl port-forward svc/argocd-server -n argocd 8080:443

echo "install finenomore app"
helm install finenomore my_project/k8s/finenomore 

# 3-2 создание проекта и приложения для finenomore
php bin/migration up
php bin/fixture up

# 4. установка gitlab-runner через helm в кластер
echo "Add helm repo for gitlab-runner"
helm repo add gitlab https://charts.gitlab.io

echo "Install gitlab-runner"
helm install --namespace default gitlab-runner -f scripts/runners/values.yaml gitlab/gitlab-runner

# 6. установка gitlab-agent в кластер для автоматического подключения к gitlab
echo "Create gitlab agent in kubernetes"
helm repo add gitlab https://charts.gitlab.io
helm repo update
helm upgrade --install finenomore-gitlab-agent gitlab/gitlab-agent \
    --namespace gitlab-agent-test \
    --create-namespace \
    --set config.token=<TOKEN> \
    --set config.kasAddress=wss://kas.gitlab.com


# 7. установка DAST в кластер
echo "Create namespace for DAST"
kubectl create namespace finenomore-dast

# 8. установка секрета для доступа к gitlab container registry для DAST пространства
echo "Install secret registry for DAST"
kubectl create secret docker-registry gitlab-credentials --docker-server=registry.gitlab.com --docker-username=chizhovvictor --docker-password=<ACCESS TOKEN> --docker-email=test@test.com -n finenomore-dast

# 9. установка defectdojo в кластер для дашборда уязвимостей
echo "Create namespace for defectdojo"
kubectl create namespace defectdojo

echo "Add helm repo for defectdojo"
helm repo add defectdojo https://raw.githubusercontent.com/DefectDojo/django-DefectDojo/helm-charts
helm repo add yc-courses-ru-devsecops-helm-charts https://yandex-cloud-examples.github.io/yc-courses-ru-devsecops-helm-charts/

echo "Install defectdojo"
helm install --namespace defectdojo defectdojo defectdojo/defectdojo --version --values defectdojo/values-custom.yaml --set createRabbitMqSecret=true
helm install defectdojo yc-courses-ru-devsecops-helm-charts/defectdojo --namespace defectdojo --create-namespace --values ./defectdojo/values-custom.yaml --set createRabbitMqSecret=true
# 10. вывод пароля для доступа к defectdojo
echo "DefectDojo admin password: $(kubectl \
      get secret defectdojo \
      --namespace=defectdojo \
      --output jsonpath='{.data.DD_ADMIN_PASSWORD}' \
      | base64 --decode)"



kubectl delete secrets defectdojo defectdojo-redis-specific defectdojo-postgresql-specific
kubectl delete serviceAccount defectdojo
kubectl delete pvc redis-data-defectdojo-redis-master-0 data-defectdojo-postgresql-0