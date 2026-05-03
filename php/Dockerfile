FROM php:8.3.0-fpm-bookworm
RUN apt update
RUN apt upgrade -y
RUN docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql 
