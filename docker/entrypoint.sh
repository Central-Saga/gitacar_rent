#!/bin/sh

set -e

php artisan config:cache
php artisan event:cache
php artisan view:cache
php artisan storage:link --force

exec "$@"