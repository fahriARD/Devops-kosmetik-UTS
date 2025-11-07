FROM php:8.2-apache

# Aktifkan ekstensi mysqli
RUN docker-php-ext-install mysqli

# Copy source code
COPY . /var/www/html/

# Ekspos port 80
EXPOSE 80
