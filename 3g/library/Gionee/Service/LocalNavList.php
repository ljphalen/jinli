<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 本地化导航
 */
class Gionee_Service_LocalNavList {

    static $limitOutArr = array(
        102 => array(30, 5, 11, 1),//新闻列表
        109 => array(30, 1, 13, 31),//笑话段落
        113 => array(30, 5, 14, 21),//美图展示
    );

    public static function count($params) {
        return self::_getDao()->count($params);
    }

    /**
     *
     * @param int $id
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     *
     * @param array $data
     * @param int   $id
     */
    public static function set($data, $id) {
        if (!is_array($data)) {
            return false;
        }
        return self::_getDao()->update($data, intval($id));
    }

    /**
     *
     * @param array $ids
     * @param array $data
     */
    public static function sets($ids, $data) {
        if (!is_array($data) || !is_array($ids)) {
            return false;
        }
        return self::_getDao()->updates('id', $ids, $data);
    }

    /**
     *
     * @param int $id
     */
    public static function del($id) {
        return self::_getDao()->delete(intval($id));
    }


    public static function getBy($where = array(), $orderBy = array()) {
        return self::_getDao()->getBy($where, $orderBy);
    }

    /**
     *
     * @param array $data
     */
    public static function add($data) {
        if (!is_array($data)) {
            return false;
        }
        $id  = 0;
        $ret = self::_getDao()->insert($data);
        if ($ret) {
            $id = self::_getDao()->getLastInsertId();
        }
        return $id;
    }


    public static function getCommonData($id) {
        $limitArr = array(
            'news_img'    => 5,
            'news_list'   => 5,
            'img3'        => 3,
            'img4'        => 4,
            'img5'        => 5,
            'gou_img3'    => 3,
            'words5'      => 5,
            'fun_text'    => 1,
            'fun_list'    => 5,
            'topic'       => 5,
            'comic'       => 3,
            'share_index' => 3,
        );
        $now      = time();
        $params   = array(
            'status'     => 1,
            'column_id'  => $id,
            'start_time' => array('<', $now),
            'end_time'   => array('>', $now)
        );
        $info     = Gionee_Service_LocalNavColumn::get($id);
        $style    = $info['style'];
        $orderBy  = array('sort' => 'ASC', 'id' => 'desc');
        $limit    = isset($limitArr[$style]) ? $limitArr[$style] : 10;
        $tmpList  = Gionee_Service_LocalNavList::getList(1, $limit, $params, $orderBy);
        $list     = Gionee_Service_LocalNavList::buildStyleData($style, $tmpList);
        return $list;
    }

    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id' => 'DESC')) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        return self::_getDao()->getList($start, $limit, $params, $orderBy);
    }

    public static function buildStyleData($style, $list) {
        $func = '_style_' . $style;
        if (!method_exists('Gionee_Service_LocalNavList', $func)) {
            $func = '_style_common';
        }
        return self::$func($list);
    }

    public static function out_data_2_cache() {
        $ret = false;
        foreach (self::$limitOutArr as $id => $tmpV) {
            $rows     = self::getOutData($id, true);
            $ret[$id] = count($rows);
        }
        return $ret;
    }

    public static function getOutData($id, $sync = false) {
        $rcKey = 'LOCALNAV:' . $id;
        $ret   = Common::getCache()->get($rcKey);
        if ($ret === false || $sync) {
            $ret = self::_buildOutData($id);
            Common::getCache()->set($rcKey, $ret, 3600);
        }
        return $ret;
    }

    private static function _buildOutData($id) {
        $limit = 10;
        if (isset(self::$limitOutArr[$id])) {
            list($limit,) = self::$limitOutArr[$id];
        }
        $orderBy = array('sort' => 'ASC');
        $now     = time();

        $params = array(
            'status'     => 1,
            'column_id'  => $id,
            'start_time' => array('<=', $now),
            'end_time'   => array('>=', $now),
        );

        $info = Gionee_Service_LocalNavColumn::get($id);

        $list0 = array();
        if ($id == 102) {//获取人工新闻图片
            $params['column_id'] = 101;
            $orderBy['is_out']   = 'asc';
            $tmpList             = Gionee_Service_LocalNavList::getList(1, 5, $params, $orderBy);
            $list0               = Gionee_Service_LocalNavList::buildStyleData($info['style'], $tmpList);
            foreach ($list0 as $k => $v) {
                $list0[$k]['t'] = 'img';
            }
            $limit = 25;
        }
        $orderBy['id'] = 'desc';
        //人工数据
        $params['column_id'] = $id;
        $params['is_out']    = 0;
        $tmpList             = Gionee_Service_LocalNavList::getList(1, $limit, $params, $orderBy);
        $list1               = Gionee_Service_LocalNavList::buildStyleData($info['style'], $tmpList);

        if ($id == 102) {
            foreach ($list1 as $k => $v) {
                $list1[$k]['t'] = 'txt';
            }
        }

        //外部数据
        $params['is_out'] = 1;
        $newLimit         = $limit - count($list1);
        $tmpList          = Gionee_Service_LocalNavList::getList(1, $newLimit, $params, $orderBy);
        $list2            = Gionee_Service_LocalNavList::buildStyleData($info['style'], $tmpList);
        if ($id == 102) {
            foreach ($list2 as $k => $v) {
                $list2[$k]['t'] = 'txt';
            }
        }
        $list = array_merge($list0, $list1, $list2);
        return $list;
    }

    static public function banner($sync = false) {
        $rcKey  = 'LOCALNAV:banner';
        $rcvKey = 'LOCALNAV:banner:ver';
        $list   = Common::getCache()->get($rcKey);
        if ($list === false || $sync) {
            $list = self::_banner();
            //$nowVer = crc32(Common::jsonEncode($list));
            $nowVer = time();
            $sysVer = Common::getCache()->get($rcvKey);
            if ($sysVer != $nowVer) {
                Common::getCache()->set($rcvKey, $nowVer, Common::T_ONE_DAY);
            }
            Common::getCache()->set($rcKey, $list, Common::T_ONE_DAY);
        }
        return $list;
    }

    static private function _banner() {
        $now     = time();
        $orderBy = array('sort' => 'ASC', 'id' => 'DESC');
        $where   = array(
            'column_id'  => 1,
            'status'     => 1,
            'start_time' => array('<', $now),
            'end_time'   => array('>', $now)
        );
        $tmpList = Gionee_Service_LocalNavList::getsBy($where, $orderBy);
        $imgPath = Common::getImgPath();
        $list    = array();
        foreach ($tmpList as $val) {
            if (intval($val['model_id'])) {
                $list['model'][] = array(
                    'id'         => intval($val['id']),
                    'enabled'    => 1,
                    'info'       => $val['name'],
                    'url'        => Common::clickUrl($val['id'], 'LOCAL_NAV', $val['link']),
                    'image'      => $imgPath . $val['img'],
                    'model_id'   => $val['model_id'],
                    'model_type' => $val['model_type'],
                );
            } else {
                $list['normal'][] = array(
                    'id'         => intval($val['id']),
                    'enabled'    => 1,
                    'info'       => $val['name'],
                    'url'        => Common::clickUrl($val['id'], 'LOCAL_NAV', $val['link']),
                    'image'      => $imgPath . $val['img'],
                    'model_id'   => $val['model_id'],
                    'model_type' => $val['model_type'],
                );
            }
        }
        return $list;
    }

    public static function getsBy($where = array(), $orderBy = array()) {
        return self::_getDao()->getsBy($where, $orderBy);
    }

    static public function hotsite($sync = false) {
        $rcKey  = 'LOCALNAV:hotsite';
        $rcvKey = 'LOCALNAV:hotsite:ver';
        $list   = Common::getCache()->get($rcKey);
        if ($list === false || $sync) {
            $list = self::_hotsite();
            //$nowVer = crc32(Common::jsonEncode($list));
            $nowVer = time();
            $sysVer = Common::getCache()->get($rcvKey);
            if ($sysVer != $nowVer) {
                Common::getCache()->set($rcvKey, $nowVer, Common::T_ONE_DAY);
            }
            Common::getCache()->set($rcKey, $list, Common::T_ONE_DAY);
        }

        return $list;
    }

    public static function getModelData($mids = array()) {
        if (empty($mids)) return;
        return self::_getModelHotsite($mids);
    }


    static private function _getModelHotsite($mids) {
        $key  = '3G:LOCALNAV:MODEL:DATA:' . implode(',', $mids);
        $list = Common::getCache()->get($key);
        if ($list === false) {
            $now     = time();
            $orderBy = array('sort' => 'ASC', 'id' => 'DESC');
            $where   = array(
                'column_id'  => 3,
                'status'     => 1,
                'start_time' => array('<', $now),
                'end_time'   => array('>', $now),
                'model_id'   => array("IN", $mids),
            );
            $tmpList = Gionee_Service_LocalNavList::getsBy($where, $orderBy);
            $imgPath = Common::getImgPath();
            foreach ($tmpList as $val) {
                $link               = Common::clickUrl($val['id'], 'LOCAL_NAV', $val['link']);
                $list[$val['sort']] = array(
                    'id'        => intval($val['id']),
                    'sort'      => intval($val['sort']),
                    'name'      => $val['name'],
                    'url'       => $link,
                    'icon'      => $imgPath . $val['img'],
                    'textcolor' => $val['color'],
                );
            }
            Common::getCache()->set($key, $list, 100);
        }
        return $list;
    }


    static private function _hotsite() {
        $now     = time();
        $orderBy = array('sort' => 'ASC', 'id' => 'DESC');
        $where   = array(
            'column_id'  => 3,
            'status'     => 1,
            'start_time' => array('<', $now),
            'end_time'   => array('>', $now),
        );
        $list    = array();
        $tmpList = Gionee_Service_LocalNavList::getsBy($where, $orderBy);
        $imgPath = Common::getImgPath();
        foreach ($tmpList as $val) {
            $link   = Common::clickUrl($val['id'], 'LOCAL_NAV', $val['link']);
            $list[] = array(
                'id'         => intval($val['id']),
                'sort'       => intval($val['sort']),
                'name'       => $val['name'],
                'url'        => $link,
                'icon'       => $imgPath . $val['img'],
                'textcolor'  => $val['color'],
                'model_type' => $val['model_type'],
                'model_id'   => $val['model_id'],
            );
        }
        return $list;
    }

    private static function _style_topic($list) {
        $data = array();
        foreach ($list as $k => $v) {
            $ext       = json_decode($v['ext'], true);
            $topicInfo = Gionee_Service_Topic::getInfo($ext['id']);
            if (!empty($topicInfo['id'])) {
                $data[$k]             = array(
                    'id'   => $v['id'],
                    'pos'  => $v['sort'],
                    'url'  => Common::clickUrl($v['id'], 'LOCAL_NAV', $v['link']),
                    'name' => $v['name'],
                );
                $data[$k]['topic_id'] = $topicInfo['id'];
                $data[$k]['option']   = $topicInfo['option'];
                $data[$k]['img']      = $topicInfo['img'];
                $data[$k]['typeimg']  = $topicInfo['typeimg'];
                $data[$k]['desc']     = $ext['desc'];
                $data[$k]['type']     = $topicInfo['type'];
            }
        }
        return $data;
    }

    private static function _style_lottery($list) {
        $data = array();
        foreach ($list as $k => $v) {
            $data[$k]    = array(
                'id'   => $v['id'],
                'pos'  => $v['sort'],
                'name' => $v['name'],
                'link' => Common::clickUrl($v['id'], 'LOCAL_NAV', $v['link']),
            );
            $ext         = json_decode($v['ext'], true);
            $lotteryData = Gionee_Service_Config::getValue('LOTTERY_TYPE_DATA');
            $lotteryData = !empty($lotteryData) ? json_decode($lotteryData, true) : array();
            $type        = $ext['type'];
            if (!empty($lotteryData[$type])) {
                $tmp                        = str_replace('#', ',', $lotteryData[$type]['code']);
                $tmp1                       = explode(',', $tmp);
                $lotteryData[$type]['code'] = array_slice($tmp1, 0, 7);
                $data[$k]                   = array_merge($data[$k], $lotteryData[$type]);
                !empty($data[$k]['date']) && $data[$k]['date'] = date('Y年m月d日', strtotime($data[$k]['date']));
            }
        }
        return $data;
    }

    private static function _style_tip_link($list) {
        $data = array();
        foreach ($list as $k => $v) {
            $data[$k] = array(
                'id'   => $v['id'],
                'pos'  => $v['sort'],
                'link' => Common::clickUrl($v['id'], 'LOCAL_NAV', $v['link']),
                'name' => $v['name'],
            );
            $ext      = json_decode($v['ext'], true);
            if (!empty($ext['icon'])) {
                $data[$k]['icon']  = $ext['icon'];
                $data[$k]['color'] = $ext['color'];
                $data[$k]['text']  = $ext['text'];
            }
        }
        return $data;
    }

    private static function _style_fun_text($list) {
        $data = array();
        foreach ($list as $k => $v) {
            $data[$k] = array(
                'id'   => $v['id'],
                'pos'  => $v['sort'],
                'link' => Common::clickUrl($v['id'], 'LOCAL_NAV', $v['link']),
                'name' => $v['name'],
                'ext'  => $v['ext'],
            );
        }
        return $data;
    }

    private static function _style_common($list) {
        $imgPath = Common::getImgPath();
        $data    = array();
        foreach ($list as $k => $v) {
            $data[$k] = array(
                'id'   => $v['id'],
                'pos'  => $v['sort'],
                'link' => Common::clickUrl($v['id'], 'LOCAL_NAV', $v['link']),
            );
            if ($v['name']) {
                $data[$k]['name'] = $v['name'];
            }
            if ($v['img']) {
                $img = $v['img'];
                if (strpos($v['img'], 'http') === false) {
                    $img = $imgPath . $v['img'];
                }
                $data[$k]['img'] = $img;
            }
            if ($v['color']) {
                $data[$k]['color'] = $v['color'];
            }
            if ($v['ext']) {
                $data[$k]['ext'] = $v['ext'];
            }
            $data[$k]['is_out'] = $v['is_out'];
        }
        return $data;
    }

    /**
     *
     * @return Gionee_Dao_LocalNavList
     */
    public static function _getDao() {
        return Common::getDao("Gionee_Dao_LocalNavList");
    }


    public static function run() {
        //广告数据同步缓存
        Gionee_Service_LocalNavList::banner(true);
        //热门站点数据同步缓存
        Gionee_Service_LocalNavList::hotsite(true);
        $str = "\n";
        if (!Common::isOverseas()) {//抓取外部数据
            $out = Gionee_Service_LocalNavList::out_data_2_card_news();
            $str .= 'up_card_news:' . Common::jsonEncode($out) . "\n";
            $out = Gionee_Service_LocalNavList::out_data_2_card_img();
            $str .= 'up_card_img:' . Common::jsonEncode($out) . "\n";
            $out = Gionee_Service_LocalNavList::out_data_2_card_fun();
            $str .= 'up_card_fun:' . Common::jsonEncode($out) . "\n";
            $out = Gionee_Service_LocalNavList::out_data_2_card_comic();
            $str .= 'up_card_comic:' . Common::jsonEncode($out) . "\n";
            $out = Gionee_Service_LocalNavList::out_data_2_card_stock();
            $str .= 'up_card_stock:' . Common::jsonEncode($out) . "\n";
            $out = Gionee_Service_LocalNavList::out_data_2_cache();
            $str .= 'out_data_2_cache:' . Common::jsonEncode($out) . "\n";
        }

        return $str;
    }

    public static function make_appcache_file() {
        $list      = Gionee_Service_LocalNavType::getsBy(array('type' => 3, 'flag' => 0));
        $staticDir = Common::staticDir();
        $cn        = 'Front_Browser_localnav';
        $caches    = Common::getConfig('cacheConfig', $cn);
        $out       = '';
        foreach ($list as $val) {
            $id          = $val['id'];
            $navData     = Gionee_Service_LocalNavList::getData($id, true);
            $imgFileList = array();
            foreach ($navData['list'] as $vList) {
                $imgs = $vList['list'];
                if ($id == 11) {
                    $imgs = $vList['list']['img'];
                }
                foreach ($imgs as $vData) {
                    if (!empty($vData['img'])) {
                        $ext = pathinfo($vData['img'], PATHINFO_EXTENSION);
                        if (in_array($ext, array('jpg', 'png')) && substr($vData['img'], 0, 7) == 'http://') {
                            $imgFileList[] = $vData['img'];
                        }
                    }
                }
            }

            $rcvKey = 'LOCALNAV:COLUMN:VER:' . $id;
            $sysVer = Common::getCache()->get($rcvKey);
            $str    = '';
            $str .= "CACHE MANIFEST\n";
            $str .= "\n#version:" . $sysVer . "\n\n";
            foreach ($caches as $key => $value) {
                $str .= sprintf("\n\n%s:\n", $key);
                if ($key == 'CACHE' && is_array($imgFileList)) {
                    $value = array_merge($imgFileList, $value);
                }
                $str .= implode("\n", $value);
            }
            $out .= "id:{$id},sysVer:{$sysVer}\n";


            $filename = $staticDir . 'localnav/' . $id . '.appcache';
            $out .= Common::writeContentToFile($filename, $str);
            $out .= ' (' . date('m/d H:i:s', filemtime($filename)) . ')';
            $out .= "\n";
            $filename = $staticDir . 'localnav_' . $id . '.cache';
            $out .= Common::writeContentToFile($filename, $str);
            $out .= ' (' . date('m/d H:i:s', filemtime($filename)) . ')';
            $out .= "\n";
        }
        return $out;
    }

    public static function card_data_2_html() {
        $list      = Gionee_Service_LocalNavType::getsBy(array('type' => 3, 'flag' => 0));
        $staticDir = Common::staticDir();
        $out       = '';
        foreach ($list as $val) {
            $id         = $val['id'];
            $data       = Gionee_Service_LocalNavList::getData($id, true);
            $webroot    = Common::getCurHost();
            $staticroot = Yaf_Application::app()->getConfig()->staticroot;
            $tpl        = array(
                'title'         => $data['info']['name'],
                'list'          => $data['list'],
                'initData'      => $data['initJSData'],
                'id'            => $id,
                'webroot'       => $webroot,
                'staticResPath' => $staticroot . '/apps/3g',
                'staticSysPath' => $staticroot . '/sys',
                'token'         => Common::getToken(),
                'version'       => Gionee_Service_Config::getValue('styles_version'),
            );

            $obj = new Yaf_View_Simple(BASE_PATH . '/application/modules/Front/views');
            $str = $obj->render('browser/localnav.phtml', $tpl);

            $filename = $staticDir . 'localnav/' . $id . '.html';
            $d        = '';
            if (strlen($str) > 100) {
                Common::writeContentToFile($filename, $str) . "\n";
                $d = date('m/d H:i:s', filemtime($filename));
            }
            $out .= $filename . '(' . $d . ')' . "\n";
        }
        return $out;
    }

    //获取外部数据
    static private function _get_out_data($columnId) {
        list($total, , $typeId, $out_column_id) = self::$limitOutArr[$columnId];
        $sourceIds = Nav_Service_NewsData::getSourceIds($out_column_id);
        echo "{$columnId}:{$total}:{$typeId}:" . implode(',', $sourceIds) . ":";


        $where = array('source_id' => array('IN', $sourceIds), 'status' => 1);
        $list  = array();

        $tmpList = Nav_Service_NewsDB::getRecordDao()->getList(0, $total * 3, $where, array('id' => 'desc'));
        foreach ($tmpList as $val) {
            $list[crc32($val['title'])] = $val;
        }

        return array($list, $typeId, $total);
    }


    public static function out_data_2_card_news() {
        $ids      = array();
        $columnId = 102;
        list($tmpList, $typeId, $total) = self::_get_out_data($columnId);
        $sort = 1;
        $a    = $b = 1;
        foreach ($tmpList as $value) {
            if (!empty($value['img'])) {
                if ($sort % 6 == 1) {
                    $columnId = 101;
                    $sortN    = $a;
                    $a++;
                } else {
                    $columnId = 102;
                    $sortN    = $b;
                    $b++;
                }

                $now  = time();
                $data = array(
                    'name'       => $value['title'],
                    //'link'       => $value['link'],
                    'link'       => Common::getCurHost() . '/nav/news/detail?act=card&id=' . $value['id'],
                    'updated_at' => $now,
                    'img'        => $value['img'],
                    'start_time' => $now - Common::T_ONE_HOUR,
                    'end_time'   => strtotime('+1 year'),
                    'ext'        => 0,
                    'sort'       => $sortN,
                    'is_out'     => 1,
                    //'status'     => 1,
                );

                $path   = '/news/' . date('Ymd');
                $newImg = Common::downImg($data['img'], $path);
                if ($newImg) {
                    Common::genThumbImg($newImg, 640, 300, 0);
                    $data['img'] = $newImg;
                    $data['ext'] = 1;
                }

                $where = array('column_id' => $columnId, 'is_out' => 1, 'sort' => $sortN);

                $row = Gionee_Service_LocalNavList::getBy($where);
                if (empty($row)) {
                    $data['is_out']     = 1;
                    $data['status']     = 1;
                    $data['column_id']  = $columnId;
                    $data['type_id']    = $typeId;
                    $data['created_at'] = $now;
                    $id                 = Gionee_Service_LocalNavList::add($data);
                    $str                = $id . '_' . $data['img'];
                    $ids[$sort]         = $str;
                } else {
                    Gionee_Service_LocalNavList::set($data, $row['id']);
                    $str        = $row['id'] . '_' . basename($data['img']);
                    $ids[$sort] = $str;
                }

                echo $sort;
                if ($sort == $total) {
                    break;
                }
                $sort++;
            }
            echo '.';
        }

        echo "\n";
        return $ids;
    }

    public static function out_data_2_card_img() {
        $ids      = array();
        $columnId = 113;
        list($tmpList, $typeId, $total) = self::_get_out_data($columnId);
        $sort = 1;
        foreach ($tmpList as $value) {
            //preg_match("/性感|诱惑|魅惑|性|比基尼/", $value['title'], $matches);
            $matches = array();
            if (empty($matches) && !empty($value['img'])) {
                $now  = time();
                $data = array(
                    'name'       => $value['title'],
                    'link'       => Common::getCurHost() . '/nav/pic/detail?act=card&id=' . $value['id'],
                    'updated_at' => $now,
                    'img'        => $value['img'],
                    'start_time' => $now - Common::T_ONE_HOUR,
                    'end_time'   => strtotime('+1 year'),
                    'ext'        => 0,
                    'sort'       => $sort,
                    'is_out'     => 1,
                    'status'     => 1,
                );

                $path   = '/news/' . date('Ymd');
                $newImg = Common::downImg($data['img'], $path);
                if ($newImg) {
                    Common::genThumbImg($newImg, 210, 270, true);
                    $data['img'] = $newImg;
                    $data['ext'] = 1;
                }

                $where = array('column_id' => $columnId, 'is_out' => 1, 'sort' => $sort);
                $row   = Gionee_Service_LocalNavList::getBy($where);
                if (empty($row)) {
                    $data['is_out']     = 1;
                    $data['status']     = 1;
                    $data['created_at'] = $now;
                    $data['column_id']  = $columnId;
                    $data['type_id']    = $typeId;
                    $id                 = Gionee_Service_LocalNavList::add($data);
                    $str                = $id . '_' . $data['img'];
                    $ids[$sort]         = $str;
                } else {
                    Gionee_Service_LocalNavList::set($data, $row['id']);
                    $str        = $row['id'] . '_' . basename($data['img']);
                    $ids[$sort] = $str;
                }
                echo $sort;
                if ($sort == $total) {
                    break;
                }
                $sort++;
            }
            echo '.';
        }
        echo "\n";
        return $ids;
    }

    public static function out_data_2_card_fun() {
        $ids      = array();
        $columnId = 109;
        list($tmpList, $typeId, $total) = self::_get_out_data($columnId);
        $sort = 1;
        foreach ($tmpList as $value) {
            $ext      = '';
            $contents = json_decode($value['content'], true);
            foreach ($contents as $v) {
                $ext .= $v['value'];
            }
            $ext = str_ireplace('　', '', $ext);
            $len = mb_strlen($ext, 'utf-8');//70-120
            if ($len > 70 && $len < 120) {
                $now  = time();
                $data = array(
                    'name'       => $value['title'],
                    'link'       => Common::getCurHost() . '/nav/fun/detail?act=card&id=' . $value['id'],
                    'updated_at' => $now,
                    'img'        => $value['img'],
                    'start_time' => $now - Common::T_ONE_HOUR,
                    'end_time'   => strtotime('+1 year'),
                    'ext'        => $ext,
                    'sort'       => $sort,
                    'is_out'     => 1,
                    'status'     => 1,
                );

                $path   = '/news/' . date('Ymd');
                $newImg = Common::downImg($data['img'], $path);
                if ($newImg) {
                    Common::genThumbImg($newImg, 210, 270, true);
                    $data['img'] = $newImg;
                    $data['ext'] = 1;
                }

                $where = array('column_id' => $columnId, 'is_out' => 1, 'sort' => $sort);
                $row   = Gionee_Service_LocalNavList::getBy($where);
                if (empty($row)) {
                    $data['is_out']     = 1;
                    $data['status']     = 1;
                    $data['created_at'] = $now;
                    $data['column_id']  = $columnId;
                    $data['type_id']    = $typeId;
                    $id                 = Gionee_Service_LocalNavList::add($data);
                    $str                = $id . '_' . $data['img'];
                    $ids[$sort]         = $str;
                } else {
                    Gionee_Service_LocalNavList::set($data, $row['id']);
                    $str        = $row['id'] . '_' . basename($data['img']);
                    $ids[$sort] = $str;
                }
                echo $sort;
                if ($sort == $total) {
                    break;
                }
                $sort++;
            }
            echo '.';
        }
        echo "\n";
        return $ids;
    }

    static public function out_data_2_card_comic() {
        $total     = 3;
        $comicData = Gionee_Service_LocalNavList::getComicOutData();
        $ids       = array();
        $tmp       = Gionee_Service_LocalNavColumn::getBy(array('style' => 'comic'));
        if (empty($tmp['id'])) {
            return array();
        }
        $columnId = $tmp['id'];
        $typeId   = $tmp['type_id'];
        $sort     = 1;
        foreach ($comicData as $value) {
            $ext  = '';
            $now  = time();
            $data = array(
                'name'       => $value['name'],
                'link'       => $value['out_link'],
                'updated_at' => $now,
                'img'        => $value['img'],
                'start_time' => $now - Common::T_ONE_HOUR,
                'end_time'   => strtotime('+1 year'),
                'ext'        => $ext,
                'sort'       => $sort,
                'is_out'     => 1,
                //'status'     => 1,
            );

            $path   = '/news/' . date('Ymd');
            $newImg = Common::downImg($data['img'], $path);
            if ($newImg) {
                Common::genThumbImg($newImg, 180, 240, true);
                $data['img'] = $newImg;
                $data['ext'] = 1;
            }

            $where = array('column_id' => $columnId, 'is_out' => 1, 'sort' => $sort);
            $row   = Gionee_Service_LocalNavList::getBy($where);
            if (empty($row)) {
                $data['is_out']     = 1;
                $data['status']     = 1;
                $data['created_at'] = $now;
                $data['column_id']  = $columnId;
                $data['type_id']    = $typeId;
                $id                 = Gionee_Service_LocalNavList::add($data);
                $str                = $id . '_' . $data['img'];
                $ids[$sort]         = $str;
            } else {
                Gionee_Service_LocalNavList::set($data, $row['id']);
                $str        = $row['id'] . '_' . basename($data['img']);
                $ids[$sort] = $str;
            }
            echo $sort;
            if ($sort == $total) {
                break;
            }
            $sort++;
            echo '.';
        }
        echo "\n";
        return $ids;
    }

    static public function out_data_2_card_stock() {
        $ids = array();
        $tmp = Gionee_Service_LocalNavColumn::getBy(array('style' => 'stock_news'));
        if (empty($tmp['id'])) {
            return array();
        }
        $columnId = $tmp['id'];
        $typeId   = $tmp['type_id'];
        $total    = 5;
        $tmp      = Nav_Service_NewsDB::getSourceDao()->getsBy(array('column_id' => 8));
        $tids     = array();
        foreach ($tmp as $v) {
            if ($v['status'] == 1) {
                $tids[] = $v['id'];
            }
        }

        $where = array('status' => 1);
        if (!empty($tids)) {
            $where['source_id'] = array('IN', $tids);
        }
        echo "{$columnId}:{$total}:{$typeId}:" . implode(',', $tids) . ":";

        $tmpList                 = array();
        $where['out_created_at'] = array('<', Common::getTime());
        $_tmpList                = Nav_Service_NewsDB::getRecordDao()->getList(0, $total, $where, array('out_created_at' => 'desc'));
        $i                       = 0;
        foreach ($_tmpList as $val) {
            $tmpList[$i] = $val;
            $i++;
        }

        $sort = 1;
        foreach ($tmpList as $value) {
            $img = !empty($value['img'])?$value['img']:'';
            //if (!empty($value['img'])) {
                $now  = time();
                $data = array(
                    'name'       => $value['title'],
                    //'link'       => $value['link'],
                    'link'       => Common::getCurHost() . '/nav/news/detail?act=card&id=' . $value['id'],
                    'updated_at' => $now,
                    'img'        => $img,
                    'start_time' => $now - Common::T_ONE_HOUR,
                    'end_time'   => strtotime('+1 year'),
                    'ext'        => 0,
                    'sort'       => $sort,
                    'is_out'     => 1,
                    'status'     => 1,
                );

                $where = array('column_id' => $columnId, 'is_out' => 1, 'sort' => $sort);
                $row   = Gionee_Service_LocalNavList::getBy($where);
                if (empty($row)) {
                    $data['is_out']     = 1;
                    $data['status']     = 1;
                    $data['column_id']  = $columnId;
                    $data['type_id']    = $typeId;
                    $data['created_at'] = $now;
                    $id                 = Gionee_Service_LocalNavList::add($data);
                    $str                = $id . '_' . $data['img'];
                    $ids[$sort]         = $str;
                } else {
                    Gionee_Service_LocalNavList::set($data, $row['id']);
                    $str        = $row['id'] . '_' . basename($data['img']);
                    $ids[$sort] = $str;
                }

                echo $sort;
                if ($sort == $total) {
                    break;
                }
                $sort++;
            //}
            echo '.';
        }

        echo "\n";
        return $ids;
    }

    static public function getData($id, $sync = false) {
        $rcKey = 'LOCALNAV:COLUMN:' . $id;
        $ret   = Common::getCache()->get($rcKey);
        if (empty($ret) || $sync) {
            $info       = Gionee_Service_LocalNavType::get($id);
            $data       = $initJSData = array();
            $orderBy    = array('sort' => 'ASC', 'id' => 'DESC');
            $columnList = Gionee_Service_LocalNavColumn::getsBy(array('type_id' => $info['id']), $orderBy);
            foreach ($columnList as $val) {
                if ($val['id'] == 101) {
                    continue;
                }
                if (isset(Gionee_Service_LocalNavList::$limitOutArr[$val['id']])) {
                    $tmpList = Gionee_Service_LocalNavList::getOutData($val['id'],$sync);
                    list($limit, $pageSize, $tid) = Gionee_Service_LocalNavList::$limitOutArr[$val['id']];
                    if ($val['id'] == 102) {
                        //后台人工添加数据从这2个数组中合并
                        $arr = array();
                        foreach ($tmpList as $v) {
                            $arr[$v['t']][] = $v;
                        }
                        $imgList = $arr['img'];

                        $txtList = array_slice($arr['txt'], 0, $pageSize);
                        $list    = array('img' => $imgList, 'txt' => $txtList);

                    } else {
                        $list = array_slice($tmpList, 0, $pageSize);
                    }
                    $initJSData[$tid] = self::_buildJSInitData($tmpList, $val);
                } else {
                    $list = Gionee_Service_LocalNavList::getCommonData($val['id']);
                }

                if (!empty($list)) {
                    $data[$val['id']] = array(
                        'id'    => $val['id'],
                        'style' => $val['style'],
                        'stat'  => array('type' => $info['name'], 'column' => $val['name']),
                        'list'  => $list
                    );
                }
            }

            $nowVer = crc32(Common::jsonEncode($data));
            $rcvKey = 'LOCALNAV:COLUMN:VER:' . $id;
            $sysVer = Common::getCache()->get($rcvKey);
            if ($sysVer != $nowVer) {
                error_log(date('Y-m-d H:i:s') . "{$id} - sysVer:{$sysVer},nowVer:{$nowVer}\n", 3, '/tmp/localnav');
                Common::getCache()->set($rcvKey, $nowVer, Common::T_ONE_DAY);
            }

            $ret['info']       = $info;
            $ret['list']       = $data;
            $ret['initJSData'] = isset($initJSData[$id]) ? $initJSData[$id] : '{}';
            Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
        }

        return $ret;
    }

    /**
     *
     * @param int $id 传入的卡片ID
     *
     * @return array
     */
    private static function _getModelTypeData($id) {
        $info      = self::_getNavTypeData($id);
        $ua        = Util_Http::ua();
        $model     = $ua['model'];
        $version   = $ua['app_ver'];
        $ip        = $ua['ip'];
        $mids      = Gionee_Service_LocalNavType::getModelTypeIds($model, $version, $ip, 'localnav');
        $modelData = Gionee_Service_LocalNavType::getModelTypeData($mids, $info['sort'], $info['type']);
        if (isset($modelData[$info['sort']])) {
            $info = $modelData[$info['sort']];
        }
        return $info;
    }

    private static function _getNavTypeData($id) {
        $rsKey = "LOCALNAV:TYPE:DATA:" . $id;
        $data  = Common::getCache()->get($rsKey);
        if (empty($data)) {
            $data = Gionee_Service_LocalNavType::get($id);
            Common::getCache()->set($rsKey, $data, Common::T_ONE_DAY);
        }
        return $data;
    }

    static private function _buildJSInitData($tmpList, $info) {

        $dataVer = crc32(Common::jsonEncode($tmpList));
        $list    = array();
        $imgs    = array();

        if ($info['id'] == 102) {//去掉第一个图片默认数据
            $arr = array();
            foreach ($tmpList as $v) {
                $arr[$v['t']][] = $v;
            }

            foreach ($arr['img'] as $val) {
                $ext    = pathinfo($val['img'], PATHINFO_EXTENSION);
                $img    = empty($val['ext']) ? $val['img'] : $val['img'] . '_640x300.' . $ext;
                $imgs[] = array(
                    'id'    => intval($val['id']),
                    'link'  => $val['link'],
                    'title' => $val['name'],
                    'img'   => $img,
                );
            }

            $tmpList = $arr['txt'];
        }

        foreach ($tmpList as $val) {
            $tmp = array(
                'id'    => intval($val['id']),
                'link'  => $val['link'],
                'title' => $val['name'],
            );

            if ($info['style'] == 'fun_text') {
                unset($tmp['title']);
                $tmp['text'] = $val['ext'];
            }

            if (!empty($val['img'])) {
                $tmp['img'] = $val['img'];
            }

            $list[] = $tmp;
        }

        $data = array(
            'timestamp' => $dataVer,
            'img'       => $imgs,
            'list'      => $list,
        );
        $out  = array(
            'success' => true,
            'msg'     => '',
            'data'    => $data
        );
        return Common::jsonEncode($out);
    }

    static public function getComicOutData($sync = false) {
        $rcKey = 'COMIC_OUT_DATA';
        $data  = Common::getCache()->get($rcKey);
        if (empty($data) || $sync) {
            $cc        = '700001834';
            $client_id = 3659;
            $key       = '8*Sgp#';
            $time      = date('YmdHis');
            $sign      = md5($client_id . $key . $time);
            $url       = sprintf('http://218.207.208.30:8080/pae/comic/get?client_id=%d&time=%s&sign=%s', $client_id, $time, $sign);
            $respStr   = file_get_contents($url);
            $respArr   = json_decode($respStr, true);
            $data      = array();
            foreach ($respArr as $k => $val) {
                $id               = $val['id'];
                $sign             = md5($client_id . $key . $id . $time);
                $dataUrl          = sprintf('http://218.207.208.30:8080/pae/comic/details/get?client_id=%d&id=%s&time=%s&sign=%s', $client_id, $id, $time, $sign);
                $respDataStr      = file_get_contents($dataUrl);
                $respDataArr      = json_decode($respDataStr, true);
                $outLink          = sprintf('http://wap.dm.10086.cn/m/dm/x?x=%s&cc=%s', $id, $cc);
                $data[$val['id']] = array(
                    'name'     => $val['name'],
                    'data_url' => $dataUrl,
                    'img'      => $respDataArr['cover3'],
                    'desc'     => $respDataArr['brief'],
                    'out_time' => $respDataArr['updated_time'],
                    'out_link' => $outLink,
                );

            }

            Common::getCache()->set($rcKey, $data, Common::T_ONE_DAY);
        }

        return $data;
    }


    static public function grap_stock_share_index() {
        $stockStr   = file_get_contents('http://hq.sinajs.cn/rn=' . time() . '&list=s_sh000001,s_sz399001,s_sz399006');
        $tmpArr     = explode("\n", $stockStr);
        $shareIndex = array();
        foreach ($tmpArr as $val) {
            $val = str_ireplace(array('"', ';', 'var hq_str_s_'), '', $val);
            $val = str_ireplace('=', ',', trim($val));
            if ($val) {
                $t            = explode(",", iconv('gbk', 'utf8', $val));
                $shareIndex[] = $t;
            }
        }
        $staticDir = Common::staticDir();
        $filename  = $staticDir . 'stock_share_index';
        $out       = array(
            'timestamp' => time(),
            'list'      => $shareIndex,
            'req_freq'  => 5,
        );
        return Common::writeContentToFile($filename, Common::jsonEncode($out));
    }


    static private function _stat_all_h5nav($date, $dataType) {
        $tmpArr = Gionee_Service_NgType::getsBy(array('page_id'=>1));
        foreach($tmpArr as $val) {
            $tids[] = $val['id'];
        }
        $columnData = Gionee_Service_NgColumn::getsBy(array('type_id'=>array('IN',$tids)));

        $sum       = 0;
        $detailSum = array();
        $dt        = $dataType == 1 ? Gionee_Service_Log::TYPE_CONTENT_UV : Gionee_Service_Log::TYPE_NAV;

        foreach ($columnData as $key => $val) { //先得到所有数据的统计信息
            $calculated       = Gionee_Service_Log::getNgColumnData($val['id'], date('Ymd', strtotime($date)), date('Ymd', strtotime($date)), $dt);
            $temp[$val['id']] = 0;

            foreach ($calculated as $cal) {
                foreach ($cal as $k => $v) {
                    $sum += $v;
                    $temp[$val['id']] += $v;
                    $detailSum[$val['id']][$k] += $v;
                }
            }
        }
        //var_dump($detailSum, $sum);
        return $sum;
    }


    static private function _stat_all_localnav($date, $dataType = 0) {

        $contentList = Gionee_Service_LocalNavList::getsBy(array());

        foreach ($contentList as $v) {
            $column            = Gionee_Service_LocalNavColumn::get($v['column_id']);
            $ids[]             = $v['id'];
            $words[$v['id']]   = $v['name'];
            $columns[$v['id']] = $column['name'];
        }
        $data = array();
        $sum  = 0;
        if (!empty($ids)) {
            $type           = $dataType == 1 ? 'localnav_uv' : 'localnav_pv';
            $params         = array();
            $params['type'] = $type;
            $params['key']  = array('IN', $ids);
            $params['date'] = date('Ymd', strtotime($date));

            $orderby  = array('`key`' => 'desc');
            $dataList = Gionee_Service_Log::getsBy($params, $orderby);

            foreach ($dataList as $k => $v) {
                $t                   = date('Y-m-d', strtotime($v['date']));
                $name                = isset($columns[$v['key']]) ? $columns[$v['key']] : '';
                $data[$name]['name'] = $name;
                $data[$name][$t] += $v['val'];
                $sum += $v['val'];
            }


        }
        //var_dump($data, $sum);
        return $sum;
    }

    static private function _stat_all_nav_fun($date, $dataType = 0) {
        $columns = Nav_Service_NewsData::getColumnList('fun');
        foreach ($columns as $val) {
            $vers['detail_' . $val['appId']] = $val['appName'] . '总详情';
        }
        $where          = array();
        $where['ver']   = 'nav_fun';
        $where['key']   = array_keys($vers);
        $where['stime'] = strtotime($date);
        $where['etime'] = strtotime($date);
        $where['type']  = $dataType == 1 ? 'uv' : 'pv';

        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        $sum   = 0;
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']] = $val['val'];
            $sum += $val['val'];
        }

        //var_dump($ret, $sum);
        return $sum;
    }


    static private function _stat_all_nav_pic($date, $dataType = 0) {
        $columns = Nav_Service_NewsData::getColumnList('pic');
        foreach ($columns as $val) {
            $vers['detail_' . $val['appId']] = $val['appName'] . '总详情';
        }
        $where          = array();
        $where['ver']   = 'nav_pic';
        $where['key']   = array_keys($vers);
        $where['stime'] = strtotime($date);
        $where['etime'] = strtotime($date);
        $where['type']  = $dataType == 1 ? 'uv' : 'pv';

        $datas = Gionee_Service_Log::getListByWhere($where);
        $ret   = array();
        $sum   = 0;
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']] = $val['val'];
            $sum += $val['val'];
        }

        //var_dump($ret, $sum);
        return $sum;
    }


    static private function _stat_all_nav_news($date, $dataType = 0) {
        $vers = array(
            'detail'      => '列表内容访问',
            'detail_rec'  => '推荐内容访问',
            'detail_h5'   => 'H5内容访问',
            'detail_card' => '卡片内容访问',
        );

        $where['ver']   = array('IN', array_keys($vers));
        $where['stime'] = strtotime($date);
        $where['etime'] = strtotime($date);
        $where['type']  = $dataType == 1 ? 'navnews_uv' : 'navnews_pv';
        $datas          = Gionee_Service_Log::getListByWhere($where);
        $ret            = array();
        $sum            = 0;
        foreach ($datas as $val) {
            $ret[$val['key']][$val['date']][$val['ver']] = $val['val'];
            $sum += $val['val'];
        }
        //var_dump($ret, $sum);
        return $sum;
    }

    static public function run_all_stat_data($date, $key, $dataType = 0) {
        $params = array(
            'date' => $date,
            'type' => $dataType == 1 ? 'uv' : 'pv',
            'key'  => $key,
            'ver'  => 'stat_all_data',
        );

        $row  = Gionee_Service_Log::getBy($params);
        $func = '_stat_' . $key;
        $num  = call_user_func_array(array(self, $func), array($date, $dataType));
        if (!empty($num)) {
            $params['val'] = $num;
            if (!empty($row['id'])) {
                Gionee_Service_Log::set($params, $row['id']);
            } else {
                Gionee_Service_Log::add($params);
            }
        }

        return $params;
    }

}