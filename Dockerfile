FROM composer:2.8 as builder
WORKDIR /app
COPY composer.* ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader
COPY . .

FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql opcache

WORKDIR /var/www
COPY --from=builder /app .

ENV APACHE_DOCUMENT_ROOT /var/www/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf \
 && a2enmod rewrite headers

RUN chown -R www-data:www-data /var/www

EXPOSE 80
CMD ["apache2-foreground"]
