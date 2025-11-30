## clone the projects :

https://github.com/sukhiram99/user-managements.git urles

## create database name user_management_app

## migrate the commands

php artisan migrate --path=database/migrations/0001_01_01_000000_create_users_table.php
php artisan migrate --path=database/migrations/0001_01_01_000001_create_cache_table.php
php artisan migrate --path=database/migrations/0001_01_01_000002_create_jobs_table.php
php artisan migrate --path=database/migrations/2025_11_30_122438_create_roles_table.php
php artisan migrate --path=database/migrations/2025_11_30_122458_create_permissions_table.php
php artisan migrate --path=database/migrations/2025_11_25_153651_create_role_permission_table.php
php artisan migrate --path=database/migrations/2025_11_30_122438_create_roles_table.php
php artisan migrate --path=database/migrations/2025_11_25_153455_create_role_user_table.php
php artisan migrate --path=database/migrations/2025_11_30_144253_create_user_remarks_table.php

and seeder also

php artisan db:seed --class=RolePermissionSeeder
