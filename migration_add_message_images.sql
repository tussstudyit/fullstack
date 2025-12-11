-- Migration: Add image column to messages table
ALTER TABLE messages ADD COLUMN image VARCHAR(255) NULL AFTER message;
