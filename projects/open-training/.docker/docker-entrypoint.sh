#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-suggest --no-interaction

        php projects/open-training/bin/console assets:install --env=prod --no-debug
        php projects/open-training/bin/console cache:clear

        ## Wait until database connection is ready
        until mysql -u $DATABASE_USER -h $DATABASE_HOST --password="$DATABASE_PASSWORD" -e "" ; do
            >&2 echo "Waiting for database service to start."
            sleep 3
        done

	    php projects/open-training/bin/console doctrine:schema:update --dump-sql --force
	fi

	# Permissions hack because setfacl does not work on Mac and Windows
	chown -R www-data projects/open-training/var
fi

exec docker-php-entrypoint "$@"