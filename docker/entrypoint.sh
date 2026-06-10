#!/bin/sh

# =============================================================================
#  🚗  GitaCar Rent — Entrypoint
#  Laravel production optimization before supervisor starts
# =============================================================================

set -e

php artisan optimize
php artisan storage:link --force

exec "$@"