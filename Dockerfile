FROM php:7.2

WORKDIR /application

## Install composer globally
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

COPY . /application
