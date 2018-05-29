<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_ScoreLog
 * @author terry
 *
 */
class Gou_Service_ScoreLog extends Common_Service_Base{

    private static $score_summary = array(
        'uid'       => '',
        'sum_score' => 0,
        'sum_sign'  => 0,
        'sum_cut'   => 0,
        'sum_scut'  => 0,
        'sum_fcut'  => 0,
        'sum_runon' => 0,
        'sign_date' => 0,
        'look_task_date' => 0,
        );

    /**
     * @description 积分列表
     * @param int $page current page
     * @param int $limit page size
     * @param array $params conditions
     * @param array $orderBy order by index string
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    /**
     * @function get
     * @description 根据id获取单条记录
     *
     * @param integer $id input id
     * @return array
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     * get user score info by uid
     * @param array $params
     * @param array $orderBy
     * @return boolean|mixed
     */
    public static function getBy($params,  $orderBy = array()) {
        if(!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getBy($data, $orderBy);
    }

    /**
     * get user score info list by uid
     * @param array $params
     * @param array $orderBy
     * @return boolean|mixed
     */
    public static function getsBy($params, $orderBy = array()) {
        if(!is_array($params)) return false;
        $data = self::_cookData($params);
        return self::_getDao()->getsBy($data, $orderBy);
    }

    /**
     * @description 添加记录
     *
     * @param array $data data will created
     * @return boolean
     */
    public static function add($data){
        if(!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->insert($data);
    }

    /**
     *
     * @param $params
     * @return bool|string
     */
    public static function count($params){
        if(!is_array($params)) return false;
        $params = self::_cookData($params);
        return self::_getDao()->count($params);
    }

    /**
     * @description 添加每日积分任务
     * @param $data
     * @return bool|int
     */
    public static function addTask($data){
        if (!is_array($data)) return false;
        $data = self::_cookDataTask($data);
        return self::_getDaoTask()->insert($data);
    }

    /**
     * @description 更新每日积分任务
     *
     * @param array $params
     * @param array $step
     * @return bool|int
     */
    public static function updateTask($params, $step){
        if (!is_array($params)) return false;
        $params = self::_cookDataTask($params);
        return self::_getDaoTask()->increment('count', $params, $step);
    }

    /**
     * @description 获取每日某个积分任务
     * @param $params
     * @return bool|array
     */
    public static function getTask($params){
        if (!is_array($params)) return false;
        $params = self::_cookDataTask($params);
        return self::_getDaoTask()->getBy($params);
    }

    /**
     * @description 获取每日所有积分任务
     * @param $params
     * @return bool|array
     */
    public static function getTasks($params){
        if (!is_array($params)) return false;
        $params = self::_cookDataTask($params);
        return self::_getDaoTask()->getsBy($params);
    }


    /**
     * 会员积分处理适配器, 仅适配积分类型的一级类型
     * @param int $id   积分类型ID
     * @param string $uid  会员UID
     * @param string $now_time  日期
     * @return bool
     */
    public static function score($id = '', $uid = '', $now_time = ''){
        list($score_onoff, $score_date) = self::scoreAvailable();

        $score_type = Common::resetKey(Common::getConfig('scoreConfig'), 'id');

        if($score_onoff && $score_date && $uid && is_int($id) && array_key_exists($id, $score_type)){
            $now_time = $now_time?strtotime($now_time):Common::getTime();
            $adapter = 'adapter'.ucfirst($score_type[$id]['adapter']);
            if(method_exists(__CLASS__, $adapter)){
                return self::$adapter($score_type[$id], $uid, $now_time);
            }
        }

        return false;
    }

    /**
     * 处理积分日志和积分任务
     * @param $config
     * @param $uid
     * @param $now_time
     * @return bool     判断每日积分任务是否超过, 超过返回 false
     */
    private static function scoreLogAndTask($config, $uid, $now_time){

        //获取积分任务信息
        $task = self::getTask(array(
            'uid' => $uid,
            'type_id' => $config['id'],
            'date' => date('Y-m-d', $now_time)));

        if($task){
            if($task['count'] < $config['limit']){
                //添加积分日志
                self::add(array(
                    'uid' => $uid,
                    'date' => date('Y-m-d', $now_time),
                    'create_time' => $now_time,
                    'score' => $config['score'],
                    'type_id' => $config['id']));

                //更新每日任务
                self::updateTask(array(
                    'uid' => $uid,
                    'date' => date('Y-m-d', $now_time),
                    'type_id' => $config['id']), 1);

                return true;
            }
        }else{
            //添加积分日志
            self::add(array(
                'uid' => $uid,
                'date' => date('Y-m-d', $now_time),
                'create_time' => $now_time,
                'score' => $config['score'],
                'type_id' => $config['id']));


            //添加每日任务
            self::addTask(array(
                'uid' => $uid,
                'date' => date('Y-m-d', $now_time),
                'type_id' => $config['id'],
                'count' => 1));

            return true;
        }

        return false;
    }

    /**
     * 处理积分累计日志, 并返回所获积分
     * @param $config
     * @param $uid
     * @param $now_time
     * @param $count        累计次数
     * @return int|bool     返回获取分数
     */
    private static function scoreUpto($config, $uid, $now_time, $count){
        if(isset($config['upto'])){
            foreach($config['upto'] as $item){
                if($count !=0 && intval($count)%$item['total'] == 0){
                    //如果达到累计规则, 添加积分日志
                    self::add(array(
                        'uid' => $uid,
                        'date' => date('Y-m-d', $now_time),
                        'create_time' => $now_time,
                        'score' => $item['score'],
                        'type_id' => $item['id']));

                    //添加累计任务
                    self::addTask(array(
                        'uid' => $uid,
                        'date' => date('Y-m-d', $now_time),
                        'type_id' => $item['id'],
                        'count' => 1));

                    //返回增加的累计分
                    return $item['score'];
                }
            }
        }
        return false;
    }

    /**
     * 处理积分连续日志, 并返回所获积分
     * @param $config
     * @param $uid
     * @param $now_time
     * @return int|bool  返回获取分数
     */
    private static function scoreRunon($config, $uid, $now_time, $runon){
        if(isset($config['runon'])) {
            foreach ($config['runon'] as $item) {
                if ($item['total'] === $runon) {
                    //如果达到累计规则, 添加积分日志
                    self::add(array(
                        'uid' => $uid,
                        'date' => date('Y-m-d', $now_time),
                        'create_time' => $now_time,
                        'score' => $item['score'],
                        'type_id' => $item['id']));

                    //添加累计任务
                    self::addTask(array(
                        'uid' => $uid,
                        'date' => date('Y-m-d', $now_time),
                        'type_id' => $item['id'],
                        'count' => 1));

                    //返回增加的累计分
                    return $item['score'];
                }
            }
        }
        return false;
    }

    /**
     * 每日签到
     * @param array $config
     * @param string $uid
     * @param string $now_time
     * @return bool
     */
    private static function adapterMrqd($config, $uid, $now_time){
        try {
            parent::beginTransaction();

            //处理积分日志和积分任务
            if(self::scoreLogAndTask($config, $uid, $now_time)){
                self::$score_summary['uid'] = $uid;

                $sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
                if(!$sum) $sum = array();
                $sum = array_merge(self::$score_summary, $sum);

                $sum['sum_sign']++;
                $sum['sum_score'] += $config['score'];

                //处理积分累计日志
                $score_upto = self::scoreUpto($config, $uid, $now_time, $sum['sum_sign']);
                $sum['sum_score'] += intval($score_upto);

                $yesterday = date('Y-m-d', $now_time-3600*24);
                if($sum['sign_date'] == 0 || ($sum['sign_date'] && $sum['sign_date'] == $yesterday)) $sum['sum_runon']++;

                //处理积分连续日志
                $score_runon = self::scoreRunon($config, $uid, $now_time, $sum['sum_runon']);
                if($score_runon !== false){
                    $sum['sum_runon'] = 0;
                    $sum['sum_score'] += intval($score_runon);
                }

                $sum['sign_date'] = date('Y-m-d', $now_time);

                //更新积分总计
                Gou_Service_ScoreSummary::replace($sum);
            }else{
                return false;
            }

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
        }
    }

    /**
     * 每日砍价
     * @param array $config
     * @param string $uid
     * @param string $now_time
     * @return bool
     */
    private static function adapterMrkj($config, $uid, $now_time){
        try {
            parent::beginTransaction();

            //处理积分日志和积分任务
            if(self::scoreLogAndTask($config, $uid, $now_time)){
                self::$score_summary['uid'] = $uid;
                $sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
                if(!$sum) $sum = array();
                $sum = array_merge(self::$score_summary, $sum);

                $sum['sum_cut']++;
                $sum['sum_score'] += $config['score'];

                //处理积分累计日志
                $score_upto = self::scoreUpto($config, $uid, $now_time, $sum['sum_cut']);
                $sum['sum_score'] += $score_upto;

                //更新积分总计
                Gou_Service_ScoreSummary::replace($sum);
            }else{
                return 0;
            }

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
        }
    }


    /**
     * 每日分享砍价
     * @param array $config
     * @param string $uid
     * @param string $now_time
     * @return bool
     */
    private static function adapterMrfxkj($config, $uid, $now_time){
        try {
            parent::beginTransaction();

            //处理积分日志和积分任务
            if(self::scoreLogAndTask($config, $uid, $now_time)){
                self::$score_summary['uid'] = $uid;
                $sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
                if(!$sum) $sum = array();
                $sum = array_merge(self::$score_summary, $sum);

                $sum['sum_scut']++;
                $sum['sum_score'] += $config['score'];

                //处理积分累计日志
                $score_upto = self::scoreUpto($config, $uid, $now_time, $sum['sum_scut']);
                $sum['sum_score'] += $score_upto;

                //更新积分总计
                Gou_Service_ScoreSummary::replace($sum);
            }else{
                return 0;
            }

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
        }
    }

    /**
     * 每日邀请好友帮忙砍价
     * @param array $config
     * @param string $uid
     * @param string $now_time
     * @return bool
     */
    private static function adapterMrhykj($config, $uid, $now_time){
        try {
            parent::beginTransaction();

            //处理积分日志和积分任务
            if(self::scoreLogAndTask($config, $uid, $now_time)){
                self::$score_summary['uid'] = $uid;
                $sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
                if(!$sum) $sum = array();
                $sum = array_merge(self::$score_summary, $sum);

                $sum['sum_fcut']++;
                $sum['sum_score'] += $config['score'];

                //处理积分累计日志
                $score_upto = self::scoreUpto($config, $uid, $now_time, $sum['sum_fcut']);
                $sum['sum_score'] += $score_upto;

                //更新积分总计
                Gou_Service_ScoreSummary::replace($sum);
            }else{
                return 0;
            }

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
        }
    }

    /**
     * 每日分享购物大厅
     * @param array $config
     * @param string $uid
     * @param string $now_time
     * @return bool
     */
    private static function adapterMrfxgwdt($config, $uid, $now_time){
        try {
            parent::beginTransaction();

            //处理积分日志和积分任务
            if(self::scoreLogAndTask($config, $uid, $now_time)){
                self::$score_summary['uid'] = $uid;
                $sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
                if(!$sum) $sum = array();
                $sum = array_merge(self::$score_summary, $sum);

                $sum['sum_score'] += $config['score'];

                //更新积分总计
                Gou_Service_ScoreSummary::replace($sum);
            }else{
                return 0;
            }

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
        }
    }

    /**
     * 每日分享购物大厅链接
     * @param array $config
     * @param string $uid
     * @param string $now_time
     * @return bool
     */
    private static function adapterMrfxlj($config, $uid, $now_time){
        try {
            parent::beginTransaction();

            //处理积分日志和积分任务
            if(self::scoreLogAndTask($config, $uid, $now_time)){
                self::$score_summary['uid'] = $uid;
                $sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
                if(!$sum) $sum = array();
                $sum = array_merge(self::$score_summary, $sum);

                $sum['sum_score'] += $config['score'];

                //更新积分总计
                Gou_Service_ScoreSummary::replace($sum);
            }else{
                return 0;
            }

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
        }
    }

    /**
     * 关注购物大厅微信
     * @param array $config
     * @param string $uid
     * @param string $now_time
     * @return bool
     */
    private static function adapterGzwx($config, $uid, $now_time){
        try {
            parent::beginTransaction();

            //处理积分日志和积分任务
            if(self::scoreLogAndTask($config, $uid, $now_time)){
                self::$score_summary['uid'] = $uid;
                $sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
                if(!$sum) $sum = array();
                $sum = array_merge(self::$score_summary, $sum);

                $sum['sum_score'] += $config['score'];

                //更新积分总计
                Gou_Service_ScoreSummary::replace($sum);
            }else{
                return 0;
            }

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
        }
    }

    /**
     * 判断积分活动是否可用
     * @return array
     * $score_onoff 积分系统开关
     * $score_date 积分系统有效期
     */
    public static function scoreAvailable(){
        //积分系统开关
        $score_onoff = (bool)Gou_Service_Config::getValue('gou_score');

        //积分开始和结束日期, 判断是否在有效期内
        $score_sdate = strtotime(Gou_Service_Config::getValue('gou_score_sdate'));
        $score_edate = strtotime(Gou_Service_Config::getValue('gou_score_edate'));
        $today = Common::getTime();
        $score_date = false;

        if($today >= $score_sdate && $today <= $score_edate) $score_date = true;

        return list($score_onoff, $score_date) = array($score_onoff, $score_date);
    }


    /**
     * 获取积分规则类型
     * @return array
     */
    public static function scoreType(){
        $score_type = array();
        foreach(Common::getConfig('scoreConfig') as $item){
            $score_type[] = $item;
            if(isset($item['upto'])) $score_type[] = $item['upto'][0];
            if(isset($item['runon'])) $score_type[] = $item['runon'][0];
        }
        return Common::resetKey($score_type, 'id');
    }

    /**
     * gou_score_log 参数过滤
     *
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['date'])) $tmp['date'] = $data['date'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['score'])) $tmp['score'] = $data['score'];
        if(isset($data['type_id'])) $tmp['type_id'] = intval($data['type_id']);
        return $tmp;
    }

    /**
     *
     * @return Gou_Dao_ScoreLog
     */
    private static function _getDao() {
        return Common::getDao("Gou_Dao_ScoreLog");
    }

    /**
     * gou_score_task 参数过滤
     *
     * @param array $data
     * @return array
     */
    private static function _cookDataTask($data) {
        $tmp = array();
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['date'])) $tmp['date'] = $data['date'];
        if(isset($data['type_id'])) $tmp['type_id'] = intval($data['type_id']);
        if(isset($data['count'])) $tmp['count'] = intval($data['count']);
        return $tmp;
    }

    /**
     * @return Gou_Dao_ScoreTask
     */
    private static function _getDaoTask(){
        return Common::getDao("Gou_Dao_ScoreTask");
    }

}