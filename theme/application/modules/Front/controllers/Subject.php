<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Front_BaseController {

    public $actions = array(
        'indexUrl' => '/subject/index',
        'downloadUrl' => '/detail/down',
    );

    public function indexAction() {

        $page = 1;
        $perpage = 9;
        $firstpage = 9;
        $sid = $this->getInput('sid');
        $source = $this->getInput('source');
        $subject = Theme_Service_Subject::getSubject(intval($sid));

        if ($subject['catagory_id'] == 9) {
            $adv_imgs = explode(",", $subject['img']);
            unset($adv_imgs[0]);
            $subject['adv_imgs'] = $adv_imgs;
            $subject['desc'] = $subject['descrip'];
            unset($subject['descrip']);

            $this->assign("subject", $subject);
            $this->display('advimage');
        } else {
            //主题排序
            $orderby = $this->getInput('orderby');
            if ($orderby == 'down') $sort = array('down' => 'DESC'); //down: 按下载量排序
            else if ($orderby == 'auto') $sort = array('sort' => 'DESC', 'id' => 'DESC'); //auto: 按主题的ID排序
            else {
                // $orderby = 'subjcet';
                $sort = array(''); //默认为按专题中定义的顺序排，由Theme_Service_File::getCanuseFiles控制
            }
            //手机参数
            $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);

            //该专题下的所有主题
            $in_ids = Theme_Service_SubjectFile::getBySubjectId($sid, array());
            $in_ids = $this->mk_subject_ids($in_ids);
            $package_type = $this->getInput('package');

            $para['resulution'] = $params['resulution'];
            $para['status'] = 4;


            if (isset($params['package'])) {
                $package_type = $params['package'];
            } else {
                $package_type = $this->getInput('package');
            }
            if ($package_type == "v1" || !$package_type) {
                $para['package_type'] = 1;
            }
            if ($package_type == "v2") {
                $para['package_type'] = 2;
                unset($para['resulution']);
            }

            //分辨率过滤;
            $files = Theme_Service_File::getCanuseFiles(0, 100000, $in_ids, '', $para);

            if ($files[1]) {
                //取出主题id;
                $subject_ids = $this->mk_subject_ids($files[1], "id");
                if (!$sid) {
                    rsort($subject_ids);
                }
                //取出对应分辨率下的主题;
                list($file_total, $files, $imgs) = Theme_Service_Subject::get_subject_files_inid($para, $subject_ids, $page, $perpage, $firstpage, '');

                $this->assign('files', $files);
            }

            $this->assign('page', $page);
            $this->assign('perpage', $this->perpage);
            $this->assign('file_total', $file_total);
            $this->assign('subject', $subject);
            $this->assign('imgs', $imgs);
            $this->assign('source', $source);
            $this->assign('moreUrl', '/subject/more?sid=' . $sid . '&orderby=' . $orderby . '&firstpage=' . $firstpage);
        }
    }

    public function moreAction() {
        //专题
        $sid = $this->getInput('sid');
        if (!$sid) exit;
        $subject = Theme_Service_Subject::getSubject($sid);

        if (!$subject) exit;


        //分页
        $page = intval($this->getPost('page')); //当前页

        if ($page < 2) $page = 2;
        $firstpage = intval($this->getInput('firstpage')); //首页显示的数量
        $perpage = 9; //每页显示的数量
        //主题排序
        $orderby = $this->getInput('orderby');
        if ($orderby == 'down') $sort = array('down' => 'DESC'); //down: 按下载量排序
        else if ($orderby == 'auto') $sort = array('sort' => 'DESC', 'id' => 'DESC'); //auto: 按主题的ID排序
        else $sort = array(); //默认为按专题中定义的顺序排，由Theme_Service_File::getCanuseFiles控制
//手机参数
        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);



        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }
        if ($package_type == "v1" || !$package_type) {
            $para['package_type'] = 1;
            $para['resulution'] = $params['resulution'];
            $para['status'] = 4;
        }
        if ($package_type == "v2") {
            $para['package_type'] = 2;
            unset($para['resulution']);
            // $para['resulution'] = $params['resulution'];
            $para['status'] = 4;
        }


        $webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $webrootdown = Yaf_Application::app()->getConfig()->webroot;
        $downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $pt = Util_Cookie::get('THEME_PT', true);


        $in_ids = Theme_Service_SubjectFile::getBySubjectId($sid, array());
        $in_ids = $this->mk_subject_ids($in_ids);


        $files = Theme_Service_File::getCanuseFiles(0, 100000, $in_ids, '', $para);

        $subject_ids = $this->mk_subject_ids($files[1], "id");
        if (!$sid) {
            rsort($subject_ids);
        }
        // $subject_ids = $this->mk_subject_ids($in_ids);


        list($total, $files, $imgs) = Theme_Service_Subject::get_subject_files_inid($para, $subject_ids, $page, $perpage, $firstpage);
        $hasnext = (ceil(($total - $firstpage) / $perpage) + 1 - $page) > 0 ? true : false; //还有没有下一页
        // $i = ($page - 2) * $perpage + $firstpage + 1; //在循环中标识当前项目的序号，从1开始

        $i = ($page - 2) * $perpage + 1; //在循环中标识当前项目的序号，从1开始

        $list = array();
        foreach ($files as $key => $value) {

            $pre = $next = 1;
            if ($total == $i) $next = 0;
            $link = sprintf("%s/detail?id=%d&pre=%d&next=%d&updatetime=%d&sid=%d", $webrootdown, $value['id'], $pre, $next, $value['packge_time'], $sid);
            $list[] = array(
                'id' => $value['id'],
                'title' => Util_String::substr($value['title'], 0, 6, '', true),
                'link' => $link,
                'down' => $webrootdown . $this->actions['downloadUrl'] . '/' . $value['id'] . '_' . $pt,
                'since' => $value['since'],
                'img' => $webroot . '/attachs/theme/' . $imgs[$value['id']]['pre_face_s'],
                'resume' => $value['summary']);
            $i ++;
        }

        $this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    /*
     * 历史专题列表
     */

    public function listAction() {
        $page = 1;
        $perpage = 10;
        // list($total , $subjects) = Theme_Service_Subject::getList($page , $perpage , array('type_id' => array('=' , Theme_Service_Subject::$subject_type_ids['normal'])));
        list($total, $subjects) = Theme_Service_Subject::getList($page, $perpage, array('type_id' => array('!=', ' ')));

        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);
        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }

        if ($package_type == "v2") {
            $date = strtotime("2014-5-15");
            foreach ($subjects as $key => $val) {
                if ($val["pre_publish"] < $date) {
                    unset($subjects[$key]);
                }
            }
        }


        foreach ($subjects as $keys => &$values) {
            if ($values['catagory_id'] == 9) {
                // $tem = substr($values['img'], 0, strpos($values['img'], ","));
                $potion = strpos($values['img'], ",");
                if ($potion) {
                    $tem = substr($values['img'], 0, $potion);
                } else {
                    $tem = $values['img'];
                }
                $subjects[$keys]['img'] = $tem;
            }
        }


        /* if ($page == 1) {
          unset($subjects[0]);
          unset($subjects[1]);
          } */
        $this->assign('subjects', $subjects);
        $this->assign('file_total', $total);
    }

    /*
     * 历史专题列表加载更多
     */

    public function listMoreAction() {
        $webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $attachPath = $webroot . '/attachs/theme/';

        $webrootdown = Yaf_Application::app()->getConfig()->webroot;
        $page = $this->getPost('page');
        if ($page <= 2) $page = 2;
        $perpage = 10;

        //  list($total , $subjects) = Theme_Service_Subject::getList($page , $perpage , array('type_id' => array('=' , Theme_Service_Subject::$subject_type_ids['normal'])));
        list($total, $subjects) = Theme_Service_Subject::getList($page, $perpage, array('type_id' => array('!=', ' ')));
        $hasnext = (ceil($total / $perpage) - $page) > 0 ? true : false; //还有没有下一页
        $i = ($page - 1) * $perpage + 1;
        $list = array();

        array_pop($subjects);
        array_pop($subjects);


        $params = json_decode(Util_Cookie::get('THEME_PARAMS', true), true);

        if (isset($params['package'])) {
            $package_type = $params['package'];
        } else {
            $package_type = $this->getInput('package');
        }

        if ($package_type == "v2") {
            $date = strtotime("2014-5-15");

            foreach ($subjects as $key => $val) {
                if ($val["pre_publish"] < $date) {
                    unset($subjects[$key]);
                }
            }
        }


        foreach ($subjects as $key => $value) {
            if ($value['catagory_id'] == 9) {
                $potion = strpos($value['img'], ",");

                if ($potion) {
                    $tmp = substr($value['img'], 0, $potion);
                } else {
                    $tmp = $value['img'];
                }

                $imgAddr = $tmp;
            } else {
                $imgAddr = $value['img'];
            }
            $list[] = array(
                'url' => $webrootdown . '/subject?sid=' . $value['id'] . '|' . $value['title'],
                'title' => $value['title'],
                'img' => $attachPath . $imgAddr);
            $i ++;
        }



        $this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    private function mk_subject_ids(array $subject = array(), $types = 'file_id') {
        if (!$subject) return FALSE;
        $subject_ids = array();

        foreach ($subject as $vales) {
            $subject_ids[] = $vales[$types];
        }
        return $subject_ids;
    }

}
