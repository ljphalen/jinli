CREATE TABLE `start_icon` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`path` VARCHAR(100) NOT NULL DEFAULT '0',
	`publish` TINYINT(1) NOT NULL DEFAULT '0',
	`created_time` INT(10) NOT NULL DEFAULT '0',
	`update_time` INT(10) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;


ALTER TABLE `theme_file`
	ADD COLUMN `hot_sort` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `editer_sort`;

ALTER TABLE `wallpaperlive_file`
	ADD COLUMN `hot_sort` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0' AFTER `wallpaperlive_type`;

ALTER TABLE `wallpaper_file_images`
	ADD COLUMN `hot_sort` MEDIUMINT(8) NULL DEFAULT '0' AFTER `wallpaper_note`;

ALTER TABLE `admin_user` ADD COLUMN `nick_name` VARCHAR(50) NULL DEFAULT '' AFTER `username`;
ALTER TABLE `admin_user` ADD COLUMN `sex` TINYINT(1) NOT NULL AFTER `nick_name`;
ALTER TABLE `admin_user` ADD COLUMN `icon` VARCHAR(255) NULL AFTER `sex`;

---------------------------------------------------------
ALTER TABLE `theme_file`
ADD COLUMN `style`  varchar(128) NULL DEFAULT null COMMENT '主题风格' AFTER `editer_sort`;