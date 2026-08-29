FROM php:8.4-fpm

# Linux(Debian) 패키지와 PHP 확장 설치
RUN apt-get update \
    && apt-get install -y git curl unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Composer 설치
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]