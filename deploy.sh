#!/bin/bash

echo "Start script."

service apache2 restart

cd /var/www/laravel-ksr-boston_abundance/
php artisan l5-swagger:generate

echo "End script."
