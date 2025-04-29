resource "digitalocean_droplet" "web" {
  image  = "ubuntu-20-04-x64"
  name   = "devsecops"
  region = "nyc3"
  size   = "s-1vcpu-1gb"
  ssh_keys = [
    "79:27:00:ff:c0:a6:5e:d3:10:d7:ac:06:ac:f0:8f:4a"
  ]
  
}
