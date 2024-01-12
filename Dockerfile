#syntax=docker/dockerfile:1.4

ARG PHP_VERSION=8.2
ARG COMPOSER_VERSION=2.6

FROM composer:${COMPOSER_VERSION} AS composer_image

FROM php:${PHP_VERSION}-fpm AS php_image
LABEL org.opencontainers.image.licenses="proprietary" \
      #org.opencontainers.image.base.name="docker.io/bitnami/minideb:bullseye" \
      #org.opencontainers.image.created="2023-10-11T20:26:42Z" \
      #org.opencontainers.image.ref.name="4.0.2-3-debian-11-r21" \
      #org.opencontainers.image.source=""
      #org.opencontainers.image.title="opencart" \
      org.opencontainers.image.url="https://github.com/max-antipin/web-proxy" \
      org.opencontainers.image.vendor="Max Antipin <max.v.antpin@gmail.com>" \
      org.opencontainers.image.version="0.0.2"
ARG PRODUCT_NAME="Maxie Systems Web Proxy"
# Do not need to change WORKDIR: it was set to /var/www/html in the previous image.
# WORKDIR /var/www/html/

FROM php_image AS php_dev
LABEL org.opencontainers.image.description="${PRODUCT_NAME} development environment"
RUN --mount=type=bind,source=.docker/php/install.sh,target=install.sh \
    --mount=type=bind,source=.docker/php/xdebug.ini,target=xdebug.ini \
  set -eu && ./install.sh --dev
COPY --from=composer_image --link /usr/bin/ /usr/local/bin/composer

FROM scratch AS app_src
COPY ./public/ ./public/
COPY ./public-dolly/ ./public-dolly/
COPY ./src/ ./src/
COPY ./templates/ ./templates/

FROM php_dev AS php_app_test
LABEL org.opencontainers.image.description="${PRODUCT_NAME} test environment"
COPY --link composer.* phpunit.xml ./
COPY --link --from=app_src . .
COPY --link ./tests/ ./tests/
RUN set -eux; \
  mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"; \
  composer install --classmap-authoritative --no-progress

FROM php_image AS php_app
LABEL org.opencontainers.image.description="${PRODUCT_NAME} application"
RUN --mount=type=bind,source=.docker/php/install.sh,target=install.sh \
  set -eu && ./install.sh --no-dev
COPY --link --from=app_src . .
RUN --mount=type=bind,source=composer.json,target=composer.json \
    --mount=type=bind,source=composer.lock,target=composer.lock \
    --mount=type=bind,from=composer_image,source=/usr/bin/composer,target=/usr/local/bin/composer \
  set -eux; \
  composer install --classmap-authoritative --no-progress --no-dev
