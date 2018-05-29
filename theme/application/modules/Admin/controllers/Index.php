<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author liyd

 */
class IndexController extends Admin_BaseController {

    public $actions = array(
        'editpasswd' => '/Admin/User/edit',
        'logout' => '/Admin/Login/logout',
        'default' => '/Admin/Index/default',
        'getdesc' => '/Admin/Index/getdesc',
        'search' => '/Admin/Index/search',
        'passwdUrl' => '/Admin/User/passwd',
    );


    /**
     *
     * Enter description here ...
     */
    public function indexAction() {   
        if(empty($this->userInfo['nick_name']) && $this->userInfo['admin_type'] == 1){
            $this->redirect('/Admin/Ucenter/edit');
        } else {
            if($this->userInfo["groupid"] == 0) {
                $this->redirect('/Admin/File/index');
            } elseif ($this->userInfo["groupid"] == 1) {
                $this->redirect('/Admin/Wallpapermy/index');
            } elseif ($this->userInfo["groupid"] == 2) {
                $this->redirect('/Admin/wallpaperadmin/index');
            } elseif ($this->userInfo["groupid"] == 3) {
                $this->redirect('/Admin/wallpaperadmin/index');
            } elseif ($this->userInfo["groupid"] == 4) {
                $this->redirect('/Admin/Paycheck/personalapply');
            }         
        }

        $this->assign("meunOn", "bz_zy");
        $this->assign('username', $this->userInfo['username']);
        $this->assign("groupid", $this->userInfo['groupid']);
        $this->assign('mainview', json_encode(array_values($mainview)));
    }

    /**
     *
     * Enter description here ...
     */
    public function defaultAction() {
        $this->assign('uid', $this->userInfo['uid']);
        $this->assign('username', $this->userInfo['username']);

        //未读消息
        list($count, $message) = Theme_Service_Message::getList(1, 100, array('status' => 0, 'uid' => $this->userInfo['uid']));
        $this->assign('message', $message);
        $this->assign('count', $count);
    }

}
