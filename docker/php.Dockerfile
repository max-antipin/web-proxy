ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-fpm AS php-dev
RUN apt-get update \
    && apt-get install -y zlib1g-dev libzip-dev unzip \
    && docker-php-ext-install zip \
    && pecl install xdebug-3.2.1 \
	&& docker-php-ext-enable xdebug \
    && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html/
# ниже нужно копировать не index.php, а полностью собранную версию скрипта.
#COPY ./public/index.php ./public-w-dirs/my/sub-folder/
#COPY ./public-dolly/ ./public-w-dirs/dolly-sites/v2/