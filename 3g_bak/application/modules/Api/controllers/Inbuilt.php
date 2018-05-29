<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 内置书签页
 * @author panxb
 *
 */
class InbuiltController extends Api_BaseController {

    public function toAction() {
        $model    = Yaf_Registry::get("current_model_name");
        $postData = $this->getInput(array('key', 'version', 'operator'));
        $info     = Gionee_Service_Inbuilt::getByKey($postData['key']);
        if (!empty($info['tourl'])) {
            header("Location:" . $info['tourl']);
            exit;
        }
        header("Location:" . Common::getCurHost() . '/nav');
        exit;
    }
}