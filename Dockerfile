FROM dunglas/frankenphp:latest

WORKDIR /app

COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
