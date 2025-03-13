## Project Description
*This API is designed as a directory for clinics. It returns below information in JSON format.*


### Tech Stack
- PHP 8

## To run locally
- Ensure you have the following installed: 
PHP,
Git

1. Clone the repo. 
git clone https://github.com/Timmyojo/clinic-directory
cd clinic-directory

2. Run composer install to install dependency for composer autoload and dotenv.

3. Setup the environment variables by creating a .env file in the parent directory

4. Start the server, to run the api locally.
php -S localhost:8000 index.php

5. Register Account
Once the server is running, open your browser and go to http://localhost:8000/register to create account and get your api key.

4. Authenticate
Send your API key as an X-API-KEY header to gain access token and refresh token:
http://localhost:8000/api/authenticate

5. Access Resources
Send a valid method and send along your access token as authorization header with Bearer prefix to the resource endpoint to access resource on the endpoint:
http://localhost:8000/api/clinic

5. Expected Response
```json
{
    "id": 4,
    "clinic_name": "Peadent Clinic",
    "owner": "Ojo",
    "location": "Abuja",
    "user_id": 3
}
```