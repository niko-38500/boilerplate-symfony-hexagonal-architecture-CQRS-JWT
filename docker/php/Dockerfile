FROM php:8.3-fpm-alpine

#RUN chmod a+rw /var/www/html/ -R
RUN apk add --update linux-headers &&\
    apk add --no-cache $PHPIZE_DEPS git zip unzip libzip-dev libpq-dev &&\
    pecl install xdebug &&\
    docker-php-ext-install pdo pgsql pdo_pgsql &&\
    docker-php-ext-enable pdo pgsql pdo_pgsql xdebug

COPY ./conf /usr/local/etc/php/conf.d/

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer