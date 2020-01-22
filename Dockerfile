ARG PHP_VERSION=7.2
FROM php:${PHP_VERSION}-cli

RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev && docker-php-ext-install zip

ARG COVERAGE
RUN if [ "$COVERAGE" = "pcov" ]; then pecl install pcov && docker-php-ext-enable pcov; fi

# Install composer to manage PHP dependencies
RUN apt-get update && apt-get install -y git zip
RUN curl https://getcomposer.org/download/1.9.0/composer.phar -o /usr/local/sbin/composer
RUN chmod +x /usr/local/sbin/composer
RUN composer self-update

WORKDIR /app
