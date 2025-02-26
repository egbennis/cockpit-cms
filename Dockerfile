FROM php:8.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Cockpit files
COPY . /var/www/html/

# Create storage directories
RUN mkdir -p ./storage/cache ./storage/tmp ./storage/uploads ./storage/data ./storage/database \
    && chmod -R 0777 ./storage

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/cockpit.conf \
    && a2enconf cockpit

# Create a script to handle the dynamic port
RUN echo '#!/bin/bash\n\
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf\n\
echo "Listen $PORT" > /etc/apache2/ports.conf\n\
echo "ServerName localhost" >> /etc/apache2/apache2.conf\n\
apache2-foreground' > /usr/local/bin/start-apache.sh \
    && chmod +x /usr/local/bin/start-apache.sh

# Start Apache with our custom script
CMD ["/usr/local/bin/start-apache.sh"]
