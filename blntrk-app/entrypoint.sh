#!/bin/bash
set -e

PORT=${PORT:-10000}
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# SQLite hanya untuk percobaan; di Render gunakan database terkelola (DB_CONNECTION=pgsql) agar data permanen
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    mkdir -p /var/www/html/database
    [ -f /var/www/html/database/database.sqlite ] || touch /var/www/html/database/database.sqlite
    echo "PERINGATAN: memakai SQLite di filesystem sementara, data hilang saat redeploy/restart." >&2
fi

mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Migrasi + data dasar (idempoten). Data demo hanya bila SEED_DEMO=true.
php artisan migrate --force --seed
if [ "${SEED_DEMO:-false}" = "true" ]; then
    php artisan db:seed --class=TransaksiDemoSeeder --force || true
fi

php artisan storage:link || true
php artisan view:cache || true
php artisan config:cache || true
php artisan route:cache || true

# Scheduler Laravel (status terlambat/kedaluwarsa) dijalankan tiap menit di latar belakang
(
    while true; do
        su -s /bin/bash www-data -c "php /var/www/html/artisan schedule:run" >> /var/www/html/storage/logs/scheduler.log 2>&1 || true
        sleep 60
    done
) &

exec apache2-foreground
