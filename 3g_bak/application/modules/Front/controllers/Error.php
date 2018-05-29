<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ErrorController extends Yaf_Controller_Abstract {

    public function init() {
        Yaf_Dispatcher::getInstance()->disableView();

        //3g.gionee.com/aorate/soft 特殊跳转
        $server = Util_Http::getServer();
        $url    = 'http://' . $server['SERVER_NAME'] . $server['REQUEST_URI'];
        if (strpos($url, 'aorate/soft')) {
            $redirect = Gionee_Service_Redirect::getRedirectByUrl(md5($url));
            if ($redirect) {
                Common::redirect($redirect['redirect_url']);
            }
        }
    }

    public function errorAction($exception) {
        /* error occurs */
        $code   = $exception->getCode();
        $msg    = $exception->getMessage();
        $module = $this->getRequest()->getModuleName();
        $name   = $module . '-' . $_SERVER["REQUEST_URI"];
        if (stristr(ENV, 'product')) {
            $path = '/data/3g_log/err/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $txt = sprintf('%s %s %s (%s) %s', date('YmdHis'), $name, json_encode(Util_Http::ua()), $code, $msg);
            error_log($txt . "\n", 3, $path . date('Ymd'));
            exit('Access Denied!');
        } else {
            echo 404, ':', $msg;
        }
        exit;
    }
}
