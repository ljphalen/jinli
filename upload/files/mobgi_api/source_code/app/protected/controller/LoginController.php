<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LoginController
 *
 * @author a
 */
$AppCt=Doo::loadController("AppDooController");
class LoginController extends DooController {
    private $AppDoo;
    public function __construct() {
        //parent::__construct();
        Doo::db()->reconnect("admin");
    }
    public function index(){
        if(Doo::session()->__get("isadmin")==1){//如果已经登陆则跳转
            header("Location: /reproduct/lists");
        }
        $data["rootUrl"]=Doo::conf()->MISC_BASEURL;
        $data["project_title"]=Doo::conf()->PROJECT_TITLE;
        $this->view()->render(Doo::conf()->TEMPLATE_PATH."/login", $data,Doo::conf()->TEMPLATE_COMPILE_ALWAYS);
    }
    //登陆
    public function login(){
        $username=htmlspecialchars($_POST["username"]);
        $password=htmlspecialchars($_POST["password"]);
        if(empty($username)){
            $this->AppDoo->showMsg("用户名为空");
        }
        if(empty($password)){
            $this->AppDoo->showMsg("密码为空");
        }
        $login=Doo::loadModel("datamodel/Admins",TRUE);
        
        //先检测帐号是否存在
        $sql = "SELECT count(1) as num FROM admins WHERE username='" . addslashes(htmlspecialchars(trim($username))) . "' ";
        $exist_result=Doo::db()->fetchRow($sql);
        if($exist_result['num'] == 0){
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('用户名不存在');history.go(-1)</script>";
            return;
        }
        
        //先检测帐号是否被锁定
        $sql = "SELECT lock_num FROM admins WHERE username='" . addslashes(htmlspecialchars(trim($username))) . "' LIMIT 1";
        $lock_result=Doo::db()->fetchRow($sql);
        $lock_num = $lock_result['lock_num'];
        if($lock_num>=4){
            header("Content-Type:text/html;charset=utf-8");
            echo "<script>alert('密码输入错误超过4次，您的帐号已被锁定');history.go(-1)</script>";
            return;
        }
        
        $sql = "SELECT * FROM admins AS admin
			LEFT JOIN roles AS role
				ON admin.role_id = role.id
			WHERE username='" . addslashes(htmlspecialchars(trim($username))) . "'
				AND password='" . md5(md5($_POST['password'])) . "'
			LIMIT 1";
        $checkadmin=Doo::db()->fetchRow($sql);
        if (!empty($checkadmin)) {
            $sql = "update admins set lock_num = 0 WHERE username='" . addslashes(htmlspecialchars(trim($username))) . "' LIMIT 1";
            Doo::db()->query($sql);
            
            $sql = "UPDATE admins
                    SET date_login='" . time() . "'
                    WHERE adminid='".$checkadmin["adminid"]."'
                            ";
            Doo::db()->query($sql);
            Doo::session()->__set("isadmin",1);
            Doo::session()->__set("admininfo",$checkadmin);
            Doo::db()->query("INSERT INTO user_logs SET title='登录页面', action='Login', msg='login system', date='".date('Y-m-d H:i:s')."', username='".$username."'");
            header("Location: /aboutme/index");
//            header("Location: /reproduct/lists");
        } else {
            $sql = "update admins set lock_num = lock_num + 1 WHERE username='" . addslashes(htmlspecialchars(trim($username))) . "' LIMIT 1";
            Doo::db()->query($sql);
            
            $_SESSION = array();
            Doo::session()->destroy();
            header("Content-Type:text/html;charset=utf-8");
            $lock_nownum = $lock_num + 1;
            $left_num = 4 - $lock_nownum;
            
            $warning = '密码输入错误'. $lock_nownum . '次,';
            if($left_num > 0){
                $warning .= '还能再尝试'.$left_num.'次';
            }else{
                $warning .= '您的帐号已被锁定';
            }
            echo "<script>alert('".$warning."');history.go(-1)</script>";
        }
    }
    //登出
    public function loginout(){
        Doo::session()->__unset("isadmin");
        Doo::session()->__unset("admininfo");
        $_SESSION = array();
        unset($_SESSION);
        header("Location: /login/index");
    }
}

?>
