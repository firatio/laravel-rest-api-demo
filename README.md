# REST API Demo with Laravel
This is a simple demonstration of test-driven development of a REST API with Laravel 8.

WORK IN PROGRESS...

## A simple shopping list API
- Register a user with email and password
- Log in a user with email and password
- Log out a logged in user
- View information about the logged in user
- Create an item with name and notes
- Get items of the logged in user
- View an item
- Update an item
- Delete an item

## Registration Tests
- Users can register with valid information
- Users cannot register with invalid email
- Users cannot register with invalid password
- Users cannot register with the same email address again

## Login Tests
- Unregistered users cannot log in
- Registered users can log in
- Unauthenticated users cannot see user information
- Logged in users can see user information
- Logged in users can log out
- Unauthenticated users cannot log out

## Items Tests
- Unauthenticated users cannot create items
- Requests with invalid tokens cannot create items
- Authenticated users can create items
- Authenticated users can get a list of their items
- Authenticated users can view their items
- Authenticated users cannot view items that belong to other users
- Authenticated users can delete their items
- Authenticated users cannot delete items that belong to other users
- Authenticated users can update their items
- Authenticated users cannot update items that belong to other users

## API Endpoints
### Registration
- POST api/register
    - 201 Success, return ID of the registered user
    - 400 Validation failed

### Login
- POST api/login
    - 200 Success, return token of the logged in user
    - 401 Credentials do not match a user

- GET api/user
    - 200 Success, return email of the logged in user
    - 401 User is not authenticated

- POST api/logout
    - 204 Success
    - 401 User is not authenticated

### Items
- POST api/items
    - 201 Success, return ID of the item
    - 401 User is not authenticated
    - 400 Validation failed

- GET api/items
    - 200 Success, return list of items
    - 401 User is not authenticated

- GET api/items/{ID}
    - 200 Success, return item
    - 401 User is not authenticated
    - 403 User is not authorized
    - 404 Item cannot be found

- DELETE api/items/{ID}
    - 204 Success
    - 401 User is not authenticated
    - 403 User is not authorized
    - 404 Item cannot be found

- PUT api/items/{ID}
    - 204 Success
    - 400 Validation failed
    - 401 User is not authenticated
    - 403 User is not authorized
    - 404 Item cannot be found

## Installation
It is assumed that you are familiar with git, Laravel and MySQL setup and usage.

### Requirements
- Git
- PHP 7.3+ (with mysqli and pdo_mysql)
- MySQL 5.7+
- Composer

### Steps
1. Download the project to your preferred folder:
```
git clone https://github.com/firatio/laravel-rest-api-demo.git
```

2. Create a MySQL database for the application
 
3. Go to the project folder and install Laravel by running:
```
composer install
```

4. Create an .env file using .env.example file

5. Update .env file by configuring database information: database host, name and user information

6. Create an application key
```
php artisan key:generate
```

## Usage
Run the tests
```
./vendor/bin/phpunit
```