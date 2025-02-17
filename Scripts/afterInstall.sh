#!/bin/bash

cd /var/www/filament
php artisan key:generate
php artisan migrate
