FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libgd-dev libicu-dev libxml2-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql gd zip intl xml curl fileinfo bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan storage:link || true

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
