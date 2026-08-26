FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones de PostgreSQL para Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nginx

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Dar permisos a las carpetas de almacenamiento
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host 0.0.0.0 --port 8080
