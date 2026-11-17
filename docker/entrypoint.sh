#!/bin/sh
set -e

# Konfigurasi port dinamis untuk Railway / Render / Cloud Run ($PORT)
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf

# Pastikan kepemilikan dan permission storage & bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Jika berjalan di environment production dan APP_KEY tersedia, jalankan cache optimasi
if [ -n "$APP_KEY" ]; then
    echo "Mengoptimalkan konfigurasi Laravel..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Jalankan migrasi database otomatis jika variabel RUN_MIGRATIONS=true
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Menjalankan migrasi database..."
    php artisan migrate --force || true
fi

# Jalankan seeder database otomatis jika variabel RUN_SEEDER=true
if [ "$RUN_SEEDER" = "true" ]; then
    echo "Menjalankan database seeder..."
    php artisan db:seed --force || true
fi

echo "Memulai server Apache pada port ${PORT}..."
exec apache2-foreground
