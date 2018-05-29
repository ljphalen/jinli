<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 导航版本
 * @author tiansh
 *
 */
class VersionController extends Api_BaseController {

    public function indexAction() {
        echo Gionee_Service_Config::getValue('nav_version');
        exit();
    }

    public function splashAction() {
        echo Gionee_Service_Config::getValue('splash_version');
        exit();
    }

    public function blackurlAction() {
        echo Gionee_Service_Config::getValue('blackurl_version');
        exit();
    }

    public function staticAction() {
        echo 0;
        exit;
        $fid = $this->getInput('fid');
        $ret = Gionee_Service_Config::version_static($fid);
        echo crc32($ret[0]);

        exit;
    }
}