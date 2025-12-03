FROM php:8.2-apache

# Enable Apache modules
RUN a2enmod rewrite

# Install PostgreSQL extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Set Apache Document Root (nếu bạn dùng index.php ở root)
WORKDIR /var/www/html/

# Copy source code
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html
