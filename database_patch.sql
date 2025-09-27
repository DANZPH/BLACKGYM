-- Database patch to add missing columns for existing BLACKGYM installations
-- Run this if you already have the database and need to add the missing columns

-- Add Balance column to Members table if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'members' 
     AND column_name = 'Balance' 
     AND table_schema = DATABASE()) > 0,
    'SELECT "Balance column already exists"',
    'ALTER TABLE members ADD COLUMN Balance DECIMAL(10,2) DEFAULT 0.00'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add PaymentType column to Payments table if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_name = 'payments' 
     AND column_name = 'PaymentType' 
     AND table_schema = DATABASE()) > 0,
    'SELECT "PaymentType column already exists"',
    'ALTER TABLE payments ADD COLUMN PaymentType ENUM(''Subscription'',''Session'') DEFAULT ''Subscription'''
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing payments with default PaymentType based on PaymentMethod
UPDATE payments 
SET PaymentType = CASE 
    WHEN PaymentMethod LIKE '%session%' OR PaymentMethod LIKE '%Session%' THEN 'Session'
    ELSE 'Subscription'
END 
WHERE PaymentType IS NULL;

SELECT 'Database patch completed successfully!' as Status;