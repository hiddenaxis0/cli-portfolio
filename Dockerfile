FROM php:8.1-apache

WORKDIR /var/www/html

# Install Composer and dependencies for SMTP mailing
RUN apt-get update && apt-get install -y --no-install-recommends unzip git ca-certificates && rm -rf /var/lib/apt/lists/*
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm composer-setup.php

COPY composer.json /var/www/html/
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy the site into the Apache document root
COPY . /var/www/html/
RUN composer dump-autoload --optimize --no-dev

# Ensure the messages directory is writable by the webserver
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

EXPOSE 80