FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Fix DirectoryIndex to ensure index.php runs
RUN sed -i 's/DirectoryIndex .*/DirectoryIndex index.php/' /etc/apache2/mods-enabled/dir.conf

# Fix Apache hostname warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install PostgreSQL PDO driver
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy project
WORKDIR /var/www/html
COPY . /var/www/html

# Permissions
RUN chown -R www-data:www-data /var/www/html
