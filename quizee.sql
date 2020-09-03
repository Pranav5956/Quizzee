-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2020 at 11:54 AM
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

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`fid`, `uid`, `qnid`, `attempt_no`, `feedback_text`) VALUES
(5, 2, 141, 1, 'Only one difference provided'),
(6, 2, 141, 2, 'Excellent answer!'),
(7, 2, 142, 2, 'Good'),
(8, 2, 143, 2, 'Good confidence'),
(9, 5, 211, 1, 'Thank you Pranav Balaji for your feedback!'),
(10, 5, 212, 1, 'Thank you');

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
(299, 0, NULL, 0, 4, 141),
(300, 1, 'True', 0, 0, 142),
(301, 2, 'False', 1, 1, 142),
(302, 1, 'Chimpanzee', 1, 1, 143),
(303, 2, 'Humans', 1, 1, 143),
(304, 3, 'Giraffe', 0, 0, 143),
(305, 4, 'Orangutans', 1, 1, 143),
(306, 5, 'Humming Bird', 0, 0, 143),
(307, 6, 'Platypus', 1, 1, 143),
(308, 1, 'Orangutan', 0, 0, 144),
(309, 2, 'Platypus', 1, 1, 144),
(310, 3, 'Rats', 0, 0, 144),
(311, 4, 'Humans', 0, 0, 144),
(408, 0, NULL, 0, 0, 211),
(409, 1, 'Good', 0, 0, 212),
(410, 2, 'Bad', 0, 0, 212),
(411, 3, 'Not bad', 0, 0, 212),
(412, 4, 'OK', 0, 0, 212);

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
(141, 1, 'Elucidate the differences between a lion and a tiger.', 'D', 72),
(142, 2, 'Do whales have teeth?', 'TF', 72),
(143, 3, 'Pick out the mammals from the following animals.', 'MCMQ', 72),
(144, 4, 'Which mammal out of the following lays eggs?', 'MCQ', 72),
(211, 1, 'Please provide feedback on the application and it\'s features.', 'D', 110),
(212, 2, 'How would you describe the application\'s user interface?', 'MCQ', 110);

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
(72, 'Qc859c51b9c89d0eb551f0e5aad7f7801', 'Biology Quiz - 1', 'C', 'fea63cd0', '1598885783', 5),
(110, 'Qaf9bd8132ff30b2ec4318b1173b06f23', 'General Survey', 'O', NULL, '1599074136', 2);

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
(109, 1, 2, 72, 142, 301, NULL, 1, '1599029486'),
(110, 1, 2, 72, 141, 299, 'Lions dont have stripes while tigers have black stripes', 1, '1599029486'),
(111, 1, 2, 72, 144, 309, NULL, 1, '1599029486'),
(112, 1, 2, 72, 143, 307, NULL, 1, '1599029486'),
(113, 1, 2, 72, 143, 303, NULL, 1, '1599029486'),
(114, 1, 2, 72, 143, 306, NULL, 0, '1599029486'),
(115, 1, 2, 72, 143, 302, NULL, 1, '1599029486'),
(116, 2, 2, 72, 141, 299, '1. Lion has a mane while a tiger doesn\'t.\r\n2. Lion does not have stripes while tiger has black stripes.\r\n3. Lion is the \'King of the Jungle\' but the tiger isn\'t.\r\n4. Tiger is the national animal of India, but the Lion isn\'t a national animal.\r\n', 4, '1599072662'),
(117, 2, 2, 72, 142, 301, NULL, 1, '1599072662'),
(118, 2, 2, 72, 143, 302, NULL, 1, '1599072662'),
(119, 2, 2, 72, 143, 305, NULL, 1, '1599072662'),
(120, 2, 2, 72, 143, 307, NULL, 1, '1599072662'),
(121, 2, 2, 72, 143, 303, NULL, 1, '1599072662'),
(122, 2, 2, 72, 144, 309, NULL, 1, '1599072662'),
(123, 1, 5, 110, 211, 408, 'The application is very nice. The dark theme is fantastic!', 0, '1599074208'),
(124, 1, 5, 110, 212, 409, NULL, 0, '1599074208');

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
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`resp_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `responses_ibfk_2` (`oid`),
  ADD KEY `responses_ibfk_3` (`qnid`),
  ADD KEY `responses_ibfk_4` (`qid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `oid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=413;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `qnid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `qid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `resp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`oid`) REFERENCES `options` (`oid`),
  ADD CONSTRAINT `responses_ibfk_3` FOREIGN KEY (`qnid`) REFERENCES `questions` (`qnid`),
  ADD CONSTRAINT `responses_ibfk_4` FOREIGN KEY (`qid`) REFERENCES `quizzes` (`qid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
