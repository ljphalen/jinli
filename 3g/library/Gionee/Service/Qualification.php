<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Qualification  {

	public static function  add($params){
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}

	public static function getList($page,$pageSize,$where,$order){
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList(($page-1)*$pageSize,$pageSize,$where,$order));
	}

	public static function count($params=array()){
		return self::_getDao()->count($params);
	}
	public static function get($id){
		return self::_getDao()->get($id);
	}

	public static function edit($params,$id){
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	public static function getBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	public static function getsBy($params,$order=array()){
		if(!is_array($params))return  false;
		return self::_getDao()->getsBy($params,$order);
	}
	public static function delete($id){
		return self::_getDao()->delete($id);
	}

	private  static function _checkData($params){
		$temp = array();
		if(isset($params['parter_id']))								$temp['parter_id'] = $params['parter_id'];
		if(isset($params['bank_name']))							$temp['bank_name']	 = $params['bank_name'];
		if(isset($params['company_tel']))							$temp['company_tel']	 = $params['company_tel'];
		if(isset($params['company_name']))					$temp['company_name'] = $params['company_name'];
		if(isset($params['company_address']))				$temp['company_address']	 = $params['company_address'];
		if(isset($params['bank_number']))						$temp['bank_number']	 = $params['bank_number'];
		if(isset($params['bill_type']))								$temp['bill_type']	 = $params['bill_type'];
		if(isset($params['bill_content']))							$temp['bill_content']	 = $params['bill_content'];
		if(isset($params['receiver_name']))						$temp['receiver_name']	 = $params['receiver_name'];
		if(isset($params['receiver_address']))					$temp['receiver_address']	 = $params['receiver_address'];
		if(isset($params['receiver_tel']))							$temp['receiver_tel']	 = $params['receiver_tel'];
		if(isset($params['email']))										$temp['email']	 = $params['email'];
		if(isset($params['tax_image']))								$temp['tax_image']	 = $params['tax_image'];
		if(isset($params['tax_number']))							$temp['tax_number']	 = $params['tax_number'];
		if(isset($params['created_time']))						$temp['created_time']	 = $params['created_time'];
		if(isset($params['created_name']))						$temp['created_name']	 = $params['created_name'];
		if(isset($params['edit_time']))								$temp['edit_time']	 = $params['edit_time'];
		if(isset($params['edit_name']))							$temp['edit_name']	 = $params['edit_name'];
		return $temp;
	}

	/**
	 *
	 * @return Gionee_Dao_Qualification
	 */
	private static  function _getDao(){
		return Common::getDao("Gionee_Dao_Qualification");
	}
}