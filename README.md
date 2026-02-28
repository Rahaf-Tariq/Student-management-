# Student Management System

A single-file PHP application for managing student records. Supports adding, editing, deleting and viewing students using MySQL as the database.

---

## Requirements

- XAMPP (or any local server with Apache and MySQL)
- PHP 7.4 or higher
- A modern browser

---

## Setup

1. Make sure XAMPP is installed and both Apache and MySQL are running.
2. Place `crudSystem.php` inside:
   ```
   C:\xampp\htdocs\crudSystem\
   ```
3. Open your browser and go to:
   ```
   http://localhost/crudSystem/crudSystem.php
   ```
4. The database and table will be created automatically on first load.

---

## Database

- Database name: `school_db`
- Table name: `students`

| Field | Type |
|---|---|
| id | INT, Auto Increment |
| name | VARCHAR |
| father_name | VARCHAR |
| email | VARCHAR |
| course | VARCHAR |
| marks | DECIMAL |
| created_at | TIMESTAMP |

---

## Features

- Add new student via form
- Edit existing student record
- Delete student with confirmation
- View all records in a table
- Notification toasts on add, edit and delete
- Student count, passed count and average marks displayed

---

## File Structure

```
crudSystem/
    crudSystem.php    — entire application in one file
    index.php         — redirects to crudSystem.php
    README.md         — this file
```

---
## Project demo video 
https://drive.google.com/file/d/1hO0V9kgHjiFMOB821ytXXGjFKm_IFyNH/view?usp=sharing

## Notes

- No frameworks or external libraries used.
- All database operations handled through PHP with MySQLi.
- Database and table are created automatically if they do not exist.


