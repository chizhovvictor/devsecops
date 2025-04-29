variable "do_token" {
  description = "DigitalOcean API Token"
  type        = string
  sensitive   = true  # чтобы не выводился в логах
}