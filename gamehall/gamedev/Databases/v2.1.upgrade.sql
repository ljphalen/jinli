-- 联运添加审核文档
ALTER TABLE union_authorize  ADD COLUMN `doc_file` varchar(255) DEFAULT NULL COMMENT '文件路径' AFTER `note`;