FROM php:7.1-fpm

RUN apt-get update

# Some basic extensions
RUN docker-php-ext-install -j$(nproc) json mbstring pdo pdo_mysql

# Curl
RUN apt-get install -y libcurl4-openssl-dev
RUN docker-php-ext-install -j$(nproc) curl

# Intl
RUN apt-get install -y libicu-dev
RUN docker-php-ext-install -j$(nproc) intl

# Zip
RUN apt-get install -y libzip-dev
RUN docker-php-ext-install -j$(nproc) zip

# Composer
RUN curl -sS https://getcomposer.org/installer | php \
   && mv composer.phar /usr/local/bin/composer \
   && chmod +x /usr/local/bin/composer

#xDebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini