FROM dunglas/frankenphp:1.1-php8.2

# Laravel debe vivir en /app para Railway
WORKDIR /app

# Copiamos TODO el proyecto
COPY . .

# Instalamos dependencias Laravel
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Permisos necesarios
RUN chmod -R 777 storage bootstrap/cache

ENV DOCUMENT_ROOT=/var/www/html/public
ENV WEBROOT=/var/www/html/public

RUN sed -i 's|root /var/www/html;|root /var/www/html/public;|g' /etc/nginx/sites-enabled/default.conf


# Exponer el puerto esperado por Railway
EXPOSE 8080

# Ejecutar frankenphp con el Caddyfile del proyecto
CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
