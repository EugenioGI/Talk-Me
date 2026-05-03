# Usamos PHP con Apache
FROM php:8.3-apache-bookworm

# 1. Instalamos dependencias del sistema y extensiones de PHP
RUN apt update && apt upgrade -y && apt install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install mysqli pdo_mysql zip

# 2. Instalamos Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 3. Habilitamos mod_rewrite
RUN a2enmod rewrite

# 4. Copiamos los archivos de Composer primero para aprovechar la caché de Docker
WORKDIR /var/www/html
COPY ./WebApp/composer.* ./

# 5. Instalamos las dependencias de PHP (sin scripts para evitar errores)
RUN composer install --no-dev --no-scripts --no-autoloader

# 6. Copiamos TODO el código de tu WebApp
COPY ./WebApp/ /var/www/html/

# 7. Generamos el autoloader final
RUN composer dump-autoload --optimize

# 8. Permisos para Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80