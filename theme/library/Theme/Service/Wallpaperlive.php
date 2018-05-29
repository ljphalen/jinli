<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class Theme_Service_Wallpaperlive {

    private static $filed_status = "wallpaperlive_status";
    private static $onlinetime = "wallpaperlive_onlinetime";
    private static $id = "wallpaperlive_id";

    private static function _getDao() {
        return Common::getDao("Theme_Dao_Wallpaperlive");
    }

    private static function _getEnginDao() {
        return Common::getDao("Theme_Dao_WallpaperliveEngin");
    }

    public static function update($data, $value) {
        return self::_getDao()->update($data, $value);
    }

    /**
     * 添加动态壁纸引擎...
     * @param array $data
     * @param key    字段名;
     * @param Value  值;
     * @return int
     */
    public static function addEngin(array $data = array()) {
        if (!$data) return 0;
        $filedname = implode(",", array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";

        $res = self::_getEnginDao()->setDataDao($filedname, $values);
        return $res;
    }

    /**
     * 更新动态壁纸引擎;
     * @param type $enginid 引擎id;
     * @param array $data   更新数据　key=>val;
     * @return type int 1,0;
     */
    public static function updateEngindata($enginid, array $data = array(), $showsql = FALSE) {
        if (!$enginid) return 0;

        $where = "id=$enginid";

        $key = array_keys($data);
        $val = array_values($data);
        foreach ($key as $k => $s) {
            $dataStr = "$s = '$val[$k]'";
            if ($k < count($key) - 1) $dataStr .= ",";
        }
        $res = self::_getEnginDao()->updataDao($dataStr, $where, $showsql);

        return $res;
    }

    public static function delEngin($eid, $sqls = false) {
        $res = self::_getEnginDao()->delDao($eid, $sqls);
        return $res;
    }

    /**
     * 动态壁纸列表;
     * @retrue array;
     *
     */
    public static function getlistEngin($where = '', $fileds = "*") {
        if (!$where) $where = "1 = 1";
        $res = self::_getEnginDao()->getDataDao($where, $fileds);
        return $res;
    }

    public static function runDao($func, $args) {
        return self::_getDao()->$func($args);
    }

    public static function insert_into_sql($filedname, $val, $showsql = false) {
        return self::_getDao()->setDataDao($filedname, $val, $showsql);
    }

    public static function insert_into($filedname, $val, $showsql = false) {
        if (is_array($filedname)) $filedname = implode(",", $filedname);
        if (is_array($val)) $val = implode(",", $val);


        return self::_getDao()->setDataDao($filedname, $val, $showsql);
    }

    public static function getListByWhere($where, $filed = "*", $showsql = false) {
        $res = self::_getDao()->getDataDao($where, $filed, $showsql);
        return $res;
    }

    //通过ids取数据
    public static function getListByids($ids, $order_filed = false, $filed = '*', $showsql = false) {
        if ($order_filed) $where = self::$id . " in($ids) ORDER BY FIELD(" . self::$id . ",$ids)";
        else $where = $where = self::$id . " in($ids)";

        return self::getListByWhere($where, $filed, $showsql);
    }

    //修改动态壁纸的状态;
    public static function update_status($status, $id, $showsql = false) {
        //如果状态是上线，则更新上线时间;
        if ($status == 4) $data = self::$filed_status . '=' . $status . ',' . self::$onlinetime . '=' . time();
        else $data = self::$filed_status . "=" . $status;

        $where = self::$id . "=$id";
        return self::_getDao()->updataDao($data, $where, $showsql);
    }

    public static function update_info(array $arr, $id, $showsql) {
        $i = 0;
        foreach ($arr as $k => $v) {
            $data .=$k . "=" . "'$v'";
            $i++;
            if ($i < count($arr)) $data .=",";
        }
        $where = self::$id . "=$id";

        return self::_getDao()->updataDao($data, $where, $showsql);
    }

    public static function update_addLike($id) {
        $where = self::$id . "=$id";
        $data = "wallpaperlive_like=wallpaperlive_like+1";
        return self::_getDao()->updataDao($data, $where);
    }

    public static function update_addDown($id) {
        $where = self::$id . "=$id";
        $data = "wallpaperlive_down=wallpaperlive_down+1";
        return self::_getDao()->updataDao($data, $where);
    }

//delete  动态壁纸;
    public static function delWallpaper($id, $showsql = false) {
        return self::_getDao()->delDao($id, $showsql);
    }

    private function escapeStr($string) {
        $string = str_replace(array("\0", "%00", "\r"), '', $string);
        $string = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/', '/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $string);
        $string = str_replace(array("%3C", '<'), '&lt;', $string);
        $string = str_replace(array("%3E", '>'), '&gt;', $string);
        $string = str_replace(array('"', "'", "\t", '  '), array('&quot;', '&#39;', '    ', '&nbsp;&nbsp;'), $string);
        return $string;
    }

    /**
     * 拷贝文件
     * $file 文件名
     * $src 源目录
     * $dst 目标目录
     */
    private function copyFile($file, $src, $dst) {
        if (!$file || !$src || !$dst) return false;

        $system = new Util_System();
        if ($system->smkdir($dst) !== true) return Common::formatMsg(-1, '文件处理失败!');

        $ret = copy($src . '/' . $file, $dst . '/' . $file);

        if ($ret !== true) return Common::formatMsg(-1, '文件处理失败!');
        @chmod($dst . '/' . $file, 0777);
        return true;
    }

    public static function inster_Engin_data($data) {

    }

    public static function uploadEngin($name, $dir) {
        //定义文件路径
        $attachPath = Common::getConfig('siteConfig', 'attachPath'); //网站图片附件目录
        $tmpPath = Common::getConfig('siteConfig', 'tmpPath');  //zip包临时存放目录
        $clock = Common::getConfig('siteConfig', 'liveEngin');  //zip文件存放目录


        $zip = new Util_System();
        if ($zip->smkdir($tmpPath) !== true) return Common::formatMsg(-1, '文件处理失败!');

        $file = $_FILES[$name];
        if ($file['error']) {
            return Common::formatMsg(-1, '文件上传失败:' . $file['error']);
        }
        if (!$file['tmp_name'] || $file['tmp_name'] == '') return Common::formatMsg(-1, '文件上传失败:' . $file['error']);
//取文件名
        $file_name = self::escapeStr($file['name']);
        //  $prifix = Util_String::substr($file_name, 0, strrpos($file_name, '.'));
//重命名
        $newname = strtolower(Common::randStr(4)) . date('His');

//充许上传的文件类型
        $allowType = array('zip');

//zip包临时保存目录
        $year_month = date('Ym');
        $savePath = sprintf('%s/%s/%s', $tmpPath, $year_month, $newname);

//上传
        $uploader = new Util_Upload(array('allowFileType' => $allowType, 'maxSize' => 28672));
        $ret = $uploader->upload($name, $newname, $savePath);
        if ($ret < 0) {
            return Common::formatMsg(-1, '上传失败:' . $ret);
        }
        $extension = pathinfo($file_name)['extension'];

        //上传文件为zip格式;
        if ($extension == 'zip') {
            //将包文件拷贝到指定目录
            $path_ym_str = $year_month . '/' . $newname;
            $clock_ym_str = sprintf('%s/%s', $clock, $path_ym_str);

            //$resPath = $attachPath . 'engin/' . $path_ym_str;
            self::copyFile($ret['newName'], $savePath, $clock_ym_str);
            $zipFile_up = $path_ym_str . '/' . $newname . ".zip";

            $data = array(
                'url' => $zipFile_up,
            );
            return Common::formatMsg(0, '', $data);
        }
    }

    public static function getAll($where, $limits, $pages) {
        return self::_getDao()->getAll($where, $limits, $pages);
    }

    public static function get($value) {
        return self::_getDao()->get($value);
    }

    public static function gets($field, $values) {


        return self::_getDao()->gets($field, $values);
    }

    public static function getByIdsToSort($ids) {
        $strIds = implode(",", $ids);

        return self::_getDao()->getByFileIds($strIds, FALSE, true);
    }

}
