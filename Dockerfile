# Sử dụng PHP 8.1
FROM php:8.1-cli

# Cài đặt các thư viện và công cụ cần thiết
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev git zip unzip

# Cài đặt GD extension cho PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Thiết lập thư mục làm việc cho ứng dụng
WORKDIR /app

# Copy các file từ dự án vào trong Docker container
COPY . .

# Cài đặt các phụ thuộc Laravel thông qua Composer
RUN composer install --no-dev
