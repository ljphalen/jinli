<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class LoginController extends Admin_BaseController {

    public $actions = array(
        'loginUrl' => '/Admin/Login/login',
        'logoutUrl' => '/Admin/Login/logout',
        'indexUrl' => '/Admin/Index/index'
    );

    /**
     *
     * Enter description here ...
     */
    public function indexAction() {


        $this->assign('loginUrl', $this->actions['loginUrl']);
        $this->assign('logoutUrl', $this->actions['logoutUrl']);
        $this->assign('indexUrl', $this->actions['indexUrl']);
    }

    /**
     *
     * Enter description here ...
     */
    public function loginAction() {


        $login = $this->getRequest()->getPost();
        if (!isset($login['username']) || !isset($login['password'])) {
            return $this->showMsg(-1, '用户名或者密码不能为空.');
        }


        $ret = Admin_Service_User::login($login['username'], $login['password']);


        if (Common::isError($ret)) return $this->showMsg($ret['code'], $ret['msg']);
        if (!$ret) $this->showMsg(-1, '登录失败.');
        $this->redirect('/Admin/Index/index');
    }

    /**
     *
     * Enter description here ...
     */
    public function logoutAction() {
        setcookie("VAL", '', 0, '/', null);
        setcookie("ACC", '', 0, '/', null);
        Admin_Service_User::logout();
        $this->redirect($this->ucenterLoginUrl);
        exit;
    }

}
