CREATE TABLE IF NOT EXISTS `3g_push_tools` (
  `pid` int(10) unsigned NOT NULL,
  `type` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `text` text NOT NULL,
  `after_open` varchar(128) NOT NULL,
  `big_picture_url` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `activity` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

ALTER TABLE `3g_push_tools`
  ADD PRIMARY KEY (`pid`);

ALTER TABLE `3g_push_tools`
  MODIFY `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;