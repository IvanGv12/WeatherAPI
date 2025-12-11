# 1. Base Image
FROM richarvey/nginx-php-fpm:latest

# 2. Set Environment Variables
# Permite que composer se ejecute como root sin advertencias (necesario en muchos entornos de contenedores como Railway).
ENV COMPOSER_ALLOW_SUPERUSER=1
# Define el directorio donde trabajaremos dentro del contenedor.
WORKDIR /var/www/html

# 3. Copy Application Code
# Copiamos primero solo los archivos de Composer para cachear la capa de instalación de dependencias.
COPY composer.json composer.lock ./

# 4. Install Dependencies
# Instalamos las dependencias. Esto es CRÍTICO que suceda ANTES de cualquier comando 'php artisan'.
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# 5. Copy Remaining Code
# Copiamos el resto del proyecto.
COPY . .

# 6. Configure Web Server Root
# Estas variables configuran la imagen de Nginx/PHP-FPM para servir desde 'public'.
ENV DOCUMENT_ROOT=/var/www/html/public
ENV WEBROOT=/var/www/html/public

# 7. Laravel Setup Commands
# Ejecutamos comandos de optimización y permisos.
# Nota: La mayoría de los comandos 'artisan' fallarán si la APP_KEY o las credenciales DB están mal, por eso añadimos '|| true'.
# Railway es muy eficiente al gestionar el caché de configuración, por lo que muchos de estos 'clear'/'cache' son opcionales.

# 7.1. Permisos recomendados para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# 7.2. Generar storage link
RUN php artisan storage:link || true

# 7.3. Limpieza de caché
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan view:clear || true
RUN php artisan route:clear || true

# 7.4. Optimización de caché (Opcional, pero útil para rendimiento)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# 8. Expose Port
EXPOSE 80