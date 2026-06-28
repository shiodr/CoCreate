# CoCreate

CoCreate is a responsive PHP and MySQL collaboration platform for finding teammates for hobby projects, creative ideas, and skill-based activities.

## Features

- Register and login with PHP sessions
- Password storage with `password_hash()`
- Browse, search, and filter project listings
- Create, edit, and delete owned projects
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
  sql/
  uploads/
```

## Setup

1. Put the project folder inside your local server directory, such as `htdocs` for XAMPP.
2. Start Apache and MySQL.
3. Open phpMyAdmin and import `sql/cocreate.sql`.
4. Check database settings in `includes/db.php`.
5. Open the app in the browser:

```text
http://localhost/CoCreate/
```

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
