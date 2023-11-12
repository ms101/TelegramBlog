FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    unzip

# Install and enable Xdebug
RUN pecl install xdebug-3.2.1 \
	&& docker-php-ext-enable xdebug