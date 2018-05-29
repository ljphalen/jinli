-- 下载sdk时，按上传文件名进行保存
ALTER TABLE article ADD COLUMN `file_name` varchar(250) DEFAULT NULL COMMENT "上传文件名" AFTER file_path;

-- 添加四个分类字段
ALTER TABle apks
ADD COLUMN `category_p` varchar(255) DEFAULT NULL COMMENT '主分类' AFTER `category_one`,
ADD COLUMN `category_p_son` varchar(255) DEFAULT NULL COMMENT '主分类的子分类' AFTER `category_p`,
ADD COLUMN `category_s` varchar(255) DEFAULT NULL COMMENT '次分类' AFTER `category_p_son`,
ADD COLUMN `category_s_son` varchar(255) DEFAULT NULL COMMENT '次分类的子分类' AFTER `category_s`;