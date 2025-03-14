# Utilizzo di PHP 8.2 con Apache
FROM php:8.2-apache

# Installo le dipendenze necessarie per l'estensione PDO MySQL
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Installo l'estensione PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Installo l'estensione mongodb via PECL e la abilito
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Abilito il modulo rewrite di Apache
# RUN a2enmod rewrite

# Set the ServerName directive globally to suppress warnings
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Script di entrypoint per la configurazione del container
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]

# Expose port 80 per l'accesso al server web
EXPOSE 80
