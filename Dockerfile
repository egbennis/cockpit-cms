FROM php:8.3-apache

# Install dependencies and required extensions
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libsqlite3-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip pdo pdo_sqlite

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Cockpit files
COPY . /var/www/html/

# Create storage directories with proper permissions
RUN mkdir -p ./storage/cache ./storage/tmp ./storage/uploads ./storage/data ./storage/database \
    && chmod -R 0777 ./storage \
    && chown -R www-data:www-data ./storage

# Set correct permissions for the entire app
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 777 /var/www/html/storage

# Configure Apache properly
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/cockpit.conf \
    && a2enconf cockpit

# Create a startup script
RUN echo '#!/bin/bash\n\
# Make sure storage is writable on startup\n\
chmod -R 0777 /var/www/html/storage\n\
chown -R www-data:www-data /var/www/html/storage\n\
# Set document root environment variable\n\
export APACHE_DOCUMENT_ROOT=/var/www/html\n\
# Configure port\n\
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf\n\
apache2-foreground' > /usr/local/bin/start-apache.sh \
    && chmod +x /usr/local/bin/start-apache.sh

# Start Apache with our custom script
CMD ["/usr/local/bin/start-apache.sh"]
