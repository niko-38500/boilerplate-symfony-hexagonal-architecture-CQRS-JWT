FROM php:8.2-fpm

RUN chmod a+rw /var/www/html/ -R
RUN pecl install xdebug \
    uopz.so \
    && docker-php-ext-enable xdebug

COPY ./conf /usr/local/etc/php/conf.d/