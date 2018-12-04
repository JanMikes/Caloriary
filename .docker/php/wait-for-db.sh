#!/usr/bin/env bash
set -e

## Wait until database connection is ready
until mysql -u $DATABASE_USER -h $DATABASE_HOST --password="$DATABASE_PASSWORD" -e "" ; do
    >&2 echo "Waiting for database service to start."
    sleep 3
done

bin/console migrations:migrate --no-interaction --allow-no-migration

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec "$@"