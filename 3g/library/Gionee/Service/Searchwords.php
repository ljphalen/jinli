<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Gionee_Service_Searchwords {
	
	public  static  function add($params){
		if(!is_array($params)) return false;
		return self::_getDao()->insert($params);
	}
	
	public static function getLastId(){
		return self::_getDao()->max();
	}
	
	public  static function  getsBy($params){
		if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	public static  function increment($params,$field,$val){
		if(!is_array($params)) return false;
		return self::_getDao()->increment($field,$params,$val);
	}
	
	
	public static function getSearchHot($page,$size,$sdate,$edate,$group,$sort){
		if($page<1){
			$page = 1;
		}
		$start = ($page -1)*$size;
		$params = array('query_date'=>array(array('>=',$sdate),array('<=',$edate)));
		return array(self::getCount($params),self::_getDao()->getSearchHot($start,$size,$params,$group,$sort));
	}
	
	public static function getCount($params){
		return self::_getDao()->count($params);
	}
	
	/**
	 * 数据同步到数据库中
	 */
	
	public static  function write2DB(){
		$key = 'SEARCH:'.date('YmdH',time()-3600);
		$allData = Common::getCache()->hGetAll($key);
		foreach ($allData as $k=>$v){
			$content = base64_decode($k);
			$date = date('Y-m-d',time()-3600);
			$res = Gionee_Service_Searchwords::getsBy(array('query_date'=>$date,'content'=>$content));
			if($res){
				Gionee_Service_Searchwords::increment(array('id'=>$res['id']), 'number', $v);
			}else{
				$params = array('content'=>$content,'number'=>$v,'query_date'=>$date);
				Gionee_Service_Searchwords::add($params);
			}
		}
		Common::getCache()->delete($key);
	}
	private  static function _getDao(){
		return Common::getDao("Gionee_Dao_Searchwords");
	}
	
}