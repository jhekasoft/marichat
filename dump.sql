CREATE TABLE `shopping_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `status` enum('undone','done') NOT NULL DEFAULT 'undone',
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `shopping_list` (`id`, `title`, `status`, `datetime`) VALUES
(1,	'First task',	'undone',	'2014-08-23 20:49:22'),
(12,	'Second task',	'undone',	'2014-08-23 20:49:37'),
(13,	'Third task',	'done',	'2014-08-23 20:49:55'),
(34,	'5th task',	'undone',	'2014-08-23 20:51:02');
