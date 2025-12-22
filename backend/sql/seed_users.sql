USE portfolio;

/*
  seed_users.sql
  - Creates/updates predictable test users
  - Safe to run multiple times (idempotent)
  - Ensures name is not empty
*/

INSERT INTO users (name, email, password, role)
VALUES
  ('Admin',      'admin@portfolio.com',      '$2y$10$wRv1gGWUcapYln6br2LOH.Jx7Y58uVctC8Xcissq12gvpRxSlvI5a', 'admin'),
  ('User',       'user@portfolio.com',       '__HASH__$2y$10$wRv1gGWUcapYln6br2LOH.Jx7Y58uVctC8Xcissq12gvpRxSlvI5a', 'user'),
  ('Test Admin', 'testadmin@example.com',    '$2y$10$wRv1gGWUcapYln6br2LOH.Jx7Y58uVctC8Xcissq12gvpRxSlvI5a', 'admin')
ON DUPLICATE KEY UPDATE
  name     = VALUES(name),
  password = VALUES(password),
  role     = VALUES(role);

/* Extra safety: if any old rows exist with empty name, backfill them */
UPDATE users
SET name = 'Admin'
WHERE email = 'admin@portfolio.com' AND (name IS NULL OR name = '');

UPDATE users
SET name = 'User'
WHERE email = 'user@portfolio.com' AND (name IS NULL OR name = '');

UPDATE users
SET name = 'Test Admin'
WHERE email = 'testadmin@example.com' AND (name IS NULL OR name = '');

/* Verify */
SELECT id, name, email, role
FROM users
WHERE email IN ('admin@portfolio.com', 'user@portfolio.com', 'testadmin@example.com')
ORDER BY id;
