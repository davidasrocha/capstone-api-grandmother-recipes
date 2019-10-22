FROM composer:1.8 as composer
FROM php:7.3-fpm

ENV PHP_MAX_EXECUTION_TIME=30
ENV PHP_MEMORY_LIMIT=128M
ENV PHP_MAX_INPUT_VARS=1000
ENV PHP_UPLOAD_MAX_FILESIZE=10M
ENV PHP_POST_MAX_SIZE=10M
ENV PHP_MAX_FILE_UPLOAD=10
ENV PHP_ERROR_REPORTING="E_ALL & ~E_STRICT & ~E_DEPRECATED"
ENV PHP_DISPLAY_ERRORS=Off
ENV PHP_DISPLAY_STARTUP_ERRORS=Off

ENV PATH="$PATH:/var/www/html/bin"

ENV APP_ENV=prod
ENV APP_DEBUG=0

WORKDIR /var/www/html

COPY . /var/www/html

# copy php configurations files
# remove unused folders
RUN cp -R ./docker/php/conf.d/* /usr/local/etc/php/conf.d && \
    rm -rf ./docker ./kubernetes

# hadolint ignore=DL3008,DL3015
RUN apt-get update -y && \
    apt-get install -y \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libssl-dev && \
#     remove custom repositories
     rm -rf /etc/apt/sources.list.d/*

RUN docker-php-ext-install pdo pdo_mysql zip bcmath gd

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --no-scripts -o

EXPOSE 9000