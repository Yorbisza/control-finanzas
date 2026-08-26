FROM php:8.2-fpm

# Instalar dependencias del sistema, extensiones de PostgreSQL y certificados SSL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    ca-certificates \
    nginx

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# IMPORTANTE: Antes de instalar, nos aseguramos de que el archivo de configuración no rompa el build
# Si usas la opción 1009 => 1 en config/database.php, esto pasará sin problemas.
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Dar permisos a las carpetas de almacenamiento
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8080

# Comando de arranque: Limpiar caché, migrar y servir
# Usamos un script de una sola línea para asegurar que si algo falla, lo veamos en los logs
CMD php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php artisan serve --host 0.0.0.0 --port 8080
