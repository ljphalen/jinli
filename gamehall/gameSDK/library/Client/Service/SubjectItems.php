<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 专题子项
 * Client_Service_SubjectItems
 * @author wupeng
 */
class Client_Service_SubjectItems {

	/**
	 * 返回所有记录
	 * @return array
	 */
	public static function getAllSubjectItems() {
		return array(self::getDao()->count(), self::getDao()->getAll());
	}
	
	/**
	 * 分页查询
	 * @param int $page
	 * @param int $limit
	 * @param array $searchParams
	 * @param array $sortParams
	 * @return array
	 */	 
	public static function getPageList($page = 1, $limit = 10, $searchParams = array(), $sortParams = array()) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::getDao()->getList($start, $limit, $searchParams, $sortParams);
		$total = self::getDao()->count($searchParams);
		return array($total, $ret);
	}
	
	/**
	 * 根据主键查询一条记录
	 * @param int $item_id
	 * @return array
	 */
	public static function getSubjectItems($item_id) {
		if (!$item_id) return null;		
		$keyParams = array('item_id' => $item_id);
		return self::getDao()->getBy($keyParams);
	}
	
	/**
	 * 根据主键更新一条记录
	 * Enter description here ...
	 * @param array $data
	 * @param int $item_id
	 * @return boolean
	 */
	public static function updateSubjectItems($data, $item_id) {
		if (!$item_id) return false;
		$dbData = self::checkField($data);
		if (!is_array($dbData)) return false;
		$keyParams = array('item_id' => $item_id);
		return self::getDao()->updateBy($dbData, $keyParams);
	}
	
	/**
	 * 根据主键删除一条记录
	 * @param int $item_id
	 * @return boolean
	 */
	public static function deleteSubjectItems($item_id) {
		if (!$item_id) return false;
		$keyParams = array('item_id' => $item_id);
		return self::getDao()->deleteBy($keyParams);
	}
	
	/**
	 * 根据主键删除多条记录
	 * @param array $keyList
	 * @return boolean
	 */
	public static function deleteSubjectItemsList($keyList) {
		if (!is_array($keyList)) return false;
		return self::getDao()->deletes('item_id', $keyList);
	}
	
	/**
	 * 添加一条记录
	 * @param array $data
	 * @return boolean
	 */
	public static function addSubjectItems($data) {
		if (!is_array($data)) return false;
		$dbData = self::checkField($data);
		$ret = self::getDao()->insert($dbData);
		if($ret) {
		    return self::getDao()->getLastInsertId();
		}
		return false;
		
	}
	
    /**
     * 获取主题下所有子项
     * @param unknown $sub_id
     * @return array
     */
	public static function getsBySubId($sub_id) {
	    $params = array('sub_id' => $sub_id);
	    $sortParams = array('sort'=>'desc', 'item_id'=>'asc');
	    return self::getDao()->getsBy($params, $sortParams);
	}

	/**
	 * 删除主题对应的子项
	 * @param unknown $subject_id
	 */
	public static function deleteItemsBySubjectId($subject_id) {
	    return self::getDao()->deleteBy(array('sub_id' => $subject_id));
	}
	
	/**
	 * 初始化子项
	 * @param unknown $sub_id
	 * @return array
	 */
	public static function initSubjectItemsBySubId($sub_id) {
	    $data = array();
	    $size = 4;
	    for ($i = 0; $i < $size; $i++) {
	        $data[] = array(
	            'sub_id' => $sub_id, 
	            'sort' => $size - $i, 
	            'title' => '', 
	            'resume' => '', 
	            'view_tpl' => 0, 
	        );
	    }
	    return self::getDao()->mutiFieldInsert($data);
	}
	
	/**
	 * 更新自定义专题
	 * @param unknown $itemTitles
	 * @param unknown $itemResume
	 * @param unknown $gameResume
	 */
	public static function updateCustomSubject($itemTitles, $itemResumes, $gameResumes) {
	    if(count($itemTitles) == 0 && count($itemResumes) == 0 && count($gameResumes) == 0) {
	        return true;
	    }
	    Common_Service_Base::beginTransaction();
	    foreach ($itemTitles as $item_id => $title) {
	        self::updateSubjectItems(array('title' => $title), $item_id);
	    }
	    foreach ($itemResumes as $item_id => $resume) {
	        self::updateSubjectItems(array('resume' => $resume), $item_id);
	    }
	    foreach ($gameResumes as $id => $resume) {
	        Client_Service_SubjectGames::updateSubjectGames(array('resume' => $resume), $id);
	    }
	    Common_Service_Base::commit();
	    return true;
	}
	
	/**
	 * 检查数据库字段
	 * @param array $data
	 * @return array
	 */
	private static function checkField($data) {
		$dbData = array();
		if(isset($data['item_id'])) $dbData['item_id'] = $data['item_id'];
		if(isset($data['sub_id'])) $dbData['sub_id'] = $data['sub_id'];
		if(isset($data['sort'])) $dbData['sort'] = $data['sort'];
		if(isset($data['title'])) $dbData['title'] = $data['title'];
		if(isset($data['resume'])) $dbData['resume'] = $data['resume'];
		if(isset($data['view_tpl'])) $dbData['view_tpl'] = $data['view_tpl'];
		return $dbData;
	}

	/**
	 * @return Client_Dao_SubjectItems
	 */
	private static function getDao() {
		return Common::getDao("Client_Dao_SubjectItems");
	}
	
}
