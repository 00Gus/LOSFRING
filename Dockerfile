FROM php:8.2-apache

# Instalar extensiones necesarias (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite (opcional, por si se necesita más adelante)
RUN a2enmod rewrite

# Copiar el código de la aplicación al DocumentRoot de Apache
COPY . /var/www/html/

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Variables de entorno por defecto (se pueden sobreescribir en docker-compose)
ENV DB_HOST=db \
    DB_NAME=minismart_db \
    DB_USER=minismart_user \
    DB_PASS=minismart_pass

# Ajustar permisos mínimos (puedes afinar según tu entorno)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80



