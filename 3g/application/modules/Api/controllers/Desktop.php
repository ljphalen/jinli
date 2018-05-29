<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 合作:桌面接口
 *
 */
class DesktopController extends Api_BaseController {
    /**
     * 支持4.0
     * 搜索引擎
     */
    public function searchUrlAction() {
        $app        = 'browser';
        $where      = array('type' => 1);
        if (!empty($app)) {
            foreach (Gionee_Service_Browserurl::$app as $k => $v) {
                if ($v == $app) {
                    $where['app'] = array('&', $k);
                    break;
                }
            }
        }

        $rcKey = 'DESKTOP_SEARCHURL:' . crc32(json_encode($where));
        $tmp   = Common::getCache()->get($rcKey);
        if ($tmp === false) {
            $list = Gionee_Service_Browserurl::getsBy($where, array('sort' => 'DESC'));
            $tmp  = array();
            foreach ($list as $val) {
                $tmp[] = array(
                    'id'      => intval($val['id']),
                    'name'    => $val['name'],
                    'url'     => html_entity_decode($val['url']),
                    'iconUrl' => Common::getImgPath() . $val['icon'],
                );
            }
            Common::getCache()->set($rcKey, $tmp, 600);
        }


        $ret = array(
            'timestamp' => time(),
            'list'      => $tmp,
        );
        $this->output(0, '', $ret);
    }

    /**
     * 热词
     */
    public function searchHotWordsAction() {
        $num = $this->getInput('num');
        $arr = Common::getCache()->get("baidu_hotwords");
        $rnd = 4;
        if (!empty($num)) {
            $rnd = ($num == 'server') ? 50 : $num;
        }
        $maxnum = count($arr);
        $tmp    = array_rand($arr, min($maxnum, $rnd));

        $t = array();
        foreach ($tmp as $n) {
            $val = $arr[$n];
            $t[] = array('keyword' => $val['text'], 'url' => $val['url']);
        }

        $ret = array(
            'timestamp' => time(),
            'list'      => $t,
        );
        $this->output(0, '', $ret);

    }

    /**
     * 关键字匹配
     */
    public function searchLikeWordsAction() {

        $keyword = $this->getInput('keyword');
        $from    = Gionee_Service_Baidu::getFromNo();

        $rcKey   = 'LIKE_WORDS:' . crc32($keyword);
        $content = Common::getCache()->get($rcKey);
        if (empty($content)) {
            $url     = "http://m.baidu.com/su?from={$from}&wd={$keyword}&ie=utf-8&action=opensearch";
            $curl    = new Util_Http_Curl($url);
            $content = $curl->get();
            Common::getCache()->set($rcKey, $content, 600);
        }

        $ret = array(
            'timestamp' => time(),
        );

        if (!empty($content)) {
            $result         = json_decode($content, true);
            $ret['keyword'] = $result[0];
            $ret['list']    = $result[1];
        }

        $this->output(0, '', $ret);
    }
}
