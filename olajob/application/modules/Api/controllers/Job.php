<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class JobController
 */
class JobController extends Api_BaseController
{
    public $perpage = 20;

    public function adAction() {
        list(, $ads) = Ola_Service_Ad::getList(0, 3, array("status"=>1));
        array_walk($ads, function(&$val){
           $val = array(
               "id"=>$val["id"],
               "title"=>$val["title"],
               "img"=>Common::getAttachPath() . $val["img"],
               "link"=>$val["link"],
               );
        });
        $this->output(0, "success", $ads);
    }


    public function paramsAction()
    {
        list(, $areas) = Ola_Service_Area::getList(0, 20);
        list(, $category) = Ola_Service_Category::getList(0, 20, array('status'=>1));

        array_walk($category, function(&$val){
            $val["img"] = Common::getAttachPath() . $val["img"];
        });

        $sort = array(array("sort"=>"PUB_ASC","name"=>"发布时间"));

        $this->output(0, '', array("area"=>$areas, "category"=>$category, "sort"=>$sort));

    }

    public function attrAction() {
        $attrs = array();

        list(, $attrs["area"]) = Ola_Service_Area::getList(0, 20);
        list(, $attrs["category"]) = Ola_Service_Category::getList(0, 20, array('status'=>1));
        $attrs["education"] = Ola_Service_User::education();
        $attrs["sex"] = Ola_Service_Job::sex();
        $attrs["mysex"] = Ola_Service_User::sex();
        $attrs["checkType"] = Ola_Service_Job::checkType();
        $attrs["jobType"] = Ola_Service_Job::jobType();
        $attrs["moneyType"] = Ola_Service_Job::moneyType();

        array_walk($attrs, function(&$val){
            $val = Ola_Service_Job::format($val);
        });
        $this->output(0, "success", $attrs);

    }

    public function listAction()
    {
        $page = intval($this->getInput("page"));
        $area_id = $this->getInput("area");
        $category_id = $this->getInput("category");
        $sort = $this->getInput("sort");

        if (!$page) $page = 1;
        $search = array();
        if ($area_id) $search["area_id"] = $area_id;
        if ($category_id) $search["category_id"] = $category_id;
        $search['status'] = 1;
//        if ($sort) $search["sort"] = $sort;

        list($total, $jobs) = Ola_Service_Job::getList($page, $this->perpage, $search, array('sort'=>'DESC', 'create_time'=>'DESC'));
        array_walk($jobs, "Ola_Service_Job::detail_info");

        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$jobs, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }


    public function publishAction()
    {
        $info = $this->getPost(array(
            "category_id",
            "area_id",
            "title",
            "job_type",
            "company_name",
            "money",
            "money_type",
            "check_type",
            "sex",
            "address",
            "phone",
            "description",
            "author"));
        $user = $this->getUserInfo();
        if (!$user) $this->output(-1, "获取用户信息失败.");

        $info["user_id"] = $this->userInfo["id"];
        $info["status"] = 2;

        $job = Ola_Service_Job::getBy(array('user_id'=>$user['id'],'title'=>$info['title'], 'category_id'=>$info['category_id']));
        if($job)  $this->output(-1, "该信息已发布");
        $this->cookData($info);

        $ret = Ola_Service_Job::add($info);
        if (!$ret) $this->output(-1, "兼职发布失败");

        Ola_Service_User::updateTJ('publish_num', $user['id']);
        $this->output(0, "兼职发布成功,请等待审核", array("redirect"=>Common::getWebRoot(). "/front/user/my_publish"));
    }

    /**
     *
     */
    public function favoriteAction() {
        $id = intval($this->getInput("id"));
        if (!$id) $this->output(-1, "参数错误");

        $user = $this->getUserInfo();
        if (!$user) {
            $refer = Common::getWebRoot().'/front/index/info?id='.$id;
            Util_Cookie::set("REFER", $refer, true);
            $this->output(-1, "请登录", array("redirect"=>"/front/user/login"));
        }

        $isfavorite = Ola_Service_Report::getBy(array("user_id"=>$user["id"], "job_id"=>$id));
        if ($isfavorite) $this->assign(-1, "此兼职信息您已经收藏");

        $ret = Ola_Service_Favorite::add(array("job_id"=>$id, "user_id"=>$user["id"]));
        if (!$ret) $this->output(-1, "收藏失败");

        //updat tj
        Ola_Service_User::updateTJ('favorite_num', $user['id']);
        Ola_Service_Job::icrement("favorite_count", $id);

        $this->output(0, "收藏成功");
    }

    /**
     *
     */
    public function signupAction() {
        $id = intval($this->getInput("id"));
        $description = $this->getPost("description");

        $job = Ola_Service_Job::get($id);
        if(!$job) $this->output(-1, "职位不存在");
        if($job['status'] != 1)   $this->output(-1, "无法报名");

        $info = $this->getPost(array("realname", "sex", "education", "birthday"));
        if (!$id) $this->output(-1, "参数错误");

        $user = $this->getUserInfo();
        if (!$user) {
            $refer = Common::getWebRoot().'/front/index/info?id='.$id;
            Util_Cookie::set("REFER", $refer, true);
            $this->output(-1, "请登录", array("redirect"=>"/front/user/login"));
        }

        $issignup = Ola_Service_Signup::getBy(array("user_id"=>$user["id"], "job_id"=>$id));
        if ($issignup) $this->assign(-1, "此兼职信息您已经报名");

        $userInfo = Ola_Service_User::get($user["id"]);
        //if (!$userInfo["realname"]) {
            if (!$info["realname"]) $this->output(-1, "姓名不能为空");
            if (!$info["sex"]) $this->output(-1, "性别不能为空");
            if (!$info["education"]) $this->output(-1, "学历不能为空");
            if (!$info["birthday"]) $this->output(-1, "出生日期不能为空");

            Ola_Service_User::update($info, $userInfo["id"]);
        //}

        $ret = Ola_Service_Signup::add(array("job_id"=>$id, "user_id"=>$user["id"], "description"=>$description, 'status'=>2));
        if (!$ret) $this->output(-1, "报名失败");

        Ola_Service_User::updateTJ('signup_num', $user['id']);
        Ola_Service_Job::icrement("signup_count", $id);
        $this->output(0, "报名成功", array("redirect"=>Common::getWebRoot()."/front/index/info?id=".$id));
    }

    /**
     *
     */
    public function reportAction() {
        $id = intval($this->getInput("id"));
        if (!$id) $this->output(-1, "参数错误");

        $user = $this->getUserInfo();
        if (!$user) {
            $refer = Common::getWebRoot().'/front/index/info?id='.$id;
            Util_Cookie::set("REFER", $refer, true);
            $this->output(-1, "请登录", array("redirect"=>"/front/user/login"));
        }

        $isreport = Ola_Service_Report::getBy(array("user_id"=>$user["id"], "job_id"=>$id));
        if ($isreport) $this->assign(-1, "此兼职信息您已经举报");

        $ret = Ola_Service_Report::add(array("job_id"=>$id, "user_id"=>$user["id"]));
        if (!$ret) $this->output(-1, "举报失败");

        Ola_Service_Job::icrement("report_count", $id);

        $this->output(0, "举报成功");
    }

    private function cookData($data)
    {
        if (!$data["title"]) $this->output(-1, "请输入标题");
        if (!$data["category_id"]) $this->output(-1, "请选择分类");
        if (!$data["money"]) $this->output(-1, "请输入待遇");
        if (!$data["money_type"]) $this->output(-1, "请选择待遇单位");
        if (!$data["check_type"]) $this->output(-1, "请选择结算方式");
        if (!$data["sex"]) $this->output(-1, "请选择性别");
        if (!$data["area_id"]) $this->output(-1, "请选择区域");
        if (!$data["job_type"]) $this->output(-1, "请选择职位来源");
        // if (!$data["address"]) $this->output(-1, "请输入具体地址");
        if ($data["job_type"] == 2) {
            if (!$data["company_name"]) $this->output(-1, "请输入公司名称");
        }
        if (!$data["author"]) $this->output(-1, "请填写负责人");
        if (!$data["phone"]) $this->output(-1, "请输入联系方式");
        if (Util_String::strlen($data['description']) > 500)  $this->output(-1, "个人简介不能超过500字");
        return $data;
    }

}