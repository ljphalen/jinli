<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class Theme_Service_File extends Common_Service_Base {

    protected static $name = 'Theme_Dao_File';

    /**
     *
     * Enter description here ...
     */
    public static function getAllFile($ids) {
        return self::_getDao()->getAllFile($ids);
    }

    /**
     * where条件查询;
     * @param String $where sql条件
     * @param String $fileds 要返回的sql字段，为空 反回所有;
     */
    public static function getByWhere($where, $fileds = "*") {

        return self::_getDao()->getByWheres($fileds, $where);
    }

    /**
     * 取主题的id号；
     * @param type $num
     * @return type
     */
    public static function getThemeIds($num = 500, $sort = "sort", $type = 2) {

        return self::_getDao()->getThemeIdsDao($num, $sort, $type);
    }

    /**
     * 取主题的id号；
     * @param type $num
     * @return type
     */
    public static function getThemeIds_order($num = 500, $sort = "sort", $type = 2) {

        return self::_getDao()->getThemeIdsDao_order($num, $sort, $type);
    }

    public static function getThemePrice() {
        return self::_getDao()->getByWheres("id,price", "price > 0");
    }

    public static function Themelikes($themeid) {
        $redis = Common::getQueue();

        $res = $redis->hget("theme:likes", $themeid);
        if ($res) {
            $res_array = str_replace("[", "", $res);
            $res_array = str_replace("]", "", $res_array);
            $res_array = explode(",", $res_array);
            $res_ids = array_splice($res_array, 0, 1000);
            return $res_ids;
        }
        $themeids_one = Theme_Service_IdxFileType::getByFileId($themeid);
        $themeids_two = Theme_Service_IdxFilesedType::getsedTypebyFileid($themeid);
        $r_onetype = Theme_Service_IdxFileType::getByTypeId($themeids_one[0]['type_id']);
        foreach ($r_onetype as $v) {
            $res_onetype[$v['file_id']] = $v;
        }
        if ($themeids_two) {
            foreach ($themeids_two as $v) {
                $sed_ids .= $v['sedtype_id'] . ",";
            }
            $sed_ids = substr($sed_ids, 0, -1);
            $r_sedtype = Theme_Service_IdxFilesedType::getSedTypeByTypeid($sed_ids);
            foreach ($r_sedtype as $g) {
                // $tmp[$g['file_id']][] = $g;
                if ($res_onetype[$g['file_id']] && $g['file_id'] !== $themeid) {
                    $tmp[$g['file_id']][] = $res_onetype[$g['file_id']];
                }
            }
        }
        if (!$tmp) $tmp = $res_onetype;

        foreach ($tmp as $k => $a) {
            $t[$k] = count($a);
        }
        arsort($t);
        foreach ($t as $f => &$e) {
            if ($f == $themeid) {
                unset($t[$f]);
                break;
            }
        }

        $res_ids = array_splice(array_keys($t), 0, 1000);

        $redis->hset("theme:likes", $themeid, $res_ids);
        $redis->expireAt("theme:likes", time() + 3600);
        return $res_ids;
    }

    public static function getThemeIdsBytime($num = 1200) {
        $redis = Common::getQueue();
        $ids = self::chageThemeIds($num);
        $t_time = time();
        $date = date("Y-m-d:H", $t_time);
        $hour = date("H", $t_time);

//两小时生成一次随机排序;
        if ($hour % 2 === 0) {
            $res = $redis->hget("theme:hotids", $date);
            if (!$res) {
                $redis->hset("theme:hotids", $date, $ids);
                $res = $redis->hget("theme:hotids", $date);
            }
        } else {
//取上一小时的了排序;
            $key = date("Y-m-d:H", strtotime("-1 hour", $t_time));
            $res = $redis->hget("theme:hotids", $key);
//如果两时内都没有请求，没生成随机排序，生成前一小时的排序;
            if (!$res) {
                $redis->hset("theme:hotids", $key, $ids);
                $res = $redis->hget("theme:hotids", $key);
            }
        }
        return $res;
    }

    private static function chageThemeIds($num) {
        for ($i = 0; $i < $num; $i++) {
            $ids[] = $i;
        }
        shuffle($ids);

        return $ids;
    }

    /**
     *
     * Enter description here ...
     */
    public static function getIndexFile($ids, $order = '') {


        return self::_getDao()->getIndexFile($ids, $order);
    }

    public static function setUpFiles($fileInfor) {
        return self::_getDao()->setUpFile($fileInfor);
    }

    public static function updateWhere($data, $where) {
        return self::_getDao()->updateWhereDao($data, $where);
    }

    /**
     *
     * @param type $ids
     * @param type $type
     * @param type $orederby string
     * @return int
     *
     */
    public static function get_filesids_type($ids, $type = 2, $orederby = '') {
        if (!$ids) return 0;
        if ($type == 3) {
            $res = self::_getDao()->getByFilsids_type_v3($ids, $orederby);
        } else {
            $res = self::_getDao()->getByFilsids_type($ids, $orederby);
        }
        return $res;
    }

    public static function get_allFiles_id($params = array(), $sort = '', $filed_name = '') {
        if (!$params) return FALSE;
        $params = array(
            'lock_style' => $params['lock_style'],
            'resulution' => $params['resulution'],
            'font_size' => $params['font_size'],
            'status' => $params['status'],
            "package_type" => $params['package_type'],
        );
        if ($params['package_type'] == 2) {
            unset($params["resulution"]);
            $res = self::_getDao()->get_list_filesid($params, $sort, $filed_name);
        }
        if ($params['package_type'] == 1) {
            $res = self::_getDao()->get_list_filesid($params, $sort, $filed_name);
        }//取出id号


        if ($res) {
            foreach ($res as $values) {
                $ids[] = $values['id'];
            }
        }
        return $ids;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $order_by = 'id', $sort = "DESC") {


        if (isset($params['file_type'])) $tmp['file_type'] = $params['file_type'];
        if (isset($params['file_ids'])) $tmp['file_ids'] = $params['file_ids'];
        $params = self::_cookData($params);
        if (isset($tmp['file_type'])) $params['file_type'] = $tmp['file_type'];
        if (isset($tmp['file_ids'])) $params['file_ids'] = $tmp['file_ids'];
        if ($page < 1) $page = 1;
        $start = ($page - 1 ) * $limit;


        $ret = self::_getDao()->getList($start, $limit, $params, $order_by, $sort);
        $total = self::_getDao()->getCount($params);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getCanuseFiles($page = 1, $limit = 10, $in_ids = array(), $not_in_ids = array(), $params = array(), $order_by = array(), $first_page_limit = -1) {
        $params = self::_cookData($params);

        if ($page < 1) $page = 1;
        $start = ($page - 1 ) * $limit;
        if ($in_ids) {
            $order_by = array('field' => '(id,' . implode(',', $in_ids) . ')');
        }
        if ($first_page_limit >= 0) {
            if ($page == 1) {
//$first_page_limit为首页的容量
                $limit = $first_page_limit;
            } else {
//从第二页开始，本页起始序号要减去单页容量与首页容量的差值
                $start = $start - ($limit - $first_page_limit);
            }
        }
        if ($params['package_type'] == 3) {
            $params['package_type'] = array(array(">=", 2), array("<=", 3));
        }
        $ret = self::_getDao()->getCanuseFiles($start, $limit, $in_ids, $not_in_ids, $params, $order_by, $group);

        $total = self::_getDao()->getCanuseCount($in_ids, $not_in_ids, $params, $group);
        return array(count($ret), $ret, $total);
    }

    public static function getCanuseFilesCount($where = array()) {
        $total = self::_getDao()->getCanuseCount("", "", $where);
        return $total;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getFile($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    public static function getFiles($ids, $fields) {
        if (!$ids) {
            return FALSE;
        }
        return self::_getDao()->gets($fields, $ids);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getPre($id) {
        if (!intval($id)) return false;
        return self::_getDao()->getPre(intval($id));
    }

//点攒;
    public static function addLikes($fileds, $sid) {
        if (!intval($sid)) return 0;
        return self::_getDao()->addLikes($fileds, $sid);
    }

//获取攒的数量;
    public static function get_count_likes($sid) {

        if (!intval($sid)) return 0;
        $where = "id = $sid";


        return self::_getDao()->getByWheres("likes", $where);
    }

//获取下载的数量;
    public static function get_count_downs($sid) {
        if (!intval($sid)) return 0;
        $where = "id = $sid";
        return self::_getDao()->getByWheres("down", $where);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getNext($id) {
        if (!intval($id)) return false;
        return self::_getDao()->getNext(intval($id));
    }

    /**
     * getResolution
     */
    public static function getResolution() {
        return self::_getDao()->getResolution();
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateFile($data, $id) {
        if (!is_array($data)) return false;
//$data['update_time'] = Common::getTime();
        $data = self::_cookData($data);

        return self::_getDao()->update($data, intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function deleteFile($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function addFile($data) {
        if (!is_array($data)) return false;
        $data['create_time'] = Common::getTime();
        $data['status'] = 1;
        $data = self::_cookData($data);
        $ret = self::_getDao()->insert($data);

        if (!$ret) return $ret;
        return self::_getDao()->getLastInsertId();
    }

    /**
     *
     * @param array $file_ids
     * @return multitype:
     */
    public static function getByIds($ids) {
        if (!is_array($ids)) return false;
        return self::_getDao()->getByFileIds($ids);
    }

    public static function getByIds_order($ids) {
        return self::_getDao()->getByFileIds_orderyDao($ids);
    }

    /**
     * get by
     */
    public static function getBy($params = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params);
    }

    public static function updateVersion() {
        Theme_Service_Config::setValue('file_version', Common::getTime());
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['user_id'])) $tmp['user_id'] = intval($data['user_id']);
        if (isset($data['title'])) $tmp['title'] = Util_String::unicode2utf8($data['title']);
        if (isset($data['file'])) $tmp['file'] = $data['file'];
        if (isset($data['descript'])) $tmp['descript'] = $data['descript'];
        if (isset($data['designer'])) $tmp['designer'] = Util_String::unicode2utf8($data['designer']);
        if (isset($data['resulution'])) $tmp['resulution'] = $data['resulution'];
        if (isset($data['min_version'])) $tmp['min_version'] = $data['min_version'];
        if (isset($data['max_version'])) $tmp['max_version'] = $data['max_version'];
        if (isset($data['font_size'])) $tmp['font_size'] = $data['font_size'];
        if (isset($data['android_version'])) $tmp['android_version'] = $data['android_version'];
        if (isset($data['rom_version'])) $tmp['rom_version'] = $data['rom_version'];
        if (isset($data['channel'])) $tmp['channel'] = $data['channel'];
        if (isset($data['lock_style'])) $tmp['lock_style'] = $data['lock_style'];
        if (isset($data['file_size'])) $tmp['file_size'] = $data['file_size'];
        if (isset($data['hit'])) $tmp['hit'] = $data['hit'];
        if (isset($data['down'])) $tmp['down'] = intval($data['down']);
        if (isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
        if (isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
        if (isset($data['status'])) $tmp['status'] = intval($data['status']);
        if (isset($data['since'])) $tmp['since'] = intval($data['since']);
        if (isset($data['packge_time'])) $tmp['packge_time'] = intval($data['packge_time']);
        if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
        if (isset($data['open_time'])) $tmp['open_time'] = intval($data['open_time']);
        if (isset($data['status'])) $tmp['status'] = intval($data['status']);
        if (isset($data['package_type'])) $tmp['package_type'] = intval($data['package_type']);
        if (isset($data['package'])) $tmp['package'] = $data['package'];
        if (isset($data['Ename'])) $tmp['Ename'] = $data['Ename'];
        if (isset($data['reason'])) $tmp['reason'] = $data['reason'];
        if (isset($data['is_faceimg'])) $tmp['is_faceimg'] = $data['is_faceimg'];
        if (isset($data['hot_sort'])) $tmp['hot_sort'] = $data['hot_sort'];
        if (isset($data['style'])) $tmp['style'] = $data['style'];
        if (isset($data['price'])) $tmp['price'] = $data['price'];
        return $tmp;
    }

    /**
     *
     * @return Theme_Dao_File
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_File");
    }

    /**
     * 上传文件
     *
     * @param array $user
     * @param array $file_data
     * @param array $type
     */
    public static function add($user = array(), $file_data = array(), $imgs) {

        if (!is_array($user) || !is_array($file_data)) return false;
        try {
//开始事务
            $transactionOn = parent::beginTransaction();
            if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);

//添加文件
            $file_id = self::addFile($file_data);


            if (!$file_id) throw new Exception("add file fail.", -209);


//分类
            /*
              $type_data = array();

              foreach ($type as $key => $value) {
              $type_data[$key]['id'] = '';
              $type_data[$key]['file_id'] = $file_id;
              $type_data[$key]['type_id'] = $value;
              }
              $type_ret = Theme_Service_IdxFileType::batchAdd($type_data);
              if (!$type_ret) throw new Exception("add file_types fail.", -220); */


//图片
            $img_data = array();
//        $arr_img = explode(',', html_entity_decode($imgs));

            foreach ($imgs as $key => $value) {
                $img_data[$key]['id'] = '';
                $img_data[$key]['file_id'] = $file_id;
                $img_data[$key]['img'] = $value;
            }

            $img_ret = Theme_Service_FileImg::batchAdd($img_data);

            if (!$img_ret) throw new Exception("add file_img fail.", -221);


//添加到系列索引
            list(, $series) = Theme_Service_Series::getAllSeries();

            $series_data = array();
            foreach ($series as $key => $value) {
                $series_data[$key]['id'] = '';
                $series_data[$key]['file_id'] = $file_id;
                $series_data[$key]['series_id'] = $value['id'];
                $series_data[$key]['sort'] = 0;
            }


            $series_ret = Theme_Service_IdxFileSeries::batchAdd($series_data);

            if (!$series_ret) throw new Exception("add file_series fail.", -227);


//记录日志
            $log_data = array(
                'uid' => $user['uid'],
                'username' => $user['username'],
                'message' => $user['username'] . '上传了文件：<a href=/Admin/File/detail/?id=' . $file_id . '>' . $file_data['title'] . '</a>',
                'file_id' => $file_id
            );
            $log_ret = Admin_Service_AdminLog::addAdminLog($log_data);


            if (!$log_ret) throw new Exception("add log fail.", -210);

//发消息
            /*    $message_data = array();
              $group_id = 2;
              //消息内容
              list(, $users) = Admin_Service_User::getList(1, 20, array('groupid' => $group_id));
              if ($users) {
              foreach ($users as $key => $value) {
              $message_data[$key]['id'] = '';
              $message_data[$key]['uid'] = $value['uid'];
              $message_data[$key]['content'] = $user['username'] . '上传了文件：<a href=/Admin/File/detail/?id=' . $file_id . '>' . $file_data['title'] . '</a>';
              $message_data[$key]['status'] = 0;
              $message_data[$key]['create_time'] = Common::getTime();
              }
              }


              if ($message_data) {
              $message_ret = Theme_Service_Message::batchAdd($message_data);
              if (!$message_ret) throw new Exception("add message fail.", -211);
              }

              echo "qqqqqacx";
              exit; */
//事务提交
            if ($transactionOn) {
                $return = parent::commit();
                if ($return) return $file_id;
            }
        } catch (Exception $e) {
            parent::rollBack();
//出错监控
            error_log(json_encode($file_data) . ";
" . $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
            return false;
        }
    }

    /**
     * 修改文件
     *
     * @param int $file_id
     * @param array $file_data
     * @param array $type_data
     * @param array $log_data
     * @param array $message_data
     */
    public static function transactionUpdate($file_id, $file_data = array(), $type_data = array(), $img_data = array(), $log_data = array(), $message_data = array()) {
        if (!intval($file_id)) return false;
        if (!is_array($file_data) || !is_array($type_data) || !is_array($img_data) || !is_array($log_data) || !is_array($message_data)) return false;
        try {
//开始事务
            $transactionOn = parent::beginTransaction();
            if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);

//修改文件
            $ret = self::updateFile($file_data, $file_id);
            if (!$ret) throw new Exception("update file fail.", -212);

//分类
            $del_ret = Theme_Service_IdxFileType::deleteByFileId($file_id);
//if(!$del_ret) throw new Exception("del file_types fail.", -213);
            $type_ret = Theme_Service_IdxFileType::batchAdd($type_data);
            if (!$type_ret) throw new Exception("add file_types fail.", -220);

//图片
            $delimg_ret = Theme_Service_FileImg::deleteByFileId($file_id);
//if(!$delimg_ret) throw new Exception("del file_size fail.", -223);
            $size_ret = Theme_Service_FileImg::batchAdd($img_data);
            if (!$size_ret) throw new Exception("add file_size fail.", -229);

//记日志
            $log_ret = Admin_Service_AdminLog::addAdminLog($log_data);
            if (!$log_ret) throw new Exception("add log fail.", -210);

//发消息
            if ($message_data) {
                $message_ret = Theme_Service_Message::batchAdd($message_data);
                if (!$message_ret) throw new Exception("add message fail.", -211);
            }

//事务提交
            if ($transactionOn) {
                $return = parent::commit();
                return $return;
            }
        } catch (Exception $e) {
            parent::rollBack();
//出错监控
            error_log(json_encode($file_data) . ";
" . $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
            return false;
        }
    }

    /**
     * 修改文件
     *
     * @param int $file_id
     * @param array $file_data
     * @param array $type_data
     * @param array $log_data
     * @param array $message_data
     */
    public static function edit($file_id, $file_data = array(), $rom_data = array()) {
        if (!intval($file_id)) return false;
        if (!is_array($file_data)) return false;
        try {
//开始事务
            $transactionOn = parent::beginTransaction();
            if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);

//修改文件
            $ret = self::updateFile($file_data, $file_id);
            if (!$ret) throw new Exception("update file fail.", -212);

//rom
            $idx_file_rom = Theme_Service_IdxFileRom::getByFileId($file_id);
            if ($idx_file_rom) {
                $del_rom_ret = Theme_Service_IdxFileRom::deleteByFileId($file_id);
                if (!$del_rom_ret) throw new Exception("del file_rom fail.", -218);
            }

            if ($rom_data && is_array($rom_data)) {
                $rom_ret = Theme_Service_IdxFileRom::batchAdd($rom_data);
                if (!$rom_ret) throw new Exception("add file_rom fail.", -220);
            }

//事务提交
            if ($transactionOn) {
                $return = parent::commit();
                return $return;
            }
        } catch (Exception $e) {
            parent::rollBack();
//出错监控
            error_log(json_encode($file_data) . ";
" . $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
            return false;
        }
    }

    /**
     * 删除文件
     *
     * @param int $id
     * @param array $log_data
     */
    public static function delete($id, $log_data = array()) {
        if (!intval($id)) return false;
        if (!is_array($log_data)) return false;
        try {
//开始事务
            $transactionOn = parent::beginTransaction();
            if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);

//删除文件
            $ret = self::deleteFile($id);
            if (!$ret) throw new Exception("delete file fail.", -212);

//删除分类
            $idx_file_type = Theme_Service_IdxFileType::getByFileId($id);
            if ($idx_file_type) {
                $del_ret = Theme_Service_IdxFileType::deleteByFileId($id);
                if (!$del_ret) throw new Exception("del file_types fail.", -213);
            }


//删除rom索引
            $file_roms = Theme_Service_IdxFileRom::getByFileId($id);
            if ($file_roms) {
                $del_rom_ret = Theme_Service_IdxFileRom::deleteByFileId($id);
                if (!$del_rom_ret) throw new Exception("del file_rom fail.", -218);
            }

//删除系列索引
            $file_series = Theme_Service_IdxFileSeries::getByFileId($id);
            if ($file_series) {
                $del_series_ret = Theme_Service_IdxFileSeries::deleteByFileId($id);
                if (!$del_series_ret) throw new Exception("del file_series fail.", -250);
            }

//记日志
            $log_ret = Admin_Service_AdminLog::addAdminLog($log_data);
            if (!$log_ret) throw new Exception("add log fail.", -210);


//删除专题索引
            $file_subject = Theme_Service_SubjectFile::getByFileId($id);
            if ($file_subject) {
                $subject_ret = Theme_Service_SubjectFile::deleteByFileId($id);
                if (!$subject_ret) throw new Exception("del subject fail.", -229);
            }

//删除图片
            $file_img = Theme_Service_FileImg::getByFileId($id);
            if ($file_img) {
                $img_ret = Theme_Service_FileImg::deleteByFileId($id);
                if (!$img_ret) throw new Exception("del img fail.", -239);
            }

//事务提交
            if ($transactionOn) {
                $return = parent::commit();
                return $return;
            }
        } catch (Exception $e) {
            parent::rollBack();
//出错监控
            error_log(json_encode($id) . ";
" . $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
            return false;
        }
    }

    /**
     * 修改文件状态
     *
     * @param int $id
     * @param array $file_data
     * @param array $log_data
     * @param array $message_data
     */
    public static function editStatus($id, $status, $file_data = array(), $log_data = array(), $message_data = array()) {
        if (!intval($id)) return false;
        if (!is_array($file_data) || !is_array($log_data)) return false;
        try {
//开始事务
            $transactionOn = parent::beginTransaction();
            if (!$transactionOn) throw new Exception("Begin transaction fail.", -201);

//更新文件状态
            $ret = self::updateFile($file_data, $id);
            if (!$ret) throw new Exception("update file status fail.", -206);

//记录日志
            $log_ret = Admin_Service_AdminLog::addAdminLog($log_data);
            if (!$log_ret) throw new Exception("add log fail.", -207);

//发消息
            if ($message_data) {
                $message_ret = Theme_Service_Message::batchAdd($message_data);
                if (!$message_ret) throw new Exception("add message fail.", -208);
            }

//如果文件状态改为下架,要删除删除专题索引
            if ($status == 5) {
//删除专题索引
                $file_subject = Theme_Service_SubjectFile::getByFileId($id);
                if ($file_subject) {
                    $subject_ret = Theme_Service_SubjectFile::deleteByFileId($id);
                    if (!$subject_ret) throw new Exception("del subject fail.", -229);
                }
            }
//事务提交
            if ($transactionOn) {
                $return = parent::commit();
                return $return;
            }
        } catch (Exception $e) {
            parent::rollBack();
//出错监控
            error_log(json_encode($file_data) . ";
" . $e->getCode() . '; ' . $e->getMessage() . "\n", 3, BASE_PATH . "data/file_error.log");
            return false;
        }
    }

    private static function mk_qu_dot($data) {
        foreach ($data as $v) {
            if (!($v == "." || $v == "..")) {
                $name[] = $v;
            }
        }

        return $name;
    }

    /**
     *
     * @param unknown_type $name
     * @param unknown_type $dir
     * @return multitype:unknown_type
     */
    public static function uploadFile($name, $dir) {
//定义文件路径
        $attachPath = Common::getConfig('siteConfig', 'attachPath'); //网站附件目录
        $tmpPath = Common::getConfig('siteConfig', 'tmpPath');  //zip包临时存放目录
        $themePath = Common::getConfig('siteConfig', 'themePath');  //ux文件存放目录
        $liveWallpaper = Common::getConfig('siteConfig', 'liveWallpaper');  //ux文件存放目录


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
        $allowType = array('gnz', 'zip');

//zip包临时保存目录
        $savePath = sprintf('%s/%s/%s', $tmpPath, date('Ym'), $newname);

//上传
        $uploader = new Util_Upload(array('allowFileType' => $allowType, 'maxSize' => 28672));
        $ret = $uploader->upload($name, $newname, $savePath);
        if ($ret < 0) {
            return Common::formatMsg(-1, '上传失败:' . $ret);
        }
        $url = sprintf('/%s/%s/%s/', $dir, date('Ym'), $newname);
//解压的目录
        $unzipPath = $savePath;
//包名
        $zipName = sprintf('%s/%s', $savePath, $ret['newName']);
        $res = $zip->unzipFile($zipName, $unzipPath);
        $extension = pathinfo($file_name)['extension'];

//动态壁纸;
        if ($extension == 'zip') {
//将包文件拷贝到指定目录
            $path = date('Ym') . '/' . $newname;
            $liveWallpaper_path = sprintf('%s/%s', $liveWallpaper, $path);
            self::copyFile($ret['newName'], $savePath, $liveWallpaper_path);
            $attachPath = Common::getConfig('siteConfig', 'attachPath');
            $zipFile = $liveWallpaper_path . '/' . $newname . ".zip";
            $zipPath = $liveWallpaper_path . '/' . $newname;
            $zip = zip_open($zipFile);

            while ($zip_entry = zip_read($zip)) {//读取zip并将指针转向下一个
                $file_name = zip_entry_name($zip_entry); //获取zip中的文件名
                if (preg_match_all("/.*preview.*\.jpg/", $file_name, $arr)) {
// $imgpath[] = '/' . $path . '/' . $newname . '/' . $file_name;
                    $imgarr[] = $file_name;
                }
            }
            zip_close($zip); //关闭zip文件
            $zip = new ZipArchive;
            $res = $zip->open($zipFile);
            if ($res === TRUE) {
//解压缩到文件夹
                $zip->extractTo($zipPath, $imgarr);
                $zip->close();
            } else {
                echo 'failed, code:' . $res;
            }

//改图片扩展命;
            foreach ($imgarr as $v) {
                $tem = pathinfo($zipPath . DIRECTORY_SEPARATOR . $v);
                $name = $tem['filename'];
                $resPath = $attachPath . 'livepaper/' . $path;
                $system = new Util_System();
                if ($system->smkdir($resPath) !== true) return Common::formatMsg(-1, '不能创建目录!');
                rename($tem['dirname'] . "/" . $tem['basename'], $resPath . "/" . $name . ".jpg");
                $img_jpg[] = $path . "/" . $name . ".jpg";
            }
            /* print_r($imgarr);
              echo "<br/>";
              print_r($zipPath);
              echo "<br/>";
              print_r($path);

              exit; */
            $data = array(
                "path" => '/' . $path . '/' . $newname . ".zip",
                "size" => $file["size"],
                'imgs' => $img_jpg,
            );
            return Common::formatMsg(0, '', $data);
// return $savePath;
        }

//主题包资源;
        if ($res == true) {
//检测是大文件
//配件文件properties
            $language_properties = $savePath . '/language.properties';
            if (!self::checkExists($language_properties)) return Common::formatMsg(-1, '未找到配置文件!');
            $content = file_get_contents($language_properties);
            $str = str_replace("\n", "&", str_replace("\r", "", $content));
            $properties = array();
            parse_str($str, $properties);
//$properties = parse_ini_file($language_properties);
            if (!$properties) return Common::formatMsg(-1, '配置文件错误!');

//配件文件since
            $since_properties = $savePath . '/since.properties';
            if (!self::checkExists($since_properties)) return Common::formatMsg(-1, '未找到配置文件!');
            $content = file_get_contents($since_properties);
            $arr_content = explode("\n", $content);
            $packge_time = strtotime(str_replace('#', '', $arr_content[0]));

            $str = str_replace("\n", "&", str_replace("\r", "", $content));

            $since = array();
            parse_str($str, $since);
//$properties = parse_ini_file($language_properties);

            if (!$since) return Common::formatMsg(-1, '配置文件错误!');

//将包文件拷贝到指定目录
            $themefilePath = sprintf('%s/%s/%s', $themePath, date('Ym'), $newname);

            self::copyFile($ret['newName'], $savePath, $themefilePath);

//获取包文件大小
            $file_size = filesize($themefilePath . '/' . $ret['newName']);

//拷贝图片到附件目录
            $filePath = $attachPath . $dir . '/' . date('Ym') . '/' . $newname;
            $ret_img = self::getImg($savePath, $properties['screen_density'], $attachPath, $url, $properties['screen_density']);
// $ret_img = self::getImg($filePath, $properties['screen_density'], $attachPath, $url, $properties['screen_density']);
        } else {
            return Common::formatMsg(-1, '文件处理失败:' . $ret);
        }

        $themeMgr_ver = explode('|', $properties['themeMgr_ver']);
        $ui_ver = explode('|', $properties['ui_ver']);

        $file_data = array(
            'file' => '/' . date('Ym') . '/' . $newname . '/' . $ret['newName'],
            'title' => Util_String::unicode2utf8(Util_String::unicode2utf8($properties['zh_CN'])),
            'e_name' => Util_String::unicode2utf8(Util_String::unicode2utf8($properties['en_US'])),
            'verson' => Util_String::unicode2utf8(Util_String::unicode2utf8($properties['gnz_Ver'])),
            'designer' => Util_String::unicode2utf8(Util_String::unicode2utf8($properties['author'])),
            'resulution' => $properties['screen_density'],
            'min_version' => $themeMgr_ver[0],
            'max_version' => $themeMgr_ver[count($themeMgr_ver) - 1],
            'font_size' => $properties['font_style'],
            'android_version' => $ui_ver[0],
            'rom_version' => $ui_ver[count($ui_ver) - 1],
            'channel' => $properties['local'],
            'lock_style' => $properties['lock_style'],
            'style' => Util_String::unicode2utf8(Util_String::unicode2utf8($properties['style'])),
            'file_size' => $file_size,
            'since' => $since['since'],
            'packge_time' => $packge_time
        );

        $img_data = $ret_img;
        return Common::formatMsg(0, '', array('file_data' => $file_data, 'img_data' => $img_data));
    }

    private function mk_zip_files($path) {
        $revie_tmp = $path . "/wallpaper/preview/";
        $dir = @ dir($revie_tmp);

        while (($file = $dir->read() ) !== false) {
            if (( $file != "." ) AND ( $file != "..")) {
                $res_dir = $file;
            }
        }
        $dir->close();


        print_r($res_dir);
        exit;
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
     * 检测文件是否存在
     */
    private function checkExists($file) {
        if (!$file) return false;
        return file_exists($file);
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
     * get img  取主题包图片
     *
     */
    private function getImg($tmpPath, $resulution, $attachPath, $url, $resulution) {

        $tmp_path = $tmpPath . '/other/preview/';
        $tmp_bg = $tmpPath . '/GNLauncherPlus/res/drawable-' . $resulution . '/gn_launcher_main_menu_bg.9.png';

        $arr_img_jpg = array(
            '0' => 'pre_face.jpg',
            '1' => 'pre_directions.jpg',
            '2' => 'pre_lockscreen.jpg',
            '3' => 'pre_one.jpg',
            '4' => 'pre_icon1.jpg',
            '5' => 'pre_icon2.jpg',
            '6' => 'pre_dial.jpg',
            '7' => 'pre_mms.jpg',
            '8' => 'pre_widget_weather.jpg',
            '9' => 'pre_widget_clock.jpg',
            '10' => 'pre_widget_music.jpg',
            '11' => 'pre_face_small.jpg',
            '12' => 'pre_icon3.jpg',
            '13' => 'pre_icon4.jpg',
            '14' => 'pre_icon5.jpg',
            '15' => 'pre_icon6.jpg',
            '16' => 'pre_icon7.jpg',
            '17' => 'pre_icon8.jpg',
            '18' => 'pre_icon9.jpg',
            '19' => 'pre_icon10.jpg',
        );


        $arr_img = array(
            '0' => 'pre_face.png',
            '1' => 'pre_directions.png',
            '2' => 'pre_lockscreen.png',
            '3' => 'pre_one.png',
            '4' => 'pre_icon1.png',
            '5' => 'pre_icon2.png',
            '6' => 'pre_dial.png',
            '7' => 'pre_mms.png',
            '8' => 'pre_widget_weather.png',
            '9' => 'pre_widget_clock.png',
            '10' => 'pre_widget_music.png',
            '11' => 'pre_face_small.png',
            '12' => 'pre_icon3.png',
            '13' => 'pre_icon4.png',
            '14' => 'pre_icon5.png',
            '15' => 'pre_icon6.png',
            '16' => 'pre_icon7.png',
            '17' => 'pre_icon8.png',
            '18' => 'pre_icon9.png',
            '19' => 'pre_icon10.png',
        );
        $imgs = array();

//如果没有这张图片，就说明包里面的预览图已经合并好了，
//不需要程序再合并了 by 刘洪标 2013-04-18
        $pre_one = file_exists($tmp_path . 'pre_one.png');

        if ($pre_one) {
            list($pre_one_width, $pre_one_height) = getimagesize($tmp_path . '/' . $arr_img[3]);
        }
        foreach ($arr_img as $key => $value) {
            if (file_exists($tmp_path . '/' . $value)) {
                if ($pre_one) {

//统一图片大小
                    $name = explode('.', $value);
                    list($width, $height) = getimagesize($tmp_path . '/' . $value);
                    if ($width > $pre_one_width) {
                        $nheight = ceil(($height * $pre_one_width ) / $width);
                        self::CreateThumb($tmp_path . $value, $tmp_path, $name[0], $pre_one_width, $nheight);
                    }

                    if ($value == 'pre_lockscreen.png') {
                        self::addWater($tmp_path . $arr_img[3], $tmp_path . $arr_img[2], $tmp_path, $name[0]);
                    }
                    if ($value == 'pre_icon1.png') {
                        self::addWater($tmp_path . $arr_img[3], $tmp_path . $arr_img[4], $tmp_path, $name[0]);
                    }
                    if ($value == 'pre_icon2.png') {
                        if (!file_exists($tmp_bg)) $tmp_bg = $tmp_path . $arr_img[3];
                        self::addWater($tmp_path . $arr_img[3], $tmp_bg, $tmp_path, 'tmp');
                        self::addWater($tmp_path . 'tmp.png', $tmp_path . $arr_img[5], $tmp_path, $name[0]);
                    }
                    if ($value == 'pre_widget_weather.png') {
                        self::addWater($tmp_path . $arr_img[3], $tmp_path . $arr_img[8], $tmp_path, $name[0]);
                    }
                    if ($value == 'pre_widget_clock.png') {
                        self::addWater($tmp_path . $arr_img[3], $tmp_path . $arr_img[9], $tmp_path, $name[0]);
                    }
                    if ($value == 'pre_widget_music.png') {
                        self::addWater($tmp_path . $arr_img[3], $tmp_path . $arr_img[10], $tmp_path, $name[0]);
                    }
                }

                $ret[$key] = self::checkImg($tmp_path, $value, $attachPath, $url, $resulution);


                if ($ret[$key]) array_push($imgs, $ret[$key]);
            }
        }

//详细页图片默认加载改为jpg/webp格式;
        foreach ($imgs as $value) {
//$tem_imgs[] = str_replace(".png", ".jpg", $value);
            $tem_imgs[] = str_replace(".png", ".webp", $value);
        }


        return $tem_imgs;
    }

    /**
     *
     * @param unknown_type $tmp_path
     * @param unknown_type $img
     * @param unknown_type $filePath
     * @return string|NULL
     */
    private function checkImg($tmp_path, $img, $attachPath, $url, $resulution) {

        if (self::checkExists($tmp_path . '/' . $img)) {

            list($width, $height) = getimagesize($tmp_path . '/' . $img);

//根据分辨率生成详情页缩略图和首页缩略图
            if ($resulution == 'xxhdpi') {
                $s_width = 260;
                $nwidth = 300;
            } else {
                $s_width = 140;
                $nwidth = 260;
            }

//生成首页和列表页缩略图
            if ($img == 'pre_face.png') {

                $s_height = ceil(($height * $s_width ) / $width);
                $system = new Util_System();
                if ($system->smkdir($attachPath . $url) !== true) {
                    return Common::formatMsg(-1, '文件处理失败!');
                }
                $img_name = explode('.', $img);

                self::CreateThumb($tmp_path . $img, $attachPath . $url, $img_name[0] . '_s', $s_width, $s_height, true);

                self::CreateWebp($attachPath . $url . $img_name[0] . '_s.jpg', '', $img_name[0] . '_s' . '.webp');
            }
//压缩详情页图片
            if ($width > $nwidth) {
                $nheight = ceil(($height * $nwidth ) / $width);
                $system = new Util_System();
                if ($system->smkdir($attachPath . $url) !== true) {

                    return Common::formatMsg(-1, '文件处理失败!');
                }
                $img_name = explode('.', $img);

                self::CreateThumb($tmp_path . $img, $attachPath . $url, $img_name[0], $nwidth, $nheight, true);
//self::CreateThumb($tmp_path . $img, $attachPath . $url, $img_name[0], $nwidth, $nheight, true);

                self::CreateWebp($attachPath . $url . $img_name[0] . '.jpg', '', $img_name[0] . '.webp');
            } else {
                self::copyFile($img, $tmp_path, $attachPath . $url);
            }

//复制大图到附件目录
            self::copyFile($img, $tmp_path, $attachPath . $url . '/full-scale');
//创建预览图的Webp版本
            self::CreateWebp($attachPath . $url . $img, '', $img . '.webp');

            $img_name_full = explode('.', $img);

//self::CreateWebp($attachPath . $url . '/full-scale/' . $img, '', $img . '.webp');
            self::CreateThumb($tmp_path . $img, $attachPath . $url . '/full-scale/', $img_name[0], $width, $height, true);
            self::CreateWebp($attachPath . $url . '/full-scale/' . $img_name_full[0] . '.png', '', $img_name_full[0] . '.webp');
            return $url . $img;
        }
        return null;
    }

    /**
     * CreateThumb
     */
    public function CreateThumb($srcFile, $savePath, $thumbName, $dstW, $dstH, $isJpeg = false) {
        $image = new Util_Img($srcFile);
        $image->resize($dstW, $dstH, '', '', '', 80, $isJpeg);
        $image->save($thumbName, $savePath);
    }

    /**
     *
     * addWater
     */
    private function addWater($srcFile, $waterFile, $savePath, $fileName) {



        $image = new Util_Img($srcFile);
        $image->addWatermark($waterFile);
        $image->writeWatermark(100);
        $image->save($fileName, $savePath);
    }

    /**
     * 创建webp图片
     * @param string $srcFile 原文件完整路径
     * @param string $dstPath 目标路径，为空时使用原文件路径
     * @param stirng $dstFileName 目标文件名，为空时替换原文件名的扩展名为webp
     */
    private function CreateWebp($srcFile, $dstPath, $dstFileName) {
        if (file_exists($srcFile)) {

            $quality = 75; //图片质量，default is 75
            $srcPathInfo = pathinfo($srcFile); //原文件咱径信息
//目标文件名
            if (empty($dstFileName)) $dst_fileName = $srcPathInfo['filename'] . ".webp";
            else $dst_fileName = $dstFileName;
//目标路径
            if (empty($dstPath)) $dst_path = $srcPathInfo['dirname'];
            else $dst_path = $dstpath;

            if (function_exists(image2webp)) {
                image2webp($srcFile, $dst_path . '/' . $dst_fileName, $quality);
            }
        }
    }

}
