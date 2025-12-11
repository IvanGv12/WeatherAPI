# 1. Base Image
FROM richarvey/nginx-php-fpm:latest

# 2. Set Environment Variables
# Permite que composer se ejecute como root
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /var/www/html

# 3. Copy Application Code
# Copiamos solo los archivos de Composer para cachear la capa
COPY composer.json composer.lock ./  

# 4. Install Dependencies
# Instalamos las dependencias ignorando requerimientos de PHP antiguos
# Esto previene fallos por el conflicto de 'hirak/prestissimo'
RUN composer install --no-dev --optimize-autoloader --prefer-dist --ignore-platform-reqs

# 5. Copy Remaining Code
# Copiamos el resto del proyecto.
COPY . .

# 6. Configure Web Server Root
# Estas variables configuran la imagen de Nginx/PHP-FPM para servir desde 'public'.
ENV DOCUMENT_ROOT=/var/www/html/public
ENV WEBROOT=/var/www/html/public

# 7. Laravel Setup Commands
RUN chown -R www-data:www-data storage bootstrap/cache
RUN php artisan storage:link || true
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan view:clear || true
RUN php artisan route:clear || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# 8. Expose Port
EXPOSE 80