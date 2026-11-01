# TUDU

TUDU is a simple, responsive task manager built with Laravel 12 and Blade. It lets you add tasks, notes, due dates and priorities; mark tasks complete; edit or delete them; and filter the list by status. It uses the MySQL-compatible MariaDB server included with XAMPP.

## Requirements

- PHP 8.2 or later with the MySQL PDO extension enabled
- Composer
- XAMPP with Apache and MySQL (MariaDB) running

## Run locally

### 1. Create the database

Open http://localhost/phpmyadmin, select **New**, enter `tudu_db` as the database name, and click **Create**.

### 2. Configure and start TUDU

From this folder, run:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Open http://127.0.0.1:8000. If `.env` already exists, keep it and skip the `Copy-Item` step. The default XAMPP setup uses the MySQL username `root` with an empty password. If yours differs, update `DB_USERNAME` and `DB_PASSWORD` in `.env`.

## Main files

- `routes/web.php` — task routes
- `app/Http/Controllers/TaskController.php` — validation and CRUD actions
- `app/Models/Task.php` — task model and casts
- `database/migrations/2026_10_28_000000_create_tasks_table.php` — task table
- `resources/views/tasks/index.blade.php` — TUDU interface
- `public/css/app.css` — responsive styling

This project is an assignment-friendly starter and does not include user accounts; tasks are shared on the local app.
