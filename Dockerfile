# Extiendo la imagen oficial de PHP con Apache
FROM php:7.4-apache

# Instalar dependencias del sistema para Composer y extensiones de PHP
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libzip-dev \
        && docker-php-ext-install zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www/html