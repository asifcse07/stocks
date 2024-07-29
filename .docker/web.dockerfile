FROM php:8.2-apache

RUN apt-get update && apt-get upgrade -y && apt-get install wget -y
RUN apt-get install wget libmcrypt-dev libpng-dev libfreetype6-dev libjpeg-dev ghostscript libzip-dev -y
RUN apt-get install git curl npm -y
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN apt-get install -y libzip-dev zip
RUN docker-php-ext-install zip

# composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY .docker/docker.php.ini /usr/local/etc/php/conf.d/docker.php.ini

# install phpredis
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-configure pcntl --enable-pcntl && docker-php-ext-install pcntl

RUN apt-get install -y libssh2-1 libssh2-1-dev
RUN pecl install ssh2-1.3.1
RUN docker-php-ext-enable ssh2

RUN docker-php-ext-install pdo pdo_mysql mysqli pcntl posix

RUN ln -s /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY .docker/docker.conf /etc/apache2/sites-enabled/000-default.conf

RUN a2enmod rewrite
