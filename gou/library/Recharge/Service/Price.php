<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Recharge_Service_Price extends Common_Service_Base {
	/**
	 * 获取价格数据
	 * @param integer $page
	 * @param integer $limit
	 * @param array $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList() {
		$params = self::_cookData($params);
		$list = self::_getDao()->getAll(array('sort'=>'DESC'));
		return $list;
	}
	
	/**
	 * 获取单条价格数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getPrice($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 添加价格数据
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addPrice($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		
		parent::beginTransaction();
		try {
			self::_getDao()->insert($data);
			$price_id = self::_getDao()->getLastInsertId();
			if (empty($price_id)){
				parent::rollBack();
				return  false;
			}
			
			// 初始化运营商价格数据
			for ($i = 1; $i <= 3; $i++){
				self::_getDaoSub()->insert(array(
					'pid'=>$price_id,
					'operator'=>$i,
				));
			}
			
			parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
		}
		
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改价格数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updatePrice($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * 删除价格数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deletePrice($id){
		return self::_getDao()->delete(intval($id));
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['price'])) $tmp['price'] = intval($data['price']);
		if(isset($data['range'])) $tmp['range'] = $data['range'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		return $tmp;
	}
	
	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $sort) {
		if (!is_array($params) || !is_array($sort)) return false;
		$ret = self::_getDao()->getsBy($params, $sort);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @return Recharge_Dao_Price
	 */
	private static function _getDao() {
		return Common::getDao("Recharge_Dao_Price");
	}
	
	private static function _getDaoSub() {
		return Common::getDao("Recharge_Dao_Operator");
	}
}