User Management System
This is a simple User Management System with:

React (Frontend)

PHP & MySQL (Backend API)

Axios for API requests

It allows you to add, edit, delete, and list users. Duplicate emails are validated server-side.

Requirements
PHP >= 7.4

MySQL

Node.js & npm

Setup Instructions
Backend (PHP API)
Place the backend files in your server directory (e.g., htdocs for XAMPP).

Import the users.sql file into phpMyAdmin to create the database and table.

Update database.php with your database credentials:
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'user_crud';
Start the PHP built-in server (or use XAMPP/WAMP):
php -S localhost:8000

The API will be available at http://localhost:8000/api.php.

Frontend (React App)
Go to the frontend folder:
cd frontend

Install dependencies:
nginx
Copy
Edit
npm install

Start the React app:
npm start

Make sure App.js has the API URL set correctly:
const API_URL = 'http://localhost:8000/api.php';
The app will run at http://localhost:3000.

Features
Add, edit, delete users

Prevent duplicate emails

Success and error messages in UI

API Endpoints
Method	Endpoint	Description
GET	/api.php	Get all users
POST	/api.php	Create user
PUT	/api.php?id={id}	Update user
DELETE	/api.php?id={id}	Delete user

