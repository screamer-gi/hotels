CREATE TABLE `intervals` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `date_start` date NOT NULL,
 `date_end` date NOT NULL,
 `price` float NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `date_start` (`date_start`),
 UNIQUE KEY `date_end` (`date_end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
