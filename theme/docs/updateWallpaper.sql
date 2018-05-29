----------------------2015-5-4------------------------------
ALTER TABLE `wallpaper_subject`
MODIFY COLUMN `w_image`  varchar(5120) CHARACTER SET utf8 
COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `w_image_face`;

----------------------------------------------------------------------