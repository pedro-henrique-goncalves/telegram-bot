FROM php:8.2-fpm-alpine

WORKDIR /var/www/telegram-bot

# Dependências do sistema
RUN apk add --no-cache \
    bash \
    git \
    nano \
    nodejs \
    npm \
    yarn \
    mysql-dev \
    libxml2-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    autoconf \
    gcc \
    g++ \
    make

# Extensões PHP necessárias para Laravel + Horizon
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    bcmath \
    pdo_mysql \
    pcntl \
    mbstring \
    zip

# Composer
RUN curl -sS https://getcomposer.org/installer \
 | php -- --install-dir=/usr/local/bin --filename=composer
