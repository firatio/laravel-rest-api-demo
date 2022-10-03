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

## API Endpoints
### Registration
- POST api/register
    - 201 Success, return ID of the registered user
    - 400 Validation fails

### Login
- POST api/login
    - 401 Credentials do not match a user
    - 200 Success, return token of the logged in user

- GET api/user
    - 401 User is not authenticated
    - 200 Success, return email of the logged in user

- POST api/logout
    - 401 User is not authenticated
    - 204 Success