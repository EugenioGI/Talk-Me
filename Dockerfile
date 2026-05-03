FROM php:8.3-apache-bookworm

# 1. Instalamos lo necesario
RUN apt update && apt install -y unzip libzip-dev && docker-php-ext-install mysqli pdo_mysql zip

# 2. Instalamos Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 3. Configuramos Apache para que su raíz sea la carpeta /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# 4. Directorio de trabajo
WORKDIR /var/www/html

# 5. Copiamos archivos de Composer de tu carpeta WebApp
COPY ./WebApp/composer.* ./

# 6. Instalamos librerías (esto creará la carpeta /var/www/html/vendor)
RUN composer install --no-dev --no-scripts --no-autoloader

# 7. Copiamos TODO el contenido de WebApp al contenedor
# Esto incluye las carpetas public, resources, css, js, etc.
COPY ./WebApp/ .

# 8. Generamos el autoloader
RUN composer dump-autoload --optimize

# 9. Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80