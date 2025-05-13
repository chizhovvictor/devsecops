output "server_ip" {
  value = digitalocean_kubernetes_cluster.k8s.ipv4_address
}