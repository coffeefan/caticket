CREATE TABLE IF NOT EXISTS `events` (
  `eventid` int(11) NOT NULL AUTO_INCREMENT,
  `eventstart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventend` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventname` varchar(50) DEFAULT NULL,
  `maxvisitors` int(11) DEFAULT NULL,
  `reportsended` int(11) DEFAULT '0',
  PRIMARY KEY (`eventid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(150) NOT NULL,
  `lastname` varchar(150) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `visitors` (
  `visitorid` int(11) NOT NULL AUTO_INCREMENT,
  `eventid` int(11) NOT NULL DEFAULT '0',
  `reservationkey` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `reservationstart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `firstname` varchar(128) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `lastname` varchar(128) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `address` varchar(128) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `zip` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `city` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `email` varchar(192) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `mobile` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `isdeleted` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`visitorid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=173 DEFAULT CHARSET=utf8;

