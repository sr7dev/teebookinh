#!/bin/bash

echo "Start script."

service apache2 restart

cd /var/www/backteehive/
composer install --no-interaction
php artisan migrate --force
php artisan l5-swagger:generate

echo "End script."
