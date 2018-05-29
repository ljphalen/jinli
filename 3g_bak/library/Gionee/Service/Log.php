<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Gionee_Service_Log {

    const TYPE_SOHU              = 'sohu';
    const TYPE_PV                = 'pv';
    const TYPE_UV                = 'uv';
    const TYPE_URL               = 'url';
    const TYPE_URL_UV            = 'url_uv';
    const TYPE_NAV               = 'nav';
    const TYPE_NG_INDEX          = 'ng_index';
    const TYPE_NG_LIST           = 'ng_list';
    const TYPE_BAIDU_HOT         = 'baidu_hot';
    const TYPE_TOPIC             = 'topic';
    const TYPE_TOPIC_LIST        = 'topic_list';
    const TYPE_TOPIC_MAIN        = 'topic_main';
    const TYPE_TOPIC_CONTENT     = 'topic_content';
    const TYPE_CONTENT_UV        = 'content_uv';
    const TYPE_NEWS_CONTENT_UV   = 'news_content_uv';
    const TYPE_WORLD_CUP         = 'world_cup';
    const TYPE_NEWS_UV           = 'news_uv';
    const TYPE_SITE_INDEX        = 'sitemap';
    const TYPE_INBUILT           = 'inbuilt';
    const TYPE_BOOKMARK          = 'bookmark';
    const TYPE_USER_UV           = 'user_uv';
    const TYPE_USER_PV           = 'user_pv';
    const TYPE_USER              = 'user';
    const TYPE_LOCAL_NAV_PV      = 'localnav_pv';
    const TYPE_LOCAL_NAV_UV      = 'localnav_uv';
    const TYPE_VOD_PV            = 'vod_pv';
    const TYPE_VOD_UV            = 'vod_uv';
    const TYPE_WXHELP_PV         = 'wxhelp_pv';
    const TYPE_WXHELP_UV         = 'wxhelp_uv';
    const TYPE_E_ADDRESS_ADD_PV  = 'address_add_pv';
    const TYPE_E_ADDRESS_ADD_UV  = 'address_add_uv';
    const TYPE_E_ORDER_SUBMIT_PV = 'entity_order_pv';
    const TYPE_E_ORDER_SUBMIT_UV = 'entity_order_uv';
    const TYPE_NAVNEWS_PV        = 'navnews_pv';
    const TYPE_NAVNEWS_UV        = 'navnews_uv';
    const TYPE_SNATCH_G_PV       = 'snatch_goods_pv';
    const TYPE_SNATCH_G_UV       = 'snatch_goods_uv';
    const TYPE_PARTNER_PV        = 'partner_pv';
    const TYPE_PARTNER_UV        = 'partner_uv';


    static $types = array(
        self::TYPE_PV,
        self::TYPE_UV,
        self::TYPE_SOHU,
        self::TYPE_URL,
        self::TYPE_URL_UV,
        self::TYPE_NAV,
        self::TYPE_NG_INDEX,
        self::TYPE_NG_LIST,
        self::TYPE_BAIDU_HOT,
        self::TYPE_TOPIC,
        self::TYPE_TOPIC_LIST,
        self::TYPE_TOPIC_MAIN,
        self::TYPE_TOPIC_CONTENT,
        self::TYPE_CONTENT_UV,
        self::TYPE_NEWS_CONTENT_UV,
        self::TYPE_WORLD_CUP,
        self::TYPE_NEWS_UV,
        self::TYPE_SITE_INDEX,
        self::TYPE_INBUILT,
        self::TYPE_BOOKMARK,
        self::TYPE_USER,
        self::TYPE_USER_UV,
        self::TYPE_USER_PV,
        self::TYPE_VOD_PV,
        self::TYPE_VOD_UV,
        self::TYPE_LOCAL_NAV_PV,
        self::TYPE_LOCAL_NAV_UV,
        self::TYPE_WXHELP_PV,
        self::TYPE_WXHELP_UV,
        self::TYPE_E_ADDRESS_ADD_PV,
        self::TYPE_E_ADDRESS_ADD_UV,
        self::TYPE_E_ORDER_SUBMIT_PV,
        self::TYPE_E_ORDER_SUBMIT_UV,
        self::TYPE_NAVNEWS_PV,
        self::TYPE_NAVNEWS_UV,
        self::TYPE_SNATCH_G_PV,
        self::TYPE_SNATCH_G_UV,
        self::TYPE_PARTNER_PV,
        self::TYPE_PARTNER_UV,
    );
    static $statKeys = array(
        'all'  => array(
            '3g_all' => '全站',
        ),
        '3g'   => array(
            '3g_recweb_url'  => '推荐网址',
            '3g_recweb_site' => '推荐站点',
            '3g_nav'         => '导航',
            '3g_news'        => '新闻',
            '3g_vendor'      => '合作商',
            '3g_index'       => '首页',
            'sites_map'      => '网址大全',
        ),
        'voip' => array(
            'voip_uv_list' => 'voip拨打页',
            'voip_uv_call' => 'voip拨号',
            'voip_uv'      => 'voip规则页',
            'voip_me'      => 'voip 用户中心页',
        ),
        'user' => array(
            'user_index'             => '用户中心首面',
            'user_produces'          => '产生金币列表',
            'user_cosume_list'       => '兑换列表页',
            'user_cosume_detail'     => '兑换详情页',
            'user_cosume_success'    => '兑换成功页',
            'user_center_index'      => "信息设置页",
            'user_innermsg'          => '站内信页',
            'user_scorelog'          => '金币日志页',
            'user_activity_lot'      => '刮刮乐首页',
            'user_activity_scratch'  => "刮奖中",
            'user_experience_index'  => '经验等级页',
            'user_experience_detail' => '经验等级详情页',
        ),
    );

    static $userKeys = array(
        'user_day_incre'               => '每日新增用户',
        'user_total_number'            => '累计用户数',
        'user_signin_num'              => '每日签到用户',
        'user_sinin_scores'            => '每日签到获得的金币数',
        'user_earn_num'                => '每日获得金币用户',
        'user_earn_amount'             => '每日获得金币总数',
        'user_tasks_num'               => '完成任务用户数',
        'user_tasks_times'             => '完成任务次数',
        'user_exchange_num'            => '每日兑换成功用户数',
        'user_exchange_total'          => '每日兑换总次数',
        'user_exchange_scuessed_times' => '每日兑换成功次数',
        'user_exchange_costs'          => '每日兑换消耗金币数',
        'user_currency_scores'         => '当前为止流通的金币数',
        'user_activity_lot'            => '刮刮乐首页',
        'user_activity_scratch'        => "刮奖中"
    );

    const TYPE_U_G_EXCHANGE    = 'u_g_exchange';
    const TYPE_U_G_EXCHANGE_UV = 'u_g_exchange_uv';
    const TYPE_U_G_DETAIL      = 'u_g_detail';
    const TYPE_U_G_DETAIL_UV   = 'u_g_detail_uv';
    const TYPE_U_G_SCORE       = 'u_g_score';
    const TYPE_U_G_SCORE_UV    = 'u_g_score_uv';
    const TYPE_U_G_PHONE       = 'u_g_phone';
    const TYPE_U_G_PHONE_UV    = 'u_g_phone_uv';
    const TYPE_U_G_IMG         = 'u_g_img';
    const TYPE_U_G_IMG_UV      = 'u_g_img_uv';
    static $userTypes = array(
        self::TYPE_U_G_EXCHANGE,
        self::TYPE_U_G_EXCHANGE_UV,
        self::TYPE_U_G_DETAIL,
        self::TYPE_U_G_DETAIL_UV,
        self::TYPE_U_G_SCORE,
        self::TYPE_U_G_SCORE_UV,
        self::TYPE_U_G_PHONE,
        self::TYPE_U_G_PHONE_UV,
        self::TYPE_U_G_IMG,
        self::TYPE_U_G_IMG_UV,
    );

    const TYPE_V_TOTAL_USER      = 'v__total_user';
    const TYPE_V_E_TOTAL_USER    = 'v_exchange_total_user';
    const TYPE_V_E_TOTAL_SECONDS = 'v_exchange_total_seconds';
    const TYPE_V_TOTAL_SECONDS   = 'v_total_seconds';
    const TYPE_V_COST_SCORES     = 'v_exchange_cost_scores';
    static $voipTypes = array(
        self::TYPE_V_TOTAL_USER,
        self::TYPE_V_E_TOTAL_USER,
        self::TYPE_V_E_TOTAL_SECONDS,
        self::TYPE_V_COST_SCORES,
        self::TYPE_V_TOTAL_SECONDS
    );

    public static function incrBy($type, $key, $num = 1) {
        $nowDay = date('Ymd');
        Common::getCache()->hIncrBy($type . '_' . $nowDay, $key, $num);
        if (!stristr(ENV, 'product')) {
            self::sync2DB($type);
        }
    }

    public static function sync2DB($type) {
        $nowDay = date('Ymd', time() - 3600); //目的 在切换日期的时候不会漏掉数据
        $key    = $type . '_' . $nowDay;
        $list   = Common::getCache()->hGetAll($key);
        Common::getCache()->delete($key);
        foreach ($list as $key => $num) {
            $tmpKey = explode(':', $key);
            $params = array(
                'date' => $nowDay,
                'type' => $type,
                'key'  => isset($tmpKey[0]) ? $tmpKey[0] : '',
            );
            if (!empty($tmpKey[1])) { //渠道号
                $params['ver'] = $tmpKey[1];
            }

            $row = Gionee_Service_Log::getBy($params);

            if (!empty($row['id'])) {
                $params['val'] = $row['val'] + $num;
                Gionee_Service_Log::set($params, $row['id']);
            } else {
                $params['val'] = $num;
                Gionee_Service_Log::add($params);
            }
        }
    }

    public static function getBy($param) {
        if (!is_array($param)) return false;
        return self::_getDao()->getBy($param);
    }

    public static function update($params, $id) {
        if (!is_array($params)) return false;
        return self::_getDao()->update($params, $id);
    }

    /**
     *
     * @return Gionee_Dao_Log
     */
    private static function _getDao() {
        return Common::getDao("Gionee_Dao_Log");
    }

    public static function getLastIdByType($type, $limit) {
        $ret  = array();
        $list = self::_getDao()->getLastIdByType($type, $limit);
        foreach ($list as $v) {
            $ret[] = $v['key'];
        }
        return $ret;
    }

    /**
     * @param string $type
     * @param array  $keys
     * @param string $sDate
     * @param string $eDate
     *
     * @return array
     */
    public static function getUrlList($keys, $sDate, $eDate, $type = Gionee_Service_Log::TYPE_URL) {
        $params  = array(
            'type' => $type,
            'key'  => array('IN', $keys),
            'date' => array(array('>=', $sDate), array('<=', $eDate))
        );
        $orderBy = array('val' => 'DESC');
        $list    = self::_getDao()->getsBy($params, $orderBy);
        $pvData  = array();
        foreach ($keys as $k) {
            $pvData[$k][$sDate] = 0;
        }

        foreach ($list as $v) {
            $k              = $v['key'];
            $d              = date('Y-m-d', strtotime($v['date']));
            $num            = $v['val'];
            $pvData[$k][$d] = $num;
        }
        return $pvData;
    }

    /**
     * @param string $type
     * @param array  $keys
     * @param string $sDate
     * @param string $eDate
     *
     * @return array
     */
    public static function getStatData($type, $keys, $sDate, $eDate) {
        if (empty($type) || empty($keys)) {
            return false;
        }
        $params = array(
            'type' => $type,
            'key'  => array('IN', array_keys($keys)),
            'date' => array(array('>=', $sDate), array('<=', $eDate))
        );

        $list   = self::_getDao()->getsBy($params);
        $pvData = array();
        foreach ($list as $v) {
            $d              = date('Y-m-d', strtotime($v['date']));
            $num            = $v['val'];
            $k              = $keys[$v['key']];
            $pvData[$k][$d] = $num;
        }

        return $pvData;
    }

    public static function getPvUvStatByKey($type, $key, $sDate, $eDate) {
        if (empty($type) || empty($key)) {
            return false;
        }
        $params = array(
            'type' => $type,
            'key'  => $key,
            'date' => array(array('>=', $sDate), array('<=', $eDate))
        );
        $list   = self::_getDao()->getsBy($params);
        $data   = array();
        foreach ($list as $k => $v) {
            $d        = date('Y-m-d', strtotime($v['date']));
            $data[$d] = $v['val'];
        }
        return $data;
    }

    public static function getNgColumnData($columnId, $sDate, $eDate, $type = self::TYPE_NAV, $other = array()) {
        if (!$columnId) {
            return array();
        }
        $searchArr = array('column_id' => $columnId);
        !empty($other['condition']) && $searchArr = array_merge($searchArr, $other['condition']);
        $columList = Gionee_Service_Ng::getsBy($searchArr, array('sort' => 'ASC', 'id' => 'ASC'));
        $keys      = array(0);
        foreach ($columList as $k => $v) {
            $keys[] = $v['id'];
        }

        $params = array(
            'type' => $type,
            'key'  => array('IN', $keys),
            'date' => array(array('>=', $sDate), array('<=', $eDate)),
        );
        if (isset($other['ver'])) {
            $params['ver'] = $other['ver'];
        }
        $list = self::_getDao()->getsBy($params);

        //基于渠道号的统计，做出一定的修改 panxb 2014-5-15
        $tempData = $pvData = array();
        foreach ($list as $v) {
            $d                          = date('Y-m-d', strtotime($v['date']));
            $m                          = $v['key'];
            $tempData[$m][$d][$v['id']] = $v['val'];
        }
        foreach ($tempData as $k => $v) {
            foreach ($v as $m => $n) {
                $sm = 0;
                foreach ($n as $j) {
                    $sm += $j;
                }
                $pvData[$k][$m] = $sm;
            }
        }

        return $pvData;
    }

    public static function getNgTypeData($typeId, $sDate, $eDate) {

        $typeList = Gionee_Service_Ng::getsBy(array('type_id' => $typeId), array('sort' => 'ASC', 'id' => 'ASC'));
        foreach ($typeList as $k => $v) {
            $keys[] = $v['id'];
        }

        $params = array(
            'type' => self::TYPE_NAV,
            'key'  => array('IN', $keys),
            'date' => array(array('>=', $sDate), array('<=', $eDate))
        );
        $list   = self::_getDao()->getsBy($params);
        $pvData = array();
        foreach ($list as $v) {
            $d              = date('Y-m-d', strtotime($v['date']));
            $num            = $v['val'];
            $k              = $v['key'];
            $pvData[$k][$d] = $num;
        }

        return $pvData;
    }


    /**
     * 根据cp获取导航pv
     *
     * @param array $condition
     */
    public static function getNgPVByCP($condition = array()) {
        $temp = array();
        !empty($condition['type']) && $temp['type'] = $condition['type'];
        !empty($condition['key']) && $temp['key'] = $condition['key'];
        !empty($condition['edate']) && !empty($condition['sdate']) && $temp['date'] = array(
            array(
                '>=',
                date('Ymd', $condition['sdate'])
            ),
            array(
                '<',
                date('Ymd', $condition['edate'])
            )
        );
        !empty($condition['ver']) && $temp['ver'] = $condition['ver'];

        $num = self::_getDao()->sum('val', $temp);
        return empty($num) ? 0 : $num;
    }

    public static function getNgTotalNumber(array $params) {
        $temp = array();
        if (isset($params['type'])) $temp['type'] = $params['type'];
        if (isset($params['key'])) $temp['key'] = $params['key'];
        if (isset($params['edate']) && isset($params['sdate'])) $temp['date'] = array(
            array(
                '>=',
                date('Ymd', $params['sdate'])
            ),
            array(
                '<=',
                date('Ymd', $params['edate'])
            )
        );
        if (isset($params['ver'])) $temp['ver'] = $params['ver'];
        $num = self::_getDao()->sum('val', $temp);
        return empty($num) ? 0 : $num;

    }

    /**
     *
     * @param arrray  $arr     基本参数
     * @param varchar $type    类型
     * @param array   $search  其它相查询的参数
     * @param array   $group   GROUP BY 的参数
     * @param array   $orderBy 排序
     * @param array   $limit   limit条件
     *
     * @return array
     */

    public static function getSumByChannel($arr = array(), $type = self::TYPE_NAV, $search = array(), $group = array(), $orderBy = array('id' => 'DESC')) {
        $params = array();
        if (isset($arr['ver'])) {
            $params['ver'] = $arr['ver'];
        }
        if (isset($arr['sdate']) && isset($arr['sdate'])) {
            $params['date'] = array(
                array('>=', date('Ymd', $arr['sdate'])),
                array('<=', date('Ymd', $arr['edate']))
            );
        }
        if (is_array($type)) {
            $params['type'] = array("IN", $type);
        } else {
            $params['type'] = $type;
        }
        if (isset($arr['key']) && is_array($arr['key'])) {
            $params['key'] = $arr['key'];
        }
        $data = self::_getDao()->complexSum('val', $search, $params, $group, $orderBy);

        return $data;
    }

    /**
     * 根据内容ID或渠道号查询统计纪录
     *
     * @param array  $array
     * @param string $type
     */
    public static function getTotalByNidVer($arr = array(), $type = self::TYPE_NAV) {
        if (!is_array($arr)) {
            return false;
        }
        $params = array();
        if (isset($arr['sdate']) && isset($arr['edate'])) $params['date'] = array(
            array(
                '>=',
                date('Ymd', $arr['sdate'])
            ),
            array(
                '<=',
                date('Ymd', $arr['edate'])
            )
        );
        if (isset($arr['type'])) $params['type'] = $arr['type']; //添加类型
        if (isset($arr['ids'])) { //
            $params['key'] = array('IN', $arr['ids']);
            $result        = self::_getDao()->complexSum('val', array('key'), $params, array('key'), array('total' => 'DESC'));
        } elseif (isset($arr['ver'])) {
            $result = self::_getDao()->sum('val', $params);
        }
        return $result;
    }

    /**
     *
     * @param string $data
     */
    public static function get($date) {
        return self::_getDao()->get($date);
    }

    public static function getsBy($param, $orderBy = array()) {
        if (!is_array($param)) return false;
        return self::_getDao()->getsBy($param, $orderBy);
    }

    /**
     *
     * @param array  $data
     * @param string $date
     */
    public static function set($data, $date) {
        if (!is_array($data)) return false;
        return self::_getDao()->update($data, $date);
    }

    /**
     *
     *
     * @param array $data
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        return self::_getDao()->insert($data);
    }

    public static function count($params) {
        return self::_getDao()->count($params);
    }

    public static function getList($page, $limit, $params = array(), $sort = array()) {
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret   = self::_getDao()->getList($start, $limit, $params, $sort);
        $total = self::count($params);
        return array($total, $ret);
    }

    public static function uvLog($val, $t_bi) {
        if (empty($t_bi)) {
            return false;
        }
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $val, $t_bi);
    }

    public static function pvLog($val) {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $val);
    }


    public static function userUvLog($val, $t_bi) {
        if (empty($t_bi)) {
            return false;
        }
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_USER_UV, $val, $t_bi);
    }

    public static function userPvLog($val) {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_USER_PV, $val);
    }

    /**
     * 搜集用户访问次数
     * 公共KEY 用 uvLog方法 (页面)
     * 独立KEY 下面有多个值(新闻ID)
     *
     * @param $uvKey  记录KEY
     * @param $val    值
     * @param $t_bi   用户唯一标记
     *
     * @return bool
     */
    public static function toUVByCacheKey($uvKey, $val, $t_bi = '') {
        if (empty($t_bi)) {
            $t_bi = Common::getUName();
        }
        $nowDay = date('Ymd');
        $key    = $uvKey . $t_bi;
        //检测用户是否点击
        $flag = Common::getCache()->hGet($key, $val);
        if (empty($flag) || $flag != $nowDay) {
            //更新用户点击标记
            Common::getCache()->hSet($key, $val, $nowDay, 86400);
            //更新点击记录
            Gionee_Service_Log::incrBy($uvKey, $val);
        }
        return true;
    }

    public static function getTotalByMonth($type, $m = '') {
        return self::_getDao()->getTotalByMonth($type, $m);
    }


    /**
     * 用户中心
     */
    public static function  userCenterDayDataReport() {
        $res = Common::getCache();

        $data          = array();
        $p['add_time'] = array(array('>=', mktime(0, 0, 0)), array('<=', mktime(23, 59, 59)));
        $signin        = User_Service_Earn::getPerDayUserAmount($p); //当天签到数据
        foreach ($signin as $k => $v) {
            if ($v['group_id'] == '1') {
                $data['user_signin_num']    = $v['user_number'];
                $data['user_signin_scores'] = $v['earn_scores'];
            } elseif ($v['group_id'] == '2') {
                $data['user_earn_num']    = $v['user_number'];
                $data['user_earn_amount'] = $v['earn_scores'];
            }
        }
        //任务完成数
        $params                    = array_merge($p, array('cat_id' => array('>', 0)));
        $tasks                     = User_Service_Earn::getDoneTasksData($params);
        $data['user_tasks_num']    = $tasks['user_number'];
        $data['user_tasks_amount'] = $tasks['times'];
        //用户兑换信息
        $exchanges                            = User_Service_Order::getUserExchangeMsg($p);
        $data['user_exchange_total']          = $exchanges['total_orders'];
        $data['user_exchange_num']            = $exchanges['user_amount'];
        $data['user_exchange_scuessed_times'] = $exchanges['successed_orders'];
        $data['user_exchange_costs']          = $exchanges['total_costs'];
        //金币流通情况
        $userMsg                      = User_Service_Gather::getSumScoresInfo();
        $data['user_total_number']    = $userMsg['total_users'];
        $yestdayUserAmount            = self::_getDao()->getBy(array(
            'date' => date('Ymd', strtotime('-1 day')),
            'type' => 'user',
            'key'  => 'user_total_number'
        ));
        $lastTotal                    = $yestdayUserAmount ? $yestdayUserAmount['val'] : 0;
        $data['user_day_incre']       = $data['user_total_number'] - $lastTotal; //每天新增用户数
        $data['user_currency_scores'] = $userMsg['total_remained_scores'];
        foreach ($data as $k => $v) {
            $params = array(
                'type' => 'user',
                'key'  => $k,
                'val'  => $v,
                'date' => date('Ymd', strtotime('now'))
            );
            $info   = Gionee_Service_Log::getBy(array(
                'type' => $params['type'],
                'key'  => $params['key'],
                'date' => $params['date']
            ));

            if ($info) {
                Gionee_Service_Log::set($params, $info['id']);
            } else {
                Gionee_Service_Log::add($params);
            }
        }
    }


    /**
     * 按类型和内容ID得到点击数列表
     *
     * @param int $type
     * @param int $ids   CP列表中需要查询的ID数组
     * @param int $sdate 开始时间
     * @param int $edate 结束时间
     */

    public static function getLogDataByType($ids, $sdate, $edate, $pageType = 0) {
        $query = $res = array();
        if (!empty($ids)) {
            $nids = self::getDataIdsByType($ids, $pageType);
            if (!empty($nids)) {
                $temp = array();
                foreach ($nids as $key => $val) {
                    foreach ($val as $s => $t) {
                        $temp[] = $t;
                    }
                }
                $types          = array(
                    '1' => 'nav',
                    '2' => 'sitemap',
                    '3' => 'sohu',
                    '4' => 'bookmark'
                );
                $query['key']   = array("IN", $temp);
                $query['sdate'] = strtotime($sdate);
                $query['edate'] = strtotime($edate);
                $query['type']  = $pageType ? array($types[$pageType]) : array_values($types);
                $sumData        = Gionee_Service_Log::getSumByChannel($query, $query['type'], array('date'), array('date'));
                foreach ($sumData as $k => $v) {
                    $res[date('Y-m-d', strtotime($v['date']))] = $v['total'];
                }
            }
        }
        return $res;
    }

    public static function getDataIdsByType($ids, $type = 1) {
        $data = $tmp = array();
        switch ($type) {
            case 1: { //导航页内容ID
                $data = Gionee_Service_Ng::getsBy(array("cp_id" => array("IN", $ids)), array('id' => 'DESC'));
                break;
            }
            case 2: {//网址大全
                $data = Gionee_Service_SiteContent::getsBy(array('cp_id' => array('IN', $ids)), array('id' => 'DESC'));
                break;
            }
            case 3: {//新闻页
                list($total, $data) = Gionee_Service_Sohu::getsBy(array(
                    'cp_id' => array(
                        'IN',
                        $ids
                    )
                ), array('id' => 'DESC'));
                break;
            }
            case 4: {//内置书签页
                $data = Gionee_Service_Bookmark::getsBy(array('cp_id' => array('IN', $ids)), array('id' => 'DESC'));
                break;
            }

            default://全部内部
            {
                $temp1 = Gionee_Service_Ng::getsBy(array("cp_id" => array("IN", $ids)), array('id' => 'DESC'));
                $temp2 = Gionee_Service_SiteContent::getsBy(array('cp_id' => array('IN', $ids)), array('id' => 'DESC'));
                $temp3 = Gionee_Service_Bookmark::getsBy(array('cp_id' => array('IN', $ids)), array('id' => 'DESC'));
                list($total, $temp4) = Gionee_Service_Sohu::getsBy(array(
                    'cp_id' => array(
                        'IN',
                        $ids
                    )
                ), array('id' => 'DESC'));
                $data = array_merge($temp1, $temp2, $temp3, $temp4);
                break;
            }
        }
        foreach ($data as $k => $v) {
            $tmp[$v['cp_id']][] = $v['id'];
        }
        return $tmp;
    }

    public static function getVoipTimeExChangeSumData($params = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getVoipExChangeMonthData($params);
    }

    /**
     *
     */

    public static function addPVUVData($pvType, $uvType, $goodsId, $source = '') {
        self::incrBy($pvType, $goodsId);
        self::toUVByCacheKey($uvType, $goodsId, $source);
    }


    /**
     * @param $arr [ver, stime, etime,type,key]
     *
     * @return bool
     */
    public static function getListByWhere($arr) {
        $params = array();
        if (isset($arr['ver'])) {
            $params['ver'] = $arr['ver'];
        }
        if (isset($arr['stime']) && isset($arr['etime'])) {
            $params['date'] = array(
                array('>=', date('Ymd', $arr['stime'])),
                array('<=', date('Ymd', $arr['etime']))
            );
        }

        if (!empty($arr['type'])) {
            if (is_array($arr['type'])) {
                $params['type'] = array("IN", $arr['type']);
            } else {
                $params['type'] = $arr['type'];
            }
        }

        if (!empty($arr['key'])) {
            if (is_array($arr['key'])) {
                $params['key'] = array("IN", $arr['key']);
            } else {
                $params['key'] = $arr['key'];
            }
        }
        $orderBy = array('type' => 'ASC', 'date' => 'ASC');

        $list = self::_getDao()->getsBy($params, $orderBy);
        return $list;
    }

    public static function getPartnerSumByWhere($arr) {
        $params = array();
        if (isset($arr['ver'])) {
            $params['ver'] = $arr['ver'];
        }
        if (isset($arr['date'])) {
            $params['date'] = $arr['date'];
        }

        if (!empty($arr['type'])) {
            $params['type'] = $arr['type'];
        }

        $num = self::_getDao()->sum('val', $params);
        return intval($num);
    }

    //经验等级用户汇总
    public static function writeLevelUserAmountToDb() {
        $config   = Common::getConfig('userConfig', 'experience_level_data');
        $rankList = array_keys($config);

        $totalAccount              = User_Service_Gather::count(array());
        $param['experience_level'] = array('>', 1);
        $levelAmount               = Gionee_Service_User::count($param);
        foreach ($rankList as $k => $v) {
            if ($v == 1) {
                $num = $totalAccount - $levelAmount;
            } else {
                $num = Gionee_Service_User::count(array('experience_level' => $v));
            }
            $params        = array(
                'date' => date('Ymd', time()),
                'type' => 'experience',
                'key'  => 'user_experience_level_' . $v,
                'ver'  => $v
            );
            $data          = Gionee_Service_Log::getBy($params);
            $params['val'] = $num;
            if (empty($data)) {
                self::add($params);
            } else {
                self::update($params, $data['id']);
            }
        }
        return true;
    }

    //
    public static function getLevelUserData($sdate, $edate, $key = 'experience') {
        $where         = array();
        $where['date'] = array(array('>=', $sdate), array('<=', $edate));
        $where['type'] = $key;
        $data          = self::getsBy($where, array('id' => 'DESC'));
        $ret           = array();
        foreach ($data as $k => $v) {
            $ret[$v['date']][$v['ver']] = $v['val'];
        }
        return $ret;
    }

    static public function syncReqTimes($date) {
        $list = Common::getCache()->hGetAll('MON_KEY_NUM:' . $date);
        arsort($list);
        foreach ($list as $name => $num) {
            if (strlen($name) > 32) {
                continue;
            }

            $params = array(
                'date' => $date,
                'type' => 'access_times_pv',
                'key'  => $name,
                'val'  => $num,
            );
            $row    = Gionee_Service_Log::getBy($params);
            if (!empty($row['id'])) {
                Gionee_Service_Log::set($params, $row['id']);
            } else {
                Gionee_Service_Log::add($params);
            }
        }

    }

    /**
     * 模块统计数据收集
     *
     * @param $module
     * @param $value
     */
    static public function incrUvPv($module, $value) {
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_PV, $value . ':' . $module);
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_UV, $value . ':' . $module);
    }
}
