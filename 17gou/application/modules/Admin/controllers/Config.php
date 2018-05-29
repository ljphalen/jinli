<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author rainkid
 *
 */
class ConfigController extends Admin_BaseController {

    public $actions = array(
        'editUrl'=>'/admin/config/index',
        'editPostUrl'=>'/admin/config/edit_post',
    );
    /**
     *
     * Enter description here ...
     */
    public function indexAction() {
        $configs = Ygou_Service_Config::getAllConfig();
        $this->assign('configs', $configs);
    }

    /**
     *
     */
    public function edit_postAction() {
        $config = $this->getInput(array(
            'android_dl_url',
            'ios_dl_url',
            'apk_version',
            'apk_uptime',
            'kefu_mail',
            'kefu_phone',
            'bd_mail',
            'bd_phone',
            'gz_weibo',
            'gz_weixin',
            'gz_qq',
        ));
        foreach($config as $key=>$value) {
            Ygou_Service_Config::setValue($key, $value);
        }
        $this->output(0, '操作成功.');
    }
}
