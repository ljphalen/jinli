<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class ListserverController extends Front_BaseController {

    public $actions = array(
        'indexUrl' => '/list/index',
        'morelUrl' => '/list/more',
        'downloadUrl' => '/detail/down',
    );
    public $perpage = 12;
    private $webroot;
    private $staticroot;
    private $downloadroot;

    /**
     * 初始化
     *
     */
    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    /**
     * 列表
     */
    public function indexAction() {
        $this->__inits();

        $page = 1;
        $tid = intval($this->getInput('tid'));
        $orderby = $this->getInput('orderby');

        $orderby = substr($orderby, 0, strpos($orderby, '|'));

        if (!$orderby || !in_array($orderby, array('id', 'down'))) {
            $orderby = 'id';
        }
        $webroot = Yaf_Application::app()->getConfig()->webroot;

        if ($tid) {
            $type_files = Theme_Service_IdxFileType::getByTypeId($tid);

            $type_files = Common::resetKey($type_files, 'file_id');
            $in_ids = array_keys($type_files);
        }
        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }

        if ($package_type == "v1") {
            $package_type = 1;
            $wheres = array('resulution' => $params['resulution'], 'status' => 4, "package_type" => 1);
        }
        if ($package_type == "v2") {
            $package_type = 2;
            unset($params['resulution']);
            $wheres = array('package_type' => 2, 'status' => 4);
        }
        //rom 版本号

        $sort = array('down' => 'DESC');
        list($total, $files, $all) = Theme_Service_File::getCanuseFiles(
                        $page, $this->perpage, $in_ids, $not_in_ids, $wheres, $sort);


        foreach ($files as $keys => $values) {

            $res_sjon[$keys]["id"] = $values["id"];
            $res_sjon[$keys]["title"] = $values["title"];
            $res_sjon[$keys]["designer"] = $values["designer"];
            $res_sjon[$keys]["down"] = $values["down"];

            $ids[] = $values["id"];
        }
        //图片
        $file_imgs = Theme_Service_FileImg::getByFileIds($ids);

        $imgs = array();
        foreach ($file_imgs as $key => $value) {
            $info = pathinfo($value['img']);
            if ($info['filename'] == 'pre_face') {
                $pre_face = explode('.', $value['img']);
                $imgs[$value['file_id']]['pre_face_s'] = $this->webroot . '/attachs/theme/' . $pre_face[0] . '_s.jpg';

                foreach ($res_sjon as $k => $v) {
                    if ($v[id] == $value['file_id']) {
                        $res_sjon[$k]['img'] = $this->webroot . '/attachs/theme/' . $pre_face[0] . '_s.jpg';
                    }
                }
            }
        }
        //if($info['filename'] == 'pre_one') $imgs[$value['file_id']]['pre_one'] = $value['img'];
        //if($info['filename'] == 'zzzzz_gn_brief_lockscreen_lockpaper') $imgs[$value['file_id']]['zzzzz_gn_brief_lockscreen_lockpaper'] = $value['img'];

        print_r($res_sjon);
        exit;
    }

    /**
     * 加载更多
     */
    public function moreAction() {
        $page = intval($this->getInput('page'));
        if ($page < 2) $page = 2;
        $tid = intval($this->getInput('tid'));
        $orderby = $this->getInput('orderby');

        if (!$orderby || !in_array($orderby, array('id', 'down'))) {
            $orderby = 'id';
        }

        if ($tid) {
            $type_files = Theme_Service_IdxFileType::getByTypeId($tid);
            $type_files = Common::resetKey($type_files, 'file_id');
            $type_file_ids = array_keys($type_files);
            $in_ids = array_keys($type_files);
        }

        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);

        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }

        if ($package_type == "v1") {
            $package_type = 1;
            $wheres = array('resulution' => $params['resulution'], 'status' => 4, "package_type" => 1);
        }
        if ($package_type == "v2") {
            $package_type = 2;
            unset($params['resulution']);
            $wheres = array('package_type' => 2, 'status' => 4);
        }
        //rom 版本号

        $sort = array('down' => 'DESC');
        list($total, $files, $all) = Theme_Service_File::getCanuseFiles(
                        $page, $this->perpage, $in_ids, $not_in_ids, $wheres, $sort);



        foreach ($files as $keys => $values) {

            $res_sjon[$keys]["id"] = $values["id"];
            $res_sjon[$keys]["title"] = $values["title"];
            $res_sjon[$keys]["designer"] = $values["designer"];
            $res_sjon[$keys]["down"] = $values["down"];

            $ids[] = $values["id"];
        }
        //图片
        $file_imgs = Theme_Service_FileImg::getByFileIds($ids);

        $imgs = array();
        foreach ($file_imgs as $key => $value) {
            $info = pathinfo($value['img']);
            if ($info['filename'] == 'pre_face') {
                $pre_face = explode('.', $value['img']);
                $imgs[$value['file_id']]['pre_face_s'] = $this->webroot . '/attachs/theme/' . $pre_face[0] . '_s.jpg';

                foreach ($res_sjon as $k => $v) {
                    if ($v[id] == $value['file_id']) {
                        $res_sjon[$k]['img'] = $this->webroot . '/attachs/theme/' . $pre_face[0] . '_s.jpg';
                    }
                }
            }
        }

        print_r($res_sjon);
        exit;
    }

}
