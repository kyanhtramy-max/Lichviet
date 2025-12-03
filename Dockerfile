FROM php:8.2-apache

# Enable apache rewrite + php module
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install PostgreSQL PDO + pgsql
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Apache must serve PHP
RUN sed -i 's/DirectoryIndex .*/DirectoryIndex index.php index.html/' /etc/apache2/mods-enabled/dir.conf

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . /var/www/html

# Fix permission
RUN chown -R www-data:www-data /var/www/html
