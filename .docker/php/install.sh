#!/bin/sh
set -eu

apk update
apk upgrade
apk add --no-cache \
  libzip-dev \
  linux-headers \
  unzip \
  zlib-dev
apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS
docker-php-ext-install zip

if [ "$1" = '--no-dev' ]; then
  mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
elif [ "$1" = '--dev' ]; then
  pecl install xdebug-3.3.1
  docker-php-ext-enable xdebug
  mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
  cp xdebug.ini "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"
else
  echo "Invalid option: $1"
  exit 1
fi

apk del .build-dependencies
rm -rf /tmp/* /var/tmp/*
