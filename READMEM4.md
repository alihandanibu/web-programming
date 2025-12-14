# Handan Portfolio – Web Programming Project (Milestone 4)

## Project Overview
This project is a **Single Page Application (SPA) portfolio website** developed as part of the *Web Programming* course.  
The application demonstrates **frontend–backend separation**, **JWT authentication**, and **role-based authorization**, which are the core requirements of Milestone 4.

The frontend is responsible for user interaction and navigation, while the backend exposes a secure REST API.

---

## Technologies Used

### Frontend
- HTML5
- CSS3
- Bootstrap 5
- JavaScript (ES6)
- jQuery
- jQuery SPApp (SPA routing)

### Backend
- PHP 8
- FlightPHP
- MySQL
- JWT (JSON Web Tokens)

### Tools
- XAMPP (Apache, PHP, MySQL)
- Postman (API testing)
- Git & GitHub

---

## Application Architecture

The project follows a **client–server architecture**:

- **Frontend**  
  - Single Page Application using hash-based routing  
  - Communicates with backend via REST API  
  - Handles UI logic and navigation  

- **Backend**  
  - RESTful API  
  - JWT-based authentication  
  - Middleware for authorization  
  - Role-based access control  

This separation improves maintainability, scalability, and security.

---

## Authentication Flow

1. User submits email and password from the frontend.
2. Backend validates credentials.
3. Backend generates a JWT token.
4. Token is returned to the frontend.
5. Frontend stores token in `localStorage`.
6. Token is sent in the `Authorization` header for protected requests.

Authorization: Bearer <JWT_TOKEN>

---

## Authorization & Roles

The system supports **role-based access control**:

- **User**
  - Can log in
  - Can access the dashboard
  - Can view own profile data

- **Admin**
  - All user permissions
  - Can access the admin page
  - Sees admin-only UI sections

Authorization is enforced:
- On the **backend** using middleware (security layer)
- On the **frontend** by conditionally rendering UI elements

---

## Dashboard

The dashboard is an **informational page** that demonstrates:

- Successful authentication
- Secure frontend–backend communication
- Display of user data fetched from protected API endpoints
- Role-based UI behavior

The dashboard intentionally avoids complex business logic to keep the focus on Milestone 4 objectives.

---

## Admin Page

The admin page is a **minimal role-based demonstration**:

- Accessible only to users with `admin` role
- Displays admin-specific information
- Non-admin users are automatically redirected

The page serves as a proof of correct authorization handling rather than a full CRUD implementation.

---

## API Endpoints (Examples)

- `POST /auth/login` – User login
- `POST /auth/register` – User registration
- `GET /users/{id}` – Fetch authenticated user data (protected)

All protected routes require a valid JWT token.

---

## Security Considerations

- Passwords are not stored in plain text
- JWT tokens are validated on every protected request
- CORS headers are configured for API communication
- Frontend access control is supported by backend authorization

---

## Testing

- Backend endpoints tested using **Postman**
- Authentication and authorization tested with different user roles
- Frontend tested through browser navigation
- Unauthorized access is properly blocked

---

## Notes

This project focuses on **authentication, authorization, and frontend–backend integration**, as required by Milestone 4.  
Additional features such as full CRUD operations, refresh tokens, and pagination can be implemented in future milestones.

---

## Author
**Ali Handan**  
IT Student – Web Programming Course
