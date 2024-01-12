variable "REGISTRY" {
  default = "localhost:5000"
}

variable "VENDOR" {
  default = "max-antipin"
}

variable "PROJECT_NAME" {
  default = "web-proxy"
}

# FQIN: Fully Qualified Image Name
function "MakeFQIN" {
  params = [service_name, tag]
  result = "${REGISTRY}/${VENDOR}/${PROJECT_NAME}-${service_name}:${tag}"
}

target "_common" {
  output = ["type=registry"]
}

target "php" {
  dockerfile = "./Dockerfile"
  inherits = ["_common"]
  tags = [ MakeFQIN("php-app", "latest") ]
  target = "php_app"
}

target "php_test" {
  inherits = [ "php" ]
  tags = [ MakeFQIN("php-app", "test") ]
  target = "php_app_test"
}

target "webserver" {
  dockerfile = "./.docker/nginx.Dockerfile"
  inherits = ["_common"]
#  secret = [
 #   "type=env,id=KUBECONFIG",
  #  "type=file,id=aws,src=${HOME}/.aws/credentials"
  #]
  tags = [ MakeFQIN("nginx", "latest") ]
#  target = "php_app_test"
}

group "default" {
  targets = [ "php", "webserver" ]
}

group "test" {
  targets = [ "php_test", "webserver" ]
}