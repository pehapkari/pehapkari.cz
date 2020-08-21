#!/bin/bash
set -e

# first arg is `-f` or `--some-option`, then append it to apache2-foreground
if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

if [ "$1" = 'apache2-foreground' ] || [ "$1" = 'bin/console' ] || [ "$1" = 'php' ] || [ "$1" = 'composer' ]; then
    composer install --prefer-dist --no-progress --no-interaction -o
    php bin/console assets:install
fi

exec "$@"
