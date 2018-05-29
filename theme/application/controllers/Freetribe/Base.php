<?php

if (!defined('BASE_PATH')) exit('Access Denied!');

/*
 * Enter description here ...
 * @author rainkid
 *
 */

class Freetribe_BaseController extends Common_BaseController {

    public $actions = array();

    /**
     *
     * Enter description here ...
     */
    public function init() {

        parent::init();
        $this->checkToken();

        $dataPath = Common::getConfig("siteConfig", "dataPath");

        $webroot = Yaf_Application::app()->getConfig()->freetribe;
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;
        $this->assign("staticSysPath", $staticroot . '/sys');
        $this->assign("staticResPath", $staticroot . '/apps/theme');
        $this->assign("webroot", $webroot);
        $this->assign("staticroot", $staticroot);
        $this->assign("dataPath", $dataPath);
        $this->assign("crUrl", $webroot . "/Cr/index");

        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();

        //参数
        $params = $this->getInput('params');
        $this->_cookParams($params);
        $this->assign('cn', sprintf('%s_%s_%s', $module, $controller, $action));

        //pt  用于统计
        $pt = Util_Cookie::get('THEME_PT', true);
        if (!$pt) {
            if ($this->getInput('pt')) {
                Util_Cookie::set('THEME_PT', $this->getInput('pt'), true, Common::getTime() + 31536000, '/');
                $pt = Util_Cookie::get('THEME_PT', true);
            }
        }
        $this->assign('pt', $pt);


        //PV统计
        Common::getCache()->increment('Theme_pv');
    }

    /**
     * 检查token
     */
    protected function checkToken() {
        if (!$this->getRequest()->isPost()) return true;
        $post = $this->getRequest()->getPost();
        $result = Common::checkToken($post['token']);
        if (Common::isError($result)) $this->output(-1, $result['msg']);
        return true;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $code
     * @param unknown_type $msg
     * @param unknown_type $data
     */
    public function output($code, $msg = '', $data = array()) {
        $callback = $this->getInput('callback');
        $out = array(
            'success' => $code == 0 ? true : false,
            'msg' => $msg,
            'data' => $data
        );
        if ($callback) {
            header("Content-type:text/javascript");
            exit($callback . '(' . json_encode($out) . ')');
        } else {
            exit(json_encode($out));
        }
    }

    /**
     * lock_style  锁屏的样式
     * local 地域
     * screen_density 手机的分辨率
     * font 字体
     * Android 系统版本号
     * rom rom版本
     * model  机型
     * @param string $params
     * eg:102_1_mdpi_1_android2.2_4.1.2_GN305
     */
    public function _cookParams($params) {
        $default_params = array(
            'lock_style' => 403,
            'area' => 1,
            'resulution' => 'hdpi',
            'font_size' => 1,
            'android_version' => 'android4.1.1',
            'rom_version' => 'ROM4.1.1',
            'model' => 'GN305',
            'bgcolor' => 'white',
            'package' => 'v1',
        );


        if (!$params) {
            $cookie_params = Util_Cookie::get('THEME_PARAMS', true);
            if (!$cookie_params) {
                $params = $default_params;
                Util_Cookie::set('THEME_PARAMS', json_encode($params), true, Common::getTime() + 31536000, '/');
            }
        } else {
            $params = explode('_', $params);


            if (count($params) < 8) $this->output(-1, '参数错误');
            $params_data = array(
                'lock_style' => $params[0] ? $params[0] : $default_params[0],
                'area' => $params[1] ? $params[1] : $default_params[1],
                'resulution' => $params[2] ? $params[2] : $default_params[2],
                'font_size' => $params[3] ? $params[3] : $default_params[3],
                'android_version' => $params[4] ? $params[4] : $default_params[4],
                'rom_version' => $params[5] ? $params[5] : $default_params[5],
                'model' => $params[6] ? $params[6] : $default_params[6],
                'bgcolor' => $params[7] ? $params[7] : $default_params[7],
                'package' => $params[8] ? $params[8] : $default_params[8],
            );


            Util_Cookie::set('THEME_PARAMS', json_encode($params_data), true, Common::getTime() + 31536000, '/');
        }
    }

}
