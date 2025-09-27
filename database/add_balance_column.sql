-- Add Balance column to Members table
ALTER TABLE Members ADD COLUMN Balance DECIMAL(10,2) DEFAULT 0.00;

-- Update existing members with default balance of 0
UPDATE Members SET Balance = 0.00 WHERE Balance IS NULL;