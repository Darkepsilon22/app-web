#!/bin/bash

# Exécuter la commande de génération des prix en arrière-plan
php bin/console app:generate-crypto-prices &

# Lancer le serveur PHP
php -S 0.0.0.0:8000 -t public
