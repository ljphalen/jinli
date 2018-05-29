<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author terry
 *
 */
class Cut_Service_Game extends Common_Service_Base{

    public static $logFile = "game.log";

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

    /**
     *
     * @param array $params
     * @return bool|string
     */
    public static function count($params = array()){
        if (!is_array($params)) return false;
        $params = self::_cookData($params);
        $sqlWhere  = self::_getDao()->_cookParams($params);
        return self::_getDao()->count($sqlWhere);
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
     *
     * @param unknown_type $params
     * @return boolean|Ambigous <boolean, mixed>
     */
    public static function getBy($params, $sort=array()) {
        if (!is_array($params)) return false;
        return self::_getDao()->getBy($params, $sort);
    }

    public static function getsBy($params,$sort=array()) {
        if (!is_array($params)) return false;
        $total = self::_getDao()->count($params);
        $ret = self::_getDao()->getsBy($params,$sort);
        return array($total,$ret);
    }

    /**
     * @param array $data data
     * @param int   $id row 2 be updated
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
     * @param array $data
     * @return bool|int
     */
    public static function add($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        $data['create_time'] = Common::getTime();
        return self::_getDao()->insert($data);
    }

    /**
     * filter data for user input
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['id'])) $tmp['id'] = $data['id'];
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['goods_id'])) $tmp['goods_id'] = $data['goods_id'];
        if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
        if(isset($data['fin_time'])) $tmp['fin_time'] = $data['fin_time'];
        return $tmp;
    }

    /**
     * 砍价游戏完成一次的处理
     * @param $user
     * @param $goods
     * @param $fin_time
     * @return array
     */
    public static function over($user, $goods, $fin_time){
        try {
            parent::beginTransaction();
            //添加游戏日志
            $game_log = array(
                'uid' => $user['uid'],
                'goods_id' => $goods['id'],
                'fin_time' => floatval($fin_time),
            );
            $ret = Cut_Service_Game::add($game_log);
            if (!$ret) throw new Exception("add game log failed.", -200);

            //更新玩家日志
            if(!floatval($user['shortest_time']) || $fin_time < floatval($user['shortest_time'])){
                $user_log['shortest_time'] = $fin_time;
                $user_log['create_time'] = Common::getTime();
                $ret = Cut_Service_User::update($user_log, $user['id']);
                if (!$ret) throw new Exception("update user log failed.", -200);
            }

            //更新砍价商品的最短游戏时间
            $goods_log = array();
            if(!floatval($goods['shortest_time']) || $fin_time < floatval($goods['shortest_time'])){
                $goods_log= array(
                    'shortest_time' => $fin_time,
                    'uid' => $user['uid']
                );
                $ret = Cut_Service_Goods::updateGoods($goods_log, $goods['id']);
                if (!$ret) throw new Exception("update goods failed.", -200);
            }
            $ret = parent::commit();
            if(!$ret) throw new Exception("game commit failed.", -200);
            if($ret) return array($user_log, $goods_log);
            return false;
        } catch (Exception $e) {
            parent::rollBack();
            Common::log(array($user['id'], $e->getCode(), $e->getMessage()), self::$logFile);
        }
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealCutUvStat($sDate, $eDate){
        return self::_getDao()->getRealCutUvStat($sDate, $eDate);
    }

    /**
     * @param $sDate
     * @param $eDate
     * @return array|bool
     */
    public static function getRealCutPvStat($sDate, $eDate){
        return self::_getDao()->getRealCutPvStat($sDate, $eDate);
    }


    /**
     * @return Cut_Dao_Game
     */
    private static function _getDao() {
        return Common::getDao("Cut_Dao_Game");
    }
}
