FROM php:7.2-fpm

COPY ./php.ini /usr/local/etc/php/
COPY ./docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/

RUN apt-get update && apt-get install -y \
    openssl \
    git \
    unzip

RUN docker-php-ext-install gettext mbstring pdo pdo_mysql

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

ENV PATH="/var/www/vendor/bin:${PATH}"

WORKDIR /var/www
