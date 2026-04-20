#!/bin/sh
set -e

cd /var/www/html

php artisan package:discover --ansi || true
php artisan storage:link || true
php artisan config:clear || true

exec apache2-foreground
