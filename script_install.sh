#!/bin/bash

echo ---- "Installation" -----

php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:create
php app/console doctrine:fixtures:load  --fixtures="src/Esimed/CarLocation/BackendBundle/DataFixtures/ORM/Data"
php app/console assets:install
php app/console assetic:dump --env=prod --no-debug
php app/console cache:clear
php app/console cache:clear --env=prod