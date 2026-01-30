FROM php:8.2-apache

# Habilitar mod_rewrite
WORKDIR /etc/apache2
RUN \
    a2enmod rewrite \
    && sed -i '172c\\tAllowOverride All' apache2.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Instalar extensiones necesarias
RUN apt-get update && apt-get install --yes --no-install-recommends \
    zlib1g-dev \
    libzip-dev \
    unzip \
    curl \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libssl-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql 

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar archivos del proyecto
COPY . /var/www/html/

# Instalar vlucas/phpdotenv
RUN composer require vlucas/phpdotenv