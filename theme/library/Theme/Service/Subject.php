<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Theme_Service_Subject {

    public static $subject_type_ids = array(
        'normal' => 1, //普通
        'new_deprecated' => 2, //上新
        'top1' => 3, //置顶1
        'top2' => 4, //置顶2
        'new' => 11, //新品推荐
        'special' => 12, //精品推荐
    );

    /**
     *
     * Enter description here ...
     */
    public static function getAllSubject() {
        return array(self::_getDao()->count(), self::_getDao()->getAll());
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $sort = array('sort' => 'DESC', 'id' => 'DESC')) {
        $params = self::_cookData($params);

        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $params
     * @param unknown_type $page
     * @param unknown_type $limit
     */
    public static function getListPage($page = 1, $limit = 10, $params = array(), $sort = array('sort' => 'DESC', 'id' => 'DESC')) {
        $params = self::_cookData($params);

        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDaoSubjectPage()->getList($start, $limit, $params, $sort);
        $total = self::_getDaoSubjectPage()->count($params);
        return array($total, $ret);
    }

    public static function addSubjectTop($info) {
        if (!$info) return 0;
        $res = self::addTopSubject($info);
        return $res;
    }

    public static function addSbujectPage($info) {
        if (!$info) return 0;
        $res = self::addPageSubject($info);
        return $res;
    }

    private static function addPageSubject($info) {
        if ($info['type'] == 1) {
            $url = $info["subject_img"];
        }
        if ($info['type'] == 9) {
            $url = $info['subject_img'] . "," . $info['adv_img'];
        }
        $subjectinfo = array(
            "title" => $info['sname'],
            "type_id" => $info["screeid"],
            "descrip" => $info['descrip'],
            "img" => $url,
            "catagory_id" => $info["type"],
            "last_update_time" => strtotime($info['ctime']),
            "create_time" => time(),
        );
        $subject_id = Theme_Service_Subject::addSubjectpage($subjectinfo);

        if ($info['type'] == 9) {
            return $subject_id;
        }
        if (!$subject_id) $this->output(-1, '操作失败');

        if ($info['type'] == 1) {
            $themeids = explode("_", $info["ids"]);
            foreach ($themeids as $key => $value) {
                $file_subject[$key]['id'] = '';
                $file_subject[$key]['file_id'] = $value;
                $file_subject[$key]['subject_id'] = $subject_id;
            }
            $result = Theme_Service_SubjectFile::batchAddThemeSubjectPage($file_subject);
            return $result;
        }
    }

    private static function addTopSubject($info) {
        if ($info['type'] == 1) {
            $url = $info["subject_img"];
        }
        if ($info['type'] == 9) {
            $url = $info['subject_img'] . "," . $info['adv_img'];
        }
        $subjectinfo = array(
            "title" => $info['sname'],
            "type_id" => $info["screeid"] + 20,
            "descrip" => $info['descrip'],
            "img" => $url,
            "catagory_id" => $info["type"],
            "last_update_time" => strtotime($info['ctime']),
            "create_time" => time(),
        );
        $subject_id = Theme_Service_Subject::addSubject($subjectinfo);
        if ($info['type'] == 9) {
            return $subject_id;
        }
        if (!$subject_id) $this->output(-1, '操作失败');

        if ($info['type'] == 1) {
            $themeids = explode("_", $info["ids"]);
            foreach ($themeids as $key => $value) {
                $file_subject[$key]['id'] = '';
                $file_subject[$key]['file_id'] = $value;
                $file_subject[$key]['subject_id'] = $subject_id;
            }
            $result = Theme_Service_SubjectFile::batchAdd($file_subject);
            return $result;
        }
    }

    /**
     * get user info by out_uid
     * @param string $out_uid
     * @return boolean|mixed
     */
    public static function getBy($params) {
        if (!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getBy($data, array('sort' => 'DESC', 'id' => 'DESC'));
    }

    public static function getsubject_byGrouptype() {
        $where = "status=2 and last_update_time <" . time() . " order by last_update_time DESC";
        return self::_getDao()->getSubject_byGroupType($where);
    }

    public static function getsubject_byGrouptypePage() {
        $where = "status=2 and last_update_time <" . time() . " order by last_update_time DESC";
        return self::_getDaoSubjectPage()->getSubject_byGroupType($where);
    }

    public static function getByPre($params) {
        if (!is_array($params)) return false;
        $data = self::_cookData($params);

//  return self::_getDao() -> getBy($data , array('pre_publish' => 'DESC' , 'sort' => 'DESC' , 'id' => 'DESC'));
        return self::_getDao()->getBy($data, array('last_update_time' => 'DESC', 'id' => 'DESC'));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getSubject($id) {
        if (!intval($id)) return false;

        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getSubjectPage($id) {
        if (!intval($id)) return false;

        return self::_getDaoSubjectPage()->get(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function getSubjectTop($id) {
        if (!intval($id)) return false;

        return self::_getDaoSubjectPage()->get(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateSubject($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateBySubject($data, $where) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);

        return self::_getDao()->updateBy($data, $where);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     * @param unknown_type $id
     */
    public static function updateBySubjectPage($data, $where) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);

        return self::_getDaoSubjectPage()->updateBy($data, $where);
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function deleteSubject($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public static function deleteSubjectpage($id) {
        return self::_getDaoSubjectPage()->delete(intval($id));
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    public static function addSubject($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $ret = self::_getDao()->insert($data);
        if (!$ret) return $ret;
        return self::_getDao()->getLastInsertId();
    }

    public static function addSubjectpage($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $ret = self::_getDaoSubjectPage()->insert($data);
        if (!$ret) return $ret;
        return self::_getDaoSubjectPage()->getLastInsertId();
    }

    /**
     * 获取专题中的主题文件列表，代码来自SubjectController，供IndexController调用
     * @param $id 专题ID
     * @param $params 手机参数
     * @param $page 当前页
     * @param $perpage 每一页的主题数量
     * @param $firstpage 首页的主题数量
     */
    public static function getSubjectFilesById($params, $page, $perpage, $firstpage = 1, $orederby = 'sort', $sort = 'DESC') {
        /* $a = array('lock_style' => array('IN' , $lock_style) , 'area' => $params['area'] ,
          'resulution' => $params['resulution'] , 'font_size' => $params['font_size'] , 'android_version' => $params['android_version']);
          print_r($a);
          exit; */

        $params = array(
            'lock_style' => $params['lock_style'],
            'resulution' => $params['resulution'],
            'font_size' => $params['font_size'],
            'status' => $params['status'],
            'package_type' => $params['package_type'],
        );
        if ($params['package_type'] == 2) {
            unset($params["resulution"]);
            $tem_files = Theme_Service_File::getList($page, $perpage, $params, $orederby, $sort);
            foreach ($tem_files[1] as $values) {
                $tem_ids[] = $values["id"];
            }
            list(, $subject_files, ) = Theme_Service_File::getCanuseFiles(1, count($tem_ids), $tem_ids, '', $params);
            $all = Theme_Service_File::getCanuseFilesCount(array("package_type" => 2, "status" => 4));
        }
        if ($params['package_type'] == 3) {
            unset($params["resulution"]);
            $tem_files = Theme_Service_File::getList($page, $perpage, $params, $orederby, $sort);
            foreach ($tem_files[1] as $values) {
                $tem_ids[] = $values["id"];
            }
            list(, $subject_files, ) = Theme_Service_File::getCanuseFiles(1, count($tem_ids), $tem_ids, '', $params);

            $all = Theme_Service_File::getCanuseFilesCount(array("package_type" => 3, "status" => 4));
        }
        if ($params['package_type'] == 1) {
            list($all, $subject_files) = Theme_Service_File::getList($page, $perpage, $params, $orederby, $sort);
        }
// $subject_files = Theme_Service_File::getList($page, $perpage, $params, $orederby);
        $subject_file_ids = array();
        foreach ($subject_files as $value) {
            $subject_file_ids[] = $value['id'];
        }
        if ($subject_file_ids) {
            $ids = array_values($subject_file_ids);
//图片
            $file_imgs = Theme_Service_FileImg::getByFileIds($ids);
//$total = $subject_files[0];
//提出封面图片;

            $imgs = self::mk_files_image_pre_face_s($file_imgs, $params['package_type']);
        }

        return array($all, $subject_files, $imgs, $file_imgs, $file_imgs);
    }

    public static function get_subject_files_inid($params, $ids, $page, $perpage, $firstpage = -1, $sort = array('id' => 'DESC')) {

        $params = array(
            'lock_style' => $params['lock_style'],
            'resulution' => $params['resulution'],
            'font_size' => $params['font_size'],
            'stauts' => 4
        );
        $subject_files = Theme_Service_File::getCanuseFiles($page, $perpage, $ids, '', $params, $sort);
        if ($subject_files[1]) {
            foreach ($subject_files[1] as $values) {
                $subject_files_ids [] = $values['id'];
            }
            $subject_files_imgs = Theme_Service_FileImg::getByFileIds($subject_files_ids);
            krsort($subject_files_imgs);

            $imgs = self::mk_files_image_pre_face_s($subject_files_imgs);
        }
        return array($subject_files[0], $subject_files[1], $imgs, $subject_files_imgs);
    }

    /**
     * 提取图片包中的封面图片
     * @param array $files_images
     * @return int
     *
     */
    public static function mk_files_image_pre_face_s(array $files_images = array(), $type = '') {
        if (!$files_images) return 0;
        $imgs = array();

        foreach ($files_images as $key => $value) {
            $info = pathinfo($value['img']);
            if ($info['filename'] == 'pre_face') {
                $pre_face = explode('.', $value['img']);
                if ($type >= 2) {
                    $imgs[$value['file_id']]['pre_face_s'] = $pre_face[0] . '_s.webp';
                } else {
                    $imgs[$value['file_id']]['pre_face_s'] = $pre_face[0] . '_s.jpg';
                }
            }
            if ($info['filename'] == 'pre_face_small') {
                $pre_face = explode('.', $value['img']);
                $imgs[$value['file_id']]['pre_face_small'] = $pre_face[0] . '.jpg';

// $imgs[$value['file_id']]['pre_face_small'] = $pre_face[0] . '_s.jpg';
            }
        }

        return $imgs;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $data
     */
    private static function _cookData($data) {
        $tmp = array();


        if (isset($data['id'])) $tmp['id'] = $data['id'];
        if (isset($data['img'])) $tmp['img'] = $data['img'];
        if (isset($data['title'])) $tmp['title'] = $data['title'];
        if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if (isset($data['last_update_time'])) $tmp['last_update_time'] = $data['last_update_time'];
        if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
        if (isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
        if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
        if (isset($data['is_publish'])) $tmp['is_publish'] = $data['is_publish'];
        if (isset($data['publish_conn'])) $tmp['publish_conn'] = $data['publish_conn'];
        if (isset($data['pre_publish'])) $tmp['pre_publish'] = $data['pre_publish'];
        if (isset($data['catagory_id'])) $tmp['catagory_id'] = $data['catagory_id'];
        if (isset($data['status'])) $tmp['status'] = $data['status'];
        return $tmp;
    }

    public static function update_status($sid, $status, $screemid) {
        if (!$sid) return null;
//专题上架
        $where = "id=$sid";


        if ($status == 2) {

            if ($screemid == 2) {
                $data = "type_id=type_id+18,status=1";
                $value = "type_id=4";
            }
            if ($screemid == 1) {
                $data = "type_id=type_id+18,status=1";
                $value = "type_id=3";
            }
            if ($screemid == 4) {
                $data = "type_id=type_id-10,status=1";
                $value = "type_id=34";
            }
            if ($screemid == 5) {
                $data = "type_id=type_id-10,status=1";
                $value = "type_id=35";
            }
            if ($screemid == 31) {
                $data = "type_id=type_id+18,status=1";
                $value = "type_id=3";
            }
            if ($screemid == 32) {
                $data = "type_id=type_id+18,status=1";
                $value = "type_id=4";
            }
            if ($screemid == 34) {
                $data = "type_id=type_id-10,status=1";
                $value = "type_id=34";
            }
            if ($screemid == 35) {
                $data = "type_id=type_id-10,status=1";
                $value = "type_id=35";
            }
            if ($screemid == 38) {
                $data = "type_id=type_id-10,status=1";
                $value = "type_id=38";
            }

//self::_getDao()->update($data, $value);

            $sets = "$value ,status=$status";
        } else {
            $sets = "status=$status,type_id=type_id-10";
        }
        return self::_getDao()->update($sets, $where);
    }

    //页面专题状态跟新;
    public static function update_statusPage($sid, $status, $screemid) {

        $sets = "status=$status";

        $where = "id=$sid";
        return self::_getDaoSubjectPage()->update($sets, $where);
    }

    /**
     *
     * @return Theme_Dao_Subject
     */
    private static function _getDao() {
        return Common::getDao("Theme_Dao_Subject");
    }

    private static function _getDaoSubjectPage() {
        return Common::getDao("Theme_Dao_SubjectPage");
    }

}
