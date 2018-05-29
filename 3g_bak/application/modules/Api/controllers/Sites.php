<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网站大全API接口
 */
class SitesController extends Api_BaseController {

    /**
     * ajax动态获得搜索内容
     */
    public function ajaxSearchAction() {
        $words = $this->getInput('words');
        $key   = "SITE:SEARCH:" . crc32($words);
        $data  = Common::getCache()->get($key);
        if (empty($data)) {
            $channelNum = Gionee_Service_Baidu::getFromNo();
            $searchUrl  = "http://m.baidu.com/su?from={$channelNum}&ie=utf-8&wd={$words}&action=opensearch";
            $result     = Util_Http::get($searchUrl);
            $data       = array();
            if ($result->state == '200') {
                $data = json_decode($result->data);
                Common::getCache()->set($key, $data, 600);
            }
        }
        $this->output('0', '', $data);
    }
}