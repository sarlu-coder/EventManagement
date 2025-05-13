#### Setup

1. Clone the project using command git clone https://github.com/sarlu-coder/EventManagement.git
2. Make a database in mysql named "EventManagement".
3. Make an .env from .env.example
4. Run command "composer install".
5. Run command "composer du"
6. Run Command "php artisan migrate"
7. Import EventManagement.postman_collection.json in postman for the api collection.
8. Either run "php artisan serve" for the base url of the project or just use with your localhost path.


#### Tech Stack
1. PHP 8.2
2. mysql 10.4
3. laravel 12

#### After setting up the project and importing the json in postman.

1. Make a user with the auth '/login' api and get the access token from the response.
2. To use any api of event and booking add Auth Type as "Bearer Token" in Authorization header and put the received access token from auth api.
3. Using the apis are self explanatory and adding and deleting attendees for a booking in "Create Bookings" and "Update Booking" attendees could be sent in an array as represented the collection.

