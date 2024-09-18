FROM php:7.4-apache

RUN apt-get update \
    &&  apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev \
    && pecl install apcu \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN a2enmod rewrite

RUN  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer 

RUN  curl -sS https://get.symfony.com/cli/installer | bash \
    &&  mv /root/.symfony5/bin/symfony /usr/local/bin/symfony 

RUN git config --global user.email "votremailgitHub"
RUN git config --global user.name "votrenamegitHub"


WORKDIR /var/www/


EXPOSE 80