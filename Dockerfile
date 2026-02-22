FROM elrincondeisma/php-for-laravel:8.3.7

WORKDIR /app

# 1. Instalar dependencias de Alpine (Ligeras y seguras)
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    $PHPIZE_DEPS

# 2. Extensiones PHP críticas para Laravel 12, Octane y Filament v4
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    exif \
    gd \
    intl \
    zip \
    pdo_mysql \
    bcmath \
    pcntl

# 3. Instalación de dependencias de Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 4. Copiar el código del proyecto
COPY . .

# 5. Preparar Laravel Octane (Swoole)
# No interactivo para evitar que el build se detenga
RUN php artisan octane:install --server="swoole" --no-interaction

# 6. Permisos para el servidor web (www-data es el estándar)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# 7. Pre-calentar la caché de Laravel 12 y Filament v4
# El comando || true evita que el build falle si no hay conexión a DB en este paso
RUN php artisan icons:cache || true \
    && php artisan filament:cache-components || true \
    && php artisan config:cache || true \
    && php artisan route:cache || true

USER www-data

EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]