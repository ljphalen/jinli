<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author terry
 *
 */
class Cut_Service_User{

    //玩家默认配置信息
    private static $user_default = array(
        'shortest_time' => 0,
        'speedup' => 0,
        'opt_num' => 3,         //默认3次机会
        'remain_time' => 0,
    );

    private static $remain_length = 1800; //时长2小时后可以复活

    /**
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return array
     */
    public static function getList($page = 1, $limit = 10, $params = array(),$sort=array()) {
        $params = self::_cookData($params);
        if ($page < 1) $page = 1;
        $start = ($page - 1) * $limit;
        $ret = self::_getDao()->getList($start, $limit, $params, $sort);
        $total = self::_getDao()->count($params);
        return array($total, $ret);
    }

    public static function getpoz($row){
        $con1 = array('shortest_time' => array(array('<', $row['shortest_time']), array('>', 0)), 'goods_id' => $row['goods_id']);
        $con2 = array('shortest_time' => $row['shortest_time'], 'create_time' => array('<', $row['create_time']), 'goods_id' => $row['goods_id']);
        $poz1 =  self::count($con1);
        $poz2 =  self::count($con2);
        return $poz1+$poz2;

    }

    public static function getAbove($row,$poz){
        $condition = array('goods_id' => $row['goods_id'], 'shortest_time' => array('>', 0));
        $sort = array('shortest_time'=>'ASC','create_time'=>'ASC');
        list(,$ret) = self::getList($poz,1,$condition, $sort);
        return $ret[0];
    }

    public static function getBelow($row,$poz){
        $condition = array('goods_id' => $row['goods_id'], 'shortest_time' => array('>', 0));
        $sort = array('shortest_time'=>'ASC','create_time'=>'ASC',);
        list(,$ret) = self::getList($poz+2,1,$condition, $sort);
        return $ret[0];
    }

    /**
     *
     * @param array $params
     * @return bool|string
     */
    public static function count($params = array()){
        if (!is_array($params)) return false;
        return self::_getDao()->count($params);
    }

    public static function increment($params = array(),$col){
        if (!is_array($params)) return false;
        return self::_getDao()->increment($col,$params);
    }

    /**
     * @param int $id
     * @return bool|mixed
     */
    public static function get($id) {
        if (!intval($id)) return false;
        return self::_getDao()->get(intval($id));
    }

    /**
     * @param string $uid
     * @param int $goods_id
     * @return bool|mixed
     */
    public static function getByUidAndGoods($uid, $goods_id){
        if (!$uid) return false;
        if (!$goods_id) return false;
        $params = array('uid' => $uid, 'goods_id' => $goods_id);
        return self::_getDao()->getBy($params);
    }

    /**
     *
     * @param array $params
     * @return boolean|Ambigous <boolean, mixed>
     */
    public static function getBy($params, $sort = array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params, $sort);
    }

    /**
     * @param $params
     * @param array $sort
     * @return array|bool
     */
    public static function getsBy($params,$sort=array()) {
        if (!is_array($params)) return false;
        $total = self::_getDao()->count($params);
        $ret = self::_getDao()->getsBy($params,$sort);
        return array($total,$ret);
    }

    /**
     * @param array $data data
     * @param int   $id
     * @return bool|int
     */
    public static function update($data, $id) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, intval($id));
    }

    /**
     * @param int $id drop id
     * @return bool|int
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     *
     * @param array $data
     * @return bool|int
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $data['create_time'] = Common::getTime();
        $data = array_merge(self::$user_default, $data);
        return self::_getDao()->insert($data);
    }

    /**
     * 添加用户
     * @param $data
     * @return array|bool
     */
    public static function addUser($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $data['create_time'] = Common::getTime();
        $user = array_merge(self::$user_default, $data);
        $result = self::_getDao()->insert($user);
        return array($result, $user);
    }

    /**
     * 复活格式化
     * @param $time
     * @return string
     */
    public static function remainTimeFmt($time){
        if(!$time) return '无需恢复';
        $diff_time = Common::getTime() - $time;
        $remain_time = self::$remain_length - $diff_time;
        if($remain_time <= 0){
            $hour = floor($diff_time/3600);
            $min = floor(($diff_time - $hour*3600)/60);
            if($hour <= 24){
                return sprintf('%d时%d分未重玩', $hour, $min);
            }else{
                $day = floor($hour/24);
                $hour = $hour%24;
                return sprintf('%d天%d时%d分未重玩', $day, $hour, $min);
            }
        }else{
            $hour = floor($remain_time/3600);
            $remain_sec = $remain_time - $hour*3600;
            $min = ceil($remain_sec/60);
            if($min == 60){
                $min = 0;
                $hour++;
            }
            return sprintf('%d时%d分后恢复', $hour, $min);
        }
    }

    /**
     * 复活剩余时间
     * @param $time
     * @return array
     */
    public static function remainTime($time){
        $now_time = Common::getTime();
        $diff_time = $now_time - $time;
        $remain_time = self::$remain_length - $diff_time;
        $remain_opt_num = 0;
//        Common::log('diff_time:' . $diff_time, 'cut.log');
        if($remain_time <= 0){
            $remain_opt_num = floor(abs($diff_time)/self::$remain_length);
            if($remain_opt_num > 3) $remain_opt_num = 3;
            return array('', 0, $remain_opt_num);
        }else{
            $hour = floor($remain_time/3600);
            $remain_sec = $remain_time - $hour*3600;
            $min = ceil($remain_sec/60);
            if($min == 60){
                $min = 0;
                $hour++;
            }
            //Common::log('min:' . $min, 'cut.log');
            $perc = $diff_time/self::$remain_length;
            if($perc > 0.99){
                $perc = floor($perc*100)/100;
            }else{
                $perc = ceil($perc*100)/100;
            }
//            Common::log('remain_opt_num:' . $remain_opt_num, 'cut.log');
            return array(sprintf('%d时%d分后恢复', $hour, $min), $perc, $remain_opt_num);
        }
    }

    /**
     * 获取超过百分比
     * @param $goods_id int
     * @param $time int
     */
    public static function overPercent($goods_id, $time){
        $over_num = Cut_Service_User::count(array('goods_id' => $goods_id, 'shortest_time' => array('>', $time)));
        $total_num = Cut_Service_User::count(array('goods_id' => $goods_id, 'shortest_time' =>array('>', 0)));

//        Common::log($time, 'cut.log');
//        Common::log($over_num, 'cut.log');
//        Common::log($total_num, 'cut.log');

        if($total_num == 0) return 0;
        $over_perc = $over_num/$total_num;
        if($over_perc > 0.99){
            $perc = floor($over_perc*100)/100;
        }else{
            $perc = ceil($over_perc*100)/100;
        }
        return $perc;
    }

    /**
     * filter data for user input
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['id'])) $tmp['id'] = intval($data['id']);
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['goods_id'])) $tmp['goods_id'] = intval($data['goods_id']);
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['shortest_time'])) $tmp['shortest_time'] = $data['shortest_time'];
        if(isset($data['speedup'])) $tmp['speedup'] = intval($data['speedup']);
        if(isset($data['opt_num'])) $tmp['opt_num'] = intval($data['opt_num']);
        if(isset($data['remain_time'])) $tmp['remain_time'] = intval($data['remain_time']);
        if(isset($data['count'])) $tmp['count'] = intval($data['count']);
        return $tmp;
    }

    /**
     *
     * @return Cut_Dao_User
     */
    private static function _getDao() {
        return Common::getDao("Cut_Dao_User");
    }
}
