FROM php:7.3-apache

RUN apk update; \
    apk upgrade;

RUN docker-php-ext-install bz2
RUN docker-php-ext-install curl
RUN docker-php-ext-install fileinfo
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install mysqli

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY ../src/ /var/www/html
