SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `Admins` (
  `AdminID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Role` varchar(50) DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Announcements` (
  `AnnouncementID` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Content` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `ExpiryDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Audience` enum('All','Members','Staff') DEFAULT 'All'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Attendance` (
  `AttendanceID` int(11) NOT NULL,
  `MemberID` int(11) DEFAULT NULL,
  `CheckIn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `CheckOut` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `AttendanceDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `AttendanceCount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Members` (
  `MemberID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `Age` int(11) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `MembershipStatus` enum('Active','Inactive','Suspended') DEFAULT 'Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Membership` (
  `MembershipID` int(11) NOT NULL,
  `MemberID` int(11) DEFAULT NULL,
  `Subscription` decimal(10,2) DEFAULT 0.00,
  `SessionPrice` decimal(10,2) DEFAULT 0.00,
  `Status` enum('Expired','Pending','Active') DEFAULT 'Pending',
  `StartDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `EndDate` timestamp GENERATED ALWAYS AS (`StartDate` + interval 1 month) STORED
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Payments` (
  `PaymentID` int(11) NOT NULL,
  `MemberID` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `ReceiptNumber` varchar(20) NOT NULL DEFAULT concat('REC-',lpad(floor(rand() * 1000000),6,'0')),
  `PaymentMethod` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Staff` (
  `StaffID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `JobTitle` enum('Trainer','Cashier') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `OTP` varchar(6) DEFAULT NULL,
  `OTPExpiration` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ResetToken` varchar(64) DEFAULT NULL,
  `ResetTokenExpiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

ALTER TABLE `Admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD KEY `UserID` (`UserID`);

ALTER TABLE `Announcements`
  ADD PRIMARY KEY (`AnnouncementID`);

ALTER TABLE `Attendance`
  ADD PRIMARY KEY (`AttendanceID`),
  ADD KEY `MemberID` (`MemberID`);

ALTER TABLE `Members`
  ADD PRIMARY KEY (`MemberID`),
  ADD KEY `UserID` (`UserID`);

ALTER TABLE `Membership`
  ADD PRIMARY KEY (`MembershipID`),
  ADD KEY `MemberID` (`MemberID`);

ALTER TABLE `Payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `MemberID` (`MemberID`);

ALTER TABLE `Staff`
  ADD PRIMARY KEY (`StaffID`),
  ADD KEY `UserID` (`UserID`);

ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

ALTER TABLE `Admins`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Announcements`
  MODIFY `AnnouncementID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Attendance`
  MODIFY `AttendanceID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Members`
  MODIFY `MemberID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Membership`
  MODIFY `MembershipID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Staff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Admins`
  ADD CONSTRAINT `Admins_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

ALTER TABLE `Attendance`
  ADD CONSTRAINT `Attendance_ibfk_1` FOREIGN KEY (`MemberID`) REFERENCES `Members` (`MemberID`) ON DELETE CASCADE;

ALTER TABLE `Members`
  ADD CONSTRAINT `Members_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

ALTER TABLE `Membership`
  ADD CONSTRAINT `Membership_ibfk_1` FOREIGN KEY (`MemberID`) REFERENCES `Members` (`MemberID`) ON DELETE CASCADE;

ALTER TABLE `Payments`
  ADD CONSTRAINT `Payments_ibfk_1` FOREIGN KEY (`MemberID`) REFERENCES `Members` (`MemberID`) ON DELETE CASCADE;

ALTER TABLE `Staff`
  ADD CONSTRAINT `Staff_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

COMMIT;
