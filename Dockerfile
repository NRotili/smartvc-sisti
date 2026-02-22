FROM elrincondeisma/php-for-laravel:8.3.7

WORKDIR /app

# 1. Instalamos dependencias del sistema necesarias usando APK (Alpine)
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    $PHPIZE_DEPS

# 2. Configurar e instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install exif gd

# 3. Copiamos el resto del código
COPY . .
RUN composer install --no-dev --optimize-autoloader

# 4. Permisos críticos para Laravel/Filament
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# 5. Configuración de Octane
# Nota: Si ya tienes octane instalado en el repo, no necesitas 'octane:install'
# Pero aseguramos que las dependencias de Swoole estén listas.
RUN php artisan octane:install --server="swoole"

# 6. Optimizaciones de Filament v4 (Súper importante)
RUN php artisan icons:cache
RUN php artisan filament:cache-components
RUN php artisan view:cache

EXPOSE 8000

# Usamos el usuario no-root por seguridad
USER www-data

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]