# PHP 8.2 con Apache
FROM php:8.2-apache

# Dipendenze necessarie per l'estensione PDO MySQL
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Installo PDO MySQL
RUN docker-php-ext-install pdo_mysql

# Installo mongodb via PECL e la abilito
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Per risolvere i messaggi di avviso di Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Creo ed eseguo startup.sh per eseguire il seed dei dati e avviare Apache
RUN echo '#!/bin/bash\n\
set -e\n\
cd /var/www/html\n\
php config/seed_data.php\n\
echo -e "\n=== BOSTARTER INIZIALIZZATO. PIATTAFORMA PRONTA! ===\n"\n\
exec apache2-foreground' > /usr/local/bin/startup.sh && \
    chmod +x /usr/local/bin/startup.sh

CMD ["/usr/local/bin/startup.sh"]

# Expose port 80 per l'accesso al web server
EXPOSE 80