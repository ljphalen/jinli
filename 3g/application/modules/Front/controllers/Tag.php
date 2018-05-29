<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 标签
 *
 */
class TagController extends Front_BaseController {

    /**
     * tag = 事件类型,事件名称,事件值
     */
    public function eventAction() {
        Yaf_Dispatcher::getInstance()->disableView();
        $val = filter_input(INPUT_GET,'val', FILTER_SANITIZE_STRING);
        if (!empty($val)) {
            $arr = explode(',', $val);
            if (count($arr) == 3) {
                Gionee_Service_Tag::incrBy($arr);
            }
        }
        echo 1;
        exit;
    }
}

?>