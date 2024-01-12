#!/bin/bash

set -eux

apt-get update
apt-get upgrade -yq
apt-get install -yq --no-install-recommends \
  libzip-dev \
  unzip \
  zlib1g-dev
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

apt-get clean
rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
rm -f /var/log/lastlog /var/log/faillog
