# Handan Portfolio (Web Programming) — Milestone 5

## Live links
- Frontend: https://portfolio-handan.vercel.app/#home
- Backend OpenAPI: https://handan-portfolio-zojo5.ondigitalocean.app/v1/openapi
- Swagger UI: https://handan-portfolio-zojo5.ondigitalocean.app/v1/docs

## How to use (Vercel)

Open the app via the Vercel link:

- https://portfolio-handan.vercel.app/#home

### What each page is for

- **Home / About / Projects / Skills / GitHub / Contact**: public pages (anyone can view).
- **Register**: create a new account.
- **Login**: sign in with your account.
- **Dashboard** (after login): manage your portfolio content (depending on your role).
- **Admin** (admins only): user/content management and admin tools.

### Typical user flow

1. Go to **Register** and create an account.
2. Go to **Login** and sign in.
3. Use **Dashboard** to view/manage your content.
4. Use **Logout** to end the session.

### Backend + database connection (production)

- The frontend is deployed on Vercel.
- The backend API is deployed separately (DigitalOcean), and the frontend communicates with it over HTTP.
- The backend is connected to a MySQL database (configured via environment variables like `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`).
- Data you create/update through the UI (e.g., contact messages, user data, portfolio items) is persisted in the database through the backend.

For API reference and testing, use Swagger UI:

- https://handan-portfolio-zojo5.ondigitalocean.app/v1/docs

## Screenshots

### Frontend
<img width="1743" height="971" alt="Frontend 1 ss" src="https://github.com/user-attachments/assets/817f9e74-4655-492c-87f4-edaec2634e09" />

<img width="1874" height="925" alt="Frontend screenshot 2" src="https://github.com/user-attachments/assets/7ec0e438-e61f-48f5-a7c4-b4de9601323c" />

### Backend
<img width="579" height="171" alt="image" src="https://github.com/user-attachments/assets/9ecc28b1-fff3-4057-abbe-4363b5e9dcc4" />
<img width="1831" height="910" alt="Swager" src="https://github.com/user-attachments/assets/d4e5bd13-e35c-4fcf-9977-d5b094424ca4" />


## Tech
- Backend: PHP + FlightPHP + PDO + MySQL, firebase/php-jwt, swagger-php
- Frontend: Bootstrap 5, SPAPP, jQuery (+ Validate), BlockUI, Toastr, Chart.js

## 1. Frontend MVC Implementation (Service Layer Only)

The frontend was refactored to strictly follow the MVC architecture required in Milestone 5.

- All HTTP (fetch) requests are located exclusively in the Service layer.
- Controllers do not contain any direct API calls.
- Views remain passive and contain no business logic.

Frontend JavaScript structure:

```text
frontend/js/
├── services/
│   ├── api.js              // API base configuration and helpers
│   ├── authService.js      // Authentication (login, register, logout)
│   ├── userService.js      // Admin user management
│   ├── projectService.js   // Project CRUD operations
│   ├── skillService.js     // Skill CRUD operations
│   ├── contactService.js   // Contact form handling
│   └── experienceService.js
│
├── app.js                  // SPA routing, authentication flow, navbar sync
├── dashboard.js            // User dashboard controller
├── admin.js                // Admin panel controller
└── features.js             // Public feature logic
```

All controllers communicate with the backend only through services. MVC requirement is fully satisfied.

## 2. Client-Side Validations (Frontend)

Client-side form validation was implemented according to lab requirements to improve UX and security.

- Login & Registration
	- Required field validation
	- Email format validation
	- Minimum password length validation
	- Form submission blocked until inputs are valid
- Contact Form
	- Required fields: name, email, message
	- Email format validation
	- Clear success and error feedback

## 3. Server-side Validations (Backend)

Backend validations ensure data integrity and security even if client-side checks are bypassed.

- Authentication
	- Email format validation
	- Password hashing using `password_hash`
	- Secure password verification on login
	- Clear error responses for invalid credentials
- Contact Service
	- Required fields validation (name, email, message)
	- Email format validation using `FILTER_VALIDATE_EMAIL`
	- Default status set to unread
- Authorization
	- JWT token verification on protected routes
	- Admin-only routes protected with role checks
	- Users cannot access admin functionality (backend enforced)


## 4. Deployment Readiness

The application is fully prepared for deployment.

Backend database configuration uses environment variables:

- `DB_HOST`
- `DB_PORT`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`

---

Contact: ali.handan@stu.ibu.edu.ba
