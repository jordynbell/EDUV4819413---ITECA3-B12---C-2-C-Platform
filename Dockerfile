FROM php:8.3-apache

WORKDIR /var/www
COPY . .
COPY config/config.main.php /var/www/config/config.php

ENV APACHE_DOCUMENT_ROOT=/var/www/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
 && a2enmod rewrite \
 && docker-php-ext-install pdo pdo_mysql mysqli

EXPOSE 80
CMD ["apache2-foreground"]