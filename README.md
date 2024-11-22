# OPTISEARCHLLC

php 8.3, laravel 11, postgres:16.4

### Initial local setup for Ubuntu

Clone this project

Switch to the docker-compose-lepp folder in project folder and run containers in the background

    docker compose up -d

Create 'optisearchllc' postgres database for project and 'optisearchllc_test' postgres database for tests

Launch the bash in web container

    docker compose exec php-fpm bash

Switch to the project folder in docker

    cd /var/www/html

Copy `.env.example` to `.env`

    cp .env.example .env

Install all the dependencies using composer

    composer install

Generate application key

    php artisan key:generate

Errors fix

    php artisan optimize:clear
    chmod 777 -R bootstrap/cache
    chmod 777 -R storage

Run migrations and seeds

    php artisan migrate --seed

Configure your hosts file on main machine

    sudo nano /etc/hosts

    127.0.0.1   optisearchllc.local

Check API-DOC `http://optisearchllc.local/docs/api`

Check Telescope `http://optisearchllc.local/telescope/requests`

Run Horizon `php artisan horizon and Horizon` and check `http://optisearchllc.local/horizon/dashboard` [https://laravel.su/docs/11.x/horizon]
