FROM php:8.2-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Fix DirectoryIndex to ensure PHP runs
RUN sed -i 's/DirectoryIndex .*/DirectoryIndex index.php index.html/' /etc/apache2/mods-enabled/dir.conf

# Remove PHP-FPM default config (nếu cần)
RUN rm -f /etc/apache2/conf-enabled/docker-php.conf

# Fix Apache hostname warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install PostgreSQL extensions
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy app source
WORKDIR /var/www/html
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html
