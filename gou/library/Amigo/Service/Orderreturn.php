<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Amigo_Service_Orderreturn {
	
	// 		退货：
	// 		我要退货——退货申请已提交——拒绝退货
	// 		我要退货——退货申请已提交——卖家待收货——退款处理中——交易完毕
	
	// 		换货：
	// 		我要换货——换货申请已提交——拒绝换货
	// 		我要换货——换货申请已提交——卖家待收货——换货已发出——交易成功
	public static $order_status = array(
			1=>'换-申请',
			2=>'换-待收货',
			3=>'换-已发货',
			4=>'换-完成',
			5=>'换-无效',
			6=>'退-申请',
			7=>'退-待收货',
			8=>'退-退款处理中',
			9=>'退-完成',
			10=>'退-无效',
	);
	
	/**
	 * 获取退换货订单列表
	 * @param unknown $page
	 * @param unknown $limit
	 * @param unknown $params
	 * @return multitype:unknown
	 */
	public static function getList($page, $limit, $params = array()){
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取一条订单记录
	 * @param unknown $id
	 * @return boolean
	 */
	public static function getOne($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function add($data){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 * 修改订单
	 * @param unknown $data
	 * @param unknown $id
	 * @return boolean
	 */
	public static function update($data, $id){
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	public static  function formatLog($data, $status){
		$update_data_array = json_decode($data, true);
		$log = '';
		if (!empty($update_data_array['status'])){
			$log .= '订单状态：' . self::$order_status[$update_data_array['status']];
		}
		return $log;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return multitype:unknown number
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['order_id'])) $tmp['order_id'] = $data['order_id'];
		if(isset($data['order_return_id'])) $tmp['order_return_id'] = $data['order_return_id'];
		if(isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		if(isset($data['phone'])) $tmp['phone'] = $data['phone'];
		if(isset($data['truename'])) $tmp['truename'] = $data['truename'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['reason_id'])) $tmp['reason_id'] = intval($data['reason_id']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['feedback'])) $tmp['feedback'] = $data['feedback'];
		if(isset($data['remark'])) $tmp['remark'] = $data['remark'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return object
	 */
	private static function _getDao() {
		return Common::getDao("Amigo_Dao_Orderreturn");
	}
}