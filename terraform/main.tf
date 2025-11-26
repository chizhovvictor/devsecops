# 1. Проект
resource "digitalocean_project" "devsecops" {
  name        = "DevSecOps"
  purpose     = "Open project for 42"
  environment = "Development"
  description = "Open project for 42"

  depends_on = [
    digitalocean_kubernetes_cluster.k8s,
  ]
}


# 2. VPC (должен быть создан до Kubernetes кластера)
# resource "digitalocean_vpc" "devsecops_vpc" {
#   name        = "devsecops-vpc"
#   region      = "nyc3"
#   ip_range    = "10.200.0.0/16"
#   description = "VPC for DevSecOps project"
# }

# 3. Kubernetes кластер
resource "digitalocean_kubernetes_cluster" "k8s" {
  name    = "devsecops"
  region  = "nyc3"
  version = "1.34.1-do.0"

  # vpc_uuid = digitalocean_vpc.devsecops_vpc.id

  tags = ["devsecops", "k8s"]

  node_pool {
    name       = "default-pool"
    size       = "s-4vcpu-16gb-amd"
    node_count = 1

    tags       = ["k8s-node"]
    auto_scale = false
  }

  # Указание, что кластер зависит от VPC
  # depends_on = [digitalocean_vpc.devsecops_vpc]
}

# 4. Привязка кластера к проекту (обязательно зависит от кластера и проекта)
resource "digitalocean_project_resources" "attach_cluster" {
  project = digitalocean_project.devsecops.id
  resources = [
    digitalocean_kubernetes_cluster.k8s.urn,
  ]
}

# 5. Container Registry (опционально можно тоже привязать к проекту, но пока без этого)
# resource "digitalocean_container_registry" "default" {
#   name                   = "finenomore-registry"
#   subscription_tier_slug = "starter"
# }

# 6. Создание nginx ingress контроллера через helm (создается как отдельные русурс в дефолтном проекте, к проекту не привязывается, но в кластере есть)
# resource "helm_release" "nginx_ingress" {
#   name       = "ingress-nginx"
#   repository = "https://kubernetes.github.io/ingress-nginx"
#   chart      = "ingress-nginx"
#   namespace  = "default"

#   set {
#     name  = "controller.service.type"
#     value = "LoadBalancer"
#   }

#   depends_on = [
#     digitalocean_kubernetes_cluster.k8s
#   ]
# }



# 8. Создание ранера через helm (к проекту в гитлабе не привязался)
# resource "helm_release" "gitlab_runner" {
#   name       = "gitlab-runner"
#   repository = "https://charts.gitlab.io"
#   chart      = "gitlab"
#   namespace  = "default"

#   set {
#     name  = "certmanager-issuer.email"
#     value = "your-email@example.com"
#   }

#   values = [
#     file("${path.module}/runners/values.yaml")
#   ]

#   depends_on = [
#     digitalocean_kubernetes_cluster.k8s
#   ]
# }



# Создание проекта в DigitalOcean

# resource "digitalocean_project" "devsecops" {
#   name        = "DevSecOps"
#   purpose     = "Open project for 42"
#   environment = "Development"
#   description = "Open project for 42"
# }

# Создание облака в DigitalOcean

# resource "digitalocean_droplet" "web" {
#   image  = "ubuntu-20-04-x64"
#   name   = "devsecops"
#   region = "nyc3"
#   size   = "s-1vcpu-1gb"
#   ssh_keys = [
#     "79:27:00:ff:c0:a6:5e:d3:10:d7:ac:06:ac:f0:8f:4a"
#   ]

# }

# если проект создан через ui, можно привзяать свой doctl на компьютере к нашему аккаунту и получить через doctl projects list айди наих проектов

# resource "digitalocean_project_resources" "attach_droplet" {
#   project = "aaa45bce-0f4e-4555-ab96-b98ea419e4bd"
#   resources = [
#     digitalocean_droplet.web.urn,
#   ]
# }

# привязка вариант с айди

# resource "digitalocean_project_resources" "attach_cluster" {
#   project = digitalocean_project.devsecops.id
#   resources = [
#     digitalocean_droplet.web.urn,
#   ]
# }


