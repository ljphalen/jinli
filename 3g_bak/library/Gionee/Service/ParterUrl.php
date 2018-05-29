<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_ParterUrl {
	

	public static function  add($params){
		$data = self::_checkData($params);
		$ret =  self::_getDao()->insert($data);
		if($ret){
			$ret = self::_getDao()->getLastInsertId();
		}
		return $ret;
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
	
	public static function getLink($parterId,$cpId,$bid,$link){
		if (intval($parterId)) {
			if (empty($bid)) {
			return false;
			}
			if (empty($cpId)) {
				if (empty($link)) {
					return false;
				}
				$data = array(
						'name'         => '--',
						'pid'          => $cpId,
						'bid'          => $bid,
						'url'          => $link,
						'status'       => 1,
						'created_time' => time()
				);
				$id   = Gionee_Service_ParterUrl::add($data);
				if (!intval($id)) {
					return false;
				}
			} else {
				$urlInfo      = Gionee_Service_ParterUrl::get($cpId);
				$link = $urlInfo['url'];
			}
		}
		return $link;
	}
	
	public static function getUrlIdList($page,$pageSize,$where,$order=array()){
		$bids  = $urlIds =   array();
		$lists = Gionee_Service_Business::getsBy($where,$order);
		foreach ( $lists as $k=>$v){
			$bids[] = $v['id'];
		}
		if(!empty($bids)){
			list($total,$urlList) = self::getList($page, $pageSize, array('bid'=>array("IN",$bids),'status'=>1), $order);
			foreach ($urlList as $k=>$j){
				$urlIds[$j['bid']][] = $j['id'];
			}
		}
		return array($total,$urlIds);
	}
	
	private  static function _checkData($params){
		$temp = array();
		if(isset($params['pid']))								$temp['pid']	 = $params['pid'];
		if(isset($params['bid']))								$temp['bid']	 = $params['bid'];
		if(isset($params['url']))								$temp['url'] = $params['url'];
		if(isset($params['url_name']))					$temp['url_name']	 = $params['url_name'];
		if(isset($params['status']))						$temp['status']	 = $params['status'];
		if(isset($params['created_time']))			$temp['created_time']	 = $params['created_time'];
		if(isset($params['edit_time']))					$temp['edit_time']	 = $params['edit_time'];
		return $temp;
	}
	/**
	 * 
	 * @return Gionee_Dao_ParterUrl
	 */
	private static  function _getDao(){
		return Common::getDao('Gionee_Dao_ParterUrl');
	}
}