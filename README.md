# REST API Demo with Laravel
Test-driven development of a REST API with Laravel 8

WORK IN PROGRESS...

## A simple shopping list API
- Register a user with email and password
- Login a user with email and password
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

## API Endpoints
### Registration
- POST api/register
    - 201 if successful, return ID of the registered user
    - 400 if validation fails
