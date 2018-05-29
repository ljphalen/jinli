<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class Admin_BaseController extends Common_BaseController {

    public $userInfo;
    public $actions = array();
    public $imageurl;
    public $ucenterLoginUrl = '';

    /**
     *
     * Enter description here ...
     */
    public function init() {
        parent::init();


        //user center init
        $this->ucenterInit();

        $this->checkRight();

        $this->checkToken();

        $this->checkCookieParams();
        $this->updateAppCache();

        $this->imageurl = Yaf_Application::app()->getConfig()->fontcroot . "/attachs/theme";
        $this->webroot = Yaf_Application::app()->getConfig()->fontcroot;
        $this->wallpaperPath = Yaf_Application::app()->getConfig()->wallpaperPath;


        $this->downloadroot = Yaf_Application::app()->getConfig()->downloadroot;

        $this->getMenu();
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        $this->assign("adminroot", $adminroot);
        $this->assign("attachPath", $this->webroot . '/attachs/theme');
        $staticroot = Yaf_Application::app()->getConfig()->adminstaticroot;
        $this->assign("staticPath", $staticroot . '/freetribe/');


        $this->assign("downloadroot", $this->downloadroot);
        $this->assign("imageurl", $this->imageurl);

        //-------------------------------------------------------------
        $userid = $this->userInfo["uid"];
        $groupid = $this->userInfo['groupid'];


        if (!in_array($groupid, array(0, 4))) {
            $res = Theme_Service_LogsMsg::getMsglog($userid, $groupid, 1);
            $this->assign("msgNum", $res['new']);
        }
    }

    private function ucenterInit() {
        $ucenterRoot = Yaf_Application::app()->getConfig()->ucenterroot;
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        $appId = Common::getConfig("appConfig", "appid");
        $randStr = Common::randStr(8);

        $this->ucenterLoginUrl = $ucenterRoot . '/members/start?client_id=' . $appId .
                '&redirect_uri=' . $adminroot . '/Admin/Index/index&response_type=code&state=' . $randStr;
        $ucenterLogoutUrl = $ucenterRoot . '/member/logout?redirect_uri=' . $adminroot . '/Admin/Login/Logout';
        //$ucenterLogoutUrl = '/Admin/Login/Logout';
        $this->assign('ucenterLogoutUrl', $ucenterLogoutUrl);

        //user center login
        $this->checkCookie();
        //$this->oauth();

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        $accessKey = $module . '/' . $controller . '/' . $action;
        $accessTable = array(
            'Admin/File/add',
            'Admin/Wallpapermyupload/index',
            'Admin/Wallpapermy/liveupload',
            'Admin/Clockmy/upload',
        );

        if (in_array($accessKey, $accessTable)) {
            $userInfo = Admin_Service_User::isLogin();
            if ($userInfo['admin_type'] == 1 && empty($userInfo['nick_name'])) {
                $this->redirect('/Admin/Ucenter/edit');
            }
        }
    }

    //用户中心cookie解密认证
    private function checkCookie() {
        $des = new Util_TripleDES();
        $r = $des->check_cookie($_COOKIE);

        if (empty($r['uid'])) return false;

        //check theme user system, if exists then continue, else, add this user
        $ucid = $r['uid'];

        $this->ucenterLogin($ucid);
    }

    //用户中心oauth2认证登录
    private function oauth() {
        $code = $this->getInput('code');
        $state = $this->getInput('state');
        if (!empty($code) && !empty($state)) {
            $des = new Util_TripleDES();
            $access_token = $des->get_token($code);
            $r = $des->verify_do($access_token);

            if (empty($r['u'])) return false;

            //check theme user system, if exists then continue, else, add this user
            $ucid = $r['u'];

            $this->ucenterLogin($ucid);
        }
    }

    //用户中心用户登录
    private function ucenterLogin($ucid) {
        $user = Common::getDao("Admin_Dao_User")->getByUcid($ucid);

        //vdump($user);
        if (empty($user)) {
            $hash = substr(md5(time()), 0, 6);
            $username = substr(md5($ucid), 0, 16);
            $password = $hash;
            $user = array(
                'ucenter_uid' => $ucid,
                'registertime' => time(),
                'username' => $username,
                'password' => md5(md5($hash) . $hash),
                'hash' => $hash,
                'groupid' => 1,
                'status' => 1,
                'admin_type' => 1,
            );
            Common::getDao("Admin_Dao_User")->insert($user);
            $user['uid'] = Common::getDao("Admin_Dao_User")->getLastInsertId();
        } else {
            //do login
            $username = $user['username'];
            $password = $user['hash'];
        }

        //already login, exit;
        if (Admin_Service_User::isLogin()) return false;

        Admin_Service_User::login($username, $password);
    }

    /**
     *
     * @param type $count 总条数
     * @param type $page   刚前页
     * @param type $Num    每页几条
     * @param type $pages  显示几个页码;
     * @param type $tid  一级分类;
     * @param type $pare  更多参数,k=>v;
     */
    protected function showPages($count, $page, $Num = 5, $pages = 10, $tid = 0, $pare = array()) {
        $pageall = ceil($count / $Num);

        if ($page > $pages / 2) $pagestart = $page - 5;
        else $pagestart = 1;

        if (is_array($pare)) {
            foreach ($pare as $k => $v) {
                $pares .=$k . '=' . $v . "&";
            }
        } else {
            $pares = FALSE;
        }
        $this->assign("tid", $tid);
        $this->assign("pare", $pares);
        $this->assign("pages", $pages);
        $this->assign("pagestart", $pagestart);
        $this->assign("pageall", $pageall);
        $this->assign("page", $page);
        $this->assign("count", $count);
    }

    protected function getmenu() {
        list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();
        $submeun = $this->submenu($this->userInfo['groupid']);
        $this->assign("submenu", $submeun);

        $nick_name = $this->userInfo['nick_name'] ? $this->userInfo['nick_name'] : $this->userInfo['username'];

        //Common::v($usermenu);
        $this->assign('mainmenu', $usermenu);
        $this->assign('username', $nick_name);
        $this->assign("groupid", $this->userInfo['groupid']);
        $this->assign("usermenu", $usermenu['_Admin_System']['items'][0]['items']);
    }

    /**
     * 检查token
     */
    protected function checkToken() {
        if (!$this->getRequest()->isPost()) return true;
        $post = $this->getRequest()->getPost();


        if (!$post) {
            $post['token'] = $this->getInput("token");
        }
        $result = Common::checkToken($post['token']);
        if (Common::isError($result)) $this->output(-1, $result['msg']);
        return true;
    }

    public function updateAppCache() {
        $action = $this->getRequest()->getActionName();
        if ($this->appCacheName && in_array($action, array('add_post', 'edit_post', 'delete', 'editstatus_post', 'update_post'))) {
            Theme_Service_Config::setValue($this->appCacheName, Common::getTime());
        }
    }

    /**
     *
     * Enter description here ...
     */
    public function checkRight() {
        $this->userInfo = Admin_Service_User::isLogin();
        $this->assign('userInfo', $this->userInfo);
        if (!$this->userInfo && !$this->inLoginPage()) {
            $this->redirect($this->ucenterLoginUrl);
        } else {
            //如果是超级管理员，则给最高权限
            if ($this->isAdmin()) {
                return true;
            }
            $module = $this->getRequest()->getModuleName();
            $controller = $this->getRequest()->getControllerName();
            $action = $this->getRequest()->getActionName();

            list($usermenu, $mainview, $usersite, $userlevels) = $this->getUserMenu();
            $mc = "_" . $module . "_" . $controller;

            $mca = "_" . $module . "_" . $controller . "_" . $action;

            /* print_r($mc);
              echo "<br/>";
              print_r($mca);
              echo "<br/>";
              print_r($userlevels);
              exit; */

            if (!in_array($mc, $userlevels) && !in_array($mca, $userlevels)) {

                exit('没有权限');
            }
        };
    }

    /**
     * 是否为超级管理员
     * @return boolean
     */
    private function isAdmin() {
        $userInfo = $this->userInfo;
        return $userInfo['username'] = 'admin';
    }

    /**
     *
     * Enter description here ...
     */
    public function inLoginPage() {
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();

        if ($module == 'Admin' && $controller == 'Login' && ($action == 'index' || $action == 'login')) {
            return true;
        }
        return false;
    }

    /**
     *
     * Enter description here ...
     */
    public function getUserMenu() {
        $userInfo = Admin_Service_User::getUser($this->userInfo['uid']);


        $groupInfo = array();
        if ($userInfo['groupid'] == 0) {
            $groupInfo = array('groupid' => 0);
        } else {
            $groupInfo = Admin_Service_Group::getGroup($userInfo['groupid']);
        }
        $menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);

        list($usermenu, $mainview, $usersite, $userlevels) = $menuService->getUserMenu($groupInfo);
        array_push($userlevels, "_Admin_Initiator", "_Admin_Index", '_Admin_Login');
        return array($usermenu, $mainview, $usersite, $userlevels);
    }

    public function cookieParams() {
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        $name = sprintf('%s_%s_%s', $module, $controller, $action);

        $tmp = array();
        $not = array('token', 's');
        foreach ($_REQUEST as $key => $value) {
            if (!in_array($key, $not)) $tmp[$key] = $this->getInput($key);
        }
        Util_Cookie::set($name, Common::encrypt(json_encode($tmp)), false, Common::getTime() + (5 * 3600));
    }

    /**
     *
     * @return boolean
     */
    public function checkCookieParams() {
        $s = $this->getInput('s');

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();
        $name = sprintf('%s_%s_%s', $module, $controller, $action);

        $params = json_decode(Common::encrypt(Util_Cookie::get($name), 'DECODE'), true);

        if (count($params) && $s) {
            $adminroot = Yaf_Application::app()->getConfig()->adminroot;

            $url = sprintf('%s/%s/%s/%s?%s', $adminroot, $module, $controller, $action, http_build_query($params));
            $this->redirect($url);
        }
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $code
     * @param unknown_type $msg
     * @param unknown_type $data
     */
    public function output($code, $msg = '', $data = array()) {
        exit(json_encode(array(
            'success' => $code == 0 ? true : false,
            'msg' => $msg,
            'data' => $data
        )));
    }

    protected function subMenu($gid) {
        $submenu = array(
            //------------套图管理 -------------------------------------------//
            "_Admin_Wallpapersets" => array(
                "conf" => array("id" => "daily", "ico" => "ico_menu8"),
                "menu" => array(
                    array(
                        "id" => "dailyList",
                        "name" => '每日精选列表',
                        "url" => "/Admin/wallpapersets/index"
                    ),
                    array(
                        "id" => "dailyAdd",
                        "name" => "发布每日精选",
                        "url" => "/Admin/wallpapersets/pubdaily"),
                )),
            //-------------------------------------------------------------//
            "_Admin_Wallpaperadminsubject" => array(
                "conf" => array("id" => "seminar", "ico" => "ico_menu5"),
                "menu" => array(
                    array(
                        "id" => "seminarList",
                        "name" => '专题列表',
                        "url" => "/Admin/wallpaperadminsubject/index",
                    ),
                    array(
                        "id" => "seminarAdd",
                        "name" => "添加专题",
                        "url" => "/Admin/wallpaperadminsubject/addsubject",
                    ),
                ),
            ),
            "_Admin_Wallpapersetnew" => array(
                "conf" => array("id" => "taotu", "ico" => "ico_menu2"),
                "menu" => array(
                    array(
                        "id" => "taotuList",
                        "name" => '套图列表',
                        "url" => "/Admin/wallpaperadmin/adminset",
                    ),
                    array(
                        "id" => "taotuPack",
                        "name" => "套图打包",
                        "url" => "/Admin/wallpaperadmin/addset",
                    ),
                ),
            ),
            //-----------------------------主题专题----------------------------------//
            "_Admin_Subject" => array(
                "conf" => array("id" => "special", "ico" => "ico_menu5"),
                "menu" => array(
                    array(
                        "id" => "specialList",
                        "name" => '顶部专题列表',
                        "url" => "/Admin/subject/index",
                    ),
                    array(
                        "id" => "pagespecialList",
                        "name" => '页面专题列表',
                        "url" => "/Admin/subject/pagelist",
                    ),
                    array(
                        "id" => "specialAdd",
                        "name" => "添加专题",
                        "url" => "/Admin/subject/add",
                    ),
                ),
            ),
            //--------------------时钟专题-------------------------------------------
            "_Admin_Clocksubject" => array(
                "conf" => array("id" => "szsubject", "ico" => "ico_menu5"),
                "menu" => array(
                    array(
                        "id" => "szsubjectList",
                        "name" => '专题列表',
                        "url" => "/Admin/clocksubject/index",
                    ),
                    array(
                        "id" => "szsubjectAdd",
                        "name" => "添加专题",
                        "url" => "/Admin/clocksubject/addsubject",
                    ),
                ),
            ),
            //--------------------动态壁纸引擎-------------------------------------------
            "_Admin_Wallpaperadminengin" => array(
                "conf" => array("id" => "bzengin", "ico" => "ico_menu4"),
                "menu" => array(
                    array(
                        "id" => "bzenginupload",
                        "name" => '添加引擎',
                        "url" => "/Admin/wallpaperadminengin/index",
                    ),
                    array(
                        "id" => "bzenginlist",
                        "name" => "引擎列表",
                        "url" => "/Admin/wallpaperadminengin/list",
                    ),
                ),
            ),
            //--------------------动态壁纸专题-------------------------------------------
            "_Admin_Livewallpapersubject" => array(
                "conf" => array("id" => "bzlwpsubject", "ico" => "ico_menu2"),
                "menu" => array(
                    array(
                        "id" => "bzlwpsubjectlist",
                        "name" => '专题列表',
                        "url" => "/Admin/livewallpapersubject/index",
                    ),
                    array(
                        "id" => "bzlwpsubjectadd",
                        "name" => "添加专题",
                        "url" => "/Admin/livewallpapersubject/add",
                    ),
                ),
            ),
            //---------------------支付中心 设计师统计信息--------------------------
            "_Admin_Paydesigner" => array(
                "conf" => array("id" => "paydesigner", "ico" => "ico_menu2"),
                "menu" => array(
                    array(
                        "id" => "paydesignersalelist",
                        "name" => '销售列表',
                        "url" => "/Admin/Pay/designer",
                    ),
                    array(
                        "id" => "paycheckpersonallist",
                        "name" => "设计师信息",
                        "url" => "/Admin/Paycheck/personallist",
                    ),
                    array(
                        "id" => "paycheckpersonalapply",
                        "name" => "设计师提现",
                        "url" => "/Admin/Paycheck/personalapply",
                    ),
                ),
            ),
            //---------------------支付中心 消息--------------------------
            "_Admin_Paymsg" => array(
                "conf" => array("id" => "paymsg", "ico" => "ico_menu2"),
                "menu" => array(
                    array(
                        "id" => "paymsgapply",
                        "name" => '提现申请',
                        "url" => "/Admin/paymsg/apply",
                    ),
                    array(
                        "id" => "paymsgchangeprice",
                        "name" => "价格修改",
                        "url" => "/Admin/paymsg/changeprice",
                    ),
                ),
            ),
        );


        if ($gid == 3) {
            $submenu['_Admin_Wallpapersetnew']['menu'][0]['name'] = "每日精选列表";
            $submenu['_Admin_Wallpapersetnew']['menu'][1]['name'] = "发布每天精选";
        }

        //财务
        if ($gid == 4) {
            $submenu["_Admin_Paydesigner"] = array(
                "conf" => array("id" => "paydesigner", "ico" => "ico_menu2"),
                "menu" => array(
                    array(
                        "id" => "paycheckpersonallist",
                        "name" => "设计师信息",
                        "url" => "/Admin/Paycheck/personallist",
                    ),
                    array(
                        "id" => "paycheckpersonalapply",
                        "name" => "设计师提现",
                        "url" => "/Admin/Paycheck/personalapply",
                    ),
                ),
            );
        }
        return $submenu;
    }

    public function mk_sqls($str) {
        $str = str_replace("and", "", $str);
        $str = str_replace("execute", "", $str);
        $str = str_replace("update", "", $str);
        $str = str_replace("count", "", $str);
        $str = str_replace("chr", "", $str);
        $str = str_replace("mid", "", $str);
        $str = str_replace("master", "", $str);
        $str = str_replace("truncate", "", $str);
        $str = str_replace("char", "", $str);
        $str = str_replace("declare", "", $str);
        $str = str_replace("select", "", $str);
        $str = str_replace("create", "", $str);
        $str = str_replace("delete", "", $str);
        $str = str_replace("insert", "", $str);
        $str = str_replace("alert", "", $str);
        $str = str_replace("  '", "", $str);
        $str = str_replace("\"", "", $str);

        $str = str_replace("or", "", $str);
        $str = str_replace(" = ", "", $str);
        $str = str_replace("%20", "", $str);
//echo $str;
        return $str;
    }

    protected function mk_imgPath($wallpaper) {

        if (!$wallpaper && is_array($wallpaper)) return 0;
        foreach ($wallpaper as &$vs) {
            if ($vs['wallpaper_online_time']) {
                $vs['wallpaper_online_time'] = date("Y-m-d H:i:s", $vs['wallpaper_online_time']);
            }
            $str_tmp = strrpos($vs['wallpaper_path'], "/");
            $tmp_url = substr($vs['wallpaper_path'], 0, $str_tmp + 1);
            $tmp_name = substr($vs["wallpaper_path"], $str_tmp + 1);

            $tmp_url = rtrim($tmp_url, '/');
            $vs["url"] = $this->wallpaperPath . '/attachs/wallpaper' . $tmp_url . '/thumbnail' . '/' . $tmp_name;
            $vs["wallpaper_path"] = $this->wallpaperPath . '/attachs/wallpaper' . $vs["wallpaper_path"];
            $name = Admin_Service_User::getUser($vs['wallpaper_user']);

            $vs["user_name"] = $name['nick_name']? : $name['username'];
        }

        return $wallpaper;
    }

}
