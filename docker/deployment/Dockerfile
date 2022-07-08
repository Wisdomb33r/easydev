FROM php:8.1-apache

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN chown -R www-data:www-data /var/www/

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli && apachectl restart
