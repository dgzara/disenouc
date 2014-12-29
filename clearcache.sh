rm -r -f app/cache/*
php app/console cache:clear --env=dev
php app/console cache:clear --env=prod
chmod -R 777 app/cache/
chmod -R 777 app/logs
chmod -R 777 web/uploads
chmod -R 777 web/media
