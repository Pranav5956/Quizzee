-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2020 at 05:31 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizee`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `fid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `qnid` int(11) NOT NULL,
  `attempt_no` int(11) NOT NULL,
  `feedback_text` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `gid` int(11) NOT NULL,
  `ugid` varchar(35) NOT NULL,
  `gname` varchar(20) NOT NULL,
  `gdesc` longtext DEFAULT NULL,
  `create_time` longtext NOT NULL,
  `creator` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `oid` int(11) NOT NULL,
  `option_number` int(11) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `isanswer` tinyint(4) NOT NULL,
  `weightage` int(3) NOT NULL DEFAULT 0,
  `qnid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`oid`, `option_number`, `description`, `isanswer`, `weightage`, `qnid`) VALUES
(942, 0, NULL, 0, 0, 396),
(1067, 1, '\'The Shining\' by Stephen King', 0, 0, 439),
(1068, 2, '\'Oh! The Places You\'ll Go\' by Dr. Seuss', 1, 2, 439),
(1069, 3, '\'Little Women\' by Louisa May Alcott', 0, 0, 439),
(1070, 4, '\'Love You Forever\' by Robert Munsch', 0, 0, 439),
(1071, 1, 'Aunt Lilian', 0, 0, 440),
(1072, 2, 'Aunt Marilyn', 1, 2, 440),
(1073, 3, 'Aunt Iris', 0, 0, 440),
(1074, 4, 'Aunt Murial', 0, 0, 440),
(1075, 0, NULL, 0, 2, 441),
(1076, 0, NULL, 0, 2, 442),
(1077, 1, 'Pugsy', 0, 0, 443),
(1078, 2, 'Birdsy', 0, 0, 443),
(1079, 3, 'Hugsy', 1, 2, 443),
(1080, 1, 'James', 0, 0, 444),
(1081, 2, 'Janet', 0, 0, 444),
(1082, 3, 'Jack', 1, 2, 444),
(1083, 4, 'Judy', 1, 2, 444),
(1084, 5, 'John', 0, 0, 444),
(1085, 6, 'Joan', 0, 0, 444),
(1086, 1, 'Phoebe', 1, 2, 445),
(1087, 2, 'Chandler', 1, 2, 445),
(1088, 3, 'Joey', 0, 0, 445),
(1089, 4, 'Monica', 0, 0, 445),
(1090, 0, NULL, 0, 2, 446),
(1091, 1, 'True', 1, 2, 447),
(1092, 2, 'False', 0, 0, 447),
(1093, 0, NULL, 0, 3, 448),
(1097, 0, NULL, 0, 1, 451);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `qnid` int(11) NOT NULL,
  `question_number` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `type` varchar(4) DEFAULT NULL,
  `qid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`qnid`, `question_number`, `description`, `type`, `qid`) VALUES
(396, 1, 'Please provide feedback on the application and it\'s features.', 'D', 153),
(439, 1, 'What book does Joey get Rachel for her birthday?', 'MCQ', 163),
(440, 2, 'Which aunt does cousin Cassie look like?', 'MCQ', 163),
(441, 3, 'Complete Joey\'s quote - \"Ohh, sorry. I hear \'divorce\', I immediately go to __________________.', 'D', 163),
(442, 4, 'What was the name of the orthodontist Rachel was meant to marry in the pilot episode?', 'D', 163),
(443, 5, 'What is the name of Joey\'s stuffed penguin?', 'MCQ', 163),
(444, 6, 'What are Ross and Monica\'s parent\'s names?', 'MCMQ', 163),
(445, 7, 'F.R.I.E.N.D.S was originally planned to have 4 \'friends\'. Which of the following were initially planned to be support roles?', 'MCMQ', 163),
(446, 8, 'The names of all six friends were inspired by characters from which American Television series?', 'D', 163),
(447, 9, 'David Schwimmer didn\'t have to audition for the character of \'Ross\'.', 'TF', 163),
(448, 10, 'State the changes of Monica\'s, Chandler and Joey\'s apartment number and also the reason for such a change.', 'D', 163),
(451, 1, 'Hello', 'D', 166);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `qid` int(11) NOT NULL,
  `uqid` varchar(35) NOT NULL,
  `qname` varchar(30) NOT NULL,
  `type` varchar(1) NOT NULL,
  `code` varchar(8) DEFAULT NULL,
  `create_time` varchar(50) DEFAULT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`qid`, `uqid`, `qname`, `type`, `code`, `create_time`, `uid`) VALUES
(153, 'Q015bafb41071d3bc6433372717aeee4e', 'General Survey', 'O', NULL, '1599227951', 2),
(163, 'Q71d617ce9eb9da47a00630be5b8a43b0', 'F.R.I.E.N.D.S Quiz', 'O', NULL, '1599460855', 5),
(166, 'Q44d1b5417b7e8ddfd2d5ac73099fe43c', 'Quiz1', 'C', 'a0cd64a2', '1601355787', 2);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_group`
--

CREATE TABLE `quiz_group` (
  `gid` int(11) NOT NULL,
  `qid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_tag`
--

CREATE TABLE `quiz_tag` (
  `qid` int(11) NOT NULL,
  `tid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `resp_id` int(11) NOT NULL,
  `attempt_no` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `qid` int(11) NOT NULL,
  `qnid` int(11) NOT NULL,
  `oid` int(11) NOT NULL,
  `response` longtext DEFAULT NULL,
  `weightage` int(3) NOT NULL DEFAULT 0,
  `attempt_time` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`resp_id`, `attempt_no`, `uid`, `qid`, `qnid`, `oid`, `response`, `weightage`, `attempt_time`) VALUES
(135, 1, 2, 163, 444, 1082, NULL, 2, '1601437914'),
(136, 1, 2, 163, 444, 1083, NULL, 2, '1601437914'),
(137, 1, 2, 163, 446, 1090, '', 0, '1601437914'),
(138, 1, 2, 163, 442, 1076, '', 0, '1601437914'),
(139, 1, 2, 163, 448, 1093, '5 to 12', 1, '1601437914'),
(140, 1, 2, 163, 439, 1068, NULL, 2, '1601437914'),
(141, 1, 2, 163, 441, 1075, '', 0, '1601437914'),
(142, 1, 2, 163, 447, 1091, NULL, 2, '1601437914'),
(143, 1, 2, 163, 443, 1079, NULL, 2, '1601437914'),
(144, 1, 2, 163, 440, 1071, NULL, 0, '1601437914'),
(145, 1, 2, 163, 445, 1086, NULL, 2, '1601437914'),
(146, 1, 2, 163, 445, 1087, NULL, 2, '1601437914');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tid` int(11) NOT NULL,
  `tname` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `pwd` longtext DEFAULT NULL,
  `login` varchar(10) NOT NULL,
  `profile_pic` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `fname`, `lname`, `email`, `pwd`, `login`, `profile_pic`) VALUES
(2, 'Admin', 'Admin', 'admin123@admin.com', 'f859233f97ee2fa01f09da3ac3beb37b2be62dc9b5ee836fe65ff32700509cb0', 'LOGIN', '../Resources/ProfilePictures/19e9c4c8b93eb1729efb4f180b5a0c55.png'),
(3, 'Pranav Balaji', '19BAI1151', 'pranav.balaji2019@vitstudent.ac.in', NULL, 'GOOGLE', NULL),
(4, 'PRANAV', 'BALAJI', 'pranavbalaji01@gmail.com', NULL, 'GOOGLE', NULL),
(5, 'Pranav', 'Balaji', 'pranavbalaji01@gmail.com', '1c91346e9cdafb7ffd764998ba400209ca81c840387d591414d2f624bbf6875d', 'LOGIN', '../Resources/ProfilePictures/d2060ec624ad4c05af0d87eaec571b4e.png'),
(6, 'Abhinav', 'Balaji', 'abhinavbalaji07055@gmail.com', NULL, 'GOOGLE', NULL),
(8, 'Admin', '123', 'admin123@gmail.com', 'f859233f97ee2fa01f09da3ac3beb37b2be62dc9b5ee836fe65ff32700509cb0', 'LOGIN', 'Resources/ProfilePictures/0475025cddca21d0de6f85892438934b.jpg'),
(9, 'Pranav', 'Balaji', 'pranavbalaji2001@outlook.com', '1c91346e9cdafb7ffd764998ba400209ca81c840387d591414d2f624bbf6875d', 'LOGIN', 'Resources/ProfilePictures/788304f6c5a13c41df33459d28fef2b3.png'),
(11, 'Abhinav', 'Balaji', 'abhinavbalaji07055@gmail.com', '06ab873de17471ab4c2c6db8f3b0f14aff6c06ba3f7f8dd2dc3eb6d150c1f5e3', 'LOGIN', 'Resources/ProfilePictures/964901953091ec0aa40a827a1b0e495c.jpg'),
(12, 'Test', '123', 'test123@gmail.com', 'cac7a005c12fcefc3a6edab8249fa5c179b80dd7eb08949923df5d4626f63462', 'LOGIN', NULL),
(13, 'Test', '123', 'test123@outlook.com', '9ec68424c8719f2ed6e09338128bf9160c129519a0acdcb08c95efe540304ca0', 'LOGIN', NULL),
(14, 'Bhavana', 'Ram', 'crypticlass01@gmail.com', 'fe1b25e00e547b3d652227c92004f8dc63542ec0c6b5ab8aab4b4070b2d87087', 'LOGIN', '../Resources/ProfilePictures/10e2fb4537576df60b3543c80dacdebb.jpg'),
(15, 'a', 'a', 'a@bleh.com', 'f9b08c82a279463bbce0d836b4d47b9954338e055686f52f5b515f9fd843d644', 'LOGIN', '../Resources/ProfilePictures/f928a874e3ef14a6bd544312f58d839b.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE `user_group` (
  `qgid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `is_admin` enum('No','Yes') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`fid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `qnid` (`qnid`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`gid`),
  ADD KEY `creator` (`creator`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`oid`),
  ADD KEY `qnid` (`qnid`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`qnid`),
  ADD KEY `qid` (`qid`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`qid`),
  ADD UNIQUE KEY `uqid` (`uqid`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `quiz_group`
--
ALTER TABLE `quiz_group`
  ADD KEY `qid` (`qid`),
  ADD KEY `gid` (`gid`);

--
-- Indexes for table `quiz_tag`
--
ALTER TABLE `quiz_tag`
  ADD KEY `qid` (`qid`),
  ADD KEY `tid` (`tid`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`resp_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `responses_ibfk_2` (`oid`),
  ADD KEY `responses_ibfk_3` (`qnid`),
  ADD KEY `responses_ibfk_4` (`qid`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tid`),
  ADD UNIQUE KEY `tname` (`tname`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `user_group`
  ADD PRIMARY KEY (`qgid`),
  ADD KEY `gid` (`gid`),
  ADD KEY `uid` (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `oid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1099;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `qnid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=453;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `qid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `resp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `user_group`
  MODIFY `qgid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`qnid`) REFERENCES `questions` (`qnid`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`creator`) REFERENCES `users` (`uid`);

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`qnid`) REFERENCES `questions` (`qnid`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `quizzes` (`qid`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);

--
-- Constraints for table `quiz_group`
--
ALTER TABLE `quiz_group`
  ADD CONSTRAINT `quiz_group_ibfk_2` FOREIGN KEY (`qid`) REFERENCES `quizzes` (`qid`),
  ADD CONSTRAINT `quiz_group_ibfk_3` FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`);

--
-- Constraints for table `quiz_tag`
--
ALTER TABLE `quiz_tag`
  ADD CONSTRAINT `quiz_tag_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `quizzes` (`qid`),
  ADD CONSTRAINT `quiz_tag_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `tags` (`tid`);

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`oid`) REFERENCES `options` (`oid`),
  ADD CONSTRAINT `responses_ibfk_3` FOREIGN KEY (`qnid`) REFERENCES `questions` (`qnid`),
  ADD CONSTRAINT `responses_ibfk_4` FOREIGN KEY (`qid`) REFERENCES `quizzes` (`qid`);

--
-- Constraints for table `user_group`
--
ALTER TABLE `user_group`
  ADD CONSTRAINT `user_group_ibfk_1` FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`),
  ADD CONSTRAINT `user_group_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
