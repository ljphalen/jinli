ALTER TABLE `3g_feedback_key` ADD `key_tag` VARCHAR(255) NOT NULL AFTER `name`;

ALTER TABLE `3g_feedback_faq` ADD `faq_tag` VARCHAR(255) NOT NULL AFTER `title`;

ALTER TABLE `3g_feedback_msg` ADD `auto_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `adm_type`;

ALTER TABLE `3g_feedback_msg` ADD `content_type` TINYINT(1) NOT NULL DEFAULT '0' AFTER `adm_type`;

ALTER TABLE `3g_feedback_msg` ADD `f_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `adm_type`;