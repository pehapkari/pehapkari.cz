####
## Build js+css assets
####
FROM node:10.15.3 as node-build

WORKDIR /build

COPY package.json yarn.* webpack.config.js ./
RUN yarn install

COPY ./assets ./assets

RUN yarn run build


####
## Build app itself
####
FROM pehapkari/pehapkari.cz-base as production

COPY composer.json composer.lock phpunit.xml.dist ./

RUN composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-suggest \
    && composer clear-cache

COPY --from=node-build /build/public/build ./public/build

RUN mkdir -p ./var/cache \
    ./var/log \
    ./var/sessions \
        && composer dump-autoload -o --no-dev \
        && chown -R www-data ./var

COPY . .


## Local build with xdebug
FROM production as dev

## TODO: we might need NPM + NODE in dev + entrypoint with npm install?

RUN composer install --prefer-dist --no-scripts --no-progress --no-suggest

COPY ./.docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

## Install Xdebug extension + cleanup
RUN pecl -q install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /tmp/* /usr/local/lib/php/doc/* /var/cache/apt/*
