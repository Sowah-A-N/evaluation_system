-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 28, 2024 at 08:59 AM
-- Server version: 8.0.31
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `course_evaluation`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_year`
--

DROP TABLE IF EXISTS `academic_year`;
CREATE TABLE IF NOT EXISTS `academic_year` (
  `academic_year_id` int NOT NULL AUTO_INCREMENT,
  `start_year` int NOT NULL,
  `end_year` int GENERATED ALWAYS AS ((`start_year` + 1)) STORED,
  `year_label` varchar(9) GENERATED ALWAYS AS (concat(`start_year`, '/',`end_year`)) STORED,
  `is_active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`academic_year_id`),
  UNIQUE KEY `is_active` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `academic_year`
--

INSERT INTO `academic_year` (`academic_year_id`, `start_year`, `is_active`) VALUES
(1, 2024, 1),
(2, 2023, 0);

-- --------------------------------------------------------

--
-- Table structure for table `active_semester`
--

DROP TABLE IF EXISTS `active_semester`;
CREATE TABLE IF NOT EXISTS `active_semester` (
  `semester_id` int NOT NULL,
  `semester_name` enum('First','Second') NOT NULL,
  `semester_value` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`semester_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `active_semester`
--

INSERT INTO `active_semester` (`semester_id`, `semester_name`, `semester_value`, `is_active`) VALUES
(1, 'First', 1, 0),
(2, 'Second', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `advisor_levels`
--

DROP TABLE IF EXISTS `advisor_levels`;
CREATE TABLE IF NOT EXISTS `advisor_levels` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `level_id` int NOT NULL,
  `department_id` int NOT NULL,
  `advisor_id` int NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `advisor_levels`
--

INSERT INTO `advisor_levels` (`t_id`, `level_id`, `department_id`, `advisor_id`) VALUES
(2, 1, 2, 11);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `year_of_completion` varchar(100) CHARACTER SET utf8mb4  NOT NULL,
  `programme` varchar(100) NOT NULL,
  `level_id` int NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`t_id`, `class_name`, `department`, `year_of_completion`, `programme`, `level_id`) VALUES
(1, ' BIT28', 'ICT', '2028', '2', 1),
(2, ' BIT27', 'ICT', '2027', '2', 2);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_code` varchar(50) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `department` varchar(100) NOT NULL,
  `level_id` int NOT NULL,
  `semester_id` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `name`, `department`, `level_id`, `semester_id`) VALUES
(1, ' BINT 108', 'Principles of Programming and Problem Solving', 'ICT', 1, 1),
(2, ' BINT 309', 'DATABASES 1', 'ICT', 2, 2),
(5, ' BINT 109', 'INTRO TO WEB DESIGN', 'ICT', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `hod_id` int NOT NULL,
  `dep_name` varchar(100) NOT NULL,
  `dep_code` varchar(50) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`t_id`, `hod_id`, `dep_name`, `dep_code`) VALUES
(1, 4, 'Department Of Transport', 'DOT'),
(2, 2, 'ICT', 'ICT'),
(3, 5, 'Marine Engineering Department', 'MEE'),
(4, 7, 'Electrical Department', 'EEE'),
(5, 20, 'test', 'TEE'),
(6, 0, 'GRADUATE SCHOOL', 'GRAD001');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
CREATE TABLE IF NOT EXISTS `evaluations` (
  `evaluation_id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `course_id` varchar(50) NOT NULL,
  `evaluation_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`evaluation_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`evaluation_id`, `student_id`, `course_id`, `evaluation_date`) VALUES
(9, 'RMUDMSHZOKWI', ' BINT 109', '2024-11-19 21:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_questions`
--

DROP TABLE IF EXISTS `evaluation_questions`;
CREATE TABLE IF NOT EXISTS `evaluation_questions` (
  `question_id` int NOT NULL AUTO_INCREMENT,
  `question_text` varchar(255) NOT NULL,
  `is_required` tinyint(1) DEFAULT '1',
  `category` varchar(50) DEFAULT 'General',
  PRIMARY KEY (`question_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `evaluation_questions`
--

INSERT INTO `evaluation_questions` (`question_id`, `question_text`, `is_required`, `category`) VALUES
(1, 'I was briefed on the course overview and objective', 1, 'Questions'),
(2, 'I am able to relate the theory to practical', 1, 'Questions'),
(3, 'There is adequate practical content', 1, 'Questions'),
(4, 'The lecture helped me to understand the learning materials', 1, 'Questions'),
(5, 'I was encouraged to ask questions', 1, 'Questions'),
(6, 'How would you rate the equipment (simulator, swimming pool, workshop) used for the practical session?', 1, 'Questions'),
(7, 'How would you assess the handouts provided?', 1, 'Questions'),
(8, 'Timetable was timely and adhered to', 1, 'Questions'),
(9, 'Lecturer was available as scheduled', 1, 'Questions'),
(10, 'How would you rate the performance of your class advisor?', 1, 'Questions'),
(11, 'How would you rate the assessments conducted', 1, 'Assessment'),
(12, 'Classroom environment was conducive to learning.', 1, 'Teaching and Learning Environment'),
(13, 'How would you rate other facilities such as washrooms and surroundings?', 1, 'Washroom & Surroundings'),
(14, 'Customer Service:  Staff were supportive', 1, 'Registry'),
(15, 'Turnaround time: Waiting time was short ', 1, 'Registry'),
(16, 'Feedback: received timely feedback on my request', 1, 'Registry'),
(17, 'Customer Service:  Staff were supportive', 1, 'Accounts'),
(18, 'Turnaround time: Waiting time was short ', 1, 'Accounts'),
(19, 'Feedback: received timely feedback on my request', 1, 'Accounts'),
(20, 'Customer Service:  Staff were supportive', 1, 'Library'),
(21, 'Turnaround time: Waiting time was short ', 1, 'Library'),
(22, 'Feedback: received timely feedback on my request', 1, 'Library'),
(23, 'Customer Service:  Staff were supportive', 1, 'Administration'),
(24, 'Turnaround time: Waiting time was short ', 1, 'Administration'),
(25, 'Feedback: received timely feedback on my request', 1, 'Administration'),
(26, 'Customer Service:  Staff were supportive', 1, 'Sickbay'),
(27, 'Turnaround time: Waiting time was short ', 1, 'Sickbay'),
(28, 'Feedback: received timely feedback on my request', 1, 'Sickbay'),
(29, 'test question', 1, 'Washroom & Surroundings');

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

DROP TABLE IF EXISTS `level`;
CREATE TABLE IF NOT EXISTS `level` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`t_id`, `level_name`) VALUES
(1, 'level 100'),
(2, 'level 200'),
(3, 'level 300'),
(4, 'level 400');

-- --------------------------------------------------------

--
-- Table structure for table `programme`
--

DROP TABLE IF EXISTS `programme`;
CREATE TABLE IF NOT EXISTS `programme` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `prog_code` varchar(20) NOT NULL,
  `prog_name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  PRIMARY KEY (`t_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `programme`
--

INSERT INTO `programme` (`t_id`, `prog_code`, `prog_name`, `department`) VALUES
(2, ' BIT', 'BSc Information Technology', 'ICT'),
(3, ' BCS', 'BSc Computer Science', 'ICT'),
(4, ' MEE', 'Bsc Marine Enginnering', 'Marine Engineering Department');

-- --------------------------------------------------------

--
-- Table structure for table `questions_archive`
--

DROP TABLE IF EXISTS `questions_archive`;
CREATE TABLE IF NOT EXISTS `questions_archive` (
  `question_id` int NOT NULL AUTO_INCREMENT,
  `question_text` varchar(255) NOT NULL,
  `category` varchar(50) DEFAULT 'General',
  `archived_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

DROP TABLE IF EXISTS `responses`;
CREATE TABLE IF NOT EXISTS `responses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `evaluation_id` int NOT NULL,
  `question_id` int DEFAULT NULL,
  `response_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `evaluation_id`, `question_id`, `response_value`) VALUES
(1, 9, 1, '3'),
(2, 9, 2, '3'),
(3, 9, 3, '3'),
(4, 9, 4, '3'),
(5, 9, 5, '3'),
(6, 9, 6, '3'),
(7, 9, 7, '3'),
(8, 9, 8, '3'),
(9, 9, 9, '3'),
(10, 9, 10, '3'),
(11, 9, 11, '5'),
(12, 9, 12, '5'),
(13, 9, 13, '5'),
(14, 9, 14, '1'),
(15, 9, 15, '1'),
(16, 9, 16, '1'),
(17, 9, 17, '1'),
(18, 9, 18, '1'),
(19, 9, 19, '1'),
(20, 9, 20, '3'),
(21, 9, 21, '2'),
(22, 9, 22, '2'),
(23, 9, 23, '3'),
(24, 9, 24, '2'),
(25, 9, 25, '2'),
(26, 9, 26, '2'),
(27, 9, 27, '4'),
(28, 9, 28, '4');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `role_name` varchar(100) NOT NULL,
  PRIMARY KEY (`t_id`),
  UNIQUE KEY `role_id` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`t_id`, `role_id`, `role_name`) VALUES
(1, 1, 'admin'),
(2, 2, 'hod'),
(3, 3, 'secretary'),
(4, 4, 'advisor'),
(5, 5, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

DROP TABLE IF EXISTS `user_details`;
CREATE TABLE IF NOT EXISTS `user_details` (
  `user_details` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `unique_id` varchar(20) CHARACTER SET utf8mb4  DEFAULT NULL,
  `password` varchar(254) NOT NULL,
  `department` varchar(50) NOT NULL,
  `class` varchar(100) DEFAULT NULL,
  `level_id` int DEFAULT NULL,
  PRIMARY KEY (`user_details`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 ;

--
-- Dumping data for table `user_details`
--

INSERT INTO `user_details` (`user_details`, `role_id`, `f_name`, `l_name`, `username`, `email`, `unique_id`, `password`, `department`, `class`, `level_id`) VALUES
(1, 1, 'Prosper', 'test', 'admin', 'admin@gmail.com', NULL, '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda', 'adminstration', NULL, NULL),
(2, 2, 'Nii Adotei', 'Addo', 'Surnii', 'niiadot19@gmail.com', NULL, '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda', 'ICT', NULL, NULL),
(3, 3, 'Selorm', 'Fugar', 'Jselly01', 'ismailabdulaisaiku@gmail.com', NULL, '$2y$10$UUNNkASS34Rv788ESGhZsu5pJvH6CrTNTj5hGmBzmH67oPF0SOM2q', 'ICT', NULL, NULL),
(4, 2, 'S', 'A-N', 'HOD DOT', 'san@gmail.com', NULL, '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda', 'Department Of Transport', NULL, NULL),
(5, 2, 'Harry ', 'Johnson', 'HOD MEE', 'harry@gmail.com', NULL, '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda', 'Marine Engineering Department', NULL, NULL),
(7, 2, 'Issac', 'Nyarko', 'HOD EE', 'q@gmail.com', NULL, '$2y$10$h9W549aZiR.Y6HxscCr7OOux8CpZLOTiSCd3oEsbgxu2QTraefefi', 'Electrical Department', NULL, NULL),
(11, 4, 'Samuel', 'Enguah', 'L100 Advisor', 'sam@gmail.com', NULL, '$2y$10$CM6eFP4uHwbmrPVTMfSO/.k2Z0FBwfd.QVF1s4SsYz69g6dwroPca', 'ICT', NULL, NULL),
(12, 5, 'Jeff', 'Nyarko', NULL, 'jsf@gmail.com', 'RMUDMSHZOKWI', '$2y$10$/8rHe8tJ0jbySZvAlSFcX.lpn.5i353g2CaiBl9FDIs8bp9Opyqda', 'ICT', ' BIT28', 1),
(18, 5, 'Hidaya', 'Sulemana', NULL, 'suleman@gmail.com', 'RMUYPD13BVJT', '$2y$10$zKUKBx6y0nEou.603bxVIO0qVCJilttgmgzQEZGetfphYN07Ypb5m', 'ICT', 'BIT 28', 1),
(17, 5, 'Denzel', 'Curry', NULL, 'denzel@gmail.com', 'RMU13CVP90QZ', '$2y$10$AaOAyLJao4SrT2lNdqMRHu1vjI3COiUvr23XNUqseC.YzmsoIUaHS', 'ICT', 'BIT 27', 2),
(19, 5, 'Naila', 'Alhassan', NULL, 'naila@gmail.com', 'RMUZVI0GHLCY', '$2y$10$YLHTEFhhKFaUyaM8y3eWiuhSzxTrC/iYp2w7eUMrefpGLH/dC4gzq', 'ICT', 'BIT 28', 1),
(20, 2, 'test', 'hod', 'hr', 'h@gmail.com', NULL, '$2y$10$2AI9SkGWdjUEFJENdQJlbufspQv4MiENm4YM/oCxhVKuZ9SdikYVS', 'test', NULL, NULL),
(21, 3, 'test', 'sec', 'meesec', 'y@gmail.com', NULL, '$2y$10$LsjKq4o66KmGrb6AYRL.FOAa9TIlSvoOMmUtmPWimmxAJ77odPBGe', 'Marine Engineering Department', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
