FROM php:8.5-fpm-alpine

RUN apk add --no-cache \
    libpq-dev \
    git \
    unzip

RUN docker-php-ext-install pdo_mysql

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --no-scripts --optimize-autoloader --prefer-dist

RUN chown -R www-data:www-data /var/www/html