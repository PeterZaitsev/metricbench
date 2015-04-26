CREATE TABLE `metrics` (
  `period` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `device_id` int(10) unsigned NOT NULL,
  `metric_id` int(10) unsigned NOT NULL,
  `cnt` int(10) unsigned NOT NULL,
  `val` double DEFAULT NULL,
  PRIMARY KEY (`period`,`device_id`,`metric_id`),
  KEY `metric_id` (`metric_id`,`period`),
  KEY `device_id` (`device_id`,`period`),
  KEY `period` (`period`)
) ENGINE=TokuDB DEFAULT CHARSET=latin1;
