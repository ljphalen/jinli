<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Browser_Service_Product{
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */

	public static function getProduct($id) {
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * @param unknown_type $series
	 * @param unknown_type $mark
	 * @return string
	 */
	public function getProductByMark($series, $mark) {
		$ret = self::_getDao()->where(array('mark'=>$mark));
		$ret['thumb'] = self::getThumb($series, $mark);
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page -1) * $limit;
		$params = self::_cookData($params);
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	
	/**
	 * 
	 */
	public static function getNewProduct() {
		return self::_getDao()->getNewProduct();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addProduct($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function updateProduct($data, $id){
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 
	 * @param unknown_type $series
	 * @return multitype:string Ambigous <>
	 */
	public static function getProductList($series) {
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$path = $attachPath . 'product' . '/' . $series;
		$list = Util_Folder::read($path, Util_Folder::READ_FILE);
		$return  = array();
		foreach ($list as $key=>$value) {
			$sp = explode('.', $value);
			$array[] = array(
				'img' => 'product/'.$series .'/'. $value,
				'type' =>$series,
				'mark' => $sp[0]);
		}
		return $array;
	}

	/**
	 * 
	 * @param unknown_type $series
	 * @param unknown_type $mark
	 * @return multitype:string
	 */
	public static function getProductPics($series, $mark) {
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$path = $attachPath . 'product' . '/' . $series . '/' . $mark;
		$list = Util_Folder::read($path, Util_Folder::READ_FILE);
		$return  = array();
		foreach ($list as $key=>$value) {
			$return[] = 'product/'.$series .'/'.$mark.'/'. $value;
		}
		return $return;
	}
	
	/**
	 *
	 * @param unknown_type $series
	 * @param unknown_type $mark
	 * @return multitype:string
	 */
	public static function getProductPic($series, $mark) {
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$path = $attachPath . 'product' . '/' . $series;
		$list = Util_Folder::read($path, Util_Folder::READ_FILE);
		foreach ($list as $key=>$value) {
			if (strpos($value, $mark) !== false) {
				return 'product/'.$series .'/'. $value;
			}
		}
		return null;
	}

	/**
	 * 
	 * @param unknown_type $series
	 * @param unknown_type $mark
	 */
	public static function getMarks($series, $mark) {
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$path = $attachPath . 'product' . '/' . $series . '/' . $mark;
		return Util_Folder::read($path, Util_Folder::READ_FILE);
	}
	
	/**
	 * 
	 * @param unknown_type $series
	 * @param unknown_type $mark
	 * @return string
	 */
	public static function getThumb($series, $mark) { 
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$path = $attachPath . 'product' . '/' . $series . '/' . $mark . '/thumb';
		$img = Util_Folder::read($path, Util_Folder::READ_FILE);
		$ret = 'product' . '/' . $series . '/' . $mark . '/thumb/'. $img[0];
		return $ret;
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $uid
	 */
	public static function deleteProduct($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private function _cookData($data) {
		$tmp = array();
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['price'])) $tmp['price'] = $data['price'];
		if (isset($data['mark'])) $tmp['mark'] = $data['mark'];
		if (isset($data['type'])) $tmp['type'] = $data['type'];
		if (isset($data['param'])) $tmp['param'] = $data['param'];
		if (isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['is_new'])) $tmp['is_new'] = intval($data['is_new']);
		return $tmp;
	}

	
	
	/**
	 * 
	 * @return Browser_Dao_Product
	 */
	private static function _getDao() {
		return Common::getDao("Browser_Dao_Product");
	}
}
