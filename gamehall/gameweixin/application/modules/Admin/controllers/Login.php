<?php
if (! defined('BASE_PATH')) exit('Access Denied!');

class LoginController extends Common_BaseController {
    
    const keyUserName = 'userName';
    const keyPassword = 'password';
    
    public $actions = array(
                    'loginPostUrl' => '/Admin/Login/loginPost',
    );
    
    public function indexAction() {
    }
    
    /**
     * 登陆
     * @author yinjiayan
     */
    public function loginPostAction() {
        $userName = $this->getInput(self::keyUserName);
        $password = $this->getInput(self::keyPassword);
        $status = $this->checkLogin($userName, $password);
        if ($status) {
            $this->ajaxJsonOutput(array(
                            Util_JsonKey::STATUS => '1', 
                            Util_JsonKey::REDIRECT_URL =>'/Admin/Index/index'));
        } else {
        	$this->ajaxJsonOutput(array(Util_JsonKey::STATUS => '0'));
        }
    }
    
    private function checkLogin($userName, $password) {
        if (!$userName || !$password) {
            return 0;
        }
        $result = Admin_Service_User::login($userName, $password);
//         Common::log(json_encode($result), 'yan');
        if (!$result || Common::isError($result)) {
            return 0;
        }
        return 1;
    }
    
    /**
     * 登出
     * @author yinjiayan
     */
    public function logoutAction() {
        Admin_Service_User::logout();
        $this->redirect("/Admin/Login/index");
    }
}

?>