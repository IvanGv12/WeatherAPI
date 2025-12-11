# 1. Base Image
FROM richarvey/nginx-php-fpm:latest

# 2. Set Environment Variables
# Permite que composer se ejecute como root sin advertencias (necesario en muchos entornos de contenedores como Railway).
ENV COMPOSER_ALLOW_SUPERUSER=1
# Define el directorio donde trabajaremos dentro del contenedor.
WORKDIR /var/www/html

# 3. Copy Application Code
# Copiamos todo el proyecto. Composer se ejecutará después en el Start Command.
COPY . .

# 4. Configure Web Server Root
# Estas variables configuran la imagen de Nginx/PHP-FPM para servir desde 'public'.
ENV DOCUMENT_ROOT=/var/www/html/public
ENV WEBROOT=/var/www/html/public

# 5. Laravel Setup Commands
# Ejecutamos comandos de optimización y permisos.
# Nota: Ahora estos comandos se ejecutan antes de que la app inicie.

# 5.1. Permisos recomendados para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

# 5.2. Generar storage link
RUN php artisan storage:link || true

# 5.3. Limpieza de caché
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan view:clear || true
RUN php artisan route:clear || true

# 5.4. Optimización de caché (Opcional, pero útil para rendimiento)
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# 6. Expose Port
EXPOSE 80