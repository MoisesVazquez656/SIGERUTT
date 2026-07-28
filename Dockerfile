FROM php:8.2-apache

RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql curl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# El proyecto asume que vive en una subcarpeta /SIGERUTT/ (BASE_URL se
# calcula a partir del nombre de carpeta, igual que en un XAMPP/htdocs local).
COPY . /var/www/html/SIGERUTT/

RUN printf '<?php header("Location: /SIGERUTT/login.php"); exit; ?>' > /var/www/html/index.php

EXPOSE 80
