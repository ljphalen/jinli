ALTER TABLE `clock_file`
ADD COLUMN `c_since`  varchar(1024) NULL COMMENT '包标识' AFTER `c_rvurl`;

