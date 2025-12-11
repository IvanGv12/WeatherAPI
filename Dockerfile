FROM php:8.2-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev nodejs npm \
    && docker-php-ext-install zip pdo pdo_mysql

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Crear carpeta de aplicación
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Instalar dependencias FRONT
RUN npm install && npm run build

# Permisos
RUN chmod -R 775 storage bootstrap/cache public/build \
    && chown -R www-data:www-data storage bootstrap/cache public/build

# Exponer puerto
EXPOSE 8080

# Comando principal
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
