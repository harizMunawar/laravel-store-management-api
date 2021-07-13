# Laravel Store Managemeent API

## Installing Web Application
### 1. Prerequisites
Before installing this project, make sure composer is already installed.
* Composer : you can download it [here](https://getcomposer.org/)

### 2. Installing
* Run `composer install`
* Run `copy .env.example .env`
* Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.
By default, the username is root and you can leave the password field empty.
* Run `php artisan key:generate`
* Run `php artisan migrate`
* Run `php artisan db:seed`

### 3. Run The Test
* To make sure everything has been setup correctly, please run the available test by commanding `php artisan test`

### 4. Run The App
* Finally, run `php artisan serve`, the API will then be served on port 8000 of localhost. All api are inside the ```\api\``` route.

## Live Server
Hosted on heroku<br>
https://data-store-management.herokuapp.com/api.

## Account
#### Superadmin
* Email : superadmin@gmail.com
* Password : validpassword

#### Admin
* Email : storeadmin@gmail.com
* Password : validpassword

## Built with
* [Laravel v 8](https://laravel.com/docs/8.x)