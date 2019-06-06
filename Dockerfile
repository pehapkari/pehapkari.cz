FROM php:7.2-apache as production

WORKDIR /var/www/pehapkari.cz

COPY ./.docker/apache/apache.conf /etc/apache2/sites-available/000-default.conf

# Install php extensions + cleanup
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        g++ \
        mysql-client \
        zlib1g-dev \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
    && docker-php-ext-configure gd --with-png-dir=/usr/include/  --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo_mysql \
    && pecl -q install \
        zip \
    && docker-php-ext-enable zip \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*

# Installing composer and prestissimo globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative --no-plugins --no-scripts

# Entrypoint
COPY ./.docker/docker-entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

COPY composer.json phpunit.xml ./

## For now installing including dev dependencies
# RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest \
RUN composer install --prefer-dist --no-autoloader --no-scripts --no-progress --no-suggest \
    && composer clear-cache

COPY . .

RUN mkdir -p ./var/cache \
    ./var/log \
    ./var/sessions \
        # && composer dump-autoload --classmap-authoritative --no-dev \
        && composer dump-autoload \
        && chown -R www-data ./var


## Local build with xdebug
FROM production as dev

COPY ./.docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

## Install Xdebug extension + cleanup
RUN pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*
