#!/bin/bash

MYSQL_USER="root"
MYSQL_PASS="339273"
MYSQL_HOST="localhost"

mysql -u "$MYSQL_USER" -p"$MYSQL_PASS" -h "$MYSQL_HOST" < bostarter_init.sql

if [  $? -eq 0 ]; then
    echo "-- OK: bostarter_init.sql --"
else
    echo "-- ERRORE: bostarter_init.sql --"
fi

mysql -u "$MYSQL_USER" -p"$MYSQL_PASS" -h "$MYSQL_HOST" BOSTARTER < bostarter_demo.sql

if [  $? -eq 0 ]; then
    echo "-- OK: bostarter_demo.sql --"
else
    echo "-- ERRORE: bostarter_demo.sql --"
fi

echo "-- BOSTARTER INIZIALIZZATO --"