<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 本地化导航
 */
class Gionee_Service_LocalNavType {

	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
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
	 * @param int $id
	 */
	public static function set($data, $id) {
		if (!is_array($data)) {
			return false;
		}
		return self::_getDao()->update($data, intval($id));
	}

	public static function getBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getBy($params, $orderBy);
	}

	/**
	 *
	 * @param array $params
	 * @param array $orderBy
	 */
	public static function getsBy($params = array(), $orderBy = array()) {
		return self::_getDao()->getsBy($params, $orderBy);
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

	/**
	 *
	 * @param array $data
	 */
	public static function add($data) {
		if (!is_array($data)) {
			return false;
		}
		return self::_getDao()->insert($data);
	}

	public static function options() {
		return self::_getDao()->getsBy(array('flag' => 0), array('sort' => 'asc', 'id' => 'desc'));
	}

	public static function getModelTypeData($mids=0,$sort=1,$type=1){
		return self::_getModelTypeData($mids,$sort,$type);
	}
	
	
	public static function getModelTypeIds($model,$version,$ip,$category){
		$mids = Gionee_Service_ModelContent::curUserModelIds($model, $version, $ip,$category);
		return $mids;
		
	}
	
	
	private static function _getModelTypeData($mids,$sort,$type){
		$data = array();
		if(!empty($mids)){
			$rKey = "LOCALNAV:MODEL:TYPE:DATA:".implode(',',$mids);
			$data = Common::getCache()->get($rKey);
			$data = '';
			if(empty($data)){
				$params = array();
				$params['is_show'] 			= 1;
				$params['status'] 			= 1;
				$params['sort']					= $sort;
				$params['type']				= $type;
				$params['start_time']	 	= array('<=',time());
				$params['end_time'] 		= array('>=',time());
				$params['model_id']		= array('IN',$mids);
				$ret  = self::getBy($params,array('id'=>'DESC'));
				foreach ($ret as $k=>$v){
					$data[$v['sort']] = $v;
				}
				Common::getCache()->set($rKey,$data,300);
			}
		}
		return $data;
	}
	/**
	 *
	 * @return Gionee_Dao_LocalNavType
	 */
	public static function _getDao() {
		return Common::getDao("Gionee_Dao_LocalNavType");
	}
}