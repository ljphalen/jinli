<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 导航模块
 */
class Gionee_Service_Ng {

    const KEY_NG_TYPE_VER   = 'NG:TYPE_VER:';
    const KEY_NG_TYPE_DATA  = 'NG:TYPE_DATA:';
    const KEY_NG_AD         = 'NG:AD';
    const KEY_NG_INDEX_VER  = 'NG:INDEX_VER';
    const KEY_NG_INDEX_DATA = 'NG:INDEX_DATA';
    const KEY_NG_NEWS_LIST  = 'NG:NEWS_LIST:';
    const KEY_UV_NAV        = 'UV:NAV:';
    const KEY_UV_NEWS       = 'UV:NEWS:';
    const KEY_UV_URL        = 'UV:SURL:';

    static $pageType = array(
        '1' => "普通模块",
        '2' => '世界杯模块',
        '3' => '彩票模块',
        '4' => '三列图片切换模块',
        '5' => '新闻模块',
        '6' => '专题模块'
    );

    public static function getList($page = 1, $limit = 10, $params = array(), $sort = array()) {
        $params = self::_cookData($params);
        $start  = (max($page, 1) - 1) * $limit;
        $ret    = self::_getDao()->getList($start, $limit, $params, $sort);
        $total  = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     *
     * getByTypeids
     *
     * @param array $ids
     *
     * @return boolean
     */
    public static function getByTypeids($ids) {
        if (!is_array($ids)) return false;
        return self::_getDao()->getByTypeids($ids);
    }

    /**
     *
     * @param int $id
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    public static function update($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     * @param array $data
     * @param array $params
     */
    public static function updateBy($data, $params) {
        if (!is_array($data) || !is_array($params)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->updateBy($data, $params);
    }

    /**
     * @param int $id
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     * @param array $data
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);

        return self::_getDao()->insert($data);
    }

    /*
     * 添加一条信息，并返回其ID号
     */
    public static function getIdByAdd($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        if (self::_getDao()->insert($data)) {
            return self::_getDao()->getLastInsertId();
        } else {
            return false;
        }

    }

    /**
     * get by
     */
    public static function getBy($params = array(), $orderBy = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params, $orderBy);
    }

    /**
     *
     * @param array $params
     *
     * @return array
     */
    public static function getsBy($params = array(), $orderBy = array(), $groupBy = array()) {
        return self::_getDao()->getsBy($params, $orderBy, $groupBy);
    }

    /**
     *
     * @param array $params
     *
     * @return array
     */
    public static function getsByStyle($params = array(), $orderBy = array(), $limit) {
        return self::_getDao()->getsByStyle($params, $orderBy, $limit);
    }

    public static function deleteOldNews() {
        return self::_getDao()->deleteOldNews();
    }

    /**
     * 批量插入今日头条新闻
     *
     * @param array $data
     */
    public static function batchAddNews($data) {
        if (!is_array($data)) return false;
        self::_getDao()->mutiInsert($data);
        return true;
    }

    /**
     *
     * @param array $data
     */
    private static function _cookData($data) {
        $tmp = array();
        if (isset($data['id'])) $tmp['id'] = $data['id'];
        if (isset($data['title'])) $tmp['title'] = $data['title'];
        if (isset($data['partner'])) $tmp['partner'] = $data['partner'];
        if (isset($data['color'])) $tmp['color'] = $data['color'];
        if (isset($data['link'])) $tmp['link'] = $data['link'];
        if (isset($data['img'])) $tmp['img'] = $data['img'];
        if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
        if (isset($data['status'])) $tmp['status'] = $data['status'];
        if (isset($data['is_interface'])) $tmp['is_interface'] = $data['is_interface'];
        if (isset($data['column_id'])) $tmp['column_id'] = $data['column_id'];
        if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
        if (isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if (isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
        if (isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
        if (isset($data['style'])) $tmp['style'] = $data['style'];
        if (isset($data['is_recommend'])) $tmp['is_recommend'] = $data['is_recommend'];
        if (isset($data['ext'])) $tmp['ext'] = $data['ext'];
        if (isset($data['checkType'])) $tmp['checkType'] = $data['checkType'];
        if (isset($data['price'])) $tmp['price'] = $data['price'];
        if (isset($data['model_type'])) $tmp['model_type'] = $data['model_type'];
        if (isset($data['model_id'])) $tmp['model_id'] = $data['model_id'];
        if (isset($data['cp_id'])) $tmp['cp_id'] = $data['cp_id'];
        if (isset($data['label_id_list'])) $tmp['label_id_list'] = $data['label_id_list'];
        return $tmp;
    }


    /**
     * 用于导航页获取数据：每分钟都在后台任务计划时执行，并把数据存在Redis时，前台只从Redis中自动获取
     * 如果Redis服务器出问题，才会从数据库读取数据信息
     *
     * @param bool $db2cache 是否同步缓存
     *
     * @return array
     */
    static public function getIndexData($db2cache = false) {
        $rcKey = Gionee_Service_Ng::KEY_NG_INDEX_DATA;
        $ret   = Common::getCache()->get($rcKey);
        if (!empty($ret) && !$db2cache) {
            return $ret;
        }

        $ret = self::_getIndexData();
        Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
        return $ret;
    }

    static private function _getIndexData() {
        $orderBy     = array('sort' => 'ASC', 'id' => 'DESC');
        $hot_nav     = self::_getHotNav($orderBy);
        $hot_nav_txt = $hot_nav_pic = $imgs = array();
        $imgPath     = Common::getImgPath();
        $t_bi        = '';
        foreach ($hot_nav as $v) {
            $v['link'] = Common::clickUrl($v['id'], 'NAV', $v['link'], $t_bi);
            if (!empty($v['img'])) {
                $hot_nav_pic[] = $v;
                $imgs[]        = $imgPath . $v['img'];
            } else {
                $hot_nav_txt[] = $v;
            }
        }

        //分类栏目名称
        $i       = 1;
        $where   = array(
            'id'      => array('>', 1),
            'page_id' => 1,
            'status'  => 1
        );
        $columns = Gionee_Service_NgType::getsBy($where, $orderBy);
        foreach ($columns as $v) {
            if (!empty($v['icon'])) {
                $imgs[] = $imgPath . $v['icon'];
            }

            if (!empty($v['id'])) {
                $t        = 'cinner' . $i;
                $typeData = self::getTypeData($v['id'], true);

                $pageData['data'][$t] = array('data' => array('data' => $typeData['content']));
                foreach ($typeData['img'] as $tmpImg) {
                    $imgs[] = $tmpImg;
                }
                $i++;
            }
        }

        $pageData['hot_nav_pic']   = $hot_nav_pic;
        $pageData['hot_nav_nopic'] = $hot_nav_txt;
        $pageData['types']         = $columns;

        $ret = array('content' => $pageData, 'img' => $imgs);
        return $ret;
    }


    /**
     * 更新分类下面的数据版本
     *
     * @param $typeId
     * @param $typeData
     */
    private static function _compareTypeDataVer($typeId, $typeData) {
        //版本号管理
        $verKey = Gionee_Service_Ng::KEY_NG_TYPE_VER . $typeId;
        $newVer = crc32(json_encode($typeData));
        $oldVer = Common::getCache()->get($verKey);
        if ($newVer != $oldVer) {
            Common::getCache()->set($verKey, $newVer);
        }
    }

    private static function _getHotNav($orderBy) {
        $time         = time();
        $column_nav   = Gionee_Service_NgColumn::getBy(array('style' => 'hot_nav', 'status' => 1), $orderBy);
        $conditionArr = array(
            'column_id'  => $column_nav['id'],
            'status'     => 1,
            'start_time' => array('<', $time),
            'end_time'   => array('>', $time),
            //'model_type'   => '0',
            //'model_id'   => 0,
        );
        $hot_nav      = Gionee_Service_Ng::getsBy($conditionArr, $orderBy);
        return $hot_nav;
    }

    /**
     * 获得缓存分类数据
     */
    static public function getTypeData($typeId, $db2cache = false) {
        $rcKey = Gionee_Service_Ng::KEY_NG_TYPE_DATA . $typeId;
        $ret   = Common::getCache()->get($rcKey);
        if (!empty($ret['content']) && !$db2cache) {
            return $ret;
        }
        $ret = self::_getTypeData($typeId);

        self::_compareTypeDataVer($typeId, $ret);
        Common::getCache()->set($rcKey, $ret, 600);
        return $ret;
    }

    /**
     * 获得数据库分类数据
     */
    private static function _getTypeData($type_id) {
        $limitStyle = array(
            'img1'       => 1,
            'ticket'     => 1,
            'img2'       => 2,
            'img3'       => 3,
            'img3switch' => 9
        );
        $time       = time();
        $type       = Gionee_Service_NgType::get($type_id);
        $where      = array('type_id' => $type_id, 'status' => 1);
        $columns    = Gionee_Service_NgColumn::getsBy($where, array('sort' => 'ASC', 'id' => 'DESC'));
        $list       = $names = $more = $news = $imgs = array();
        foreach ($columns as $key => $value) {
            $params  = array(
                'status'     => 1,
                'column_id'  => $value['id'],
                'start_time' => array('<', $time),
                'end_time'   => array('>', $time)
            );
            $orderBy = array('sort' => 'ASC', 'id' => 'ASC');
            $limit   = isset($limitStyle[$value['style']]) ? $limitStyle[$value['style']] : 100;
            $ngs     = Gionee_Service_Ng::getsByStyle($params, $orderBy, $limit);

            self::genNgStyle($type, $ngs, $value, $imgs, $list);

        }
        $ret = array('content' => $list, 'img' => $imgs);
        return $ret;
    }

    public static function genLotteryData($params = array()) {
        //url
        //$url = Gionee_Service_Config::getValue('LOTTERY_REQUEST_API');
        $url = 'http://cai88.com/api/PrizeCenter.action?withjc=1';
        if (empty($url)) return;
        $curl   = new Util_Http_Curl($url);
        $res    = $curl->get($params);
        $result = array();
        if (!empty($res)) {
            $result = json_decode($res, true);
        }
        if (isset($result['model']) && !empty($result['model'])) {
            foreach ($result['model'] as $k => $v) {
                $typeArr[$v['n']] = $v;
            }
            Gionee_Service_Config::setValue('LOTTERY_TYPE_DATA', json_encode($typeArr));
        }
        return empty($typeArr) ? array() : $typeArr;
    }

    static private function _ng_style_lottery($k, $v, &$data, &$imgs, &$newsdata) {
        $data[$k]    = array(
            'id'   => $v['id'],
            'pos'  => $v['sort'],
            'link' => Common::clickUrl($v['id'], 'NAV', $v['link']),
        );
        $ext         = json_decode($v['ext'], true);
        $lotteryData = Gionee_Service_Config::getValue('LOTTERY_TYPE_DATA');
        $lotteryData = !empty($lotteryData) ? json_decode($lotteryData, true) : array();
        if (!empty($lotteryData[$ext['lotteryType']])) {
            $tmp                                      = str_replace('#', ',', $lotteryData[$ext['lotteryType']]['code']);
            $tmp1                                     = explode(',', $tmp);
            $lotteryData[$ext['lotteryType']]['code'] = implode(',', array_slice($tmp1, 0, 7));
            $data[$k]                                 = array_merge($data[$k], $lotteryData[$ext['lotteryType']]);
            !empty($data[$k]['date']) && $data[$k]['date'] = date('Y年m月d日', strtotime($data[$k]['date']));
        }
    }

    static private function _ng_style_img3switch($k, $v, &$data, &$imgs, &$newsdata) {
        $ext = json_decode($v['ext'], true);
        if (!empty($ext['switchName'])) {
            $data[$ext['switchName']]['tname']  = $ext['switchName'];
            $data[$ext['switchName']]['list'][] = array(
                'id'    => $v['id'],
                'pos'   => $v['sort'],
                'link'  => Common::clickUrl($v['id'], 'NAV', $v['link']),
                'title' => $v['title'],
                'img'   => strpos($v['img'], 'http') === false ? Common::getImgPath() . $v['img'] : $v['img']
            );
        }
    }

    static private function _ng_style_news_list($k, $v, &$data, &$imgs, &$newsdata) {
        $ext = json_decode($v['ext'], true);
        if (!empty($ext['newsSourceId'])) {
            if (!empty($ext['is_ad']) && $ext['is_ad'] == 1) {
                $newsdata[$ext['newsSourceId']][] = array(
                    'id'    => $v['id'],
                    'title' => $v['title'],
                    'link'  => Common::clickUrl($v['id'], 'NAVNEWS', $v['link']),
                    'sort'  => $v['sort'],
                    'ad'    => 1
                );
            } else {
                $tmp  = Nav_Service_NewsDB::getSourceDao()->getsBy(array('column_id' => $ext['newsSourceId']));
                $tids = array();
                foreach ($tmp as $tmpv) {
                    $tids[] = $tmpv['id'];
                }
                $where        = array('source_id' => array('in', $tids), 'status' => 1);
                $orderBy      = array('out_created_at' => 'desc');
                $tempNewsList = Nav_Service_NewsDB::getRecordDao()->getList(0, 5, $where, $orderBy);
                //$tempNewsList = Gionee_Service_OutNews::getListBySourceId($ext['newsSourceId'], 5);
                foreach ($tempNewsList as $tempNews) {
                    $link                             = Common::getCurHost() . '/nav/news/detail?act=h5&id=' . $tempNews['id'];
                    $newsdata[$ext['newsSourceId']][] = array(
                        'id'    => $tempNews['id'],
                        'title' => $tempNews['title'],
                        'link'  => Common::clickUrl($v['id'], 'NAVNEWS', $link),
                        'sort'  => $v['sort']
                    );
                }
                $data['columns'][] = array(
                    'title'    => $v['title'],
                    'id'       => $v['id'],
                    'more_url' => $v['link']
                );
            }
        }
    }

    static private function _ng_style_ticket($k, $v, &$data, &$imgs, &$newsdata) {
        $lunar        = new Vendor_Lunar();
        $buySolarDate = date('Y-n-j', strtotime("+59 day"));
        list($sy, $sm, $sd) = explode('-', $buySolarDate);
        $ldate  = $lunar->convertSolarToLunar($sy, $sm, $sd);
        $solarD = date('n月d日', strtotime($buySolarDate));
        $lunarD = '农历' . $ldate[1] . $ldate[2];
        $nowD   = date('m月d日');
        $data   = array(
            'id'         => $v['id'],
            'pos'        => $v['sort'],
            'link'       => Common::clickUrl($v['id'], 'NAV', $v['link']),
            'title'      => $v['title'],
            'solar_date' => $solarD,
            'lunar_date' => $lunarD,
            'now_date'   => $nowD,
        );
    }

    static private function _ng_style_topic($k, $v, &$data, &$imgs, &$newsdata) {
        $ext = json_decode($v['ext'], true);
        if (!empty($ext['topicDesc'])) {
            $data[$k] = array(
                'id'    => $v['id'],
                'pos'   => $v['sort'],
                'link'  => Common::clickUrl($v['id'], 'NAV', $v['link']),
                'title' => $v['title'],
                'img'   => strpos($v['img'], 'http') === false ? Common::getImgPath() . $v['img'] : $v['img'],
                'desc'  => $ext['topicDesc'],
            );
        }
    }

    static private function _ng_style_common($k, $v, &$data, &$imgs, &$newsdata) {
        $data[$k] = array(
            'id'   => $v['id'],
            'pos'  => $v['sort'],
            'link' => Common::clickUrl($v['id'], 'NAV', $v['link']),
        );
        if ($v['title']) {
            $data[$k]['title'] = $v['title'];
        }
        if ($v['img']) {
            $imgPath         = Common::getImgPath();
            $data[$k]['img'] = $imgPath . $v['img'];
            if (strpos($v['img'], 'http') === false) {
                $imgs[] = $imgPath . $v['img'];
            }
        }
        if ($v['color']) {
            $data[$k]['color'] = $v['color'];
        }
    }

    /**
     * 生成导航样式
     *
     * @param array  $ngs
     * @param string $style
     * @param string $webroot
     * @param string $imgPath
     * @param array  $imgs
     * @param array  $list
     */
    public static function genNgStyle($type, $ngs, $value, &$imgs, &$list) {
        $imgPath  = Common::getImgPath();
        $data     = array();
        $newsdata = array();
        foreach ($ngs as $k => $v) {
            switch ($value['style']) {
                case 'lottery' :
                    $data[$k]    = array(
                        'id'   => $v['id'],
                        'pos'  => $v['sort'],
                        'link' => Common::clickUrl($v['id'], 'NAV', $v['link']),
                    );
                    $ext         = json_decode($v['ext'], true);
                    $lotteryData = Gionee_Service_Config::getValue('LOTTERY_TYPE_DATA');
                    $lotteryData = !empty($lotteryData) ? json_decode($lotteryData, true) : array();
                    if (!empty($lotteryData[$ext['lotteryType']])) {
                        $tmp                                      = str_replace('#', ',', $lotteryData[$ext['lotteryType']]['code']);
                        $tmp1                                     = explode(',', $tmp);
                        $lotteryData[$ext['lotteryType']]['code'] = implode(',', array_slice($tmp1, 0, 7));
                        $data[$k]                                 = array_merge($data[$k], $lotteryData[$ext['lotteryType']]);
                        !empty($data[$k]['date']) && $data[$k]['date'] = date('Y年m月d日', strtotime($data[$k]['date']));
                    }
                    break;
                case 'img3switch' :
                    $ext = json_decode($v['ext'], true);
                    if (!empty($ext['switchName'])) {
                        $data[$ext['switchName']]['tname']  = $ext['switchName'];
                        $data[$ext['switchName']]['list'][] = array(
                            'id'    => $v['id'],
                            'pos'   => $v['sort'],
                            'link'  => Common::clickUrl($v['id'], 'NAV', $v['link']),
                            'title' => $v['title'],
                            'img'   => strpos($v['img'], 'http') === false ? $imgPath . $v['img'] : $v['img']
                        );
                    }
                    break;
                case 'news_list':
                    $ext = json_decode($v['ext'], true);
                    if (!empty($ext['newsSourceId'])) {
                        if (!empty($ext['is_ad']) && $ext['is_ad'] == 1) {
                            $newsdata[$ext['newsSourceId']][] = array(
                                'id'    => $v['id'],
                                'title' => $v['title'],
                                'link'  => Common::clickUrl($v['id'], 'NAVNEWS', $v['link']),
                                'sort'  => $v['sort'],
                                'ad'    => 1
                            );
                        } else {
                            $tempNewsList = Gionee_Service_Ng::getnewslist($ext['newsSourceId'], 5);
                            $tmplist      = array();
                            foreach ($tempNewsList as $tempNews) {
                                $tmplist[] = array(
                                    'id'    => $tempNews['id'],
                                    'title' => $tempNews['title'],
                                    'link'  => Common::clickUrl($v['id'], 'NAVNEWS', $tempNews['link']),
                                    'sort'  => $v['sort']
                                );
                            }
                            $newsdata[$ext['newsSourceId']] = $tmplist;
                            $data['columns'][]              = array(
                                'title'    => $v['title'],
                                'id'       => $v['id'],
                                'more_url' => $v['link']
                            );
                        }
                    }
                    break;
                case 'ticket':
                    $lunar        = new Vendor_Lunar();
                    $buySolarDate = date('Y-n-j', strtotime("+59 day"));
                    list($sy, $sm, $sd) = explode('-', $buySolarDate);
                    $ldate  = $lunar->convertSolarToLunar($sy, $sm, $sd);
                    $solarD = date('n月d日', strtotime($buySolarDate));
                    $lunarD = '农历' . $ldate[1] . $ldate[2];
                    $nowD   = date('m月d日');
                    $data   = array(
                        'id'         => $v['id'],
                        'pos'        => $v['sort'],
                        'link'       => Common::clickUrl($v['id'], 'NAV', $v['link']),
                        'title'      => $v['title'],
                        'solar_date' => $solarD,
                        'lunar_date' => $lunarD,
                        'now_date'   => $nowD,
                    );
                    break;
                case 'topic':
                    $ext = json_decode($v['ext'], true);
                    if (!empty($ext['topicDesc'])) {
                        $data[$k] = array(
                            'id'    => $v['id'],
                            'pos'   => $v['sort'],
                            'link'  => Common::clickUrl($v['id'], 'NAV', $v['link']),
                            'title' => $v['title'],
                            'img'   => strpos($v['img'], 'http') === false ? Common::getImgPath() . $v['img'] : $v['img'],
                            'desc'  => $ext['topicDesc'],
                        );
                    }
                    break;
                default:
                    $data[$k] = array(
                        'id'   => $v['id'],
                        'pos'  => $v['sort'],
                        'link' => Common::clickUrl($v['id'], 'NAV', $v['link']),
                    );
                    if ($v['title']) {
                        $data[$k]['title'] = $v['title'];
                    }
                    if ($v['img']) {
                        $data[$k]['img'] = $imgPath . $v['img'];
                        if (strpos($v['img'], 'http') === false) {
                            $imgs[] = $imgPath . $v['img'];
                        }
                    }
                    if ($v['color']) {
                        $data[$k]['color'] = $v['color'];
                    }
                    break;
            }
        }

        if ($data) {
            if ($value['style'] == 'img3switch') {
                $list[] = array(
                    'id'   => $value['id'],
                    'tpl'  => 'cltab',
                    'name' => $value['name'],
                    'stat' => array('type' => $type['name'], 'column' => $value['name']),
                    'list' => array_values($data)
                );
            } elseif ($value['style'] == 'news_list') {
                $newsColumns = $data['columns'];
                unset($data['columns']);

                $list = array(
                    array(
                        'tpl'   => $value['style'],
                        'names' => $newsColumns,
                        'list'  => array_values($newsdata)
                    )
                );
            } else {
                $list[] = array(
                    'id'   => $value['id'],
                    'tpl'  => $value['style'],
                    'stat' => array('type' => $type['name'], 'column' => $value['name']),
                    'list' => $data
                );
            }
        }
    }

    static public function getnewslist($column_id, $limit = 5) {
        $tmp  = Nav_Service_NewsDB::getSourceDao()->getsBy(array('column_id' => $column_id));
        $tids = array();
        foreach ($tmp as $tmpv) {
            $tids[] = $tmpv['id'];
        }
        $where        = array('source_id' => array('in', $tids), 'status' => 1);
        $orderBy      = array('out_created_at' => 'desc');
        $tempNewsList = Nav_Service_NewsDB::getRecordDao()->getList(0, $limit, $where, $orderBy);
        foreach ($tempNewsList as $k => $tempNews) {
            $link                     = Common::getCurHost() . '/nav/news/detail?act=h5&id=' . $tempNews['id'];
            $tempNewsList[$k]['link'] = $link;
        }
        return $tempNewsList;
    }

    public static function newsListSort(&$newsList, $sort_key, $sort = SORT_ASC) {
        foreach ($newsList as $row_array) {
            $key_array[] = $row_array[$sort_key];
        }
        array_multisort($key_array, $sort, $newsList);
    }

    /**
     *获取广告图
     *
     *返回栏目ID和广告内容
     */
    public static function getAds() {
        $rcKey = 'NG:AD:1';
        $ads   = Common::getCache()->get($rcKey);
        if ($ads === false) {
            $norAds  = self::_getAds();//默入为普通广告内容
            $top_ads = self::formatAdsData($norAds);
            foreach ($top_ads as $k => $v) {
                if ($v['model_id'] > 0) {
                    $ads['model_ad'][$v['model_type']][] = $v;
                } else {
                    $ads['nor_ad'][] = $v;
                }
            }
            Common::getCache()->set($rcKey, $ads, 600);
        }
        return $ads;
    }


    /**
     *
     * @param number $model_type
     * @param number $model_id
     *
     * @return array
     */
    private static function  _getAds() {
        $time      = time();
        $where     = array('type_id' => 1, 'style' => 'img1', 'status' => 1);
        $column_ad = Gionee_Service_NgColumn::getBy($where, array('sort' => 'ASC', 'id' => 'DESC'));
        $params    = array(
            'column_id'  => $column_ad['id'],
            'status'     => 1,
            'start_time' => array('<=', $time),
            'end_time'   => array('>=', $time),
        );
        $ads       = Gionee_Service_Ng::getsBy($params, array('sort' => 'ASC', 'id' => 'ASC'));
        return $ads;
    }

    /**
     * 格式化广告内容
     *
     * @param array $ads
     *
     * @return boolean
     */
    public static function formatAdsData($ads = array()) {
        if (!is_array($ads)) {
            return array();
        }
        $imgPath = Common::getImgPath();
        $adsData = array();
        foreach ($ads as $v) {
            if ($v['img']) {
                $link = Common::clickUrl($v['id'], 'NAV', $v['link']);
                if (strpos($v['link'], 'gionee.com/index/tj?t=')) { //如果是专题内容，不用生成短链接
                    $link = $v['link'];
                }

                $adsData[] = array(
                    'id'         => $v['id'],
                    'img'        => $imgPath . $v['img'],
                    'link'       => $link,
                    'title'      => $v['title'],
                    'sort'       => $v['sort'],
                    'model_type' => $v['model_type'],
                    'model_id'   => $v['model_id'],
                );
            }
        }
        return $adsData;
    }

    /**
     *@返回信息总数
     *＠return int
     */
    public static function count($params) {
        return self::_getDao()->count($params);
    }

    /**
     * 通过Ng获得Column值
     *
     * @param array ids
     */
    public static function getColumnidsByNgId($ids) {
        if (!is_array($ids)) {
            return false;
        }
        return self::_getDao()->getElements(array('column_id,id'), $ids, array('id' => 'DESC'), array('column_id'));
    }


    /**
     * 热门导航分机型运营内容替换
     */
    public static function getSortedData($data = array()) {
        $temp = array();
        foreach ($data as $k => $v) {
            $temp[$v['sort']] = $v;
        }
        return $temp;
    }

    /**
     * 得到分机型运营内容
     */
    public static function getModelData($model, $version = '', $ip, $type = 'nav') {
        $mIds = Gionee_Service_ModelContent::curUserModelIds($model, $version, $ip, $type);
        return self::buildModelNavData($mIds);
    }

    /**
     * 构建导航对应的机型替换数据结构
     *
     * @param $mIds
     *
     * @return array|bool|mixed
     */
    public static function buildModelNavData($mIds) {
        $data = array();
        if (!empty($mIds)) {//如果规则匹配，就找出所有符合规则的数据
            $mKey = "3G:NAV:MODEL:DATA:" . implode(',', $mIds);
            $data = common::getCache()->get($mKey);
            if ($data === false) { //如果数据存在并为数组时，则认为是ID列表
                $data                 = array();
                $params               = array();
                $params['status']     = 1;
                $params['start_time'] = array("<=", time());
                $params['end_time']   = array(">=", time());
                $params['model_type'] = 1;
                $params['model_id']   = array('IN', $mIds);
                $modelDataList        = Gionee_Service_Ng::getsBy($params);
                foreach ($modelDataList as $k => $v) {
                    $data[$v['column_id']][$v['sort']] = $v;
                }
                common::getCache()->set($mKey, $data, 600);
            }
        }
        return $data;
    }

    public static function upNavFrontIndexAPPC($imgs = array()) {
        //顶部广告
        $adImages = Gionee_Service_Ng::getAds();
        //$adModel  = Gionee_Service_Ng::getAllModelData();
        foreach ($adImages as $v) {
            $imgs[] = $v['img'];
        }
        //foreach ($adModel as $v) {
        //	$imgs[] = $v['img'];
        //}
        $newsVal = md5(json_encode($imgs));
        $oldImgs = Common::getAppc('Front_Nav_index');
        $oldVal  = md5(json_encode($oldImgs));
        if ($newsVal != $oldVal) {
            Common::setAppc('Front_Nav_index', $imgs);
        }
    }

    public static function syncAppcache($cn) {
        $v   = Gionee_Service_Config::getValue('APPC_' . $cn);
        $str = "CACHE MANIFEST\n";
        $str .= "\n#version:" . $v . "\n\n";
        $caches = Common::getConfig('cacheConfig', $cn);
        foreach ($caches as $key => $value) {
            $str .= sprintf("\n\n%s:\n", $key);
            if ($key == 'CACHE') {
                $files = Common::getAppc($cn);
                if (is_array($files)) {
                    $value = array_merge($files, $value);
                }
            }
            $str .= implode("\n", $value);
        }

        $staticDir = Common::staticDir();
        $filename  = $staticDir . $cn . '.appcache';
        Common::writeContentToFile($filename, $str);
    }

    /**
     *
     * @return Gionee_Dao_Ng
     */
    private static function _getDao() {
        return Common::getDao("Gionee_Dao_Ng");
    }

    public static function updataVersion() {
        $rcKey = Gionee_Service_Ng::KEY_NG_INDEX_DATA;
        $ret   = self::_getIndexData();
        Common::getCache()->set($rcKey, $ret, 3600);
        Gionee_Service_Config::setValue('APPC_Front_Nav_index', Common::getTime());
        Gionee_Service_Config::setValue('APPC_Front_Browser_overseas', Common::getTime());
    }

    public static function cleanNgTypeData($typeId) {
        $rcKey = Gionee_Service_Ng::KEY_NG_TYPE_DATA . $typeId;
        Common::getCache()->delete($rcKey);
    }
}