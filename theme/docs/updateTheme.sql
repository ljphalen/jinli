
------------------------------v6.1.4------------

ALTER TABLE `theme_file`
ADD COLUMN `style`  varchar(128) NULL DEFAULT null COMMENT '主题风格' AFTER `editer_sort`;
-----------------------------end

--------------------2015-5-5-----------------------------------------
ALTER TABLE `theme_file`
ADD COLUMN `editer_sort`  int(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `is_faceimg`;
---------------------------------end-------------------------------------------------