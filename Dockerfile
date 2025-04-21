# Sử dụng PHP 8.1 FPM image
FROM php:8.1-fpm

# Cài đặt các dependencies cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    zip \
    unzip \
    curl \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl gd opcache pdo pdo_mysql

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài đặt Laravel dependencies
WORKDIR /app
COPY . .

# Cài đặt Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port for Laravel
EXPOSE 9000

# Khởi động PHP-FPM
CMD ["php-fpm"]
