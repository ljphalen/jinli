<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 分类游戏操作service
 * @author fanch
 *
 */
class Resource_Service_GameCategory extends Common_Service_Base{
	const MAINCATEGORY=1;
	const LESSCATEGORY=2;

	const PARENTCATEGORY=1;
	const SUBCATEGORY=10;
	/**
	 * 获取游戏主分类：一级分类 |二级分类 
	 * @param int $gameId
	 */
	public static function getMainCategory($gameId){
		//游戏主分类游戏数据
		$mainCategoryData =  Resource_Service_GameCategory::getBy(array('game_id'=>$gameId, 'level'=>self::MAINCATEGORY, 'status'=>1));
		$categoryIds = array($mainCategoryData['parent_id'], $mainCategoryData['category_id']);
		$categoryData = Resource_Service_Attribute::getsBy(array('id'=>array('IN', $categoryIds)));
		$categoryData = Common::resetKey($categoryData, 'id');

		$data = array(
				'parent' =>	array(
						'id'=> $mainCategoryData['parent_id'],
						'title'=> $categoryData[$mainCategoryData['parent_id']]['title']
				),
				'sub'=> array(
						'id'=> $mainCategoryData['category_id'],
						'title'=> $categoryData[$mainCategoryData['category_id']]['title']
				)
		);
		return $data;
	}
	
	/**
	 * 获取游戏次分类：一级分类 |二级分类
	 * @param int $gameId
	 */
	public static function getLessCategory($gameId){
		//游戏次分类数据
		$lessCategoryData =  Resource_Service_GameCategory::getBy(array('game_id'=>$gameId, 'level'=>self::LESSCATEGORY, 'status'=>1));
		if(!$lessCategoryData){
			return array();
		}
		
		$categoryIds = 	array($lessCategoryData['parent_id'], $lessCategoryData['category_id']);
		$categoryData = Resource_Service_Attribute::getsBy(array('id'=>array('IN', $categoryIds)));
		$categoryData = Common::resetKey($categoryData, 'id');
		
		$data = array(
				'parent' =>	array(
						'id'=> $lessCategoryData['parent_id'],
						'title'=> $categoryData[$lessCategoryData['parent_id']]['title']
				),
				'sub'=> array(
						'id'=> $lessCategoryData['category_id'],
						'title'=> $categoryData[$lessCategoryData['category_id']]['title']
				)
			);
		return $data;
	}
	
	/**
	 * 获取层级结构的分类
	 */
	public static function getLevelCategory(){
		$result = array();
		$parentCategory = self::getParentCategory();
		foreach ($parentCategory as $item){
			$subCategory = self::getSubCategory($item['id']);
			$item['items'] = $subCategory;
			$result[$item['id']] = $item;
		}
		return $result;
	}
	
	/**
	 * 获取一级分类的id-标题
	 * @return array
	 */
	public static function getParentCategory(){
		$result = array();
		$categoryData = Resource_Service_Attribute::getsBy(array('at_type'=>self::PARENTCATEGORY, 'status'=>1, 'editable'=>0), array('sort'=>'DESC'));
		foreach ($categoryData as $item ){
			$result[$item['id']]=array(
					'id' => $item['id'],
					'title' => $item['title'],
			);
		}
		return $result;
	}
	
	/**
	 * 根据一级分类获取二级分类的id-标题
	 * @return array
	 */
	public static function getSubCategory($parentId){
		$result = array();
		$categoryData = Resource_Service_Attribute::getsBy(array('at_type'=>self::SUBCATEGORY, 'parent_id'=>$parentId, 'status'=>1), array('sort'=>'DESC'));
		foreach ($categoryData as $item ){
			$result[$item['id']]=array(
					'id' => $item['id'],
					'title' => $item['title'],
			);
		}
		return $result;
	}
	
	/**
	 * 按条件获取单条游戏分类数据
	 * @param array $params
	 * 
	 */
	public static function getBy($params){
		if(!is_array($params)) return false;
		return self::getDao()->getBy($params);
	}
	
	/**
	 * 按条件获取游戏分类基本数据
	 * @param array $params
	 * @param array $orderBy
	 * @return 
	 */
	public static function getsBy($params, $orderBy = array()){
		if(!is_array($params)) return false;
		return self::getDao()->getsBy($params, $orderBy);
	}

	/**
	 * 通过游戏一级分类获取该分类去重后的游戏数据,禁止参数为空请求
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @param array $orderBy
	 */
	public static function getListByMainCategory($page = 1, $limit = 10, $params, $orderBy = array()){
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$result = self::getDao()->getAllByMainCategoryId($start, $limit, $params, $orderBy);
		$total = self::getDao()->getCountByParentId($params);
		return array($total, $result);
	}
	
	
	/**
	 * 分页方式获取分类游戏数据
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 * @param array $orderBy
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array() ) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$result = self::getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::getDao()->count($params);
		return array($total, $result);
	}

	/**
	 * 添加分类游戏数据
	 * @param array $data
	 * @return boolean
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		return self::getDao()->insert($data);
	}
	
	/**
	 * 分类游戏数据更新
	 * @param array $data
	 * @param array $params
	 * @return boolean
	 */
	public static function updateBy($data, $params){
		if(!is_array($data)|| !is_array($params)) return false;
		return self::getDao()->updateBy($data, $params);
	}
	
	/**
	 * 删除分类数据
	 * @param array $params
	 * @return boolean|Ambigous <boolean, unknown, number>
	 */
	public static function deleteBy($params){
		if (!is_array($params)) return false;
		return self::getDao()->deleteBy($params);
	}
	
	/**
	 * 数据过滤
	 * @param array $data
	 */
	private static function cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['category_id'])) $tmp['category_id'] = $data['category_id'];
		if(isset($data['parent_id'])) $tmp['parent_id'] = $data['parent_id'];
		if(isset($data['level'])) $tmp['level'] = $data['level'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		if(isset($data['online_time'])) $tmp['online_time'] = $data['online_time'];
		if(isset($data['downloads'])) $tmp['downloads'] = $data['downloads'];
		return $tmp;
	}
	
	
	/**
	 * 
	 * @return Resource_Dao_IdxGameResourceCategory
	 */
	private static function getDao() {
		return Common::getDao("Resource_Dao_IdxGameResourceCategory");
	}
}
