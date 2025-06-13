FROM php:8.2-fpm-bullseye

# 必要パッケージのインストール
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    libjpeg-dev libfreetype6-dev locales gnupg \
    libpq-dev libxslt-dev libssl-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Composerインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 作業ディレクトリ設定
WORKDIR /var/www/html