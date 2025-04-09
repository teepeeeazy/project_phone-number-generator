FROM php:8.3-apache

RUN a2enmod rewrite

COPY ./app /var/www/html/

# Copy a custom Apache configuration
COPY ./access.conf /etc/apache2/conf-available/custom-access.conf

# Enable the custom configuration
RUN a2enconf custom-access

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

WORKDIR /var/www/html/

EXPOSE 8080