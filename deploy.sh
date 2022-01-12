#!/bin/bash

echo "Start script."

service apache2 restart

cd /var/www/backteehive/
php artisan migrate --force
php artisan l5-swagger:generate

echo "End script."
