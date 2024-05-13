#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
chown -R www-data:www-data .
composer update --ignore-platform-reqs
php artisan key:generate
# php artisan migrate
php artisan storage:link
# php artisan db:seed
npm install
# npm run dev

php-fpm

