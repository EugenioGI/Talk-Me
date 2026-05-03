FROM php:8.3-apache-bookworm

# 1. Dependencias del sistema
RUN apt update && apt install -y \
    unzip \
    libzip-dev \
    && docker-php-ext-install mysqli pdo_mysql zip

# 2. Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 3. Habilitar mod_rewrite
RUN a2enmod rewrite

# 4. Configurar el directorio de trabajo
WORKDIR /var/www/html

# 5. Copiar archivos de Composer primero
COPY ./WebApp/composer.* ./

# 6. Instalar librerías
RUN composer install --no-dev --no-scripts --no-autoloader

# 7. COPIAR EL CONTENIDO DE WebApp
# Esto pondrá index.php, login.php y la carpeta vendor en /var/www/html/
COPY ./WebApp/ . 

# 8. Finalizar Autoload
RUN composer dump-autoload --optimize

# 9. Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80