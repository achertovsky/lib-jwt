# User

# Development
## install env
docker build -t lib-jwt .
docker run --rm -it -u $(id -u):$(id -g) -w /tmp -v ${PWD}:/tmp lib-jwt composer i
## testing
docker run --rm -it -u $(id -u):$(id -g) -w /tmp -v ${PWD}:/tmp lib-jwt vendor/bin/phpunit
