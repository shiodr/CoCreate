# CoCreate

CoCreate is a dynamic web-based platform designed to help users find teammates for hobby projects, creative collaborations, and skill-based activities. It allows users to create project listings, browse available projects, submit join requests, manage applications, and build profiles that highlight their skills and interests.

## Features

* User registration and login authentication
* Browse and search available projects
* View project details and required skills
* Create, edit, and manage project listings
* Submit requests to join projects
* Accept or reject join requests
* User profile management
* Admin dashboard for managing users and projects

## System Flow

1. Users enter the landing page and log in or register an account.
2. After authentication, users are redirected to the main dashboard.
3. Users can browse projects and view their details.
4. Interested users may submit a join request to a project owner.
5. Project owners can review requests and accept or reject applicants.
6. Users can also create projects and update their profile information.
7. Administrators can access the admin dashboard to manage users and project listings.

## Technologies Used

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP
* **Database:** MySQL
* **Database Management:** phpMyAdmin
* **Hosting:** InfinityFree or a local server environment such as XAMPP

## Database Tables

The system uses the following main database tables:

* `users` – Stores user account and profile information.
* `projects` – Stores project listings created by users.
* `join_requests` – Stores requests submitted by users who want to join projects.
* `admins` – Stores administrator account information.

## Installation Guide

1. Clone or download this repository.

```bash
git clone https://github.com/your-username/cocreate.git
```

2. Move the project folder into your local server directory.

For XAMPP:

```text
C:\xampp\htdocs\
```

3. Start Apache and MySQL using the XAMPP Control Panel.

4. Open phpMyAdmin and create a new database.

```text
cocreate_db
```

5. Import the provided SQL database file into phpMyAdmin.

6. Update the database connection settings in the PHP configuration file.

```php
$server = "localhost";
$user = "root";
$pass = "";
$dbname = "cocreate_db";
```

7. Open the project in your browser.

```text
http://localhost/cocreate/
```

## User Roles

### Regular User

Regular users can create and manage projects, browse project listings, submit join requests, review applicants for their own projects, and update their profile details.

### Administrator

Administrators can access the admin dashboard and manage registered users, projects, and other system data.

## Project Purpose

CoCreate was developed to provide a centralized platform where people with similar interests, skills, and project ideas can connect. It reduces the difficulty of finding suitable teammates by allowing users to post projects, display required skills, and manage collaboration requests in one system.

## Future Improvements

* Add project categories and filtering options
* Add real-time messaging between users
* Add notifications for join request updates
* Add project ratings and reviews
* Add file-sharing features for project teams
* Improve admin reports and analytics

## Developers

Developed as an academic web development project.

## License

This project is intended for educational purposes.
