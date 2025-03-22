#!/bin/bash
set -e

docker-compose down -v
export COMPOSE_BAKE=true
docker-compose up --build