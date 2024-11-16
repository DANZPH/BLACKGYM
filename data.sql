-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- Host: sql104.infinityfree.com
-- Generation Time: Nov 15, 2024 at 10:45 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `if0_36048499_db_user`

-- Table structure for table `Admins`
CREATE TABLE `Admins` (
  `AdminID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Role` varchar(50) DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for table `Announcements`
CREATE TABLE `Announcements` (
  `AnnouncementID` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Content` text NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `ExpiryDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Audience` enum('All','Members','Staff') DEFAULT 'All'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for table `Attendance`
CREATE TABLE `Attendance` (
  `AttendanceID` int(11) NOT NULL,
  `MemberID` int(11) DEFAULT NULL,
  `CheckIn` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `CheckOut` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `AttendanceDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for table `Members`
CREATE TABLE `Members` (
  `MemberID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Gender` enum('Male','Female','Other') NOT NULL,
  `Age` int(11) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `MembershipStatus` enum('Active','Inactive','Suspended') DEFAULT 'Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for table `Membership`
CREATE TABLE `Membership` (
  `MembershipID` int(11) NOT NULL,
  `MemberID` int(11) DEFAULT NULL,
  `Subscription` decimal(10,2) DEFAULT 0.00,
  `SessionPrice` decimal(10,2) DEFAULT 0.00,
  `Status` enum('Expired','Pending','Active') DEFAULT 'Pending',
  `StartDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `EndDate` timestamp GENERATED ALWAYS AS (`StartDate` + interval 1 month) STORED
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for table `Payments`
CREATE TABLE `Payments` (
  `PaymentID` int(11) NOT NULL,
  `MemberID` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `ReceiptNumber` varchar(20) NOT NULL DEFAULT concat('REC-',lpad(floor(rand() * 1000000),6,'0')),
  `PaymentMethod` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for table `Staff`
CREATE TABLE `Staff` (
  `StaffID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `JobTitle` enum('Trainer','Cashier') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for table `Users`
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

-- Indexes for dumped tables

-- Indexes for table `Admins`
ALTER TABLE `Admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD KEY `UserID` (`UserID`);

-- Indexes for table `Announcements`
ALTER TABLE `Announcements`
  ADD PRIMARY KEY (`AnnouncementID`);

-- Indexes for table `Attendance`
ALTER TABLE `Attendance`
  ADD PRIMARY KEY (`AttendanceID`),
  ADD KEY `MemberID` (`MemberID`);

-- Indexes for table `Members`
ALTER TABLE `Members`
  ADD PRIMARY KEY (`MemberID`),
  ADD KEY `UserID` (`UserID`);

-- Indexes for table `Membership`
ALTER TABLE `Membership`
  ADD PRIMARY KEY (`MembershipID`),
  ADD KEY `MemberID` (`MemberID`);

-- Indexes for table `Payments`
ALTER TABLE `Payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `MemberID` (`MemberID`);

-- Indexes for table `Staff`
ALTER TABLE `Staff`
  ADD PRIMARY KEY (`StaffID`),
  ADD KEY `UserID` (`UserID`);

-- Indexes for table `Users`
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

-- AUTO_INCREMENT for dumped tables

-- AUTO_INCREMENT for table `Admins`
ALTER TABLE `Admins`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- AUTO_INCREMENT for table `Announcements`
ALTER TABLE `Announcements`
  MODIFY `AnnouncementID` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `Attendance`
ALTER TABLE `Attendance`
  MODIFY `AttendanceID` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `Members`
ALTER TABLE `Members`
  MODIFY `MemberID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

-- AUTO_INCREMENT for table `Membership`
ALTER TABLE `Membership`
  MODIFY `MembershipID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

-- AUTO_INCREMENT for table `Payments`
ALTER TABLE `Payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `Staff`
ALTER TABLE `Staff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `Users`
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

-- Constraints for dumped tables

-- Constraints for table `Admins`
ALTER TABLE `Admins`
  ADD CONSTRAINT `Admins_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

-- Constraints for table `Attendance`
ALTER TABLE `Attendance`
  ADD CONSTRAINT `Attendance_ibfk_1` FOREIGN KEY (`MemberID`) REFERENCES `Members` (`MemberID`) ON DELETE CASCADE;

-- Constraints for table `Members`
ALTER TABLE `Members`
  ADD CONSTRAINT `Members_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

-- Constraints for table `Membership`
ALTER TABLE `Membership`
  ADD CONSTRAINT `Membership_ibfk_1` FOREIGN KEY (`MemberID`) REFERENCES `Members` (`MemberID`) ON DELETE CASCADE;

-- Constraints for table `Payments`
ALTER TABLE `Payments`
  ADD CONSTRAINT `Payments_ibfk_1` FOREIGN KEY (`MemberID`) REFERENCES `Members` (`MemberID`) ON DELETE CASCADE;

-- Constraints for table `Staff`
ALTER TABLE `Staff`
  ADD CONSTRAINT `Staff_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

COMMIT;
