-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 01, 2023 at 04:03 PM
-- Server version: 5.7.33
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `el-mansheya-admin`
--
CREATE DATABASE IF NOT EXISTS `el-mansheya-admin` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `el-mansheya-admin`;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL COMMENT 'Activity ID',
  `name` varchar(255) NOT NULL COMMENT 'Activity Name',
  `comment` text NOT NULL,
  `who_added` varchar(255) NOT NULL,
  `category` int(11) NOT NULL COMMENT 'Activity Category'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `data_updates`
--

CREATE TABLE `data_updates` (
  `id` int(11) NOT NULL COMMENT 'Update ID',
  `category` varchar(255) NOT NULL COMMENT 'Updated Category',
  `action` varchar(255) NOT NULL COMMENT 'Action (Add, Update, Delete, ...)',
  `date` datetime NOT NULL COMMENT 'Update Date',
  `updater_id` int(11) NOT NULL COMMENT 'Updater User',
  `updated_id` int(11) NOT NULL COMMENT 'Updated Thing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL COMMENT 'Post ID',
  `subject` text NOT NULL COMMENT 'Post Subject',
  `user_id` int(11) NOT NULL COMMENT 'User ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL COMMENT 'Student ID',
  `code` int(11) NOT NULL COMMENT 'Student Code',
  `name` varchar(255) NOT NULL COMMENT 'Student Name',
  `grade` varchar(255) NOT NULL COMMENT 'Student Grade',
  `class` varchar(255) NOT NULL COMMENT 'Student Class',
  `second_language` varchar(255) NOT NULL COMMENT 'Student Second Language',
  `national_id` varchar(255) NOT NULL COMMENT 'Student National ID',
  `address` varchar(255) NOT NULL COMMENT 'Student Address',
  `birth_date` date NOT NULL COMMENT 'Student Birth_Date',
  `father_job` varchar(255) NOT NULL COMMENT 'Student Father Job',
  `image` varchar(255) NOT NULL COMMENT 'Student Image',
  `who_added` varchar(255) NOT NULL COMMENT 'Who Added'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `students_activities`
--

CREATE TABLE `students_activities` (
  `id` int(11) NOT NULL COMMENT 'Activity ID',
  `comment` text NOT NULL COMMENT 'Activity Comment',
  `procedure` text NOT NULL COMMENT 'Students Activities Procedure',
  `who_added` varchar(255) NOT NULL COMMENT 'Who Added The Student Activity',
  `student_id` int(11) NOT NULL COMMENT 'Student ID',
  `activity_id` int(11) NOT NULL COMMENT 'Activity ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `students_phones`
--

CREATE TABLE `students_phones` (
  `id` int(11) NOT NULL COMMENT 'Phone ID',
  `phone` varchar(255) NOT NULL COMMENT 'Student Phone',
  `type` int(11) NOT NULL COMMENT 'Students Phones Type',
  `who_added` varchar(255) NOT NULL COMMENT 'Who Added',
  `student_id` int(11) NOT NULL COMMENT 'Student ID '
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'User ID',
  `username` varchar(255) NOT NULL COMMENT 'User Login Name',
  `password` varchar(255) NOT NULL COMMENT 'User Login Password',
  `email` varchar(255) NOT NULL COMMENT 'User Email',
  `full_name` varchar(255) NOT NULL COMMENT 'User Full Name',
  `image` varchar(255) NOT NULL COMMENT 'User Image',
  `date` date NOT NULL COMMENT 'User Date',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT 'User Group ID',
  `who_added` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `image`, `date`, `group_id`, `who_added`) VALUES
(2, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'mahmoudfesal8@gmail.com', 'محمود فيصل', '1654586848_kk (2).JPG', '2018-11-17', 1, 'Mahmoud Fesal');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `data_updates`
--
ALTER TABLE `data_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_updates_user_id` (`updater_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_user_id` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students_activities`
--
ALTER TABLE `students_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `students_activities_student_id` (`student_id`),
  ADD KEY `students_activities_activity_id` (`activity_id`);

--
-- Indexes for table `students_phones`
--
ALTER TABLE `students_phones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `students_phones_student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Activity ID', AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `data_updates`
--
ALTER TABLE `data_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Update ID';

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Post ID';

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Student ID', AUTO_INCREMENT=2852;

--
-- AUTO_INCREMENT for table `students_activities`
--
ALTER TABLE `students_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Activity ID';

--
-- AUTO_INCREMENT for table `students_phones`
--
ALTER TABLE `students_phones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Phone ID', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User ID', AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_updates`
--
ALTER TABLE `data_updates`
  ADD CONSTRAINT `updater_id_data_updates` FOREIGN KEY (`updater_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students_activities`
--
ALTER TABLE `students_activities`
  ADD CONSTRAINT `students_activities_activity_id` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `students_activities_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students_phones`
--
ALTER TABLE `students_phones`
  ADD CONSTRAINT `students_phones_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
