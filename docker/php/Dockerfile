FROM php:8.2-fpm

# Instalacja zależności
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libxml2-dev \
    libonig-dev \
    libpq-dev \
    cron \
    nano

# Instalacja rozszerzeń PHP
RUN docker-php-ext-install \
    pdo_pgsql \
    zip \
    intl \
    gd \
    opcache \
    mbstring \
    xml

RUN pecl install redis \
apcu
RUN docker-php-ext-enable redis
RUN docker-php-ext-enable apcu

COPY apcu.ini /usr/local/etc/php/conf.d/
RUN echo "extension=apcu.so" > /usr/local/etc/php/php.ini

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
# RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony
RUN chmod +x /usr/local/bin/symfony

# Ustawienie katalogu roboczego
WORKDIR /var/www/html

CMD ["php-fpm"]

EXPOSE 9000