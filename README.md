# CoCreate

CoCreate is a responsive PHP and MySQL collaboration platform for finding teammates for hobby projects, creative ideas, and skill-based activities.

## Features

- Register and login with PHP sessions
- Password storage with `password_hash()`
- Browse, search, and filter project listings
- Create, edit, and delete owned projects
- Add optional images to project listings
- Submit join requests with duplicate prevention
- Accept or reject applicants for owned projects
- Edit profile, skills, interests, biography, and profile picture
- Admin dashboard with user, project, and request management
- Reusable header, navigation, footer, session, and database components

## Folder Structure

```text
CoCreate/
  admin/
  assets/css/
  assets/js/
  includes/
  pages/
  sql/
  uploads/
```

## Setup

1. Put the project folder inside your local server directory, such as `htdocs` for XAMPP.
2. Start Apache and MySQL.
3. Open phpMyAdmin and import `sql/cocreate.sql`.
4. Copy `includes/config.example.php` to `includes/config.php`, then check the database settings in `includes/config.php`.
5. Open the app in the browser:

```text
http://localhost/CoCreate/
```

## InfinityFree Setup

1. In InfinityFree, create a hosting account and open the File Manager or connect by FTP.
2. Upload the project contents into your site's `htdocs` folder. Upload the contents of this folder, not an extra parent folder around it.
3. In the InfinityFree control panel, create a MySQL database.
4. Open phpMyAdmin for that database and import `sql/cocreate.sql`.
5. Copy `includes/config.example.php` to `includes/config.php`, then edit `includes/config.php` with the exact database details from InfinityFree:

```php
const DB_HOST = 'your_mysql_hostname';
const DB_NAME = 'your_database_name';
const DB_USER = 'your_database_username';
const DB_PASS = 'your_database_password';
```

6. Visit your site domain. The included `.htaccess` sends the homepage to `pages/index.php` and keeps URLs like `browse.php` working.

If you already imported an older database before project images were added, import `sql/add_project_images.sql` once in phpMyAdmin.

If uploads fail, make sure the `uploads/` folder exists on InfinityFree and is writable.

## Sample Accounts

All sample accounts use this password:

```text
password
```

- Admin: `admin`
- User: `mika`
- User: `leo`
- User: `ari`

## Notes

The `users.role` field controls admin access in the application. The `admins` table is included to match the required database design and provide a separate admin record for testing/reference.
