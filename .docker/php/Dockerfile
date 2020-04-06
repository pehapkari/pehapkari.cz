####
## Base image without app source code
####
FROM php:7.4-apache

WORKDIR /var/www/pehapkari.cz

COPY ./.docker/apache/apache.conf /etc/apache2/sites-available/000-default.conf

# Install php extensions + cleanup
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        g++ \
        postgresql-client libpq-dev \
        zlib1g-dev \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install exif \
    && pecl -q install \
        zip \
    && docker-php-ext-enable zip \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*

# Installing composer and prestissimo globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_MEMORY_LIMIT=-1
RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative --no-plugins --no-scripts

# Entrypoint
COPY ./.docker/docker-entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint
