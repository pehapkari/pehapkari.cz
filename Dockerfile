FROM composer:latest
FROM php:7.2-fpm as production

WORKDIR /app

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
COPY --from=0 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative --no-plugins --no-scripts

# Entrypoint
COPY ./.docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

COPY phpunit.xml ./
COPY ./packages /app/packages

# Composer
COPY composer.json composer.lock ./

RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest \
    && composer clear-cache

COPY . /app

RUN mkdir -p ./var/cache \
    ./var/logs \
    ./var/sessions \
    ./public/uploads/images \
    ./public/generated \
        && composer dump-autoload --classmap-authoritative --no-dev \
        && chown -R www-data ./var \
        && chmod -R 777 ./public/uploads/images ./public/generated


## Nginx
FROM nginx:latest AS nginx

WORKDIR /app

COPY --from=production /app /app
COPY ./.docker/nginx/site.conf /etc/nginx/conf.d/default.conf


## Local build with xdebug
FROM production as dev

COPY ./.docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

## Install Xdebug extension + cleanup
RUN pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*