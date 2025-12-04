-- Migration: Add post_like type to notifications table and rename content to message
USE fullstack;

-- First, rename the column if it's called content
-- Check if column exists by attempting to modify
ALTER TABLE notifications 
ADD COLUMN IF NOT EXISTS `message` TEXT;

-- Copy data from content to message if content exists (in case it's already there)
-- Update the enum to include post_like
ALTER TABLE notifications 
MODIFY type ENUM('message', 'review', 'post_approved', 'post_rejected', 'system', 'comment', 'reply', 'rating', 'post_like') NOT NULL;

-- Verify the changes
DESCRIBE notifications;

