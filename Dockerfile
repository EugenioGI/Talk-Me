# Usamos PHP 8.3 con Apache incluido (esto reemplaza tus dos imágenes)
FROM php:8.3-apache-bookworm

# 1. Actualizamos e instalamos las extensiones que tenías en tu archivo de PHP
RUN apt update && apt upgrade -y
RUN docker-php-ext-install mysqli pdo_mysql

# 2. Habilitamos mod_rewrite (casi siempre necesario en PHP/Apache)
RUN a2enmod rewrite

# 3. Copiamos tu código
# Según tu imagen anterior, el index.php está en WebApp/public
# Si quieres que la URL sea directa, copiamos el contenido de public
COPY ./WebApp/public/ /var/www/html/

# 4. Ajustamos permisos
RUN chown -R www-data:www-data /var/www/html

# 5. Render usa el puerto 80 por defecto
EXPOSE 80