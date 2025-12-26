-- Add technologies column to projects table (for storing tech tags like "PHP,MySQL,JavaScript")
ALTER TABLE projects ADD COLUMN technologies VARCHAR(500) DEFAULT NULL AFTER description;

-- Update the category column to have default categories
-- Skills already have category column, just ensure it's being used
