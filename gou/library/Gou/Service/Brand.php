<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author huangsg
 *
 */
class Gou_Service_Brand extends Common_Service_Base {
	/**
	 * 获取品牌数据
	 * @param integer $page
	 * @param integer $limit
	 * @param array $params
	 * @return multitype:unknown multitype:
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, array('is_top'=>'ASC', 'sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取所有品牌数据
	 * @return multitype:
	 */
	public static function getAllBrand(){
		return self::_getDao()->getAll(array('is_top'=>'ASC', 'sort'=>'DESC'));
	}
	
	/**
	 * 根据cate_id获取所有brand数据
	 * @param unknown $cate_id
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getBrandByCateId($cate_id){
		return self::_getDaoSub()->getsBy(array('cate_id'=>$cate_id));
	}
	
	/**
	 * 获取单条品牌数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getBrand($id){
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * 获取单条品牌数据
	 * @param integer $id
	 * @return boolean|mixed
	 */
	public static function getBy($params){
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}
	
	/**
	 * 添加品牌数据
	 * @param array $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function addBrand($data){
		if (!is_array($data)) return false;
		parent::beginTransaction();
		try {
			$cate_id = $data['cate_id'];
			$data = self::_cookData($data);
			if($data['is_top'] == 1){	//如果已经有置顶的数据，则先取消置顶
				self::_getDao()->updateBy(array('is_top'=>2), array('is_top'=>1));
			}
			
			$data['time_line'] = time();
			self::_getDao()->insert($data);
			$brand_id = self::_getDao()->getLastInsertId();
			if (empty($brand_id)){
				parent::rollBack();
				return  false;
			}
			
			// 保存关系数据
			foreach ($cate_id as $val){
				$rs = self::_getDaoSub()->insert(array(
					'cate_id'=>$val,
					'brand_id'=>$brand_id
				));
			}
			
			parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
		}
	}
	
	/**
	 * 修改品牌数据
	 * @param array $data
	 * @param integer $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateBrand($data, $id){
		if (empty($data)) return false;
		parent::beginTransaction();
		try {
			$cate_id = $data['cate_id'];
			$data = self::_cookData($data);
			if($data['is_top'] == 1){
				self::_getDao()->updateBy(array('is_top'=>2), array('is_top'=>1));
			}
			
			$data['time_line'] = time();
			self::_getDao()->update($data, intval($id));
			self::_getDaoSub()->deleteBy(array('brand_id'=>$id));
			foreach ($cate_id as $val){
				$rs = self::_getDaoSub()->insert(array(
					'cate_id'=>$val,
					'brand_id'=>$id
				));
			}
			
			parent::commit();
			return true;
		} catch (Exception $e) {
			Common::debug($e->getMessage());
			parent::rollBack();
		}
	}
	
	/**
	 * 删除品牌数据
	 * @param integer $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteBrand($id){
		parent::beginTransaction();
		try {
			self::_getDao()->delete(intval($id));
			self::_getDaoSub()->deleteBy(array('brand_id'=>$id));
			parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
		}
	}
	
	/**
	 * 根据品牌id获取其分类
	 * @param unknown $brand_id
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getBrandCate($brand_id){
		return self::_getDaoSub()->getsBy(array('brand_id'=>$brand_id));
	}
	
	/**
	 * 更新品牌访问统计信息
	 * @param unknown $brand_id
	 */
	public static function updateHits($brand_id){
		if (empty($brand_id)) return false;
		Gou_Service_ClickStat::increment(20, $brand_id);
		return self::_getDao()->increment('hits', array('id'=>intval($brand_id)));
	}
	
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['banner_img'])) $tmp['banner_img'] = $data['banner_img'];
		if(isset($data['brand_img'])) $tmp['brand_img'] = $data['brand_img'];
		if(isset($data['logo_img'])) $tmp['logo_img'] = $data['logo_img'];
		if(isset($data['brand_desc'])) $tmp['brand_desc'] = $data['brand_desc'];
		if(isset($data['is_top'])) $tmp['is_top'] = intval($data['is_top']);
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['hits'])) $tmp['hits'] = intval($data['hits']);
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Gou_Dao_Brand
	 */
	private static function _getDao() {
		return Common::getDao("Gou_Dao_Brand");
	}
	
	/**
	 * 
	 * @return Gou_Dao_BrandCateLink
	 */
	private static function _getDaoSub(){
		return Common::getDao("Gou_Dao_BrandCateLink");
	}
}