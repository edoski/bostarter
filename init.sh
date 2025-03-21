#!/bin/bash
set -e

# Controllo se il file .env esiste
if [ ! -f .env ]; then
  echo "ERRORE: File .env non trovato. Assicurati di averlo creato e di averlo configurato correttamente."
  exit 1
fi

# Faccio il build dei container
docker-compose down -v
export COMPOSE_BAKE=true
docker-compose up --build