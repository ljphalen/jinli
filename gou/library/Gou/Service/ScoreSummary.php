<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Gou_Service_ScoreSummary
 * @author terry
 *
 */
class Gou_Service_ScoreSummary extends Common_Service_Base{

    /**
     * @description 积分累计列表
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
     * @description 更新记录
     *
     * @param array $data
     * @param array $data
     * @return bool|int
     */
    public static function replace($data) {
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->replace($data);
    }

    /**
     * @description 更新记录
     * @param $field
     * @param $params
     * @param $step
     * @return bool|int
     */
    public static function increment($field, $params, $step){
        if (!is_array($params)) return false;
        $params = self::_cookData($params);
        return self::_getDao()->increment($field, $params, $step);
    }

    /**
     *
     * @param array $params
     * @return bool|string
     */
    public static function count($params = array()){
        if (!is_array($params)) return false;
        $params = self::_cookData($params);
        return self::_getDao()->count($params);
    }

    /**
     *
     * @param $data
     * @param $params
     * @return bool
     */
    public static function updateBy($data, $params){
        if (!is_array($data)) return false;
        if (!is_array($params)) return false;
        $data = self::_cookData($data);
        $params = self::_cookData($params);
        return self::_getDao()->updateBy($data, $params);
    }

    /**
     * @param $data
     * @param $value
     * @return bool
     */
    public static function update($data, $value){
        if (!is_array($data)) return false;
        $data = self::_cookData($data);
        return self::_getDao()->update($data, $value);
    }

    /**
     * @description 删除记录
     * @param integer $id
     * @return bool|int
     */
    public static function delete($id) {
        return self::_getDao()->delete(intval($id));
    }

    /**
     *
     * @param $start
     * @param $limit
     * @param int $sqlWhere
     * @param array $orderBy
     * @return array
     */
    public static function searchBy($start, $limit, $sqlWhere = 1, $orderBy = array()){
        return self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
    }

    /**
     *
     * @param array $orderBy
     * @return array
     */
    public static function getAll($orderBy = array()){
        return self::_getDao()->getAll($orderBy);
    }

    /**
     * 获取个人积分排名名次和排名百分比
     * @param int $sum_score
     * @return mixed
     */
    public static function myRank($sum_score = 0){
        if($sum_score == 0) return list($over_count, $over_percent) = array(0, '您排在<em>第n名</em>');

        $total_count = Gou_Service_ScoreSummary::count(array('sum_score'=>array('!=', $sum_score)));
        $total_count++;

        $over_count = Gou_Service_ScoreSummary::count(array('sum_score'=>array('>', $sum_score)));
        $over_count++;

        if($over_count <= 10){
            $over_percent = sprintf('您排在<em>第%s名</em>', $over_count);
        }else{
            $over_percent = ($total_count-$over_count)/$total_count;
            if($over_percent > 0.99){
                $over_percent = floor($over_percent*100);
            }else{
                $over_percent = ceil($over_percent*100);
            }
            $over_percent = sprintf('超过<em>%s</em>的用户', $over_percent . '%');
        }

        return list($over_count, $over_percent) = array($over_count, $over_percent);
    }

    /**
     * 获取某个积分记录的前后排名记录
     * @param int $sum_score
     * @return mixed
     */
    public static function myRankBoth($sum_score = 0, $uid = ''){
        $rank_above = $rank_below = array();

        $rank_above = self::getBy(array('sum_score'=>array('>=', $sum_score), 'uid'=>array('!=', $uid)), array('sum_score'=>'ASC'));

        if($rank_above){
            $rank_below = self::getBy(array('sum_score'=>array('<=', $sum_score), 'uid'=>array(array('!=', $uid), array('!=', $rank_above['uid']))), array('sum_score'=>'DESC'));
        }else{
            $rank_below = self::getBy(array('sum_score'=>array('<=', $sum_score), 'uid'=>array('!=', $uid)), array('sum_score'=>'DESC'));
        }

        if($rank_above){
            list($rk, ) = self::myRank($rank_above['sum_score']);
            $rank_above['pos'] = $rk;
        }

        if($rank_below){
            list($rk, ) = self::myRank($rank_below['sum_score']);
            $rank_below['pos'] = $rk;
        }

        return list($rank_above, $rank_below) = array($rank_above, $rank_below);
    }

    /**
     * 获取签到时间是否和当前日期相等, 如果相等说明已经
     * @param $uid
     * @return bool
     */
    public static function scoreTaskTip($uid){
        if(self::getBy(array('uid'=>$uid, 'look_task_date'=>date('Y-m-d', Common::getTime())))) return false;
        return true;
    }

    /**
     * 清空所有用户积分
     * @return bool
     */
    public static function cleanScore(){
        try {
            parent::beginTransaction();

            self::updateBy(array(
                'sum_score' =>0,
                'sum_sign'  =>0,
                'sum_cut'   =>0,
                'sum_scut'  =>0,
                'sum_fcut'  =>0,
                'sum_runon' =>0,
                'sign_date' =>0,
                'look_task_date'=>0), array());

            return parent::commit();
        } catch (Exception $e) {
            parent::rollBack();
            return fasle;
        }
    }

    /**
     * 参数过滤
     *
     * @param array $data
     * @return array
     */
    private static function _cookData($data) {
        $tmp = array();
        if(isset($data['uid'])) $tmp['uid'] = $data['uid'];
        if(isset($data['sum_score'])) {
            if(is_array($data['sum_score'])){
                $tmp['sum_score'] = $data['sum_score'];
            }else{
                $tmp['sum_score'] = intval($data['sum_score']);
            }
        }
        if(isset($data['sum_sign'])) $tmp['sum_sign'] = intval($data['sum_sign']);
        if(isset($data['sum_cut'])) $tmp['sum_cut'] = intval($data['sum_cut']);
        if(isset($data['sum_scut'])) $tmp['sum_scut'] = intval($data['sum_scut']);
        if(isset($data['sum_fcut'])) $tmp['sum_fcut'] = intval($data['sum_fcut']);
        if(isset($data['sum_runon'])) $tmp['sum_runon'] = intval($data['sum_runon']);
        if(isset($data['sign_date'])) $tmp['sign_date'] = $data['sign_date'];
        if(isset($data['look_task_date'])) $tmp['look_task_date'] = $data['look_task_date'];
        return $tmp;
    }

    /**
     *
     *
     * @return Gou_Dao_ScoreSummary
     */
    private static function _getDao() {
        return Common::getDao("Gou_Dao_ScoreSummary");
    }
}