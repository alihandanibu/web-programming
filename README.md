# Handan Portfolio — Milestone 1

This repository contains the frontend for my portfolio project for Milestone 1. It is a responsive single-page application built with Bootstrap and vanilla JavaScript. Pages live in `frontend/views/` and are loaded dynamically by `frontend/js/spapp.js`. To run locally, place the project folder in c:/xampp/htdocs/portfolio-handan, start Apache in XAMPP, and open http://localhost/portfolio-handan/frontend/index.html. Key files and folders are `frontend/index.html`, `frontend/views/`, `frontend/css/`, `frontend/js/` and `frontend/assets/images/`. The ERD diagram is at frontend/assets/images/erd-diagram.jpg.
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

# Handan Portfolio — Milestone 3

## What's New in Milestone 3:
- **Business Logic Layer**: Service classes for all entities (UserService, SkillService, ExperienceService, ProjectService, ContactService)
- **JWT Authentication**: Secure token-based authentication using Firebase/PHP-JWT
- **REST API Routes**: Complete CRUD operations with proper HTTP methods (GET, POST, PUT, DELETE)
- **OpenAPI/Swagger Documentation**: Interactive API documentation with Swagger UI
- **Route Definitions**: All endpoints documented with Swagger annotations

## Key Features:
- Secure JWT-based authentication for protected routes
- RESTful API endpoints following best practices
- Interactive API documentation at `/backend/public/v1/docs/`
- Dependency injection container using FlightPHP
- Prepared statements for SQL security
- Service layer for business logic separation

## Technologies Used:
- **FlightPHP** 3.17+ - Lightweight REST API framework
- **Firebase/PHP-JWT** 6.0 - JWT authentication
- **Zircote Swagger-PHP** 5.5 - OpenAPI documentation
- **MySQL** 5.7+ with PDO - Database layer

## How to Run:
1. Place project in `c:/xampp/htdocs/mojnoviprojekat`
2. Start Apache and MySQL in XAMPP
3. Import database: run `backend/config/database.sql` in phpMyAdmin
4. Access Swagger UI: `http://localhost/mojnoviprojekat/web-programming/backend/public/v1/docs/`

## API Testing:
1. Register: POST `/auth/register`
2. Login: POST `/auth/login` (returns JWT token)
3. Use token in Authorization header: `Authorization: Bearer <token>`
4. Access protected endpoints with the token

## Important Files:
- `backend/services/` - Business logic layer
- `backend/routes/` - API endpoint definitions
- `backend/middleware/AuthMiddleware.php` - JWT authentication
- `backend/public/v1/docs/` - Swagger documentation
- `backend/dao/` - Data access objects (from Milestone 2)
- `backend/config/database.sql` - Database schema

## Deployment Note:
For production, change the secret key in `backend/middleware/AuthMiddleware.php` from the default value.

---

# Handan Portfolio — Milestone 4

## What's New in Milestone 4

- **Role-Based Access Control (RBAC)**: Admin vs User permissions enforced on all protected endpoints
- **Ownership Enforcement**: Users can only access/modify their own data (projects, skills, experiences)
- **Frontend-Backend Integration**: SPA dynamically loads user-specific data when logged in
- **Dashboard CRUD**: Users can add/delete skills and projects from the dashboard
- **Apache-Only Setup**: No need to start a separate PHP server — everything runs through XAMPP

## Architecture

Request → Routes → AuthMiddleware → Services → DAO → MySQL

- **Routes** (`backend/routes/`): Define endpoints with Swagger annotations
- **Services** (`backend/services/`): Business logic + validation + ownership checks
- **DAO** (`backend/dao/`): Data access layer with prepared statements
- **Middleware** (`backend/middleware/AuthMiddleware.php`): JWT verification + role extraction

## Base URLs (Apache/XAMPP)
Backend API `http://localhost/mojnoviprojekat/web-programming/backend`
Frontend SPA  `http://localhost/mojnoviprojekat/web-programming/frontend/`
Swagger UI  `http://localhost/mojnoviprojekat/web-programming/backend/public/v1/docs/`

## Role & Ownership Rules

- **Admin**: Can access `GET /users` (list all users)
- **User**: Cannot access `GET /users` (returns 403)
- **Owner**: Can only access their own `/users/{id}/*` endpoints
- **Non-owner**: Gets 403 when accessing another user's data
- 
### Frontend Tests (Browser)

1. Open `http://localhost/mojnoviprojekat/web-programming/frontend/`
2. Click Login → enter `admin@portfolio.com` / `password`
3. Verify: Dashboard loads, "Admin" link visible in navbar
4. Click "View Skills" → shows user's skills (or "No skills yet" with Add button)
5. Go to Dashboard → Add a skill → Verify it appears
6. Click Logout → Token removed, redirects to home
7. Login as regular user → "Admin" link hidden

## Defense Demo Steps - how the site works

### 1. Swagger Demo
1. Open: `http://localhost/mojnoviprojekat/web-programming/backend/public/v1/docs/`
2. Expand `GET /health` → Try it out → Execute → Show 200 response
3. Expand `POST /auth/login` → Enter `{"email":"admin@portfolio.com","password":"password"}` → Execute
4. Copy the token from response
5. Click "Authorize" button (top right) → Paste `Bearer <token>` → Authorize
6. Expand `POST /auth/verify` → Execute → Show `valid: true, role: admin`
7. Expand `GET /users` → Execute → Show 200 with user list
8. Log out of Swagger, login as regular user, try `GET /users` → Show 403

### 2. Frontend Demo
1. Open: `http://localhost/mojnoviprojekat/web-programming/frontend/`
2. Show public pages: Home, About, Projects (static showcase), Skills (static showcase)
3. Login as admin → Show dashboard with profile info
4. Go to Skills page → Show dynamic user data (or empty + add button)
5. Add a skill from Dashboard → Refresh Skills page → Verify it appears
6. Show Admin panel (admin only)
7. Logout → Show navbar updates (Login/Register visible, Dashboard/Admin hidden)
8. Login as regular user → Show Admin link is hidden

### 3. Code
1. Show `backend/index.php` — Flight routing + base path handling
2. Show `backend/routes/UserRoutes.php` — Swagger annotations + middleware
3. Show `backend/services/UserService.php` — Ownership validation
4. Show `frontend/js/api.js` — Computed API_BASE for Apache
5. Show `frontend/js/dashboard.js` — CRUD operations for skills/projects

## Files Changed in Milestone 4

### Backend
- `backend/index.php` — Base path normalization for Apache
- `backend/services/*` — Ownership enforcement
- `backend/middleware/AuthMiddleware.php` — Role extraction

### Frontend
- `frontend/js/api.js` — Computed API_BASE, CRUD functions
- `frontend/js/app.js` — Auth UI (show/hide nav items)
- `frontend/js/dashboard.js` — Skills/Projects CRUD
- `frontend/js/features.js` — Dynamic data loading for logged-in users
- `frontend/views/dashboard.html` — CRUD forms
- `frontend/views/skills.html` — Dynamic/static content toggle
- `frontend/views/projects.html` — Dynamic/static content toggle

**Contact:** ali.handan@stu.ibu.edu.ba
