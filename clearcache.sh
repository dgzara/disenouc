rm -r -f app/cache/dev_old/
php app/console cache:clear --env=dev
php app/console cache:clear --env=prod
chmod -R 775 app/cache/
chmod -R 775 app/logs
