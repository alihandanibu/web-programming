-- Update testadmin user to admin role
UPDATE users SET role = 'admin' WHERE email = 'testadmin@example.com';

-- Verify the update
SELECT id, email, role FROM users WHERE email = 'testadmin@example.com';
