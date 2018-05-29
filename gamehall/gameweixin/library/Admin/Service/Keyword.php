<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class Admin_Service_Keyword {
	
    const OPT_TYPE_NEWS = 1;
    const OPT_TYPE_SYS = 2;
    const OPT_TYPE_TEXT = 3;
    
	/**
	 * 
	 * @author yinjiayan
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $perpage = 20, $params = array()) {
		if ($page < 1) {
		    $page = 1;
		}
		$start = ($page -1) * $perpage;
		$list = self::getDao()->getList(intval($start), intval($perpage), $params, array('id'=>'DESC'));
		$total = self::getDao()->count($params);
		return array($total, $list);
	}
	
	public static function getKeyworkByIDList($keylist) {
		$where = array('id' => array('IN', $keylist));
		$list = self::getDao()->getsBy($where);
		foreach($list as $key => $value) {
			$listKeyword[$value['id']] = $value['keyword'];
		}
		return $listKeyword;
	}
	
	public static function getAll() {
	    return self::getDao()->getAll();
	}
	
	/**
	 * 消息匹配关键词
	 * @author yinjiayan
	 * @param unknown $msgContent
	 * @return unknown|Ambigous <NULL, unknown>
	 */
	public static function match($msgContent) {
	    $msgContent = trim($msgContent);
	    if (!$msgContent) {
	    	return false;
	    }
	    
	    $list = self::getDao()->getAll();
	    $lastPos = 0;
	    $result = null;
	    foreach ($list as $item) {
	        $keyWordArray = explode(';', $item['keyword']);
	        foreach ($keyWordArray as $keyword){
	            $keyword = trim($keyword);
    	        if($item['match_type'] == 1) {
    	            if ($msgContent == $keyword) {
    	            	return $item;
    	            }
    	        } else {
    	            $pos = strpos($msgContent, $keyword);
    	            $isFind = $pos === 0 || $pos;
    	            if ($isFind && (!$result || $pos < $lastPos)) {
    	            	$lastPos = $pos;
    	            	$result = $item;
    	            }
    	        }
	        }
	    }
	    return $result;
	}
	
	public static function delete($id) {
	    return self::getDao()->delete($id);
	}
	
	public static function add($data) {
	    return self::getDao()->insert($data);
	}
	
	public static function get($id) {
	    return self::getDao()->get($id);
	}
	
	public static function update($data, $id) {
	    return self::getDao()->update($data, $id);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @return Admin_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Admin_Dao_Keyword");
	}
	
}