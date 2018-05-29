<?php

class UpnewController extends Admin_BaseController {

    private function __inits() {
        $this->webroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->roots = Yaf_Application::app()->getConfig()->staticroot;
        $this->staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;
        $this->upload_dir = BASE_PATH . '../attachs/theme/attachs/wallpaper/';
        $this->perpage = 12;
    }

    public function indexAction() {
        $this->__inits();
        $page = $this->getInput("page")? : 1;
        $res = Theme_Service_Upnew::getAllNum($page, $this->perpage);
        $data = $this->mk_data($res);
        $count = Theme_Service_Upnew::getCountNum();
        $this->showPages($count, $page);
        $this->assign("meunOn", "zt_newupss");
        $this->assign("file", $data);
        $this->assign("upnew", $this->webroot . '/Admin/upnew/addnews');
    }

    public function addnewsAction() {
        $n = intval($this->getPost("number"))? : 0;

        $res = Theme_Service_Upnew::insert($n);

        if (!$res) $this->output(-1, '操作失败');
        echo $res;
        exit;
    }

    private function mk_data($data) {
        if (!$data) return null;

        foreach ($data as $key => $v) {
            $tem[$key]["uptime"] = explode("_", $v)[0];
            $tem[$key]["num"] = explode("_", $v)[1];
        }

        return $tem;
    }

}
