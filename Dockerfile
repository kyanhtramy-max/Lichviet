FROM php:8.2-apache

# Cài extension PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pgsql pdo pdo_pgsql

# Copy project
COPY . /var/www/html/

# Mở quyền
RUN chown -R www-data:www-data /var/www/html

# Bật mod_rewrite nếu cần
RUN a2enmod rewrite
