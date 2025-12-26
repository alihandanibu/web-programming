# Handan Portfolio (Web Programming) — Milestone 5

Single-page portfolio web app with a FlightPHP REST API + MySQL backend.

## Live links
- Frontend: TODO
- Backend API: TODO
- Swagger UI: TODO

## Course requirements coverage (quick)
- SPA frontend (views in frontend/views/, no full page reload navigation)
- REST backend in PHP (FlightPHP) + PDO for DB access
- OpenAPI docs (Swagger UI under backend/public/v1/docs/)
- JWT auth (Bearer token) + at least 2 roles (admin/user)
- 5+ entities with CRUD: users, projects, skills, experiences, contacts
- AJAX requests (fetch/jQuery) + client/server validation

## Tech
- Backend: PHP + FlightPHP + PDO + MySQL, firebase/php-jwt, swagger-php
- Frontend: Bootstrap 5, SPAPP, jQuery (+ Validate), BlockUI, Toastr, Chart.js

## Local run (XAMPP)
1) Put the repo inside XAMPP: c:/xampp/htdocs/mojnoviprojekat/web-programming
2) Start Apache + MySQL
3) Import schema:
  - backend/config/database.sql
4) (Optional) Seed test users:
  - backend/sql/seed_users.sql
5) Open frontend:
  - http://localhost/mojnoviprojekat/web-programming/frontend/
6) Open Swagger:
  - http://localhost/mojnoviprojekat/web-programming/backend/public/v1/docs/

## Test accounts
- Admin (after seed): admin@portfolio.com / password
- If you need to reset password:
  - php backend/tools/reset_admin_password.php admin@portfolio.com password

## Environment variables (production)
Backend reads these from env (see backend/config/Database.php):
- DB_HOST
- DB_PORT
- DB_NAME
- DB_USER
- DB_PASSWORD
- DB_SSL (set true on managed MySQL)
- JWT_SECRET

## Deployment (DigitalOcean App Platform) — step-by-step

Important: this repo is graded via milestone branches. Deploy from the milestone5 branch.

### Step 1 — Create a Managed MySQL database
1) DigitalOcean → Create → Databases → MySQL
2) Copy connection details (host, port, user, password, db name)

### Step 2 — Import schema
Use one of these (pick the easiest for you):
- MySQL Workbench: connect using the DO credentials and run backend/config/database.sql
- CLI: connect with mysql client and run the .sql file

### Step 3 — Deploy backend (FlightPHP)
1) DigitalOcean → Create → Apps → GitHub
2) Select this repo and the milestone5 branch
3) Choose source directory: backend/
4) Set env vars:
  - DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD
  - DB_SSL=true
  - JWT_SECRET=some-long-random-string
5) Deploy and copy the backend URL (example): https://your-backend.ondigitalocean.app
6) Quick check:
  - GET https://your-backend.ondigitalocean.app/health

### Step 4 — Deploy frontend (static)
1) Create another App Platform component as Static Site
2) Source directory: frontend/
3) Set the API base URL for production:
  - In frontend/index.html add:
    <meta name="api-base" content="https://your-backend.ondigitalocean.app">
  (API base must NOT end with /backend)
4) Deploy and copy the frontend URL

### Step 5 — Update this README
Replace the TODO links at the top with your live Frontend / Backend / Swagger URLs.

## Branch / submission note
- Work is on branch: milestone5
- Do not merge milestone branches into main (only PR + reviewer as per course rules)

Contact: ali.handan@stu.ibu.edu.ba
