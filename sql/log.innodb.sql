CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `event` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `num_queries` int(10) unsigned NOT NULL,
  `num_rows` int(10) unsigned DEFAULT NULL,
  `time_total` float NOT NULL,
  `time_max` float NOT NULL,
  `status` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
