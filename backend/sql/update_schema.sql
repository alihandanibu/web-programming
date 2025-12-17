-- One-time update for existing DBs created from the old schema
-- Adds the `name` column required by registration.

USE portfolio;

ALTER TABLE users
  ADD COLUMN name VARCHAR(100) NOT NULL AFTER id;

-- Backfill existing users (adjust as needed)
UPDATE users SET name = COALESCE(name, 'Admin') WHERE id = 1;
UPDATE users SET name = COALESCE(name, 'User') WHERE id = 2;
