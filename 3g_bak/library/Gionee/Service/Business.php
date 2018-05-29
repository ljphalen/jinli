<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Business {
	
	public static function  add($params){
		$data = self::_checkData($params);
		return self::_getDao()->insert($data);
	}
	
	public static function getList($page,$pageSize,$where,$order){
		$page = max($page,1);
		return array(self::_getDao()->count($where),self::_getDao()->getList(($page-1)*$pageSize,$pageSize,$where,$order));
	}
	
	public static function get($id){
		return self::_getDao()->get($id);
	}
	
	public static function edit($params,$id){
		$data = self::_checkData($params);
		return self::_getDao()->update($data,$id);
	}
	
	public static function editBy($params=array(),$where = array()){
		if(!is_array($params) || !is_array($where)) return false;
		return self::_getDao()->updateBy($params, $where);
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
	
	public static  function getBussinessDetailInfo($urlIds,$sdate,$edate,$pageType =0){
		$data =  array();
		if(!empty($urlIds)){
			foreach ($urlIds as $k=>$v){
				$data[$k]['clicks'] =Gionee_Service_Log::getLogDataByType(array_values($v), $sdate, $edate,$pageType);//某一业务下所有链接点击次数之和
				$business = self::get($k);
				$parterInfo = Gionee_Service_Parter::get($business['parter_id']);
				$data[$k] ['name']=$business['name'];
				$data[$k]['price'] = $business['price'];
				$data[$k]['price_type'] = $business['price_type'];
				$data[$k]['parter_name'] = $parterInfo['name'];
				$data[$k]['pid'] = $parterInfo['id'];
				$data[$k]['bid'] = $k;
				$data[$k]['status'] = $business['status'];
				$data[$k]['start_time'] = $business['start_time'];
				$data[$k]['end_time'] = $business['end_time'];
				$data[$k]['model'] = $business['model'];
				$data[$k]['closed_time']   = $business['closed_time'];
			}
		}
		return $data;
	}
	
	/*
	 * 根据商户ID 和业务ID 得到所有业务链接ID
	*/
	
	private  static function _checkData($params){
		$temp = array();
		if(isset($params['parter_id']))				$temp['parter_id'] = $params['parter_id'];
		if(isset($params['name']))						$temp['name'] = $params['name'];
		if(isset($params['status']))					$temp['status']	 = $params['status'];
		if(isset($params['price']))						$temp['price']	 = $params['price'];
		if(isset($params['model']))					$temp['model']	 = $params['model'];
		if(isset($params['sdate']))						$temp['start_time']	 = strtotime($params['sdate']);
		if(isset($params['edate']))						$temp['end_time']	 = strtotime($params['edate']);
		if(isset($params['created_time']))		$temp['created_time']	 = $params['created_time'];
		if(isset($params['price_type']))				$temp['price_type'] = $params['price_type'];
		if(isset($params['closed_time']))			$temp['closed_time'] = $params['closed_time'];
 		return $temp;
	}
	
	/**
	 * 
	 * @return Gionee_Dao_Business
	 */
	private static  function _getDao(){
		return Common::getDao("Gionee_Dao_Business");
	}
}