FROM php:8.2-fpm
RUN apt-get update \
    && apt-get -y upgrade \
    && apt-get -y install libzip-dev git
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN docker-php-ext-install zip

# EXPOSE 8080

CMD ["php-fpm"]
