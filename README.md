Handan Portfolio — Milestone 4

What’s New in Milestone 4:
- Frontend upgraded to a clean student-style SPA with hash routes: #home, #about, #projects, #skills, #contact, #login, #register, #dashboard, #admin.
- New Dashboard view with user profile info and role-based sections (admin vs. user) + minimal Admin area.
- Simplified JS: `frontend/js/app.js` for routing/auth, `frontend/js/api.js` for API calls, `frontend/js/dashboard.js` and `frontend/js/admin.js` for page logic.
- Cleanup: removed unused `backend/forms/` and the root `composer.lock` in the project; trimmed comments to look student-written.
- Frontend .htaccess configured to serve SPA correctly (DirectoryIndex + SPA fallback).

How to Run (Local):
1) Place this repo at `c:/xampp/htdocs/mojnoviprojekat/web-programming`.
2) Start Apache and MySQL in XAMPP.
3) Import the database: open `backend/config/database.sql` in phpMyAdmin.
4) Start the backend API:

```bash
cd C:\xampp\htdocs\mojnoviprojekat\web-programming\backend
php -S localhost:3000 -t backend
```

5) Open the frontend:
- http://localhost/mojnoviprojekat/web-programming/frontend/index.html#home

Notes:
- Apache must allow `.htaccess` (AllowOverride All) for `C:/xampp/htdocs`. Restart Apache after changes.
- If the browser auto-navigates to `/index.html` at root and shows 404, copy/paste the full frontend URL above.
- `API_BASE` in `frontend/js/api.js` is set to `http://localhost:3000`.
- Role-based UI depends on the JWT token stored in `localStorage` after login.

Test Accounts:
- Admin: `admin@portfolio.com` / `password`
- User: `user@portfolio.com` / `password`

Final Project Structure:

```
web-programming/
├── backend/
│   ├── config/
│   │   ├── Database.php
│   │   └── database.sql
│   ├── dao/
│   │   ├── BaseDAO.php
│   │   ├── UserDAO.php
│   │   ├── ProjectDAO.php
│   │   ├── SkillDAO.php
│   │   ├── ExperienceDAO.php
│   │   └── ContactDAO.php
│   ├── services/
│   │   ├── UserService.php
│   │   ├── ProjectService.php
│   │   ├── SkillService.php
│   │   ├── ExperienceService.php
│   │   └── ContactService.php
│   ├── routes/
│   │   ├── AuthRoutes.php
│   │   ├── UserRoutes.php
│   │   ├── ProjectRoutes.php
│   │   ├── SkillRoutes.php
│   │   ├── ExperienceRoutes.php
│   │   └── ContactRoutes.php
│   ├── middleware/
│   │   └── AuthMiddleware.php
│   ├── data/
│   │   └── Roles.php
│   ├── public/
│   │   └── v1/
│   │       └── docs/
│   ├── index.php
│   ├── OpenApi.php (if present)
│   └── vendor/
│
├── frontend/
│   ├── views/
│   │   ├── home.html
│   │   ├── about.html
│   │   ├── projects.html
│   │   ├── skills.html
│   │   ├── contact.html
│   │   ├── login.html
│   │   ├── register.html
│   │   ├── dashboard.html
│   │   ├── admin.html
│   │   └── error_404.html
│   ├── js/
│   │   ├── app.js
│   │   ├── api.js
│   │   ├── dashboard.js
│   │   ├── admin.js
│   │   ├── features.js
│   │   └── spapp.min.js
│   ├── css/
│   │   ├── spapp.css
│   │   └── style.css
│   ├── assets/
│   │   └── images/
│   ├── index.html
│   ├── loader.php
│   └── .htaccess
│
└── README.md
```

Contact: alihandan@stu.ibu.edu.ba
- Role-based access control (RBAC)
- CORS configuration for API security
- Input validation and sanitization

##Contact

- Email: alihandan@stu.ibu.edu.ba
- GitHub: [@alihandanibu](https://github.com/alihandanibu)

##License

This project is developed for educational purposes as part of the Web Programming course at International Burch University.

---

**Course:** Web Programming  
**Institution:** International Burch University  
**Academic Year:** 2024/2025
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
For production, change the secret key in `backend/middleware/AuthMiddleware.php` from thdefault value.
```
