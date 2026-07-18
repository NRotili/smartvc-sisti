#!/bin/sh
set -e

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

if [ "$#" -gt 0 ]; then
    exec "$@"
else
    exec apache2-foreground
fi
