terraform {
  required_providers {
    digitalocean = {
      source  = "digitalocean/digitalocean"
      version = "~> 2.0"
    }
  }
}

# provider "kubernetes" {
#   host                   = digitalocean_kubernetes_cluster.k8s.endpoint
#   token                  = digitalocean_kubernetes_cluster.k8s.kube_config[0].token
#   cluster_ca_certificate = base64decode(digitalocean_kubernetes_cluster.k8s.kube_config[0].cluster_ca_certificate)
# }


# provider "helm" {
#   kubernetes {
#     host                   = digitalocean_kubernetes_cluster.k8s.endpoint
#     token                  = digitalocean_kubernetes_cluster.k8s.kube_config[0].token
#     cluster_ca_certificate = base64decode(digitalocean_kubernetes_cluster.k8s.kube_config[0].cluster_ca_certificate)
#   }
# }




# Set the variable value in *.tfvars file
# or using -var="do_token=..." CLI option
# variable "do_token" {}

# Configure the DigitalOcean Provider
provider "digitalocean" {
  token = var.do_token
}

