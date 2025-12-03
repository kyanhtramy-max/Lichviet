FROM php:8.2-apache

# Install PostgreSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Enable mod_rewrite
RUN a2enmod rewrite

# Fix ServerName warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# 🔥 ÉP APACHE ƯU TIÊN INDEX.PHP
RUN sed -i 's/DirectoryIndex .*/DirectoryIndex index.php index.html/g' /etc/apache2/mods-enabled/dir.conf

# Copy source code
WORKDIR /var/www/html
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html
