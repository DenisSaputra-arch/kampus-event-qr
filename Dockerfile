# ==============================================================================
# Dockerfile Ringan untuk Kampus Event QR (Laravel 13 + PHP 8.4 + Apache)
# Aset frontend (public/build) telah terkompilasi, hemat memori di Railway/Render
# ==============================================================================

# ------------------------------------------------------------------------------
# Tahap 1: Install Dependensi PHP (Composer)
# ------------------------------------------------------------------------------
FROM composer:2.8 AS vendor-builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# ------------------------------------------------------------------------------
# Tahap 2: Citra Produksi Utama (PHP 8.4 Apache)
# ------------------------------------------------------------------------------
FROM php:8.4-apache

# Download script instalasi ekstensi PHP resmi (mlocati)
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

# Install ekstensi PHP yang diperlukan Laravel & Simple QR Code
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    unzip \
    && install-php-extensions \
    bcmath \
    ctype \
    curl \
    dom \
    fileinfo \
    filter \
    hash \
    intl \
    json \
    mbstring \
    openssl \
    pcre \
    pdo \
    pdo_mysql \
    session \
    tokenizer \
    xml \
    zip \
    gd \
    opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Aktifkan Apache mod_rewrite dan mod_headers
RUN a2enmod rewrite headers

# Ubah DocumentRoot Apache ke direktori /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Izinkan .htaccess Override pada direktori public
RUN { \
    echo '<Directory /var/www/html/public>'; \
    echo '    Options -Indexes +FollowSymLinks'; \
    echo '    AllowOverride All'; \
    echo '    Require all granted'; \
    echo '</Directory>'; \
} > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Konfigurasi PHP Production
RUN { \
    echo 'expose_php = Off'; \
    echo 'display_errors = Off'; \
    echo 'display_startup_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'memory_limit = 256M'; \
    echo 'post_max_size = 20M'; \
    echo 'upload_max_filesize = 10M'; \
    echo 'opcache.enable = 1'; \
    echo 'opcache.memory_consumption = 128'; \
    echo 'opcache.interned_strings_buffer = 8'; \
    echo 'opcache.max_accelerated_files = 10000'; \
    echo 'opcache.validate_timestamps = 0'; \
} > /usr/local/etc/php/conf.d/laravel-production.ini

WORKDIR /var/www/html

# Salin kode sumber aplikasi (termasuk folder public/build hasil kompilasi)
COPY . .

# Salin hasil vendor dari tahap composer
COPY --from=vendor-builder /app/vendor/ /var/www/html/vendor/

# Salin script entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permission direktori storage dan cache
RUN mkdir -p /var/www/html/storage/framework/cache \
             /var/www/html/storage/framework/sessions \
             /var/www/html/storage/framework/views \
             /var/www/html/storage/logs \
             /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Port default (dapat ditimpa oleh variabel $PORT pada Railway/Render)
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
