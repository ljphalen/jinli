<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Admin_Service_Access {

    static public function _check($val, $mod = '') {

        $menuService = new Common_Service_Menu(Common::getConfig("siteConfig", "mainMenu"), 0);
        $level       = $menuService->getMainLevels();
        $access      = array();
        foreach ($level as $key => $tmpArr) {
            foreach ($tmpArr['items'] as $k => $v) {
                if (!empty($v['access'])) {
                    $access[$v['id']] = $v['access'];
                }
            }
        }

        if (empty($mod)) {
            $module     = Yaf_Dispatcher::getInstance()->getRequest()->getModuleName();
            $controller = Yaf_Dispatcher::getInstance()->getRequest()->getControllerName();
            $mod        = sprintf('_%s_%s', $module, $controller);
        }


        if (!isset($access[$mod])) {
            return true;
        }

        $loginInfo = Admin_Service_User::isLogin();
        $userInfo  = Admin_Service_User::getUser($loginInfo['uid']);
        if ($userInfo['groupid'] == 0) {
            return true;
        } else {
            $groupInfo  = Admin_Service_Group::getGroup($userInfo['groupid']);
            $accessVals = json_decode($groupInfo['access_val'], true);

            if (!empty($accessVals[$mod][$val])) {
                return true;
            }
        }
        return false;
    }

    static public function pass($val, $mod = '') {
        $ret = self::_check($val, $mod);
        if (!$ret) {
            self::output(-1, '没有权限!');
        }
    }

    /**
     *
     * @param int    $code
     * @param string $msg
     * @param string $data
     */
    static public function output($code, $msg = '', $data = array()) {
        exit(json_encode(array(
            'success' => $code == 0 ? true : false,
            'msg'     => $msg,
            'data'    => $data
        )));
    }
}
