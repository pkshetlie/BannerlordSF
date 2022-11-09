composer install
#php bin/console a:i

php bin/console doctrine:migrations:migrate --no-interaction
php bin/console c:c --env=prod
