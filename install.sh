#!/bin/bash

echo 'Presionar cualquier tecla para continuar: Se eliminará la BD!'
read -n 1 -s

php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

echo 'Cargar DataFixtures'
php app/console doctrine:fixtures:load --append

echo 'Cargando los assets'
php app/console assets:install web --symlink
php app/console assetic:dump --env=dev --no-debug
php app/console assetic:dump --env=prod --no-debug

echo 'Limpiando caché...'
bash clearcache.sh

echo 'Presionar cualquier tecla para salir'
read -n 1 -s
