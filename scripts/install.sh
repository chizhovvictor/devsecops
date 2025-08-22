
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



echo "Add helm repo for gitlab-runner"
helm repo add gitlab https://charts.gitlab.io

echo "Install gitlab-runner"
helm install --namespace default gitlab-runner -f ./runners/values.yaml gitlab/gitlab-runner

echo "Create namespace for finenomore"
kubectl create namespace finenomore

echo "Create secret for gitlab credentials"
kubectl create secret docker-registry gitlab-credentials --docker-server=registry.gitlab.com --docker-username=chizhovvictor --docker-password=<PASSWORD> --docker-email=test@test.com -n finenomore

echo "Create gitlab agent in kubernetes"
helm repo add gitlab https://charts.gitlab.io
helm repo update
helm upgrade --install test gitlab/gitlab-agent \
    --namespace gitlab-agent-test \
    --create-namespace \
    --set config.token=<TOKEN> \
    --set config.kasAddress=wss://kas.gitlab.com

