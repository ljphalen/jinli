<?php

/**
 * ErrorController
 * Feel free to change this and customize your own error message
 *
 * @author darkredz
 */
Doo::loadController("AppDooController");

class ErrorController extends AppDooController {

    public function index() {
        echo '你要访问的页面木有了';
//        echo '<p>This is handler by an internal Route as defined in common.conf.php $config[\'ERROR_404_ROUTE\']</p>
//                
//<p>Your error document needs to be more than 512 bytes in length. If not IE will display its default error page.</p>
//
//<p>Give some helpful comments other than 404 :(
//Also check out the links page for a list of URLs available in this demo.</p>';
    }

    /**
     * 打印错误提示页面
     *
     * @access public
     * @param string $error 错误信息
     */
    function displayError($error = "") {
        $this->vdata = $error;
        $this->rendc("error/display_error.html");
        exit;
    }

    function displayNotice($notice = "", $url = '') {
        $this->vdata = $url;
        $this->vdata = $notice;
        $this->rendc("error/display_notice.html");
        exit;
    }

    function displayNoPermission() {
        $this->redirect("javascript:history.go(-1)","对不起，你的权限不足！");
    }

}

?>