## Handan Portfolio — Milestone 1

This repository contains the frontend for my portfolio project for Milestone 1. It is a responsive single-page application built with Bootstrap and vanilla JavaScript. Pages live in `frontend/views/` and are loaded dynamically by `frontend/js/spapp.js`. To run locally, place the project folder in c:/xampp/htdocs/portfolio-handan, start Apache in XAMPP, and open http://localhost/portfolio-handan/frontend/index.html. Key files and folders are `frontend/index.html`, `frontend/views/`, `frontend/css/`, `frontend/js/` and `frontend/assets/images/`. The backend will be added in later milestones. The ERD diagram is at frontend/assets/images/erd-diagram.jpg. Contact: alihandan@stu.ibu.edu.ba
![erd-diagram](frontend/erddiagramjpg.jpg)

# Handan Portfolio — Milestone 2

## What's New in Milestone 2:
- **MySQL database** with 5 tables: users, projects, skills, experiences, contacts
- **DAO classes** for CRUD operations (Create, Read, Update, Delete)

## How to Run:
1. Place project in `c:/xampp/htdocs/portfolio-handan`
2. Start Apache and MySQL in XAMPP
3. Import database: open `backend/config/database.sql` in phpMyAdmin
4. Open: `http://localhost/portfolio-handan/frontend/index.html`

## Important Files:
- `frontend/` - HTML, CSS, JS files
- `backend/config/database.sql` - database setup
- `backend/dao/` - DAO classes for database access
- `backend/test_dao.php` - test if backend works

**Contact:** alihandan@stu.ibu.edu.ba
