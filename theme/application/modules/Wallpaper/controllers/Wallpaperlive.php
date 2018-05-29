<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

class WallpaperliveController extends Wallpaper_BaseController {

    /**
     * 初始化常量
     */
    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->webrootDown = Yaf_Application::app()->getConfig()->webroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
    }

    private $wallpaperType = array("1" => "微乐", "2" => "梦象");

    public function indexAction() {
        $pageNum = $this->getInput("pageNum")? : 1000;
        $liveType = $this->getInput("typeids")? : 1;

        $page = $this->getInput("page")? : 1;
        $limit = ($page - 1) * $pageNum;

        if ($liveType == "all") {
            $where = " wallpaperlive_status=4 order by wallpaperlive_onlinetime DESC limit $limit,$pageNum";
        } else {
            $where = " wallpaperlive_type in($liveType) and wallpaperlive_status=4 order by wallpaperlive_onlinetime DESC limit $limit,$pageNum";
        }
        $fileds = "wallpaperlive_id";
        $wallpaper_list = Theme_Service_Wallpaperlive::getListByWhere($where, $fileds);
        $wallpaper_ids = $this->getidsList($wallpaper_list);
        print_r(json_encode(array("wallpaperliveList" => $wallpaper_ids)));
        exit;
    }

    public function infoAction() {
        $ids = $this->getInput("ids");
        //$ids = "13,16,17";
        $wallpapers = Theme_Service_Wallpaperlive::getListByids($ids, true, "*");
        $wallpaper_info = $this->mk_image_url($wallpapers);

        print_r(json_encode(array("wallpaperlive" => $wallpaper_info)));
        exit;
    }

    public function getEnginAction() {
        $EnginName = array("1" => "微乐", "2" => "梦象");

        $this->__inits();
        $where = "status=1";
        $res = Theme_Service_Wallpaperlive::getlistEngin($where);
        foreach ($res as &$v) {
            $v['path'] = $this->downloadroot . '/Engin/' . $v['path'];
            $v['engineName'] = $EnginName[$v['type']];
            unset($v['id']);
            //unset($v['name']);
            unset($v['status']);
            //unset($v['update_time']);
            unset($v['text']);
        }
        print_r(json_encode(array("EnginInfo" => $res)));
        exit;
    }

    public function addlikeAction() {
        $liveId = $this->getInput("wallpaperliveId");
        $res = Theme_Service_Wallpaperlive::update_addLike($liveId);
        print_r(json_encode($res));
        exit;
    }

    //下载前;
    public function downPreAction() {
        echo json_encode(array("info" => 1));
        exit;
    }

    public function addDownAction() {
        $liveId = $this->getInput("wallpaperliveId");
        $res = Theme_Service_Wallpaperlive::update_addDown($liveId);
        print_r(json_encode($res));
        exit;
    }

    /**
     * 取动态壁纸引擎;
     */
    public function getEnginTypeAction() {
        $typeId = $this->getInput("typeId");
    }

    public function getlikesAction() {
        $liveId = $this->getInput("wallpaperliveId");

        $where = "wallpaperlive_id=$liveId";
        $filde = "wallpaperlive_like,wallpaperlive_down";
        $res = Theme_Service_Wallpaperlive::getListByWhere($where, $filde);

        print_r(json_encode($res[0]));
        exit;
    }

    private function getidsList($wallpaperlist) {
        foreach ($wallpaperlist as $v) {
            $ids [] = $v["wallpaperlive_id"];
        }
        return $ids;
    }

    private function mk_image_url($data) {
        if (!$data) return null;
        $this->__inits();



        foreach ($data as &$v) {
            $v['wallpaperlive_uploadtime'] = $v['wallpaperlive_onlinetime'];
            $v["wallpaperlive_path"] = $this->downloadroot . '/wallpaperlive/' . $v["wallpaperlive_path"];
            $v["wallpaperlive_auth"] = $this->wallpaperType[$v["wallpaperlive_type"]];
            if ($v["wallpaperlive_url_image"]) {
                $tem = explode(",", $v["wallpaperlive_url_image"]);

                if (count($tem) > 1) {
                    $tems = array("2.jpg", "1.jpg");
                    foreach ($tem as $key => $val) {
                        //$v["url"][] = $this->webroot . '/attachs/theme/livepaper/' . $val;
                        $str_tmp = strrpos($val, "/");
                        $tmp_url = substr($val, 0, $str_tmp + 1);
                        $tmp_name = substr($val, $str_tmp + 1);
                        $v["url"][] = $this->webroot . '/attachs/theme/livepaper/' . $tmp_url . $tems[$key];
                        if ($key >= 1) break;
                    }
                } else {
                    $v["url"][] = $this->webroot . '/attachs/theme/livepaper/' . $v["wallpaperlive_url_image"];
                    $v["url"][] = $this->webroot . '/attachs/theme/livepaper/' . $v["wallpaperlive_url_image"];
                }
            }
        }
        return $data;
    }

    public function getSubjectAction() {
        $this->__inits();
        $id = $this->getInput('sid');

        $subject = Theme_Service_Livewallpapersubject::get($id);

        //$subject['cover'] = $this->webroot.'/attachs/theme/'.$subject['cover'];
        $return = array();
        if ($subject["category"] == 0) {
            $ids = json_decode($subject['images']);
            foreach ($ids as $key => $id) {
                if (!$id) {
                    unset($ids[$key]);
                }
            }
            if (!empty($ids)) {
                $wallpaper = Theme_Service_Wallpaperlive::gets('wallpaperlive_id', $ids);
                foreach ($wallpaper as $k => $v) {
                    if ($wallpaper[$k]['wallpaperlive_url_image']) {
                        $img_paths = explode(",", $wallpaper[$k]['wallpaperlive_url_image']);
                        $wallpaper[$k]['url'][0] = $this->webroot . "/attachs/theme/livepaper/" . $img_paths[1];
                        $wallpaper[$k]['url'][1] = $this->webroot . "/attachs/theme/livepaper/" . $img_paths[0];
                    }
                    $wallpaper[$k]["wallpaperlive_path"] = $this->downloadroot . '/wallpaperlive/' . $v["wallpaperlive_path"];
                    $wallpaper[$k]['wallpaperlive_onlinetime'] = date('Y-m-d H:i:s', $v['wallpaperlive_onlinetime']);
                    $wallpaper[$k]['wallpaperlive_uploadtime'] = date('Y-m-d H:i:s', $v['wallpaperlive_uploadtime']);
                }
            }
            $return = $wallpaper;
        } else {

            $subjectImages = $this->webroot . "/attachs/theme" . $subject['images'];
            $return['conn'] = $subject['description'];
            $return['url'][0] = $this->webroot . "/attachs/theme" . $subject['images'];
        }


        //Common::v($return);
        echo json_encode($return);
        exit;
    }

}
