FROM php:8.2-apache

# Enable PHP + Apache modules
RUN a2enmod php8.2
RUN a2enmod rewrite

# Fix DirectoryIndex to load index.php first
RUN sed -i 's/DirectoryIndex .*/DirectoryIndex index.php index.html/' /etc/apache2/mods-enabled/dir.conf

# Install PostgreSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Fix Apache ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy code to web root
WORKDIR /var/www/html
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html
