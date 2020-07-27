#composer install
#php bin/console a:i
#yarn install
#yarn encore production
php bin/console doctrine:make:migration
php bin/console c:c --env=prod
