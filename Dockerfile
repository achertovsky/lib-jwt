FROM php:8.1-cli-alpine

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache $PHPIZE_DEPS && \
    pecl install pcov && \
    docker-php-ext-enable pcov
