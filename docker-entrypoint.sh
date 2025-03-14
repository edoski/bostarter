#!/bin/bash
set -e

# Attendo che MySQL sia pronto prima di eseguire il seed dei dati
echo "Waiting for MySQL..."
until php -r "try { \$pdo = new PDO('mysql:host=db;dbname=BOSTARTER', 'root', '339273'); echo 'MySQL connected\n'; } catch (PDOException \$e) { echo \$e->getMessage().'\n'; sleep(1); }"; do
	echo "MySQL not ready, waiting..."
	sleep 2
done

# Per sicurezza, aspetto ancora 5 secondi
sleep 5
echo "MySQL should be fully initialized now"

# Una volta che MySQL è pronto, eseguo lo script di seed dei dati
echo "Running seed data script..."
cd /var/www/html
php -d display_errors=1 -d error_reporting=E_ALL config/seed_data.php

# Infine, avvio Apache per servire l'applicazione
echo "Starting Apache..."
apache2-foreground
