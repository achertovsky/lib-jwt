# User
Lib called to reduce hassle in projects with jwt. Expected to be convenient solution for the purpose.

## usage
To check usage cases, please, refer to `tests/Service/JwtManagerTest.php`, `tests/Service/Functional.php`. Those tests explain how JwtManager should be used in your application.

# Development
## install env
```
docker build -t lib-jwt .
docker run --rm -it -u $(id -u):$(id -g) -w /tmp -v ${PWD}:/tmp lib-jwt composer i
```
## testing
```
docker run --rm -it -u $(id -u):$(id -g) -w /tmp -v ${PWD}:/tmp lib-jwt vendor/bin/phpunit
```
