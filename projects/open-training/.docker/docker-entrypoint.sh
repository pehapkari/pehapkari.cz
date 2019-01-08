#!/bin/bash
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ] || [ "$1" = 'php' ]; then

    ## If we are not on production, we install dev dependencies
	if [ "$APP_ENV" != 'prod' ]; then
		composer install --prefer-dist --no-progress --no-suggest --no-interaction
	fi

    php projects/open-training/bin/console assets:install --env=prod --no-debug
    php projects/open-training/bin/console cache:clear


    ## Check if variable DATABASE_HOST is set, if yes, we have database
    if [[ -v DATABASE_HOST ]]; then
        ## Wait until database connection is ready
        until mysql -u $DATABASE_USER -h $DATABASE_HOST --password="$DATABASE_PASSWORD" -e "" ; do
            >&2 echo "Waiting for database service to start."
            sleep 3
        done

        ## Update DB
        php projects/open-training/bin/console doctrine:schema:update --dump-sql --force
    fi


	# Permissions hack because setfacl does not work on Mac and Windows
	chown -R www-data projects/open-training/var
fi

exec docker-php-entrypoint "$@"