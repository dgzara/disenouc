#!/bin/bash

echo 'Presionar cualquier tecla para continuar: Se eliminará la BD!'
read -n 1 -s

php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

echo 'Cargar DataFixtures'

php app/console doctrine:fixtures:load --append

echo 'Limpiando caché...'

bash clearcache.sh

echo 'Presionar cualquier tecla para salir'
read -n 1 -s
