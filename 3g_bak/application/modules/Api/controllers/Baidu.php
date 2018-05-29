<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 百度关键字接口
 * @author tiger
 *
 */
class BaiduController extends Api_BaseController {
    //导航页关键字
    public function navAction() {
        $hotwords  = Common::getCache()->get("baidu_hotwords");
        $json_data = array_values($hotwords);
        $this->output(0, '', $json_data);
    }

    public function smAction() {
        $data = array();
        $key  = "SM:KEYWORD:LIST";
        $data = Common::getCache()->get($key);
        if (empty($data)) {
            $url = 'http://api.m.sm.cn/rest?method=tools.hot&start=';
            for ($i = 1; $i <= 3; $i++) {
                $tmp             = file_get_contents($url . $i);
                $data['mm' . $i] = json_decode($tmp, true);
            }
            $data = call_user_func_array('array_merge', array_values($data));
            Common::getCache()->set($key, $data, 100);
        }

        $a = array('red', 'yellow', 'blue', 'white');
        $b = array('1', '2', '3', '4');
        //var_dump(array_fill_keys($a, $b));exit;
        var_dump((array_column($data, 'title')));
        exit;
    }
}