php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

php -S 0.0.0.0:8000 -t public/ public/index.php
