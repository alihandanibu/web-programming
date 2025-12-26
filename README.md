# Handan Portfolio (Web Programming) — Milestone 5

## Live links
- Frontend: [https://portfolio-handan.vercel.app](https://portfolio-handan-68r3tvgnc-alihandanibus-projects.vercel.app/#home)
- Backend API: https://handan-portfolio-zojo5.ondigitalocean.app
- Swagger UI: https://handan-portfolio-zojo5.ondigitalocean.app/v1/docs

## Tech
- Backend: PHP + FlightPHP + PDO + MySQL, firebase/php-jwt, swagger-php
- Frontend: Bootstrap 5, SPAPP, jQuery (+ Validate), BlockUI, Toastr, Chart.js

## 1. Frontend MVC Implementation (Service Layer Only)

The frontend was refactored to strictly follow the MVC architecture required in Milestone 5.
All HTTP (fetch) requests are located exclusively in the Service layer
Controllers do not contain any direct API calls
Views remain passive and contain no business logic
Frontend JavaScript Structure:

frontend/js/
├── services/
│   ├── api.js              // API base configuration and helpers
│   ├── authService.js      // Authentication (login, register, logout)
│   ├── userService.js      // Admin user management
│   ├── projectService.js  // Project CRUD operations
│   ├── skillService.js    // Skill CRUD operations
│   ├── contactService.js  // Contact form handling
│   └── experienceService.js
│
├── app.js        // SPA routing, authentication flow, navbar sync
├── dashboard.js  // User dashboard controller
├── admin.js      // Admin panel controller
└── features.js   // Public feature logic

All controllers communicate with the backend only through services
MVC requirement fully satisfied

## 2. Client-Side Validations (Frontend)

Client-side form validation was implemented according to lab requirements to improve UX and security.
Login & Registration
Required field validation
Email format validation
Minimum pasword length validation
Form submission blocked until inputs are valid
Contact Form
Required fields: name, email, message
Email format validation
Clear success and error feedback

## 3. Server-side Validations (Backend)

Backend validations ensure data integrity and security even if client-side checks are bypassed.
Authentication
Email format validatio
Password hashing using password_hash
Secure password verification on login
Clear error responses for invalid credentials
Contact Service
Required fields validation (name, email, message)
Email format validation using FILTER_VALIDATE_EMAIL
Default status set to unread
Authorization
JWT token verification on protected routes
Admin-only routes protected with role checks
Users cannot access admin functionality (backend enforced)

✔ Backend validation complements frontend validation
✔ Defense-in-depth approach applied

## 4. Deployment Readiness

The application is fully prepared for deployment.
Backend
Database configuration uses environment variables
DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASSs
-----------------------------------------------------------------
Contact: ali.handan@stu.ibu.edu.ba
