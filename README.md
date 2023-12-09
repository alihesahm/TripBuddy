# TripBuddy

## Development environment setup

1. clone the repo
2. make shor you have php and composer and mysql installed in your device
3. cope .env.example to .env file for linex
```shell
cp .env.example .env
```
for window
```shell
copy .env.example .env
```
4. install the require
```shell
composer install
```
5. create the database and the table and seed it
```shell
php artisan migrate --seed
```
6. run the server
```shell
php artisan serve
```


