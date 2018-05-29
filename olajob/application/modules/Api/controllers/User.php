<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class UserController
 */

class UserController extends Api_BaseController {

    public function attrAction() {
        $attrs = array();
        $attrs["education"] = Ola_Service_User::education();
        $attrs["sex"] = Ola_Service_User::sex();
        array_walk($attrs, function(&$val){
            $val = Ola_Service_Job::format($val);
        });
        $this->output(0, "success", $attrs);

    }

    /**
     *login action
     */
    public function login_postAction() {
        $info = $this->getPost(array("phone", "password"));
        if (!$info["phone"]) $this->output(-1, "用户名不能为空");
        if (!$info["password"]) $this->output(-1, "登录密码不能为空");

        $user = Ola_Service_User::login($info["phone"], $info["password"]);
        if ($user["code"] < 0) $this->output(-1, $user["msg"]);
        $refer = Util_Cookie::get('REFER', true);
        Util_Cookie::delete('REFER');
        $url = $refer ? $refer : Common::getWebRoot()."/front/user/center";
        $this->output(0, "登录成功", array("redirect"=>$url));
    }

    /**
     * sms action
     */
    public function sms_postAction() {
        $phone = $this->getPost("phone");
        if (!Common::checkMobile($phone))  $this->output(-1, "手机号码不正确.");

        $cache = COmmon::getCache();
        $sms_key = "OLA_SMS_". $phone;

        if ($cache->get($sms_key)) {
            $this->output(-1, "验证码已经发送，请不要重复发送.");
        }

        $code = mt_rand(1000, 9999);
        $cache->set($sms_key, $code, 60 * 2);
        $ret = Common::sms($phone, "欢迎使用ola兼职。您的注册验证码是".$code."两分钟内输入有效。如非本人操作，请忽略此信息。");
        if (!$ret) {
            $cache->delete($sms_key);
            $this->output(-1, "验证码发送失败");
        }

        $this->output(0, "验证码已经发送");

    }

    /**
     * update user profile
     */
    public function profile_postAction() {
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "参数错误", array("redirect"=>"/front/user/login"));

        $info = $this->getInput(array("nickname", "realname", "sex", "birthday", "education", "phone", "description"));
        if (!$info["nickname"]) $this->output(-1, "用户昵称不能为空");
        if (!$info["realname"]) $this->output(-1, "姓名不能为空");
        if (!$info["sex"]) $this->output(-1, "请选择性别");
        if (!$info["birthday"]) $this->output(-1, "请选择出生日期");
        if (!$info["education"]) $this->output(-1, "请选择学历");
        if (!Common::checkMobile($info["phone"])) $this->output(-1, "请输入正确的手机号码");
        if (Util_String::strlen($info['description']) > 500)  $this->output(-1, "个人简介不能超过500字");

        $ret = Ola_Service_User::update($info, $user["id"]);
        if (!$ret) $this->output(-1, "修改个人资料失败.");
        $this->output(0, "资料更新成功.", array("redirect"=>Common::getWebRoot() . "/front/user/center"));
    }


    /**
     *
     */
    public function register_postAction() {
        $info = $this->getPost(array( "phone", "code", "password"));

        if (!Common::checkMobile($info["phone"])) $this->output(-1, "请输入正确的手机号码");
        if (strlen($info["password"]) < 6) $this->output(-1, "请输入至少6位密码");
        if (strlen($info["password"]) > 16) $this->output(-1, "密码长度不能超过16位");

        $user = Ola_Service_User::getBy(array('phone'=>$info['phone']));
        if($user) $this->output(-1, "该手机号码已注册");

        //valid sms code
        $cache = Common::getCache();
        $sms_key = "OLA_SMS_". $info["phone"];
        $code = $cache->get($sms_key);
        if(!$code) $this->output(-1, '验证码已过期，请重新获取');
        if ($code != $info["code"]) $this->output(-1, "验证码不正确");

        //register user
        $ret = Ola_Service_User::add($info);
        if(!$ret) $this->output(-1, "注册失败");

        $refer = Util_Cookie::get('REFER', true);
        Util_Cookie::delete('REFER');

        //登录
        Ola_Service_User::login($info['phone'], $info['password']);
        
        $url = $refer ? $refer : Common::getWebRoot()."/front/user/center";
        $this->output(0, "注册成功", array("redirect"=>$url));
    }


    public function publishedAction() {
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "参数错误");

        $perpage = 20;
        $page = intval($this->getInput("page"));
        if (!$page) $page = 1;

        list($total, $list) = Ola_Service_Job::getList($page, $perpage, array("user_id"=>$user["id"]), array('status'=>'ASC','create_time'=>'DESC'));
        array_walk($list, "Ola_Service_Job::detail_info");
        array_walk($list, function(&$val){
            $val["users_link"] = Common::getWebRoot()."/front/user/my_users?job_id=".$val["id"];
        });

        $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$list, 'hasnext'=>$hasnext, 'curpage'=>$page));

    }

    public function favoritedAction() {
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "参数错误");

        $perpage = 20;
        $page = intval($this->getInput("page"));
        if (!$page) $page = 1;

        list($total, $list) = Ola_Service_Favorite::getList($page, $perpage, array("user_id"=>$user["id"]), array('create_time'=>'DESC'));

        $temp = array();
        foreach ($list as $val) {
            $job = Ola_Service_Job::get($val["job_id"]);
            Ola_Service_Job::detail_info($job);
            $temp[] = $job;
        }

        $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    public function signupedAction() {
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "参数错误");

        $perpage = 20;
        $page = intval($this->getInput("page"));
        if (!$page) $page = 1;

        list($total, $list) = Ola_Service_Signup::getList($page, $perpage, array("user_id"=>$user["id"]), array('status'=>'DESC', 'create_time'=>'DESC'));

        $temp = array();
        foreach ($list as $val) {
            $job = Ola_Service_Job::get($val["job_id"]);
            Ola_Service_Job::detail_info($job);
            $job["signin_status"] = $val["status"];
            $temp[] = $job;
        }

        $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    public function usersAction() {
        $perpage = 20;
        $page = intval($this->getInput("page"));
        $job_id = $this->getInput("job_id");

        $job = Ola_Service_Job::get($job_id);
        if ($job["user_id"] !== $this->userInfo["id"]) $this->output(-1, "参数错误");

        list($total, $users) = Ola_Service_Signup::getList($page, $perpage, array("job_id"=>$job_id), array('status'=>'DESC','create_time'=>'DESC'));

        $temp = array();
        foreach($users as $val) {
            $user = Ola_Service_User::get($val["user_id"]);
            $birthday = explode('-', $user["birthday"]);
            $temp[] = array(
                "headimgurl"=>Common::getAttachPath().$user["headimgurl"],
                "nickname"=>$user["nickname"],
                "realname"=>$user["realname"],
                "link"=>Common::getWebRoot()."/front/user/my_userInfo?user_id=".$user["id"]."&job_id=".$job_id,
                "age"=>date("Y") - $birthday[0],
                "sex"=>Ola_Service_Job::sex($user["sex"]),
                "education"=>Ola_Service_User::education($user["education"]),
                "description"=>$val["description"],
                "status"=>$val["status"]
            );
        }

        $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$temp, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

    public function refuseAction() {
        $id = intval($this->getInput("id"));
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "参数错误");

        $sign = Ola_Service_Signup::get($id);
        if (!$sign) $this->output(-1, "参数错误");

        $job = Ola_Service_Job::getBy(array("id"=>$sign["job_id"]));
        if ($job["user_id"] !== $user["id"]) $this->output(-1, "参数错误");

        $ret = Ola_Service_Signup::update(array("status"=>1), $id);
        if (!$ret) $this->output(-1, "操作失败");

        Ola_Service_User::updateTJ('refuse_num', $user['id']);

        $this->output(0, "操作成功");
    }

    public function acceptAction() {
        $id = intval($this->getInput("id"));
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "参数错误");

        $sign = Ola_Service_Signup::get($id);
        if (!$sign) $this->output(-1, "参数错误");

        $job = Ola_Service_Job::getBy(array("id"=>$sign["job_id"]));
        if ($job["user_id"] !== $user["id"]) $this->output(-1, "参数错误");

        $ret = Ola_Service_Signup::update(array("status"=>3), $id);
        if (!$ret) $this->output(-1, "操作失败");

        Ola_Service_User::updateTJ('pass_num', $user['id']);

        $this->output(0, "操作成功");
    }

    /**
     *
     */
    public function logoutAction() {
        $ret = Ola_Service_User::logout();
        if(!$ret) $this->output(-1, "登出失败");

        $this->output(0,"登出成功");
    }

}