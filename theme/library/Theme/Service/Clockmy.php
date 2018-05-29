<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author lee
 *
 */
class Theme_Service_Clockmy {

    private static $filed_status = "c_status";
    private static $onlinetime = "c_onlinetime";
    private static $id = "id";

    /**
     *
     * Enter description here ...
     */
    public static function getListcount($params = array()) {
        return self::_getDao()->count($params);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getClock($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    public static function runDao($func, $args) {
        return self::_getDao()->$func($args);
    }

    public static function insert_into_sql($filedname, $val, $showsql = false) {
        $ret = self::_getDao()->setDataDao($filedname, $val, $showsql);
        if (!$ret) return $ret;
        return self::_getDao()->getLastInsertId();
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

    public static function increase_field($id, $field) {
        $where = self::$id . "=$id";
        $data = "$field = $field + 1";
        return self::_getDao()->updataDao($data, $where);
    }

    public static function delclock($id, $showsql = false) {
        return self::_getDao()->delDao($id, $showsql);
    }

    public static function delClocksets($field, array $data) {
        return self::_getDao()->deletes($field, $data);
    }

    public static function approveClocksets($field, array $values, array $data) {
        return self::_getDao()->updates($field, $values, $data);
    }

    /**
     *
     * @param array $key
     */
    public static function getMaxCol($key) {
        return self::_getDao()->getMaxCol($key);
    }

    /**
     *
     * @param unknown_type $name
     * @param unknown_type $dir
     * @return multitype:unknown_type
     */
    public static function uploadFile($name, $dir) {
        //定义文件路径
        $attachPath = Common::getConfig('siteConfig', 'attachPath'); //网站图片附件目录
        $tmpPath = Common::getConfig('siteConfig', 'tmpPath');  //zip包临时存放目录
        $clock = Common::getConfig('siteConfig', 'clock');  //zip文件存放目录


        $zip = new Util_System();
        if ($zip->smkdir($tmpPath) !== true) return Common::formatMsg(-1, '文件处理失败!');

        $file = $_FILES[$name];
        if ($file['error']) {
            return Common::formatMsg(-1, '文件上传失败:' . $file['error']);
        }
        if (!$file['tmp_name'] || $file['tmp_name'] == '') return Common::formatMsg(-1, '文件上传失败:' . $file['error']);
//取文件名
        $file_name = self::escapeStr($file['name']);
        $prifix = Util_String::substr($file_name, 0, strrpos($file_name, '.'));
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

        //解压的目录/data/www/themefile/clock/201501/qmdn031007
        $unzipPath = sprintf('%s/%s/%s', $clock, $year_month, $newname);
        //包临时路径/tmp/file/201501/qmdn031007/qmdn031007.zip
        $tmpzipPath = sprintf('%s/%s', $savePath, $ret['newName']);
        $zip->unzipFile($tmpzipPath, $unzipPath);
        $extension = pathinfo($file_name)['extension'];

        //上传文件为zip格式;
        if ($extension == 'zip') {
            //将包文件拷贝到指定目录
            $path_ym_str = $year_month . '/' . $newname;
            $clock_ym_str = sprintf('%s/%s', $clock, $path_ym_str);
            $resPath = $attachPath . 'clock/' . $path_ym_str;

            self::copyFile($ret['newName'], $savePath, $clock_ym_str);

            $zipFile_up = $clock_ym_str . '/' . $newname . ".zip";

            //遍历上传zip文件，获取下载zip文件路径
            $zip_up = zip_open($zipFile_up);
            while ($zip_entry_up = zip_read($zip_up)) {//读取zip并将指针转向下一个
                $file_name_up = zip_entry_name($zip_entry_up); //获取zip中的文件名

                if ((substr($file_name_up, -4) == '.zip') && (substr($file_name_up, -8) != '_pre.zip')) {
                    $zipFile_dlname = $file_name_up;
                }
                if ((substr($file_name_up, -8) == '_pre.zip')) {
                    $zipFile_rvname = $file_name_up;
                }
            }
            zip_close($zip_up);
            //关闭zip文件

            $zipFile_dl = $clock_ym_str . '/' . $zipFile_dlname;
            $zipFile_rv = $clock_ym_str . '/' . $zipFile_rvname;
            $pngres_firstdir = pathinfo($zipFile_dlname)['dirname'];
            $zipFile_dl_name = basename($zipFile_dlname, ".zip");

            //遍历下载zip文件，获取icon_small.png,icon_big.png和text.png文件路径
            $zip_dl1 = zip_open($zipFile_dl);
            while ($zip_entry_dl1 = zip_read($zip_dl1)) {//读取zip并将指针转向下一个
                $file_name_dl1 = zip_entry_name($zip_entry_dl1); //获取zip中的文件名

                if (strpos($file_name_dl1, 'config.gntxt') !== false) {
                    $str = zip_entry_read($zip_entry_dl1);
                    zip_entry_close($zip_entry_dl1);
                    $str_cont = self::pares_cont($str);
                }
                if (strpos($file_name_dl1, 'icon_small.png') !== false) {
                    $img_arr[] = $file_name_dl1;
                    $img_png['icon_small'] = $file_name_dl1;
                }
                if (strpos($file_name_dl1, 'icon_big.png') !== false) {
                    $img_arr[] = $file_name_dl1;
                    $img_png['icon_big'] = $file_name_dl1;
                }
                if (strpos($file_name_dl1, 'text.png') !== false) {
                    $img_arr[] = $file_name_dl1;
                    $img_png['text'] = $file_name_dl1;
                }
            }
            zip_close($zip_dl1);
            //关闭zip文件
            //将下载zip文件中的icon_small.png,icon_big.png和text.png解压缩到目标地址
            $des_pngresPath = $resPath . '/' . $pngres_firstdir;
            $zip_dl = new ZipArchive;
            $res_zipdl = $zip_dl->open($zipFile_dl);
            if ($res_zipdl === TRUE) {
                //解压缩到文件夹
                $zip_dl->extractTo($des_pngresPath, $img_arr);
                $zip_dl->close();
            }

            $data = array(
                'c_filename' => $zipFile_dl_name,
                'c_dlurl' => '/' . $path_ym_str . '/' . $zipFile_dlname,
                'c_rvurl' => '/' . $path_ym_str . '/' . $zipFile_rvname,
                'c_size' => filesize($zipFile_dl),
                'c_imgthumb' => '/' . $path_ym_str . '/' . $pngres_firstdir . '/' . $img_png['icon_small'],
                'c_imgthumbmore' => '/' . $path_ym_str . '/' . $pngres_firstdir . '/' . $img_png['icon_big'],
                'c_imgdetail' => '/' . $path_ym_str . '/' . $pngres_firstdir . '/' . $img_png['text'],
                'c_content' => $str_cont['FileDirName'],
                'c_name' => $str_cont['ChineseName'],
            );


            return Common::formatMsg(0, '', $data);
        }
    }

    //解析时钟配置文件;
    private function pares_cont($str) {
        if (!$str) return $str;
        $arr = explode("\n", $str);
        if (is_array($arr)) {
            foreach ($arr as $v) {
                $key = str_replace('\r', '', explode("=", $v)[0]);
                $val = explode("=", $v)[1];
                $res[$key] = str_replace("\r", '', $val);
            }

            return $res;
        }
    }

    /**
     * 遍历文件夹下面的所有文件
     *
     * @param  string  $dir  文件夹路径
     * @return string  返回所有所有文件的路径的数组
     */
    private function traversedir($dir = '.') {
        $files = array();
        if (is_file($dir)) {
            $files[] = $dir;
        } else {
            $dir_list = scandir($dir);
            foreach ($dir_list as $file) {
                if ($file != ".." && $file != ".") {
                    if (is_dir($dir . "/" . $file)) {
                        $files[$file] = traversedir($dir . "/" . $file);
                    } else {
                        $files[] = $dir . "/" . $file;
                    }
                }
            }
        }
        return $files;
    }

    /**
     * 字符转换
     *
     * @param  string  $string  转换的字符串
     * @return string  返回转换后的字符串
     */
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

    /**
     *
     * @return Theme_Dao_File
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_Clockmy");
    }

}
