<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class JobController
 */
class IndexController extends Front_BaseController
{
    public $perpage = 20;

    public $actions = array(
        "userCenter"=>"/front/user/center",
        "listUrl"=>"/api/job/list",
        "signupUrl"=>"/front/user/signup",
    );

    public function indexAction() {
        $this->assign("title","兼职列表");
    }

    public function infoAction() {
        $id = $this->getInput("id");
        if (!$id) $this->output(-1, "参数错误");

        $id = intval($this->getInput("id"));
        if (!$id) $this->output(-1, "参数错误");

        $info = Ola_Service_Job::get($id);
        Ola_Service_Job::detail_info($info);

        $user = Ola_Service_User::get($info["user_id"]);
        $this->assign("user", $user);

        $userInfo = $this->getUserInfo();
        if($userInfo) {
            $favorite = Ola_Service_Favorite::getBy(array("user_id"=>$userInfo["id"], "job_id"=>$info["id"]));
            $report = Ola_Service_Report::getBy(array("user_id"=>$userInfo["id"], "job_id"=>$info["id"]));
            $signup = Ola_Service_Signup::getBy(array("user_id"=>$userInfo["id"], "job_id"=>$info["id"]));
        }



        if(!$userInfo) {
            $info['phone'] = substr_replace($info['phone'], '****', 3, 4);
        }

        $refer = Common::currentPageUrl();
        Util_Cookie::set("REFER", $refer, true);

        $this->assign("favorited", $favorite ? true : false);
        $this->assign("reported", $report ? true : false);
        $this->assign("signup", $signup ? true : false);
        $this->assign("info", $info);
        $this->assign("userInfo", $userInfo);
        $this->assign("title", $info['title']." - 兼职详情");
    }

    public function delAction() {
        Util_Cookie::delete('OLAUNIID');
        Util_Cookie::delete('OLA-USER');
        exit('done');
    }


}