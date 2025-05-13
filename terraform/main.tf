resource "digitalocean_project" "devsecops" {
  name        = "DevSecOps"
  purpose     = "Open project for 42"
  environment = "Development"
  description = "Open project for 42"
}

resource "digitalocean_kubernetes_cluster" "k8s" {
  name    = "devsecops"
  region  = "nyc3"
  version = "latest"

  node_pool {
    name       = "default-pool"
    size       = "s-2vcpu-2gb"
    node_count = 1
  }
}

resource "digitalocean_project_resources" "attach_cluster" {
  project = digitalocean_project.devsecops.id
  resources = [
    digitalocean_kubernetes_cluster.k8s.urn,
  ]
}


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


