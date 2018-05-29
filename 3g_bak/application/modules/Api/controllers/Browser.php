<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 黑白名单网址接口
 * @author tiger
 *
 */
class BrowserController extends Api_BaseController {

    public $perpage = 20;

    /**
     * 浏览器配置接口
     */
    public function settingAction() {
        $page_version     = $this->getInput('page_version');
        $bookmark_version = $this->getInput('bookmark_version');
        $blackurl_version = $this->getInput('blackurl_version');

        $json_data = array();
        if (!empty($page_version)) {
            //三屏配置
            $newVal = Gionee_Service_Config::getValue('page_version');
            if ($page_version != $newVal) {
                $rcKey = 'BROWSER_SETTING_PAGE:' . $newVal;
                $pages = Common::getCache()->get($rcKey);
                if ($pages === false) {
                    $pages  = array();
                    $result = Gionee_Service_Page::getAll(array('sort' => 'ASC'));
                    foreach ($result as $key => $value) {
                        $pages[] = array(
                            'sort'         => $value['sort'],
                            'name'         => $value['name'],
                            'type'         => $value['page_type'],
                            'url'          => $value['url'],
                            'is_default'   => $value['is_default'],
                            'url_download' => ''
                        );
                    }
                    Common::getCache()->set($rcKey, $pages, 600);
                }

                $page        = array('type' => 'page', 'version' => $newVal, 'data' => $pages);
                $json_data[] = $page;
            }
        }

        if (!empty($bookmark_version)) {
            //默认应用
            $v = Gionee_Service_Config::getValue('bookmark_version_1');
            if ($bookmark_version != $v) {
                $path  = Common::getConfig('siteConfig', 'attachPath');
                $rcKey = 'BROWSER_SETTING_BOOKMARK:' . $v;
                $apps  = Common::getCache()->get($rcKey);
                if ($apps === false) {
                    $params = array('ver' => array('&', 1),);
                    $result = Gionee_Service_Bookmark::getsBy($params, array('sort' => 'ASC'));
                    $apps   = array();
                    foreach ($result as $key => $value) {
                        $apps[] = array(
                            'sort'      => $value['sort'],
                            'name'      => $value['name'],
                            'icon'      => Common::getImgContent($value['icon']),
                            'backgroud' => $value['backgroud'],
                            'url'       => $value['url'],
                            'is_delete' => $value['is_delete'],
                        );
                    }
                    Common::getCache()->set($rcKey, $apps, 600);
                }

                $onlineapp   = array('type' => 'onlineapp', 'version' => $v, 'data' => $apps);
                $json_data[] = $onlineapp;
            }
        }

        if (!empty($blackurl_version)) {
            //url过滤
            $blackurl_version = $this->getInput('blackurl_version');
            $v                = Gionee_Service_Config::getValue('blackurl_version');
            if ($blackurl_version != $v) {
                $rcKey = 'BROWSER_SETTING_BLACKURL:' . $v;
                $menus = Common::getCache()->get($rcKey);
                if ($menus === false) {
                    $menus  = array();
                    $result = Gionee_Service_Blackurl::getAll(array('status' => 'ASC'));
                    foreach ($result as $key => $value) {
                        $menus[] = array(
                            'name' => $value['name'],
                            'url'  => $value['url'],
                            'type' => $value['status'],
                        );
                    }
                    Common::getCache()->set($rcKey, $menus, 600);
                }

                $urlfilter   = array('type' => 'urlfilter', 'version' => $v, 'data' => $menus);
                $json_data[] = $urlfilter;
            }
        }

        $this->output(0, '', $json_data);
        exit;
    }

    /**
     * 欢迎图片
     */
    public function welcomeAction() {
        $userVer = $this->getInput('version');
        $dataVer = Gionee_Service_Config::getValue('welcome_version_3');
        if ($userVer != $dataVer) {
            $rcKey = 'WELCOME_LIST_3:' . $dataVer;
            $list  = Common::getCache()->get($rcKey);
            if ($list === false) {
                $list = Gionee_Service_Welcome::getsBy(array('ver' => 3), array('sort' => 'ASC'));
                $list = !empty($list) ? $list : array();
                Common::getCache()->set($rcKey, $list, 86400);
            }

            $data    = array();
            $nowTime = time();
            foreach ($list as $value) {
                if ($value['start_time'] > $nowTime || $value['end_time'] < $nowTime || $value['status'] == 0) {
                    continue;
                }
                $img = Common::getImgContent($value['img']);
                $img = trim($img);

                $data[] = array(
                    'name'   => $value['name'],
                    'url'    => $value['url'],
                    'imgurl' => Common::getImgPath() . $value['img'],
                    'img'    => !empty($img) ? $img : '',
                    'sort'   => intval($value['sort']),
                );
            }
            $json_data = array('version' => $dataVer, 'images' => $data);
            $this->output(0, '', $json_data);
        }

        exit;
    }

    public function outlocalnavAction() {
        $id = $this->getInput('id');
        $id_array=array('18'=>18,'11'=>11);  //热门话题18,今日看点11
        $id = intval($id_array[$id]);
        if ($id <= 0) {
            exit;
        }
        $apcKey = 'Browser_outlocalnav:' . $id;
        $data   = Cache_APC::get($apcKey);
        if (empty($data)) {
            $data = Gionee_Service_LocalNavList::getData($id);
            Cache_APC::set($apcKey, $data);
        }
        unset($data['initJSData']);
        $this->output(0, '', $data);
        exit;
    }


    public function localnavAction() {
        $id = $this->getInput('id');
        $id = intval($id);
        if ($id <= 0) {
            exit;
        }

        $apcKey = 'Browser_localnav:' . $id;
        $data   = Cache_APC::get($apcKey);
        if (empty($data)) {
            $data = Gionee_Service_LocalNavList::getData($id);
            Cache_APC::set($apcKey, $data);
        }
	    $this->output(0, '', $data);
        exit;
    }


    /**
     * 闪屏接口
     */
    public function splashAction() {
        $version = $this->getInput('version');
        list($total, $result) = Gionee_Service_Splash::getCanUseSplashs(0, 100, array('version' => $version));

        $output = array();
        if ($total) {
            $output = array(
                'img'   => Common::getImgPath() . $result[0]['img_url'],
                'title' => $result[0]['title'],
            );
        }
        echo json_encode($output);
        exit;
    }

    /**
     * 白名单接口
     */
    public function whiteAction() {
        $this->urllist(1);
    }

    /**
     * 黑名单接口
     */
    public function blackAction() {
        $this->urllist(0);
    }

    public function staticAction() {
        $fid = $this->getInput('fid');
        list($content, $finfo) = Gionee_Service_Config::version_static($fid);
        //header('Content-Type: application/octet-stream');
        header('Content-Type: text/' . $finfo['extension']);
        echo $content;
        exit;
    }

    public function categoryAction() {
        $column_version = $this->getInput('version');
        $ver            = Gionee_Service_Config::getValue('column_version');
        if ($column_version != $ver) {
            $rcKey = 'API_BROWSER_category:' . $ver;
            $out   = Common::getCache()->get($rcKey);
            if (empty($out)) {
                $page      = max(1, intval($this->getInput('page')));
                $perpage   = intval($this->getInput('perpage'));
                $type      = intval($this->getInput('type'));
                $timestamp = intval($this->getInput('timestamp'));

                if ($perpage) {
                    $this->perpage = $perpage;
                }

                $s_count = Gionee_Service_OutNews::getCountBy($timestamp);
                $s_count = Common::resetKey($s_count, 'source_id');

                $params = array();
                if ($type) $params['ptype'] = $type;
                $params['status'] = 1;

                list($total, $result) = Gionee_Service_Column::getList($page, $this->perpage, $params);
                $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

                $tmp = array();

                foreach ($result as $key => $value) {
                    $tmp[] = array(
                        'id'     => $value['source_id'],
                        'sort'   => $value['sort'],
                        'name'   => $value['title'],
                        'type'   => $value['ptype'],
                        'islink' => $value['pptype'],
                        'link'   => $value['link'],
                        'img'    => Common::getImgPath() . $value['img'],
                        'color'  => $value['color'],
                        'total'  => intval($s_count[$value['source_id']]['total']),
                    );
                }
                $out = array(
                    'version'   => $ver,
                    'timestamp' => time(),
                    'list'      => $tmp,
                    'hasnext'   => $hasnext,
                    'curpage'   => $page
                );

                Common::getCache()->set($rcKey, $out, Common::T_TEN_MIN);
            }

            $this->output(0, '', $out);
        }
        exit;
    }

    /**
     * QQ新闻列表接口
     */
    public function newsAction() {
        $page      = intval($this->getInput('page'));
        $perpage   = intval($this->getInput('perpage'));
        $source_id = intval($this->getInput('type_id'));

        $news_id = intval($this->getInput('news_id'));
        $act     = $this->getInput('act');

        if ($perpage) $this->perpage = $perpage;

        //$this->perpage = min(14, $perpage);
        if ($page < 1) $page = 1;

        $params['source_id'] = $source_id;
        if ($news_id && $act) {
            if ($act == 'pre') {
                $params['id'] = array('<', $news_id);
            } else {
                $params['id'] = array('>', $news_id);
            }
        }

        list($total, $result) = Gionee_Service_OutNews::getList($page, $this->perpage, $params, array('id' => 'DESC'));

        $tmp = array();
        foreach ($result as $key => $value) {
            array_pop($value);
            $tmp[] = $value;
        }
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;


        $out = array(
            'list'      => $tmp,
            'hasnext'   => $hasnext,
            'curpage'   => $page,
            'timestamp' => Common::getTime()
        );

        $this->output(0, '', $out);

    }

    /**
     * 新闻内容接口
     */
    public function news_contentAction() {
        $id   = intval($this->getInput('id'));
        $info = Gionee_Service_OutNews::get($id);
        $this->output(0, '', json_decode($info['content'], true));
    }

    /**
     * 新闻详情接口，预加载，一次取多条
     * perpage 条数
     * type_id 分类id
     * channel_id 渠道 1：QQ新闻 2：...
     * news_id 新闻id
     * act pre or next
     */
    public function news_detailsAction() {
        $perpage    = intval($this->getInput('perpage'));
        $source_id  = intval($this->getInput('type_id'));
        $channel_id = intval($this->getInput('channel_id'));
        $news_id    = intval($this->getInput('news_id'));
        $act        = $this->getInput('act');

        if ($perpage) $this->perpage = $perpage;

        $params['source_id'] = $source_id;
        if ($news_id && $act) {
            if ($act == 'pre') {
                $params['id'] = array('<=', $news_id);
            } else {
                $params['id'] = array('>=', $news_id);
            }
        }

        $tmp = array();

        //$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        //$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'timestamp'=>Common::getTime()));
        $this->output(0, '', array('list' => $tmp));

    }

    /**
     *
     * @param int $status
     */
    private function urllist($status) {
        header('Content-Type: text/xml');
        $output = array('<?xml version="1.0" encoding="UTF-8"?>', '<list>');
        list(, $result) = Gionee_Service_Blackurl::getList(0, 100, array('status' => $status));
        foreach ($result as $key => $value) {
            array_push($output, sprintf('<group name="%s">%s</group>', $value['name'], $value['url']));
        }
        array_push($output, '</list>');
        print_r(implode("", $output));
        exit;
    }

    /**
     * 支持4.0
     * 搜索引擎
     */
    public function searchUrlAction() {
        $app    = $this->getInput('app');
        $type   = $this->getInput('type');
        $inputV = $this->getInput('ver');
        $appver = $this->getInput('app_ver');
        $where  = array('type' => 1);
        if (!empty($app)) {
            foreach (Gionee_Service_Browserurl::$app as $k => $v) {
                if ($v == $app) {
                    $where['app'] = array('&', $k);
                    break;
                }
            }
        }

        $rcKey = 'BROWSERURL_SEARCHURL:' . crc32(json_encode($where));
        $tmp   = Common::getCache()->get($rcKey);
        if ($tmp === false) {
            $list = Gionee_Service_Browserurl::getsBy($where, array('sort' => 'DESC'));
            $tmp  = array();
            foreach ($list as $val) {
                $tmp[] = array(
                    'id'        => $val['id'],
                    'type'      => 0,
                    'name'      => $val['name'],
                    'url'       => html_entity_decode($val['url']),
                    'iconUrl'   => Common::getImgPath() . $val['icon'],
                    'operation' => $val['operation'],
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
     * 支持4.0
     * 推荐网址
     */
    public function recUrlAction() {
        $inputV = $this->getInput('ver');
        $appver = $this->getInput('app_ver');
        $where  = array('type' => 2, 'operation' => 0);

        $rcKey = 'BROWSERURL_RECURL:' . crc32(json_encode($where));
        $tmp   = Common::getCache()->get($rcKey);
        if ($tmp === false) {
            $list = Gionee_Service_Browserurl::getsBy($where);
            $tmp  = array();
            foreach ($list as $val) {
                $tmp[] = array(
                    'id'      => $val['id'],
                    'name'    => $val['name'],
                    'showUrl' => html_entity_decode($val['show_url']),
                    'realUrl' => html_entity_decode($val['url']),
                    'iconUrl' => Common::getImgPath() . $val['icon'],
                    //'operation' => $val['operation'],
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
     * 支持4.0
     * 网址库
     */
    public function urlListAction() {
        $inputV = $this->getInput('ver');
        $appver = $this->getInput('app_ver');
        $where  = array('type' => 3, 'operation' => 0);

        $rcKey = 'BROWSERURL_URLLIST:' . crc32(json_encode($where));
        $tmp   = Common::getCache()->get($rcKey);
        if ($tmp === false) {
            $list = Gionee_Service_Browserurl::getsBy($where);
            $tmp  = array();
            foreach ($list as $val) {
                $tmp[] = array(
                    'id'      => $val['id'],
                    'name'    => $val['name'],
                    'showUrl' => html_entity_decode($val['show_url']),
                    'realUrl' => html_entity_decode($val['url']),
                    'iconUrl' => Common::getImgPath() . $val['icon'],
                    //'operation' => $val['operation'],
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
     * 支持4.0 老
     * 轻应用
     */
    public function webAppAction() {
        $appver    = $this->getInput('app_ver');
        $userVer   = $this->getInput('ver');
        $model     = $this->getInput('model');
        $operators = $this->getInput('operators');//运营商
        $ret       = array();
        $v         = Gionee_Service_Config::getValue('bookmark_version_4');
        $op        = isset(Gionee_Service_Bookmark::$opParam[$operators]) ? Gionee_Service_Bookmark::$opParam[$operators] : 1;
        $params    = array(
            'ver'        => array('&', 4),
            'op_type'    => array('&', $op),
            'updated_at' => array('>', $userVer),
        );

        //$rcKey = "BROWSER_webApp:{$v}:{$op}";
        // $list  = Common::getCache()->get($rcKey);
        $list = false;
        if ($list === false) {
            $list   = array();
            $result = Gionee_Service_Bookmark::getsBy($params, array('sort' => 'ASC'));
            foreach ($result as $key => $value) {
                $url    = html_entity_decode($value['url']);
                $list[] = array(
                    'id'         => $value['id'],
                    'name'       => $value['name'],
                    'iconUrl'    => Common::getImgPath() . $value['icon'],
                    'background' => $value['backgroud'],
                    'url'        => Common::clickUrl($value['id'], 'TOURL', $url),
                    'deletable'  => $value['is_delete'],
                    'operation'  => $value['operation'] == 1 ? 4 : 0,
                    'updated_at' => intval($value['updated_at']),
                );
            }
            // Common::getCache()->set($rcKey, $list, 600);
        }
        $ret = array(
            'timestamp' => intval($v),
            'list'      => $list,
        );


        $this->output(0, '', $ret);
    }

    /**
     * 支持3.1
     * 轻应用
     */
    public function webAppV1Action() {
        $inputV    = $this->getInput('ver');
        $model     = $this->getInput('model');
        $operators = $this->getInput('optr');//运营商
        $appver    = $this->getInput('app_ver');

        $v   = Gionee_Service_Config::getValue('bookmark_version_2');
        $ret = array();

        if ($inputV != $v) {
            $op     = isset(Gionee_Service_Bookmark::$opParam[$operators]) ? Gionee_Service_Bookmark::$opParam[$operators] : 1;
            $params = array(
                'ver'     => array('&', 2),
                'op_type' => array('&', $op),
            );

            $rcKey = "BROWSER_webAppV1:{$v}:{$op}";
            $apps  = Common::getCache()->get($rcKey);
            if ($apps === false) {
                $apps   = array();
                $result = Gionee_Service_Bookmark::getsBy($params, array('sort' => 'ASC'));
                foreach ($result as $key => $value) {
                    $url    = html_entity_decode($value['url']);
                    $apps[] = array(
                        'id'         => $value['id'],
                        'sort'       => $value['sort'],
                        'name'       => $value['name'],
                        'icon'       => Util_Image::base64(Common::getImgPath() . $value['icon']),
                        'background' => $value['backgroud'],
                        'url'        => Common::clickUrl($value['id'], 'TOURL', $url),
                        'is_delete'  => $value['is_delete'],
                        'operation'  => $value['operation'],
                    );
                }
                Common::getCache()->set($rcKey, $apps, 600);
            }

            $ret[] = array('type' => 'onlineapp', 'version' => $v, 'data' => $apps);
        }

        $this->output(0, '', $ret);
    }

    /**
     * 支持4.0 新
     * 轻应用
     */
    public function webAppV2Action() {
        $appver    = $this->getInput('app_ver');
        $userVer   = $this->getInput('ver');
        $model     = $this->getInput('model');
        $operators = $this->getInput('operators');//运营商
        $ret       = array();
        $v         = Gionee_Service_Config::getValue('bookmark_version_4');
        $op        = isset(Gionee_Service_Bookmark::$opParam[$operators]) ? Gionee_Service_Bookmark::$opParam[$operators] : 1;
        $params    = array(
            'ver'        => array('&', 4),
            'op_type'    => array('&', $op),
            'updated_at' => array('>', $userVer),
        );

        //$rcKey = "BROWSER_webAppV2:{$v}:{$op}";
        //$list  = Common::getCache()->get($rcKey);
        $list = false;
        if ($list === false) {
            $list   = array();
            $result = Gionee_Service_Bookmark::getsBy($params, array('sort' => 'ASC'));
            foreach ($result as $key => $value) {
                $url    = html_entity_decode($value['url']);
                $list[] = array(
                    'id'         => $value['id'],
                    'name'       => $value['name'],
                    'iconUrl'    => Common::getImgPath() . $value['icon'],
                    'background' => $value['backgroud'],
                    'url'        => Common::clickUrl($value['id'], 'TOURL', $url),
                    'deletable'  => $value['is_delete'],
                    'operation'  => $value['operation'],
                    'updated_at' => intval($value['updated_at']),
                );
            }
            //Common::getCache()->set($rcKey, $list, 600);
        }
        $ret = array(
            'timestamp' => intval($v),
            'list'      => $list,
        );


        $this->output(0, '', $ret);
    }

    /**
     * 热词
     */
    public function searchHotWordsAction() {
        $app = $this->getInput('app');
        $num = $this->getInput('num');
        $arr = Gionee_Service_Baidu::apiKeys();
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
        $app     = $this->getInput('app');
        $from    = Gionee_Service_Baidu::getFromNo();

        $rcKey   = 'LIKE_WORDS:' . crc32($keyword);
        $content = Common::getCache()->get($rcKey);
        if (empty($content)) {
            $url = "http://m.baidu.com/su?from={$from}&wd={$keyword}&ie=utf-8&action=opensearch";
            //$content = Common::getUrlContent($url);
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


    /**
     * 启动统计接口
     */
    public function startAction() {
        $ret = time();
        $this->output(0, '', $ret);
    }

    /**
     * 本地化导航 广告接口
     */
    public function bannerAction() {
        $rcvKey  = 'LOCALNAV:banner:ver';
        $sysVer  = Common::getCache()->get($rcvKey);
        $userVer = $this->getInput('ver');
        $data    = array();
        $update  = 0;
        if ($sysVer != $userVer) {
            $list = Gionee_Service_LocalNavList::banner();
            foreach ($list['normal'] as $k => $v) {
                $data[] = $v;
            }

            $modelArr = array();
            foreach ($list['model'] as $k => $v) {
                $modelArr[$v['model_type']][] = $v;
            }

            $update = 1;
            Gionee_Service_Label::filterADData('localnav', $modelArr, $data);
        }
        $ret = array(
            'timestamp' => $sysVer,
            'updated'   => $update,
            'list'      => $data,
        );

        $this->output(0, '', $ret);
    }

    public function searchCardAction() {
        $rcKey = 'API_BROWSER_SEARCH_CARD';
        $show  = Common::getCache()->get($rcKey);
        if ($show === false) {
            $info = Gionee_Service_LocalNavType::get(2);
            $show = intval($info['status']);
            Common::getCache()->set($rcKey, $show, 600);
        }

        $ret = array(
            'timestamp' => time(),
            'show'      => $show,
            //'url'       => $tmp,
        );
        $this->output(0, '', $ret);
    }

    /**
     * 本地化导航 热门站点接口
     */
    public function hotsiteAction() {
        $rcvKey  = 'LOCALNAV:hotsite:ver';
        $sysVer  = Common::getCache()->get($rcvKey);
        $userVer = $this->getInput('ver');
        $list    = array();
        if ($sysVer != $userVer) {
            $list = Gionee_Service_LocalNavList::hotsite(true);
            Gionee_Service_Label::filterHotSiteData($list, 'localnav');

        }
        $ret = array(
            'timestamp' => $sysVer,
            'list'      => $list,
        );
        $this->output(0, '', $ret);
    }


    /**
     * 本地化导航 卡片接口
     */
    public function cardlistAction() {
        $imgPath = Common::getImgPath();
        $userVer = $this->getInput('ver');

        $list   = array();
        $sysver = Gionee_Service_Config::getValue('LOCALNAV_TYPE_VER');
        if (empty($sysver)) {
            Gionee_Service_Config::setValue('LOCALNAV_TYPE_VER', Common::getTime());
        }

        if ($sysver != $userVer) {
            $rcKey = 'BROWSER_CARDLIST:' . $sysver . '_' . $userVer;
            $list  = Common::getCache()->get($rcKey);
            if ($list === false) {
                $where   = array('updated_at' => array('>', $userVer), 'type' => array('>', 1),);
                $orderBy = array('sort' => 'ASC', 'id' => 'ASC');
                $columns = Gionee_Service_LocalNavType::getsBy($where, $orderBy);
                $ishtml  = Gionee_Service_Config::getValue('localnav_to_html');
                foreach ($columns as $v) {
                    $url = Common::getCurHost() . '/front/browser/localnav?id=' . $v['id'];

                    if (!empty($ishtml)) {
                        $url = 'http://static.3g.gionee.com/static/localnav/' . $v['id'] . '.html';
                    }

                    if (Common::isOverseas()) {
                        $url = Common::getCurHost() . '/front/browser/overseas?id=' . $v['id'];
                    }

                    $flag = intval($v['flag']);
                    if (empty($v['flag'])) {
                        $flag = !empty($v['is_show']) ? 0 : 1;
                    }

                    $list[] = array(
                        'id'        => intval($v['id']),
                        'name'      => $v['name'],
                        'url'       => $url,
                        'deletable' => intval($v['can_del']),
                        'type'      => intval($v['type']),
                        'show'      => intval($v['status']),
                        'operation' => $flag,
                        'sort'      => intval($v['sort']),
                        'info'      => $v['desc'],
                        'icon'      => !empty($v['img']) ? $imgPath . $v['img'] : '',
                    );
                }
                Common::getCache()->set($rcKey, $list, 600);
            }

        }

        $ret = array(
            'timestamp' => $sysver,
            'list'      => !empty($list) ? $list : array(),
            'manage_title' => Gionee_Service_Config::getValue('card_list_manage_title'),
        );
        $this->output(0, '', $ret);
    }


    public function cardlist2Action() {
        $imgPath = Common::getImgPath();
        $userVer = $this->getInput('ver');
        $sysver  = Gionee_Service_Config::getValue('LOCALNAV_TYPE_VER');
        $list    = array();
        if ($sysver != $userVer) {
            $rcKey = 'BROWSER_CARDLIST:' . $sysver . '_' . $userVer;
            $list  = Common::getCache()->get($rcKey);

            if ($list === false) {
                $where   = array('updated_at' => array('>', intval($userVer)), 'type' => array('>', 1),);
                $orderBy = array('sort' => 'ASC', 'id' => 'ASC');
                $columns = Gionee_Service_LocalNavType::getsBy($where, $orderBy);
                foreach ($columns as $v) {
                    $url = Common::getCurHost() . '/front/browser/localnav?id=' . $v['id'];
                    if (Common::isOverseas()) {
                        $url = Common::getCurHost() . '/front/browser/overseas?id=' . $v['id'];
                    }

                    $flag = intval($v['flag']);
                    if (empty($v['flag'])) {
                        $flag = !empty($v['is_show']) ? 0 : 1;
                    }

                    if ($flag == 0 && $v['created_at'] != $v['updated_at']) {
                        $flag = 2;
                    }

                    $list[] = array(
                        'id'        => intval($v['id']),
                        'name'      => $v['name'],
                        'url'       => $url,
                        'deletable' => intval($v['can_del']),
                        'type'      => intval($v['type']),
                        'show'      => intval($v['status']),
                        'operation' => $flag,
                        'sort'      => intval($v['sort']),
                        'info'      => $v['desc'],
                        'icon'      => !empty($v['img']) ? $imgPath . $v['img'] : '',
                    );
                }
                Common::getCache()->set($rcKey, $list, 600);
            }

        }

        $ret = array(
            'timestamp' => time(),
            'list'      => !empty($list) ? $list : array(),
        );
        $this->output(0, '', $ret);
    }


    /**
     * 服务器本地配置测试接口
     */
    public function LocalAction() {
        $data = Gionee_Service_LocalInterface::getAll();
        /* foreach ($data[1] as $k=>$v){
             $data[1][$k]['info'] = str_replace('"',"'",$v['info']);
         } */
        $this->assign('data', $data[1]);
    }

    private function _parseDomainKey($str) {
        $arr = explode('.', $str);
        $len = count($arr);
        $ret = array();
        if ($len >= 2) {
            $ret[1] = implode('.', array($arr[$len - 2], $arr[$len - 1]));
        }
        if ($len >= 3) {
            $ret[2] = implode('.', array($arr[$len - 3], $arr[$len - 2], $arr[$len - 1]));;
        }

        return $ret;
    }

    private function _parseDomainStr($str) {
        $arr = explode('.', $str);
        $len = count($arr);
        $ret = '';
        if ($len > 1) {
            $s = '';
            if ($len > 2) {
                $s = $arr[$len - 3];
            } else if ($len == 2) {
                $s = $arr[$len - 2];
            }
            $ret = substr($s, 0, 1);
        }

        return $ret;
    }

    public function faviconAction() {
        $keyword = filter_input(INPUT_POST, 'keyword');
        $urls    = json_decode($keyword, true);
        //$urls = array('m.baidu.com', 'http://www.baidu.com/xxx.html','sina.com/xxxx');
        $list = array();
        foreach ($urls as $url) {
            $urlk = urldecode($url);
            if (stristr($urlk, '/')) {
                $urlk = substr($urlk, 0, strpos($urlk, '/'));
            }

            if (stristr($urlk, 'http')) {
                $domain = parse_url($url, PHP_URL_HOST);
            } else {
                $domain = $urlk;
            }
            $domainKey = $this->_parseDomainKey($domain);
            $row       = array();
            if (!empty($domainKey[2])) {
                $row = Gionee_Service_BrowserFavicon::getByKey(md5($domainKey[2]));
            }
            if (empty($row) && !empty($domainKey[1])) {
                $row = Gionee_Service_BrowserFavicon::getByKey(md5($domainKey[1]));
            }

            $iconUrl = $title = $initial = '';
            if (!empty($row)) {
                $iconUrl = Common::getImgPath() . $row['img'];
            } else {
                $initial = $this->_parseDomainStr($domain);
            }

            $list[] = array(
                'url'     => $url,
                'initial' => $initial,
                'title'   => $title,
                'iconUrl' => $iconUrl,
                'domain'  => $domainKey,
            );
        }

        $ret = array(
            'timestamp' => time(),
            'list'      => $list,
        );
        $this->output(0, '', $ret);
    }

    public function changliaoAction() {
        $data = array(
            'timestamp' => time(),
            'list'      => array(
                array(
                    'url' => sprintf('%s/front/activity/cindex', Common::getCurHost()),
                )
            )
        );
        $this->output('0', '', $data);
    }

    public function getuserinfoAction() {
        $statTimeArr = User_Service_TaskBrowserOnline::getStageTime();
        $now         = time();
        $nowDate     = date('Ymd');
        $userInfo    = Gionee_Service_User::getCurUserInfo();
        if (empty($userInfo['id'])) {
            $stage    = 1;
            $nexttime = intval($statTimeArr[$stage][0]);
            $nextcoin = intval($statTimeArr[$stage][1]);
            $data     = array(
                'errorcode'           => -1,//0没有错误，其余错误码待定
                'errormsg'            => '用户未登陆',
                'timestamp'           => $now,
                'cookie'              => Gionee_Service_User::ckLogin(),
                'taskstage_online'    => $stage,//在线任务阶段
                'taskrule_onlinetime' => $nexttime,//当前任务秒数
                'taskrule_onlinecoin' => $nextcoin,//当前任务秒数
            );
            $this->output('0', '', $data);
        }
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);

        $uname = '';
        if (!empty($userInfo['nickname'])) {
            $uname = $userInfo['nickname'];
        } else if (Common::checkIllPhone($userInfo['username'])) {
            $uname = $userInfo['username'];
        }

        $where         = array('uid' => $userInfo['id'], 'cur_date' => $nowDate);
        $userStageList = array();
        $tmpList       = User_Service_TaskBrowserOnline::getDao()->getsBy($where);
        foreach ($tmpList as $val) {
            $userStageList[$val['cur_stage']] = $val;
        }

        $stage   = empty($userStageList) ? 1 : count($userStageList) + 1;
        $hasnext = $nexttime = $nextcoin = 0;
        if (isset($statTimeArr[$stage])) {
            $hasnext  = 1;
            $nexttime = intval($statTimeArr[$stage][0]);
            $nextcoin = intval($statTimeArr[$stage][1]);
        }

        $data = array(
            'errorcode'           => 0,//0没有错误，其余错误码待定
            'errormsg'            => "",
            'timestamp'           => $now,
            'name'                => $uname,//用户名
            'integral'            => $userScoreInfo['remained_score'],//积分
            'userlevel'           => $userInfo['experience_level'],//积分
            'hasnext'             => $hasnext,//在线任务阶段
            'taskstage_online'    => $stage,//在线任务阶段
            'taskrule_onlinetime' => $nexttime,//当前任务秒数
            'taskrule_onlinecoin' => $nextcoin,//当前任务秒数
        );

        $this->output('0', '', $data);
    }

    public function onlinetaskAction() {
        $stage   = $this->getInput('stage');
        $now     = time();
        $nowDate = date('Ymd');

        $statTimeArr = User_Service_TaskBrowserOnline::getStageTime();
        if (!isset($statTimeArr[$stage])) {
            $data = array(
                'errorcode' => -1,//0没有错误，其余错误码待定
                'errormsg'  => '非法参数',
                'timestamp' => $now,
            );
            $this->output('0', '', $data);
        }

        $userInfo = Gionee_Service_User::getCurUserInfo();
        if (empty($userInfo['id'])) {
            $data = array(
                'errorcode' => -1,//0没有错误，其余错误码待定
                'errormsg'  => '用户未登陆',
                'timestamp' => $now,
            );
            $this->output('0', '', $data);
        }


        $where         = array('uid' => $userInfo['id'], 'cur_date' => $nowDate);
        $userStageList = array();
        $tmpList       = User_Service_TaskBrowserOnline::getDao()->getsBy($where);
        foreach ($tmpList as $val) {
            $userStageList[$val['cur_stage']] = $val;
        }
        $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id']);

        $nextStage = $stage + 1;
        $hasnext   = $nexttime = $nextcoin = 0;
        if (isset($statTimeArr[$nextStage])) {
            $hasnext  = 1;
            $nexttime = intval($statTimeArr[$nextStage][0]);
            $nextcoin = intval($statTimeArr[$nextStage][1]);
        }

        if (!empty($userStageList[$stage])) {
            $data = array(
                'errorcode'           => -1,//0没有错误，其余错误码待定
                'errormsg'            => '您已领取该阶段奖励',
                'timestamp'           => $now,
                'hasnext'             => $hasnext,//在线任务阶段
                'taskstage_online'    => $nextStage,//在线任务阶段
                'taskrule_onlinetime' => $nexttime,//当前任务秒re数
                'taskrule_onlinecoin' => $nextcoin,//当前任务秒数
            );
            $this->output('0', '', $data);
        }

        $addData = array(
            'uid'        => $userInfo['id'],
            'updated_at' => $now,
            'cur_stage'  => $stage,
            'cur_date'   => $nowDate,
        );
        list($curTime, $curCoin) = $statTimeArr[$stage];
        User_Service_TaskBrowserOnline::getDao()->insert($addData);
        if (!empty($curCoin)) {
            User_Service_Gather::changeScoresAndWriteLog($userInfo['id'], $curCoin, 208, 2, $stage);

            $userScoreInfo = User_Service_Gather::getInfoByUid($userInfo['id'], true);
        }

        $uname = '';
        if (!empty($userInfo['nickname'])) {
            $uname = $userInfo['nickname'];
        } else if (Common::checkIllPhone($userInfo['username'])) {
            $uname = $userInfo['username'];
        }
        $data = array(
            'errorcode'           => 0,//0没有错误，其余错误码待定
            'errormsg'            => "",
            'timestamp'           => $now,
            'name'                => $uname,//用户名
            'integral'            => intval($userScoreInfo['remained_score']),//积分
            'userlevel'           => intval($userInfo['experience_level']),//积分
            'hasnext'             => $hasnext,//在线任务阶段
            'taskstage_online'    => $nextStage,//在线任务阶段
            'taskrule_onlinetime' => $nexttime,//当前任务秒数
            'taskrule_onlinecoin' => $nextcoin,//当前任务秒数
        );

        $this->output('0', '', $data);

    }

    public function amigologinAction() {
        $val = $this->getInput('val');
        if ($val) {
            $e       = new Util_Encrypt();
            $dataStr = $e->aesDecrypt($val);
            list($username, $outUId, $t) = explode(',', $dataStr);
            $debug = array($username, $outUId, $t);
            if (!Common::checkIllPhone($username)) {
                echo '非法号码' . json_encode($debug);
                exit;
            }

            $uaArr    = Util_Http::ua();
            $userInfo = Gionee_Service_User::getUserByName($username);
            if (empty($userInfo['id'])) {
                $parms = array(
                    'username'  => $username,
                    'mobile'    => $username,
                    'out_uid'   => $outUId,
                    'imei_id'   => $uaArr['uuid'],
                    'come_from' => 4,//从amigo系统登陆
                );

                $ret = Gionee_Service_User::addUser($parms);
                if (!$ret) {
                    echo "注册失败" . json_encode($debug);
                    exit;
                }
                $userInfo = Gionee_Service_User::getUserByName($username);
            }
            Gionee_Service_User::cookieUser($userInfo);
            echo "登陆成功" . json_encode($debug);
        }
        exit;
    }


    public function wkSwitchAction() {
		$data['timestamp'] = time();
        $app_ver = $this->getInput('app_ver');
		$app_model=$this->getInput('model');
		$rcKey = 'BROWSER_WANKA:' . $app_model . '_' . $app_ver;
		$arr = explode(".",$app_ver);
		$app_ver=@$arr[0].'.'.@$arr[1].'.'.@$arr[2];

        $a = array();

        $a[]  = array('name' => $app_model, 'app_ver' => $app_ver);
        $a[]  = array('name' => $app_model, 'app_ver' => 'default');
        //$a[]  = array('name' => $app_model);
        $a[]  = array('name' => 'default', 'app_ver' => $app_ver);
        //$a[]  = array('app_ver' => $app_ver);
        $a[]  = array('name' => 'default', 'app_ver' => 'default');



        $list  = Common::getCache()->get($rcKey);
        if ($list === false) {
			    $list = array();
                foreach($a as $v) {
                    $tmpList  = Gionee_Service_Wanka::getBy($v);
                    if(!empty($tmpList)) {
                        break;
                    }

				}
				$v  = array(
					"wk_main_switch",
					"wk_searchEngines_switch",
					"wk_hotKeyword_switch",
					"wk_suggested_switch"
				);
				foreach ($v as $k) {
					$t = 0;
					if (!empty($tmpList[$k])) {
						$t = 1;
					}
					$list[$k] = $t;
				}

				Common::getCache()->set($rcKey, $list, 60);
        }
        $data['list'] = array($list);
        $this->output(0, '', $data);
    }


/*
    public function wkSwitchAction() {
        $data['timestamp'] = time();

        $str  = Gionee_Service_Config::getValue('wanka_conf');
        $arr  = json_decode($str, true);
        $v    = array(
            "wk_main_switch",
            "wk_searchEngines_switch",
            "wk_hotKeyword_switch",
            "wk_suggested_switch"
        );
        $list = array();
        foreach ($v as $k) {
            $t = 0;
            if (!empty($arr[$k])) {
                $t = 1;
            }
            $list[$k] = $t;
        }

        $data['list'] = array($list);
        $this->output(0, '', $data);
    }
*/

    public function bootpageAction() {
        $userVer = $this->getInput('version');
        $dataVer = Gionee_Service_Config::getValue('welcome_version_4');
        if ($userVer != $dataVer) {
            $rcKey = 'WELCOME_ALL_4:' . $dataVer;
            $list  = Common::getCache()->get($rcKey);
            if ($list === false) {
                $list = Gionee_Service_Welcome::getsBy(array('ver' => 4), array('sort' => 'ASC'));
                $list = !empty($list) ? $list : array();
                Common::getCache()->set($rcKey, $list, 86400);
            }

            $data    = array();
            $nowTime = Common::getTime();

            foreach ($list as $value) {
                if ($value['start_time'] > $nowTime || $value['end_time'] < $nowTime || $value['status'] == 0) {
                    continue;
                }
                $data[] = array(
                    'id'    => intval($value['id']),
                    'name'  => $value['name'],
                    'text'  => $value['text'],
                    'url'   => trim(html_entity_decode($value['url'])),
                    'image' => Common::getImgPath() . $value['img'],
                    'sort'  => intval($value['sort']),
                );
            }
            $json_data = array('timestamp' => $dataVer, 'list' => $data);
            $this->output(0, '', $json_data);
        }

        exit;
    }

    public function getWebSiteChannelsAction() {
        $where   = array('status' => 1);
        $orderBy = array('updated_at' => 'desc');
        $tmpList = Browser_Service_ReplaceSearch::getDao()->getsBy($where, $orderBy);
        $list    = array();
        foreach ($tmpList as $val) {
            $list[] = array(
                'id'      => intval($val['id']),
                'url'     => html_entity_decode($val['url']),
                'url_new' => html_entity_decode($val['replace_url']),
            );
        }


        $out = array(
            'timestamp' => Common::getTime(),
            'list'      => $list,
        );
        $this->output(0, '', $out);
    }
    public function cardTextAction() {
        $ret  = Gionee_Service_Config::getValue('LocalnavCardTitleConf');
        $list    = array();
        $list[] = json_decode($ret, true);
        $out = array(
            'timestamp' => Common::getTime(),
            'list'      => $list,
        );
        $this->output(0, '', $out);
    }
}




