FROM php:7.2-fpm

WORKDIR /application

COPY ./.docker/php/wait-for-db.sh /wait-for-db.sh

## Install composer globally
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

## Install php extensions + cleanup
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        mysql-client \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*

COPY . /application

## Self explaining composer install
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-ansi --no-scripts --no-progress --no-suggest

COPY ./.docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

## Install Xdebug extension + cleanup
RUN pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*