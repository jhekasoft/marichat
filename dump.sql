-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `chats`;
CREATE TABLE `chats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `chat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `messages` (`id`, `time`, `user_id`, `text`, `chat_id`) VALUES
(1,	'2014-10-26 22:09:50',	1,	'Test',	0),
(2,	'2014-10-26 17:14:52',	0,	'test',	0),
(3,	'2014-10-26 17:14:57',	0,	'fdfdsf',	0),
(4,	'2014-10-26 17:17:55',	0,	'dfdf',	0);

DROP TABLE IF EXISTS `shopping_list`;
CREATE TABLE `shopping_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `status` enum('undone','done') NOT NULL DEFAULT 'undone',
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shopping_list` (`id`, `title`, `status`, `datetime`) VALUES
(1,	'First task12',	'undone',	'2014-10-26 15:36:10'),
(12,	'Second task',	'undone',	'2014-08-23 20:49:37'),
(13,	'Third task',	'done',	'2014-08-23 20:49:55'),
(34,	'5th task',	'undone',	'2014-08-23 20:51:02');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) NOT NULL,
  `registration_time` datetime NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 2014-10-26 22:27:17
