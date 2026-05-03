FROM php:8.3-apache-bookworm

# 1. Dependencias
RUN apt update && apt install -y unzip libzip-dev && docker-php-ext-install mysqli pdo_mysql zip

# 2. Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 3. Habilitar mod_rewrite y apuntar Apache a la carpeta /public
RUN a2enmod rewrite
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Directorio de trabajo
WORKDIR /var/www/html

# 5. Copiar archivos de dependencias y ejecutar Composer
COPY ./WebApp/composer.* ./
RUN composer install --no-dev --no-scripts --no-autoloader

# 6. Copiar TODO el proyecto (incluyendo las carpetas public, vendor, assets, etc.)
COPY ./WebApp/ .

# 7. Finalizar Autoload
RUN composer dump-autoload --optimize

# 8. Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80