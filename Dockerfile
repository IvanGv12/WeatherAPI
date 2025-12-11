FROM richarvey/nginx-php-fpm:latest

# Directorio base
WORKDIR /var/www/html

# Copiar el proyecto completo
COPY . .

# Configurar Laravel para que Nginx sirva el public/
ENV DOCUMENT_ROOT=/var/www/html/public
ENV WEBROOT=/var/www/html/public

# Instalar dependencias de Composer (solo producción)
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Generar storage link
RUN php artisan storage:link || true

# Optimizar Laravel
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan view:clear || true
RUN php artisan route:clear || true

RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Permisos recomendados para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# Puerto detectado por Railway
EXPOSE 80
