<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author yinjiayan
 *
 */
class Admin_Service_Material {
	
	/**
	 * 
	 * @author yinjiayan
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $perpage = 20, $params = array(), $itemParams = array()) {
		if ($page < 1) {
		    $page = 1;
		}
		$start = ($page -1) * $perpage;
		$list = array();
		$total=0;
		if($itemParams) {
			$newsIdList = self::queryNewsIdList($itemParams);
			if($newsIdList) {
				$params['id'] =  array('in', $newsIdList);
			} else {
			    return array(0, $list, array());
			}
		}
		
		$list = self::getDao()->getList(intval($start), intval($perpage), $params, array('id'=>'DESC'));
		$total = self::getDao()->count($params);
		
		$itemsList = array();
		foreach ($list as &$news) {
			$itemsList[$news['id']] = self::getItems($news['id']);
		}
		return array($total, $list, $itemsList);
	}
	
	/**
	 * 标题查询，从素材项按照标题查询复合条件的素材id
	 */
	private static function queryNewsIdList($params) {
		$newsIdList = self::getItemDao()->getNewsIdListByItem($params);
		if($newsIdList) {
			array_walk($newsIdList, function(&$value, $key, $return) {
				$value = $value[$return];
			}, 'news_id');
		}
		return $newsIdList;
	}
	
	public static function getItems($newsId) {
	    $params = array( 'news_id' => $newsId );
	    $list = self::getItemDao()->getsBy($params, array('order_index'=>''));
	    return $list;
	}
	
	public static function getTitle($newsId) {
	    $params = array( 
	                    'news_id' => $newsId, 
	                    'order_index' => 1
	    );
	    $newsItem = self::getItemDao()->getBy($params);
	    return $newsItem['title'];
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @param unknown $params
	 */
	public static function getTotal($params = array()) {
	    return self::getDao()->count($params);
	}
	
	/**
	 * 
	 * @author wupeng
	 * @param int $id
	 */
	public static function delete($id) {
		Common_Service_Base::beginTransaction();
		$ret = self::getDao()->delete(intval($id));
		if (!$ret) {
			Common_Service_Base::rollBack();
			return false;
		}
		$items['news_id'] = $id;
		$ret = self::getItemDao()->deleteBy($items);
		if (!$ret) {
			Common_Service_Base::rollBack();
			return false;
		}
		Common_Service_Base::commit();
		return true;
	}
	
	/**
	 *
	 * @author wupeng
	 * @param 数组 $params
	 * @param 二维数组 $itemsParams
	 */
	public static function add($params, $itemsParams) {
		Common_Service_Base::beginTransaction();
		$ret = self::getDao()->insert($params);
		if (!$ret) {
			Common_Service_Base::rollBack();
			return false;
		}
		$id = self::getDao()->getLastInsertId();
		foreach ($itemsParams as $key=>$items) {
			$items['news_id'] = $id;
			$itemsParams[$key] = $items;
		}
		$ret = self::getItemDao()->mutiFieldInsert($itemsParams);
		if (!$ret) {
		    Common_Service_Base::rollBack();
		    return false;
		}
		Common_Service_Base::commit();
		return true;
	}

	/**
	 *
	 * @author wupeng
	 * @param 数组 $params
	 * @param 二维数组 $itemsParams
	 */
	public static function edit($id, $params, $itemsParams) {
		Common_Service_Base::beginTransaction();
		$ret = self::getDao()->update($params, $id);
		if (!$ret) {
			Common_Service_Base::rollBack();
			return false;
		}
		$ret = self::getItemDao()->deleteBy(array('news_id' => intval($id)));
        if (!$ret) {
			Common_Service_Base::rollBack();
			return false;
		}
		foreach ($itemsParams as $items) {
	        $ret = self::getItemDao()->insert($items);
			if (!$ret) {
				Common_Service_Base::rollBack();
				return false;
			}
		}
		Common_Service_Base::commit();
		return true;
	}
	
	/**
	 * 素材和素材项同时返回
	 * @author wupeng
	 * @param unknown $id
	 */
	public static function getById($id) {
		$news = self::getDao()->get(intval($id));
		$itemsList = self::getItems($news['id']);
		return array( $news, $itemsList);
	}
	
	/**
	 * 
	 * @author yinjiayan
	 * @return Admin_Dao_Gift
	 */
	private static function getDao() {
		return Common::getDao("Admin_Dao_Material");
	}
	
	private static function getItemDao() {
	    return Common::getDao("Admin_Dao_MaterialItem");
	}
}