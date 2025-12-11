FROM dunglas/frankenphp:1.1-php8.2

# Laravel debe vivir en /app para Railway
WORKDIR /app

# Copiamos TODO el proyecto
COPY . .

# Instalamos dependencias Laravel
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Permisos necesarios
RUN chmod -R 777 storage bootstrap/cache

# Exponer el puerto esperado por Railway
EXPOSE 8080

# Ejecutar frankenphp con el Caddyfile del proyecto
CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
