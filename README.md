# CoCreate

CoCreate is a PHP and MySQL collaboration platform for finding teammates, posting projects, sharing required skills, and managing join requests.

## Features

- Register and login with PHP sessions
- Password storage with `password_hash()`
- Browse, search, filter, create, edit, and delete projects
- Upload optional project images and profile pictures
- Submit, accept, and reject join requests
- Edit collaborator profiles with skills, interests, and biography
- Admin dashboard for users, projects, and request monitoring
- Shared PHP UI helpers for project cards, media, status labels, and skill tags
- Vanilla JS enhancements for reveal motion, live project filtering, image previews, counters, mobile nav, and safer confirmations

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

## Local Setup

1. Put the project folder inside your local server directory, such as `htdocs` for XAMPP.
2. Start Apache and MySQL.
3. Create a MySQL database named `cocreate`.
4. Import `sql/cocreate.sql` into that database.
5. Edit `includes/config.php` if your local database credentials differ:

```php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'cocreate';
const DB_USER = 'root';
const DB_PASS = '';
```

6. Open the app in your browser:

```text
http://localhost/CoCreate/pages/index.php
```

If uploads fail locally, make sure `uploads/` and `uploads/projects/` exist and are writable by your web server.

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

The `users.role` field controls admin access. The `admins` table remains available for compatibility with the original database design and sample data.
