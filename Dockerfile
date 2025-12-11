FROM webdevops/php-nginx:8.2

WORKDIR /app

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 775 storage bootstrap/cache

ENV WEB_DOCUMENT_ROOT=/app/public

EXPOSE 8080
