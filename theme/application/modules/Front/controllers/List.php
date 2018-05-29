<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class ListController extends Front_BaseController {

    public $actions = array(
        'indexUrl' => '/list/index',
        'morelUrl' => '/list/more',
        'downloadUrl' => '/detail/down',
    );
    public $perpage = 12;

    /**
     * 列表
     */
    public function indexAction() {
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
            $type_file_ids = array_keys($type_files);
        }
        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }

        if ($package_type == "v1" || !$package_type) {
            $package_type = 1;
            $wheres = array('resulution' => $params['resulution'], 'status' => 4, "package_type" => 1);
        }
        if ($package_type == "v2") {
            $package_type = 2;
            unset($params['resulution']);
            $wheres = array('package_type' => 2, 'status' => 4);
        }

        // print_r($wheres);
        //rom 版本号
        $rom = Theme_Service_Rom::getRomByName($params['rom_version']);
        $file_rom = Theme_Service_IdxFileRom::getByRomId($rom['id']);
        $rom_file_ids = array();
        foreach ($file_rom as $key => $value) {
            $rom_file_ids[] = $value['file_id'];
        }
        //比较
        $not_in_ids = array();
        $in_ids = array();
        if ($type_file_ids && $rom_file_ids) {
            $in_ids = array_diff($type_file_ids, $rom_file_ids);
        } elseif ($rom_file_ids) {
            $not_in_ids = $rom_file_ids;
        } elseif ($type_file_ids) {
            $in_ids = $type_file_ids;
        }
        $sort = array('down' => 'DESC');
        list($total, $files, $all) = Theme_Service_File::getCanuseFiles($page, $this->perpage, $in_ids, $not_in_ids, $wheres, $sort);

        $files_reset = Common::resetKey($files, 'id');


        $theme_files = array();
        $imgs = array();
        if ($files_reset) {
            $ids = array_keys($files_reset);
            //图片
            $file_imgs = Theme_Service_FileImg::getByFileIds($ids);
            $imgs = array();
            foreach ($file_imgs as $key => $value) {
                $info = pathinfo($value['img']);
                if ($info['filename'] == 'pre_face') {
                    $pre_face = explode('.', $value['img']);
                    $imgs[$value['file_id']]['pre_face_s'] = $pre_face[0] . '_s.jpg';
                }
                //if($info['filename'] == 'pre_one') $imgs[$value['file_id']]['pre_one'] = $value['img'];
                //if($info['filename'] == 'zzzzz_gn_brief_lockscreen_lockpaper') $imgs[$value['file_id']]['zzzzz_gn_brief_lockscreen_lockpaper'] = $value['img'];
            }
        }
        $hasnext = (ceil((int) $all / $this->perpage) - ($page)) > 0 ? true : false;
        // $hasnext = $total >= 12 ? true : false;
        $this->assign('page', $page);
        $this->assign('perpage', $this->perpage);
        $this->assign('file_total', $all);
        $this->assign('files', $files);
        $this->assign('imgs', $imgs);
        $this->assign('tid', $tid);
        $this->assign('hasnext', $hasnext);
        $this->assign('curpage', $page);
        $this->assign('orderby', $orderby);
        $this->assign('more_url', $tid ? $webroot . $this->actions['morelUrl'] . '/?tid=' . $tid . '&orderby=' . $orderby : $webroot . $this->actions['morelUrl'] . '/?orderby=' . $orderby);
        $this->assign("cache", Theme_Service_Config::getValue('theme_index_cache'));
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
        }

        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);


        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }

        if ($package_type == "v1" || !$package_type) {
            $package_type = 1;
            $wheres = array('resulution' => $params['resulution'], 'status' => 4, "package_type" => 1);
        }
        if ($package_type == "v2") {
            $package_type = 2;

            unset($params['resulution']);
            $wheres = array('package_type' => 2, 'status' => 4);
        }

        //rom 版本号
        $rom = Theme_Service_Rom::getRomByName($params['rom_version']);
        $file_rom = Theme_Service_IdxFileRom::getByRomId($rom['id']);
        $rom_file_ids = array();
        foreach ($file_rom as $key => $value) {
            $rom_file_ids[] = $value['file_id'];
        }

        //比较
        $not_in_ids = array();
        $in_ids = array();

        if ($type_file_ids && $rom_file_ids) {
            $in_ids = array_diff($type_file_ids, $rom_file_ids);
        } elseif ($rom_file_ids) {
            $not_in_ids = $rom_file_ids;
        } elseif ($type_file_ids) {
            $in_ids = $type_file_ids;
        }
        $sort = array('down' => 'DESC');
        //  $lock_style = explode('|' , $params['lock_style']);
        //   list($total , $files) = Theme_Service_File::getCanuseFiles($page , $this -> perpage , $in_ids , $not_in_ids , array('lock_style' => array('IN' , $lock_style) , 'area' => $params['area'] ,
        //       'resulution' => $params['resulution'] , 'font_size' => $params['font_size'] , 'android_version' => $params['android_version'] , 'status' => 4) , $sort);

        list($total, $files, $all) = Theme_Service_File:: getCanuseFiles($page, $this->perpage, $in_ids, $not_in_ids, $wheres, $sort);

        $hasnext = (ceil((int) $all / $this->perpage) - ($page)) > 0 ? true : false;
        //$hasnext = $total >= 12 ? true : false;

        $files_reset = Common::resetKey($files, 'id');

        $theme_files = array();
        $imgs = array();
        $list = array();
        if ($files_reset) {
            $ids = array_keys($files_reset);
            //图片
            $file_imgs = Theme_Service_FileImg::getByFileIds($ids);
            $imgs = array();
            foreach ($file_imgs as $key => $value) {
                $info = pathinfo($value['img']);
                if ($info['filename'] == 'pre_face') {
                    $pre_face = explode('.', $value['img']);
                    $imgs[$value['file_id']]['pre_face_s'] = $pre_face[0] . '_s.jpg';
                }
                //if($info['filename'] == 'pre_face') $imgs[$value['file_id']]['pre_face'] = $value['img'];
                //if($info['filename'] == 'zzzzz_gn_brief_lockscreen_lockpaper') $imgs[$value['file_id']]['zzzzz_gn_brief_lockscreen_lockpaper'] = $value['img'];
            }

            $webroot = Yaf_Application::app()->getConfig()->fontcroot;
            $webrootdown = Yaf_Application::app()->getConfig()->webroot;
            $downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
            $pt = Util_Cookie::get('THEME_PT', true);

            $i = ($page - 1 ) * $this->perpage + 1;

            foreach ($files as $key => $value) {
                $pre = $next = 1;
                if ($all == $i) $next = 0;
                if ($i % $this->prepage == 0 && ($total - ($page * $this->perpage) == 0)) $next = 0;
                $link = sprintf("%s/detail?id=%d&pre=%d&next=%d&update_time=%d&orderby=%s&i=$i", $webrootdown, $value['id'], $pre, $next, $value['packge_time'], $orderby);
                $link = $tid ? $link . '&tid=' . $tid : $link;

                $list[$key]['id'] = $value['id'];
                $list[$key]['title'] = Util_String:: substr($value['title'], 0, 6, '', true);
                $list[$key]['link'] = $link;
                $list[$key] ['down'] = $webrootdown . $this->actions ['downloadUrl'] . '/' . $value['id'] . '_' . $pt;
                $list[$key]['since'] = $value['since'];
                $list[$key] ['img'] = $webroot . '/attachs/theme' . $imgs[$value['id']]['pre_face_s'];
                //$list[$key]['bg_img1'] = $webroot.'/attachs'.$imgs[$value['id']]['zzzzz_gn_brief_lockscreen_lockpaper'];
                //$list[$key]['bg_img2'] = $webroot.'/attachs'.$imgs[$value['id']]['pre_one'];

                $list[$key]['resume'] = $value['summary'];
                $i ++;
            }
        }


        $this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'curpage' => $page));
    }

}
