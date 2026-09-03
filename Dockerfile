FROM php:8.2-apache

# Enable mysqli extension for MySQL connectivity
RUN docker-php-ext-install mysqli

# Apache should listen on the port Render provides
ENV APACHE_LISTEN_PORT=10000
RUN sed -i "s/80/\${APACHE_LISTEN_PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

RUN a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 10000

CMD ["apache2-foreground"]
