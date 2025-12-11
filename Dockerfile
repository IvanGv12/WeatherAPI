# Imagen base recomendada para Laravel en Railway
FROM dunglas/frankenphp:1.2

WORKDIR /app

# Copiar archivos de composer primero para generar cache
COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Copiar el resto del proyecto
COPY . .

# Asegurar permisos
RUN chmod -R 775 storage bootstrap/cache

# Optimizar laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Exponer puerto 8080 (FrankenPHP usa este por defecto)
EXPOSE 8080

# Ejecutar Laravel con FrankenPHP
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
