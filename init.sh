#!/bin/bash
set -e

# Controllo se il file .env esiste
if [ ! -f .env ]; then
  echo "ERRORE: File .env non trovato. Assicurati di averlo creato e di averlo configurato correttamente."
  exit 1
fi

# Entro in /php per installare le dipendenze PHP e Node.js
cd php

# Install PHP dependencies if composer.json exists
if [ -f composer.json ]; then
  composer install --no-interaction --prefer-dist
fi

# Install Node.js dependencies if package.json exists
if [ -f package.json ]; then
  npm install
fi

# Torno alla root del progetto
cd ..

docker-compose down -v
export COMPOSE_BAKE=true
docker-compose up --build -d

echo "=== BOSTARTER INIZIALIZZATO. PIATTAFORMA PRONTA! ==="