FROM php:8.2-apache

# Cài extension PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Bật mod_rewrite
RUN a2enmod rewrite

# Bật PHP (CỰC QUAN TRỌNG CHO APACHE)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set DocumentRoot
WORKDIR /var/www/html/

# Copy code vào sau khi enable module
COPY . /var/www/html/

# Fix quyền
RUN chown -R www-data:www-data /var/www/html
