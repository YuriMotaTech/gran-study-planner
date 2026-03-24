FROM php:8.3-cli

RUN apt-get update && apt-get install -y git unzip libzip-dev libpq-dev default-mysql-client && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["sh", "-c", "composer install && php -S 0.0.0.0:8080 -t public"]
