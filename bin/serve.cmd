php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

php -S localhost:8000 -t public/ public/index.php
