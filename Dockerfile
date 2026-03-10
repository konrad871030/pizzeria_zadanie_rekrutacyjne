FROM php:8.3-fpm

RUN set -eux; \
    export DEBIAN_FRONTEND=noninteractive; \
    rm -rf /var/lib/apt/lists/*; \
    apt-get update -o Acquire::Retries=3; \
    apt-get install -y --no-install-recommends \
        -o Dpkg::Options::=--force-confdef \
        -o Dpkg::Options::=--force-confold \
        cron git unzip libzip-dev libicu-dev libonig-dev; \
    docker-php-ext-install pdo pdo_mysql intl; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
