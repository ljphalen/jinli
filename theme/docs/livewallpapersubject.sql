drop table admin_user_info;


CREATE TABLE `admin_user_info` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`uid` INT(10) NOT NULL,
	`real_name` VARCHAR(255) NOT NULL,
	`nick_name` VARCHAR(255) NOT NULL,
	`sex` TINYINT(1) NOT NULL,
	`icon` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
AUTO_INCREMENT=2
;

CREATE TABLE `livewallpaper_subject` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(100) NULL DEFAULT NULL COMMENT '专题名称',
	`description` TEXT NULL COMMENT '描述',
	`category` MEDIUMINT(4) NULL DEFAULT NULL COMMENT '分类（广告和专题）',
	`cover` VARCHAR(255) NULL DEFAULT NULL COMMENT '封面',
	`images` TEXT NULL COMMENT '专题图片',
	`screen_sort` TINYINT(5) NULL DEFAULT NULL COMMENT '屏序',
	`status` TINYINT(5) NULL DEFAULT '1' COMMENT '状态',
	`created_time` INT(10) NULL DEFAULT NULL COMMENT '创建时间',
	`online_time` INT(10) NULL DEFAULT NULL COMMENT '上线时间',
	`last_update_time` INT(10) NULL DEFAULT NULL COMMENT '最后更新时间',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=15
;
