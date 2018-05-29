<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {

    public $actions = array(
        'indexUrl' => '/index/index',
        'downloadUrl' => '/detail/down',
    );

    /*
     * 「首页内容」
     * 置顶专题1，置顶专题2
     * 新品推荐
     * 精品推荐
     * 历史专题，精品主题
     */

    public function indexAction() {
        //统计首页pv
        Common::getCache()->increment('Theme_index_pv');
        //参数

        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        //$phonegap = $this->getInput('phonegap') ? $this->getInput('phonegap') : 0;
        $para['status'] = 4;
        //v2包参数
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }
        if ($package_type == "v1" || !$package_type) {
            $para['package_type'] = 1;
            $para['resulution'] = $params['resulution'];
        }
        if ($package_type == "v2") {
            $para['package_type'] = 2;
        }

//---------------------------------------------------------------------------//
        //专题
        $where[] = array(
            'img' => array('!=', ''),
            'type_id' => array('=', Theme_Service_Subject::$subject_type_ids['top1']),
        );
        $where[] = array('img' => array('!=', ''),
            'type_id' => array('=', Theme_Service_Subject::$subject_type_ids['top2']),
        );

        $where_pre[] = array(
            'img' => array('!=', ''),
            'type_id' => array('=', Theme_Service_Subject::$subject_type_ids['top1']),
            'status' => array('=', 2),
            "last_update_time" => array('<', time())
        );
        $where_pre[] = array('img' => array('!=', ''),
            'type_id' => array('=', Theme_Service_Subject::$subject_type_ids['top2']),
            'status' => array('=', 2),
            "last_update_time" => array('<', time()),
        );

        $subject_top1 = Theme_Service_Subject::getByPre($where_pre[0]);
        $subject_top2 = Theme_Service_Subject::getByPre($where_pre[1]);
//-----------------end-------------------------------------------------------------//
        //专题类别;
        if ($subject_top1 && $subject_top2) {
            if ($subject_top1['catagory_id'] == 9) {
                $adv_imgs = $this->mk_adv_subject($subject_top1['img']);
                $subject_top1['img'] = $adv_imgs[0];

                $subject_top2_files = $this->get_ids_by_sid($subject_top2['id'], $para);
            }
            if ($subject_top2['catagory_id'] == 9) {
                $adv_imgs = $this->mk_adv_subject($subject_top2['img']);
                $subject_top2['img'] = $adv_imgs[0];
                $subject_top1_files = $this->get_ids_by_sid($subject_top1['id'], $para);
            } else {
                $subject_top1_files = $this->get_ids_by_sid($subject_top1['id'], $para);
                $subject_top2_files = $this->get_ids_by_sid($subject_top2['id'], $para);
            }
            if (isset($subject_top1_files['since'])) {
                $subject_top1['file'][] = $subject_top1_files;
            }
            if (isset($subject_top2_files['since'])) {
                $subject_top2['file'][] = $subject_top2_files;
            }

            $subjects = array($subject_top1, $subject_top2);
        } else {
            $subjects = array();
        }
//------------------------------------------------------------------------------
        //新品推荐
        //新品推荐最多取6个，不支持翻页
        //sort DESC, id DESC
        // list($new_total, $new_files, $new_imgs) = Theme_Service_Subject::getSubjectFilesById($para, 0, 6, 6, 'sort');
        list($new_total, $new_files, $new_imgs) = Theme_Service_Subject::getSubjectFilesById($para, 0, 6, 6, 'sort DESC,', "id DESC");
        $this->assign('new_total', $new_total); //主题数量
        $this->assign('new_files', $new_files); //主题列表
        $this->assign('new_imgs', $new_imgs); //主题的图片
//---------------------------------------------------------------------------------
        //精品推荐
        list($special_total, $special_files, $special_imgs) = Theme_Service_Subject::getSubjectFilesById($para, 2, 6, 6, 'sort DESC,', "id DESC");
        $this->assign('special_total', $special_total); //主题数量
        $this->assign('special_files', $special_files); //主题列表

        $this->assign('special_imgs', $special_imgs); //主题的图片
        $this->assign('page', $page);
        $this->assign('firstpage', $firstpage);

        $special_complete_s = true;
        $version = $this->getInput('version');
        if (!$version) {
            $special_complete_s = FALSE; //首页是否显示完了所有的历史主题
        }


        $this->assign('special_complete', $special_complete_s); //首页是否显示完了所有的精品主题
        $this->assign('specialUrl', '/subject?orderby = down&sid = ' . $specialSubject['id']); //点“更多”进入精品主题页面，按下载量排序
        $this->assign("themesMoreUrl", '/list?orderby=id');
        $this->assign('newSubject', $newSubject); //最新专题号
        $this->assign('specialSubject', $specialSubject);
        $this->assign('subjectsUrl', '/subject/list'); //历史专题
        //$this -> assign('subjectsUrl' , '/list?orderby=id'); //历史专题
        $this->assign('themesUrl', '/list?orderby=down'); //精品专题（全部主题）
        $this->assign('$subject_top1', $subject_top1);
        $this->assign('$subject_top2', $subject_top2);

        $this->assign('subjects', $subjects); //置顶专题
        $this->assign('params', $params); //手机参数
        $this->assign("cache", Theme_Service_Config::getValue('theme_index_cache'));
    }

    /**
     * 加载更多精品
     * GET参数：$page, $firstpage
     */
    public function moreAction() {
        $page = intval($this->getPost('page'));
        if ($page < 3) $page = 3;
        $firstpage = intval($this->getInput('firstpage'));
        $perpage = 6;
        //参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        $para['status'] = 4;
        //v2包参 数
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }
        //v1,v2
        if ($package_type == "v1" || !$package_type) {
            $para['package_type'] = 1;
            $para['resulution'] = $params['resulution'];
        }
        if ($package_type == "v2") {
            $para['package_type'] = 2;
        }

        $list = array();
        //  $specialSubject = Theme_Service_Subject::getBy(array('type_id' => array('=', Theme_Service_Subject::$subject_type_ids['special'])));
        // if ($specialSubject) {
        $webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $webrootdown = Yaf_Application::app()->getConfig()->webroot;
        $pt = Util_Cookie::get('THEME_PT', true);

        list($total, $files, $imgs) = Theme_Service_Subject::getSubjectFilesById($para, $page, 6, $firstpage, 'sort DESC,', "id DESC");
        $hasnext = (ceil(($total - $firstpage) / $perpage) + 1 - $page) > 0 ? true : false; //还有没有下一页
        //$hasnext = $total >= 6 ? true : false; //还有没有下一页
        // $total = $total + $perpage * 2;
        $i = ($page - 2) * $perpage + $firstpage + 1; //在循环中标识当前项目的序号，从1开始

        foreach ($files as $key => $value) {
            $pre = $next = 1;
            if ($total <= $i) $next = 0;

            $link = sprintf("%s/detail?id=%d&pre=%d&next=%d&updatetime=%d&sid=0", $webrootdown, $value['id'], $pre, $next, $value['packge_time'], $specialSubject['id']);

            $list[] = array('id' => $value['id'],
                'title' => Util_String::substr($value['title'], 0, 6, '', true),
                'link' => $link,
                'down' => $webrootdown . $this->actions['downloadUrl'] . '/' . $value['id'] . '_' . $pt,
                'since' => $value['since'],
                'img' => $webroot . '/attachs/theme' . $imgs[$value['id']]['pre_face_s'],
                'resume' => $value['summary']);
            $i ++;
        }
        //  }


        $this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'hasmore' => $hasmore, 'curpage' => $page));
    }

    /**
     *
     * 分类接口
     */
    public function typeAction() {
        list(, $types) = Theme_Service_FileType::getAllFileType();
        $data = array();
        foreach ($types as $key => $value) {
            $data[$key]['id'] = $value['id'];
            $data[$key]['name'] = $value['name'];
        }
        exit(json_encode(array('themeTypes' => $data)));
    }

    /*
     * 取专题下的主题id
     * @param type $sid
     * @param type $params
     * @return type/
     */

    private function get_ids_by_sid($sid, $params) {
        $subject_files = Theme_Service_SubjectFile::getBySubjectId($sid, array());
        foreach ($subject_files as $key => $value) {
            $s_file_ids[] = $value['file_id'];
        }
        //取专题下的主题
        $files = Theme_Service_File::getCanuseFiles(0, 100000, $s_file_ids, '', $params);
        if ($files[1]) {
            if ($files[0] == 1) {
                foreach ($files[1] as $values) {
                    $file_ids[] = $values['id'];
                    $file_ids['since'] = $values['since'];
                    $file_ids['title'] = $values['title'];
                }
            } else {
                foreach ($files[1] as $values) {
                    $file_ids[] = $values['id'];
                }
            }
        }

        return $file_ids;
    }

    //广告专题；
    private function mk_adv_subject($subjects) {
        $res = explode(",", $subjects);
        return $res;
    }

}
