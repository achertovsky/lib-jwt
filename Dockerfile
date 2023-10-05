FROM php:8.1-cli-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache $PHPIZE_DEPS && \
    pecl install pcov && \
    docker-php-ext-enable pcov

RUN apk add --update linux-headers &&\ 
    pecl install xdebug && \
    docker-php-ext-enable xdebug

RUN echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini &&\
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini &&\
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini &&\
    echo "xdebug.client_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini