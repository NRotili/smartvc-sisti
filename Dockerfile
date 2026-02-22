FROM php:8.3-apache

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel/Filament
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl gd bcmath zip pdo_mysql opcache

# Configurar Apache: habilitar mod_rewrite y cambiar el DocumentRoot a /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN a2enmod rewrite

# Copiar el código del proyecto
COPY . /var/www/html

# Establecer permisos para carpetas de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html