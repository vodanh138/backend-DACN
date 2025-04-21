FROM php:8.2-fpm

# Cài thêm nginx, supervisor
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpng-dev libjpeg-dev libfreetype6-dev zip git unzip curl

# Cài extension PHP
RUN docker-php-ext-install pdo pdo_mysql

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục web
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Copy file config Nginx
COPY ./nginx.conf /etc/nginx/sites-available/default

# Copy file cấu hình supervisor
COPY ./supervisord.conf /etc/supervisord.conf

# Expose cổng HTTP
EXPOSE 80

# Lệnh khởi động cả nginx và php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
