CREATE TABLE `rbl_listings` (
  `rbl_id` int(11) NOT NULL AUTO_INCREMENT,
  `blocklist` varchar(30) DEFAULT NULL,
  `id` varchar(30) DEFAULT NULL,
  `parent_id` varchar(30) DEFAULT NULL,
  `ip_range` varchar(30) DEFAULT NULL,
  `link` varchar(30) DEFAULT NULL,
  `brand` varchar(16) DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  `server_name` varchar(255) DEFAULT NULL,
  `client_id` varchar(16) DEFAULT NULL,
  `ticket_id` varchar(16) DEFAULT NULL,
  `initiated_by` varchar(30) DEFAULT NULL,
  `assigned_to` varchar(30) DEFAULT NULL,
  `date_added` date DEFAULT NULL,
  `date_resolved` date DEFAULT NULL,
  `comment` longtext,
  `resolution` longtext,
  PRIMARY KEY (`rbl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;