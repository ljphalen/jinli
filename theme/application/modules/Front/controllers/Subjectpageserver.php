<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectpageserverController extends Front_BaseController {

    public $actions = array(
        'indexUrl' => '/subject/index',
        'downloadUrl' => '/detail/down',
    );
    //$this->webroot . '/attachs' .

    private $webroot;
    private $staticroot;
    private $downloadroot;

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->webrootDown = Yaf_Application::app()->getConfig()->webroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    public function indexAction() {
        $this->__inits();
        $page = 1;
        $perpage = 1000;
        $firstpage = 1000;
        $sid = $this->getInput('sid');
        $source = $this->getInput('source');
        $subject = Theme_Service_Subject::getSubjectPage(intval($sid));

        if ($subject['catagory_id'] == 9) {
            //广告专题
            $result = $this->mk_adv_subject($subject);
            $res = json_encode(array("url" => $result["adv_imgs"], "conn" => $result['desc']));
            print_r($res);
            exit;
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
            $in_ids = Theme_Service_SubjectFile::getBySubjectIdPagelist($sid, array("id" => "ASC"));
            $in_ids = $this->mk_subject_ids($in_ids);
            if (!$in_ids) die('null');
            $para = $this->mk_package_types($params);
            //过滤;
            $files = Theme_Service_File::getCanuseFiles(0, 1000, $in_ids, '', $para);
            if ($files[1]) {
                //取出主题id;
                $subject_ids = $this->mk_subject_ids($files[1], "id");
                if (!$sid) {
                    rsort($subject_ids);
                }
                //取出对应分辨率下的主题;
                list($file_total, $files_imgs, $imgs, $all_imgs) = Theme_Service_Subject::get_subject_files_inid($para, $subject_ids, $page, $perpage, $firstpage, '');
                //
                $res_theme = $this->mk_restheme($files_imgs, $all_imgs, $imgs);
            }
            $res_jsonsub = json_encode($res_theme);
            echo $res_jsonsub;
            exit;
        }
    }

    /**
     * 合成在线广告专题数据
     * @param type $subject
     * @return type
     */
    private function mk_adv_subject($subject) {
        $adv_imgs = explode(",", $subject['img']);
        unset($adv_imgs[0]);
        if (is_array($adv_imgs)) {
            foreach ($adv_imgs as $v) {
                $res['adv_imgs'][] = $this->webroot . "/attachs/theme/" . $v;
            }
        } else {
            $res['adv_imgs'] = $adv_imgs;
        }
        $res['desc'] = $subject['descrip'];

        return $res;
    }

    /**
     * 在线主题v1,v2客户端识别
     * @param type $params
     * @return Array
     *
     */
    private function mk_package_types($params) {
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
        if ($package_type == "v3") {
            $para['package_type'] = 3;
        }
        $para['status'] = 4;
        return $para;
    }

    /**
     * 合成专题数据;
     * @param type $files_imgs
     * @param type $all_imgs
     * @param type $imgs
     * @return string
     */
    private function mk_restheme($files_imgs, $all_imgs, $imgs) {
        foreach ($files_imgs as $k => &$v) {
            //$tem_img = $imgs [$v['id']]['pre_face_s'];
            $res_theme [$k]['id'] = $v['id'];
            $res_theme [$k]['title'] = $v['title'];
            $res_theme [$k]['file'] = $v['file'];
            $res_theme [$k]['descript'] = $v['descript'];
            $res_theme [$k]["down_path"] = $this->webrootDown . "/detail/down/" . $v["id"]; //. "_," . $v["since"];
            $res_theme [$k]['designer'] = $v['designer'];
            $res_theme [$k]['file_size'] = $v['file_size'];
            //$res_theme [$k]['image'] = $this->webroot . '/attachs/theme/' . $tem_img;
            $res_theme [$k]['hit'] = $v['hit'];
            $res_theme [$k]['package'] = $v['package_type'];
            $res_theme[$k]["last_update_time"] = $v["packge_time"];
            $res_theme[$k]["likes"] = $v["likes"];
            $res_theme [$k]['down_count'] = $v['down'];
            $res_theme[$k]["since"] = $v["since"];
            $imageid = $v["id"];

            $img_name = array("pre_face.webp", 'pre_lockscreen.webp', 'pre_icon1.webp', 'pre_icon2.webp');
            if ($v['is_faceimg']) {
                $res_theme [$k]["image"] = $this->webroot . '/attachs/theme' . $imgs[$imageid]["pre_face_small"];
            } else {
                $res_theme [$k]["image"] = $this->webroot . '/attachs/theme' . $imgs[$imageid]["pre_face_s"];
            }
            if ($v['package_type'] == 3) {
                $img_name = array(
                    "pre_face.webp",
                    'pre_lockscreen.webp',
                    'pre_icon1.webp',
                    'pre_icon2.webp',
                    'pre_icon3.webp',
                    'pre_icon4.webp',
                    'pre_icon5.webp',
                    'pre_icon6.webp',
                    'pre_icon7.webp',
                    'pre_icon8.webp',
                    'pre_icon9.webp',
                    'pre_icon10.webp',
                );
            }
            $i = 0;
            //$all_imgs = $this->array_sort($all_imgs, "img");
            foreach ($all_imgs as $ks => $vals) {
                if ($imageid == $vals["file_id"]) {
                    $str_tmp = strrpos($vals['img'], "/");
                    $tmp_url = substr($vals['img'], 0, $str_tmp + 1);
                    $tmp_name = substr($vals["img"], $str_tmp + 1);
                    //$res_theme[$k]["list_imgs"]["imgs"] [] = $this->webroot . "/attachs" . $vals["img"];
                    if ($tmp_name != "pre_face_small.webp") {
                        $res_theme[$k]["list_imgs"]["imgs"] [] = $this->webroot . "/attachs/theme" . $tmp_url . $img_name[$i];
                        $res_theme[$k]["list_imgs"] ["full_imgs"][] = $this->webroot . "/attachs/theme" . $tmp_url . "full-scale/" . $img_name[$i];
                        $i++;
                    }
                }
            }
        }

        return $res_theme;
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
