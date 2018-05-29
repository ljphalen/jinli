<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ZhengController extends Front_BaseController {

    public function init() {
        $this->auth = false;
        parent::init();
    }

    public function indexAction() {
        $id = $this->getInput("id");
        $act = $this->getInput("act");

        $open_id = $this->getOpenId();
        if (!$this->userInfo["nickname"] && $act == "zheng") {
            $url = Common::currentPageUrl();
            Util_Cookie::set("LR", $url, true);
            $this->redirect("/api/weixin/openid?chk=1");
            exit;
        }

        $mid = Common::encrypt($this->userInfo['id'],"ENCODE");
        $this->assign('mid', $mid);

        $user = Fj_Service_User::getUser(Common::encrypt($id, "DECODE"));

        $this->assign('id', $id);
        $this->assign("user", $user);
    }
}