<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {

    // add
    public $actions = array(
        'aboutUrl' => '/index/about',
        'indexUrl' => '/index/index',
    );

    public function indexAction() {
        //第三方页面
        if (stristr($_SERVER['HTTP_HOST'], 'mk.')) {
            Common::redirect(Common::getCurHost() . '/vendor');
            exit;
        }
        $f = $this->getInput('f');
        $r = $this->isRedirect($f);

        if ($r === 1) { //没有GiONEE标识
            $url = Common::getCurHost() . '/nav';
        } else if ($r === 2) { //有机型
            $url = Common::getCurHost() . '/news';
        } else {
            $url = Common::getCurHost() . '/product';
        }
        Common::redirect($url);
        exit;
    }

    public function tjAction() {
        Yaf_Dispatcher::getInstance()->disableView();
        $t_bi = $this->getSource();
        $id   = intval($this->getInput('id'));
        $ch   = $this->getInput('ch');
        $t    = $words = '';

        if ($id) { //兼容老的模式
            $type = $this->getInput('type');
            $url  = html_entity_decode(urldecode($this->getInput('_url')));
        } else {
            $t       = $this->getInput('t');
            $key     = "INDEX_TJ:{$t}";
            $_tjInfo = Cache_APC::get($key);
            if (empty($_tjInfo)) {//主要目的 发push的时候  redis承受不住带宽压力  所以添加apc缓存60s
                $_tjInfo = Gionee_Service_ShortUrl::check($t);
                Cache_APC::set($key, $_tjInfo);
            }

            list($id, $type, $url, $words) = $_tjInfo;


            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_URL, $t);
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_URL_UV, $t, $t_bi);
        }
        if (!$id || !$type) {
            return false;
        }
        switch ($type) {

            case "SOHU": //搜狐新闻统计
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_SOHU, $id);
                //新闻页UV统计
                Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NEWS_UV, $id, $t_bi);
                break;
            case 'BAIDU_HOT': //百度热词统计
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_BAIDU_HOT, $t . ":" . $words);
                break;
            case 'TOPIC': //专题活动页统计
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_TOPIC, $t . ":" . $id);
                break;
            case 'TOPIC_LIST': //专题页中列表点击统计
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_TOPIC_LIST, $t . ":" . $id);
                break;
            case 'TOPIC_MAIN': //专题页中返回首页点击统计
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_TOPIC_MAIN, $t . ":" . $id);
                break;
            case 'TOPIC_CONTENT': //专题页中内容点击统计
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_TOPIC_CONTENT, $id . ':' . $ch);
                break;
            case "NAV":
            case 'NAVNEWS':
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAV, $id . ':' . $ch);
                //用户UV统计
                Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_CONTENT_UV, $id . ':' . $ch, $t_bi);
                break;
            case 'LOCAL_NAV':
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_LOCAL_NAV_PV, $id);
                Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_LOCAL_NAV_UV, $id, $t_bi);
                break;
            case "SITE":
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_SITE_INDEX, $id . ":" . $ch);
                break;
            case 'INBUILT'://内置书签页
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_INBUILT, $id);
                break;
            case 'TOURL'://BookMark 相关
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_BOOKMARK, $id);
                break;
            case 'APP':
                Gionee_Service_App::updateTJ($id);
                break;
            case 'NAV_SEARCH':
            case 'NEWS_SEARCH' :
                $word    = $this->getInput('word');
                $q       = $this->getInput('q');
                $keyword = $this->getInput('keyword');
                if (strpos($url, '?') === false) {
                    $url .= '?';
                } else {
                    $url .= '&';
                }
                if ($word) $url .= 'word=' . $word;
                if ($q) $url .= 'q=' . $q;
                if ($keyword) $url .= 'keyword=' . $keyword;

                break;
            case 'NAVAD':
                if (!empty($id)) {
                    $info = Nav_Service_NewsAd::getInfo($id);
                    Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $id . ':ad_pos');
                    Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $id . ':ad_pos');
                    if (!empty($info['pos'])) {
                        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $info['pos'] . ':ad_pos');
                        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $info['pos'] . ':ad_pos');
                    }
                }
                break;
            case 'LABEL':
                list($mod, $val) = explode(':', $id);
                if (!empty($mod) && !empty($val)) {
                    Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $val . ':' . $mod);
                    Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $val . ':' . $mod);
                }
                break;
            case 'NEWSAD':
            default:
                break;
        }
        if (empty($url)) {
            $url = Yaf_Application::app()->getConfig()->webroot;
        }

        if (substr($url, 0, 4) != 'http') {
            $url = 'http://' . $url;
        }
        Common::redirect($url);
        exit;
    }


    /**
     * 百度搜索内容记录
     */
    public function searchAction() {
        $from  = $this->getInput('from');
        $words = $this->getInput('word');
        $key   = 'SEARCH:' . date('YmdH', time() - 3600);
        $field = base64_encode($words);
        $value = Common::getCache()->hGet($key, $field);
        if ($value) {
            Common::getCache()->hIncrBy($key, $field);
        } else {
            Common::getCache()->hSet($key, $field, 1, 24 * 3600);
        }
        Common::redirect('http://m.baidu.com/s?from=' . $from . '&word=' . urlencode($words));
    }


    /**
     * tag = 事件类型,事件名称,事件值
     */
    public function clickAction() {
        $tag = $this->getInput('tag');
        if (!empty($tag)) {
            $arr = explode(',', $tag);
            if (count($arr) == 3) {
                $t_bi = $this->getSource();
                Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $arr[1] . ':' . $arr[0]);
                Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $arr[1] . ':' . $arr[0], $t_bi);
            }
        }
        echo 1;
        exit;
    }


    /**
     * 搜索引擎跳转
     */
    public function keywordAction() {
        $args    = array(
            'keyword' => FILTER_SANITIZE_STRING,
            'from'    => FILTER_SANITIZE_STRING,
        );
        $params  = filter_input_array(INPUT_GET, $args);
        $keyword = $params['keyword'];
        $from    = $params['from'];
        if (empty($from) || empty($keyword)) {
            Common::redirect(Common::getCurHost() . '/nav');
        }

        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $keyword . ':' . $from);
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $keyword . ':' . $from, $this->getSource());

        $url  = 'http://m.baidu.com/s?from=1008021d&word={searchTerms}';
        $list = Gionee_Service_SearchKeyword::all();

        if (!empty($list[$from])) {
            $url = html_entity_decode($list[$from]);
        }

        $url = str_ireplace('{searchTerms}', urlencode($keyword), $url);
        Common::redirect($url);
    }
}
