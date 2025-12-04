-- =============================================
-- MIGRATION: Add avatar and bio columns to users table
-- =============================================

USE fullstack;

-- Add avatar and bio columns to users table if they don't exist
ALTER TABLE users ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER phone;
ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL AFTER avatar;

-- =============================================
-- END OF MIGRATION
-- =============================================
