FROM webdevops/php-nginx:8.2

# Directorio de trabajo
WORKDIR /app

# Copiamos el proyecto
COPY . .

# Copiamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalamos dependencias de Laravel
RUN composer install --optimize-autoloader --no-dev --prefer-dist

# Ajustamos permisos necesarios
RUN chown -R application:application /app/storage /app/bootstrap/cache

# Importante: indicamos dónde está el index.php
ENV WEB_DOCUMENT_ROOT=/app/public

# Railway usa puerto 8080
EXPOSE 8080
