# User
Lib called to reduce hassle in projects with jwt. Expected to be convenient solution for the purpose.

## usage
```
$jwtSecretKey = 'sosecretkeywow';

$manager = new HmacJwtManager(
    new PayloadNormalizer(),
    $jwtSecretKey
);

$jwt = $manager->create(
    new Payload(
        'token subject here',
        time() //optional param to define expiration time
    )
);

$manager->validate(
    $jwt
);
```

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
