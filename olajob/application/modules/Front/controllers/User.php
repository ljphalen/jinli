<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class UserController
 */

class UserController extends Front_BaseController {

    public $actions = array(
        "registerUrl"=>"/front/user/register",
        "loginUrl"=>"/front/user/login",
        "signupUrl"=>"/front/user/my_signup",
        "favUrl"=>"/front/user/my_favorite",
        "jobuserUrl"=>"/front/user/jobusers",
        "pubUrl"=>"/front/user/my_publish",
        "profileUrl"=>"/front/user/profile",
        "profile_postUrl"=>"/api/user/profile_post",
        "publishUrl"=>"/front/user/publish",
        "usersUrl"=>"/front/user/my_users",
        "userInfoUrl"=>"/front/user/my_userInfo"
    );

    public function init() {
        parent::init();
        $action = $this->getRequest()->getActionName();

        if (!in_array($action, array("login", "register")) && !$this->userInfo) {
            $refer = Common::currentPageUrl();
            //Common::log('init:'.$refer, 'refer.log');
            Util_Cookie::set("REFER", $refer, true);
            $this->redirect(Common::getWebRoot()."/front/user/login");
        }
    }

    public function registerAction() {
        $this->assign("title","注册");
    }

    public function loginAction() {
        $this->assign("title","登录");
    }

    public function centerAction() {
        $info = Ola_Service_User::get($this->userInfo["id"]);

        $this->assign("info", $info);
        $this->assign("title", "个人中心");
    }

    public function signupAction() {
        $id = $this->getInput("id");
        $userInfo = Ola_Service_User::get($this->userInfo["id"]);

        $this->assign("user", $userInfo);
        $this->assign("id", $id);
        $this->assign("title", "在线报名");
    }

    public function publishAction() {
        $this->assign("title", "发布兼职");
    }

    public function my_usersAction() {
        $job_id = $this->getInput("job_id");

        $this->assign("job_id", $job_id);
        $this->assign("title", "报名人员列表");
    }

    public function my_signupAction() {
        $this->assign("title", "我报名的职位");
    }

    public function my_publishAction() {
        $this->assign("title", "我发布的职位");
    }

    public function my_favoriteAction() {
        $this->assign("title", "我收藏的职位");
    }

    public function my_userInfoAction() {
        $user_id = $this->getInput("user_id");
        $job_id = $this->getInput("job_id");

        $signup = Ola_Service_Signup::getBy(array("user_id"=>$user_id, "job_id"=>$job_id));
        if (!$signup) $this->output(-1, "参数错误");

        $user = Ola_Service_User::get($user_id);
        Ola_Service_User::deal_info($user);

        $this->assign("signup", $signup);
        $this->assign("user", $user);
        $this->assign("title", "个人信息");
    }


    public function profileAction() {
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "参数错误");

        $info = Ola_Service_User::get($user["id"]);
        $this->assign("info", $info);
        $this->assign("title","个人资料");
    }
}