## Production build
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

# Install composer and prestissimo globally
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer global require hirak/prestissimo --no-plugins --no-scripts

COPY ./projects/open-training/.docker/database-setup.sh /database-setup.sh

# Copy packages
COPY ./packages /app/packages

# Composer
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install

# Copy this specific project
COPY ./projects/open-training /app/projects/open-training

RUN php projects/open-training/bin/console assets:install --env=prod --no-debug \
    && php projects/open-training/bin/console cache:clear


## Local build with xdebug
FROM production as dev

COPY ./projects/open-training/.docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

## Install Xdebug extension + cleanup
RUN pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*