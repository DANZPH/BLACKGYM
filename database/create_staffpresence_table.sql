-- Create StaffPresence table to track staff attendance
CREATE TABLE StaffPresence (
    PresenceID INT PRIMARY KEY AUTO_INCREMENT,
    StaffID INT NOT NULL,
    Status ENUM('Present', 'Absent', 'Late') NOT NULL,
    Date DATE NOT NULL,
    TimeIn TIME,
    TimeOut TIME,
    Notes TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (StaffID) REFERENCES Staff(StaffID) ON DELETE CASCADE,
    UNIQUE KEY unique_staff_date (StaffID, Date)
);

-- Insert some sample data for existing staff (optional)
-- This will mark all existing staff as present for today if they don't have a record
INSERT IGNORE INTO StaffPresence (StaffID, Status, Date)
SELECT StaffID, 'Present', CURDATE()
FROM Staff
WHERE NOT EXISTS (
    SELECT 1 FROM StaffPresence sp
    WHERE sp.StaffID = Staff.StaffID AND sp.Date = CURDATE()
);