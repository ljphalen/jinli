CREATE TABLE IF NOT EXISTS `3g_browser_wanka` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `app_ver` varchar(50) NOT NULL DEFAULT '',
  `wk_main_switch` tinyint(1) NOT NULL DEFAULT '1',
  `wk_searchEngines_switch` tinyint(1) NOT NULL DEFAULT '1',
  `wk_hotKeyword_switch` tinyint(1) NOT NULL DEFAULT '1',
  `wk_suggested_switch` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `3g_browser_wanka`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `3g_browser_wanka`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;