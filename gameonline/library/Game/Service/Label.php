<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter desLabeliption here ...
 * @author lichanghau
 *
 */
class Game_Service_Label{

	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllLabel() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	
	public static function getLabel($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
    public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addLabel($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addLabel2($data,$label,$subject) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//添加标签索引
			$ret= self::_getIdxGameLabelDao()->mutiInsert($label);
			if (!$ret) throw new Exception('Add label fail.', -204);
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	
	public static function updateLabel($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteLabel($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * Enter desLabeliption here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];		
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
		
	/**
	 *
	 * @return Gou_Dao_Label
	 */
	private static function _getDao() {
		return Common::getDao("Game_Dao_Label");
	}
	
	/**
	 *
	 * @return Game_Dao_IdxGameLabel
	 */
	private static function _getIdxGameLabelDao() {
		return Common::getDao("Game_Dao_IdxGameLabel");
	}
}
