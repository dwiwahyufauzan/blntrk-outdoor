#!/bin/bash
set -e

PORT=${PORT:-10000}
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

php artisan migrate --force --seed
php artisan db:seed --class=TransaksiDemoSeeder --force || true
php artisan storage:link || true
php artisan view:cache || true
php artisan config:cache || true
php artisan route:cache || true

exec apache2-foreground
