
CREATE TABLE IF NOT EXISTS `module_simple_alerts` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) NOT NULL,
  `pid` bigint(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `alert` TEXT NOT NULL,
  `active` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB;
