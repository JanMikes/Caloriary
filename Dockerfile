FROM php:7.2

WORKDIR /application

## Install composer globally
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

COPY . /application

## Self explaining composer install
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-ansi --no-scripts --no-progress --no-suggest