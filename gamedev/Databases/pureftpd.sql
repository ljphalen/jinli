DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `User` varchar(32) NOT NULL DEFAULT '',
  `Password` varchar(64) NOT NULL DEFAULT '',
  `Uid` int(3) NOT NULL DEFAULT '500',
  `Gid` int(3) NOT NULL DEFAULT '500',
  `Dir` varchar(255) NOT NULL DEFAULT '',
  `QuotaSize` int(4) NOT NULL DEFAULT '50',
  `Status` enum('0','1') NOT NULL DEFAULT '1',
  `ULBandwidth` int(2) NOT NULL DEFAULT '100',
  `DLBandwidth` int(2) NOT NULL DEFAULT '100',
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Created_at` int(10) NOT NULL DEFAULT 0,
  `LastModif` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `User` (`User`),
  KEY `Uid` (`Uid`),
  KEY `Gid` (`Gid`),
  KEY `Dir` (`Dir`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;