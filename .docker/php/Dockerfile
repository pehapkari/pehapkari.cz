####
## Base image without app source code
####
FROM php:7.4

WORKDIR /var/www/pehapkari.cz

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
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_MEMORY_LIMIT=-1
