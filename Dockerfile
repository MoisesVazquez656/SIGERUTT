FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql && a2enmod rewrite

# El proyecto asume que vive en una subcarpeta /SIGERUTT/ (BASE_URL se
# calcula a partir del nombre de carpeta, igual que en un XAMPP/htdocs local).
COPY . /var/www/html/SIGERUTT/

RUN printf '<?php header("Location: /SIGERUTT/login.php"); exit; ?>' > /var/www/html/index.php

EXPOSE 80
