<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 壁纸资源
 * @author Lyd
 *
 */
class Theme_Service_WallFileImage {

    //wallpaper_ ;
    /* private static $fileds = array(
      "wallpaper_id", 'wallpaper_name', 'wallpaper_updatetime',
      'wallpaper_user', 'wallpaper_type', 'wallpaper_size',
      'wallpaper_path', 'wallpaper_width', 'wallpaper_height'
      );
     */

    public static function update($data, $value){
        return self::_getDao()->update($data, $value);
    }

    public static function getdata($data) {
        return self::_getDao()->getAll($data);
    }

    public static function getByWhere($where, $fileds = "*") {
        return self::_getDao()->get_byWheres($where, $fileds);
    }

    public static function set_into_data($file, $username = null) {
        if (!$file['files']) return 0;
        //
        $tmp = get_object_vars($file["files"][0]);

        //拼装
        $files_info = self::mk_into_val($tmp, $username);

        //取出key;
        $filds = array_keys($files_info);
        $filds = implode(",", $filds);

        $val = array_values($files_info);
        $val = self::mk_array_tostring($val);
        return self::_getDao()->set_into_data($filds, $val);
    }

    /**
     * ids 取壁纸信息;
     * @param type $ids
     * @param type $ordery (true,false)
     * @return type
     */
    public static function get_in_images($ids, $ordery = '') {
        return self::_getDao()->getByFileIds($ids, $ordery);
    }

    /**
     * ids 取壁纸信息后台用;
     * @param type $ids
     * @param type $ordery (true,false)
     * @return type
     */
    public static function get_in_imagesAdmin($ids, $ordery = '') {
        return self::_getDao()->getByFileIdsAdmin($ids, $ordery);
    }

    public static function del_wallpaper($wallid) {
        return self::_getDao()->del_wallpaper($wallid);
    }

    //上传转态 ajax
    public static function update_up_status() {
        return self::_getDao()->update_up_status();
    }

    /**
     *
     * @param Array $data
     * @param Fileds $data['fileds'] 要返里的字段;
     * @param Where $data['where'] 查询条件 ;
     * @param sort $data['sort'] 排序字段 ;
     * @param sort_val $data['sort_val'] ASC DESC ;
     * @param  num $data['num'] 每页显示个数
     * @param  limit $data['limit'] 从第几条开始
     * @return Array
     */
    public static function get_all_imges($data) {
        $result = self::_getDao()->getAll($data);

        return $result;
    }

    public static function addCounts($id, $filds) {
        return self::_getDao()->addCounts($id, $filds);
    }

    public static function get_bywheres($wheres, $filed = "*") {
        return self::_getDao()->get_bywheres($wheres, $filed);
    }

    public static function get_userCounts($id) {
        $where = "wallpaper_id = $id";
        $fileds = "wallpaper_like_count,wallpaper_down_count ";
        return self::_getDao()->get_bywheres($where, $fileds);
    }

    public static function update_setStatus($status, $wid) {
        return self::_getDao()->updateStatus($status, $wid);
    }

    public static function updateFiled($value, $filed, $id) {
        return self::_getDao()->updateFiled($value, $filed, $id);
    }

    public static function updatewheres($wheres, $id) {
        return self::_getDao()->updatewheres($wheres, $id);
    }

    private function mk_imges_path($data) {

        if (!is_array($data)) return 0;

        foreach ($data as $keys => $vals) {
            if ($vals["wallpaper_name"] && $vals["wallpaper_path"]) {

            }
        }
    }

    private function mk_array_tostring($filds) {
        $res = implode("','", $filds);
        return $res;
    }

    private function mk_into_val($file, $userName) {
        if (!is_array($file)) return 0;

        $showName = substr($file["name"], strripos($file["name"], "_") + 1);
        $tem_val = array(
            'wallpaper_name' => $showName, // self::mk_sqls($file['name']),
            'wallpaper_updatetime' => time(),
            'wallpaper_user' => $userName,
            'wallpaper_type' => 0,
            'wallpaper_size' => $file['size'],
            'wallpaper_path' => self::mk_sqls(self::mk_file_path($file['url'])),
            'wallpaper_width' => $file['width'],
            'wallpaper_height' => $file['height'],
            'wallpaper_up_status' => 0,
            'wallpaper_show_name' => $showName,
        );

        return $tem_val;
    }

    private function mk_file_path($url) {
        $str = "/attachs/wallpaper/";

        $str_res = str_replace($str, '/', substr($url, strrpos($url, $str)));
        return $str_res;
    }

    public function mk_sqls($str) {
        $str = str_replace("and", "", $str);
        $str = str_replace("execute", "", $str);
        $str = str_replace("update", "", $str);
        $str = str_replace("count", "", $str);
        $str = str_replace("chr", "", $str);
        $str = str_replace("mid", "", $str);
        $str = str_replace("master", "", $str);
        $str = str_replace("truncate", "", $str);
        $str = str_replace("char", "", $str);
        $str = str_replace("declare", "", $str);
        $str = str_replace("select", "", $str);
        $str = str_replace("create", "", $str);
        $str = str_replace("delete", "", $str);
        $str = str_replace("insert", "", $str);
        $str = str_replace("'", "", $str);
        $str = str_replace("\"", "", $str);
        $str = str_replace(" ", "", $str);
        $str = str_replace("or", "", $str);
        $str = str_replace(" = ", "", $str);
        $str = str_replace("%20", "", $str);
        //echo $str;
        return $str;
    }

    private static function _getDao() {
        return Common::getDao("Theme_Dao_WallFileImage");
    }

}
