FROM php:8.1-apache

# Copy the entire site into the Apache document root
COPY . /var/www/html/

# Ensure the messages directory is writable by the webserver
RUN chown -R www-data:www-data /var/www/html && \
	chmod -R 755 /var/www/html

EXPOSE 80