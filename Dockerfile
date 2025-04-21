FROM php:8.2-fpm

# Cài đặt các phụ thuộc cần thiết
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Đặt thư mục làm việc cho container
WORKDIR /var/www

# Sao chép các file ứng dụng của bạn vào container
COPY . .

# Cài đặt các phụ thuộc PHP với Composer
RUN composer install --no-dev --optimize-autoloader

# Expose port 9000 để PHP-FPM có thể phục vụ HTTP requests
EXPOSE 9000

# Khởi động php-fpm server
CMD ["php-fpm", "-F"]
