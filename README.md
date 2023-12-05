# IQ Steps

## Development environment setup

1. clone the repo
2. cope .env.example to .env file for linex
```shell
cp env.example .env
```
for window
```shell
copy env.example .env
```
2. install the require
```shell
composer.phar install
```
3. create the database and the table and seed it
```shell
php artisan migrate --seed
```


