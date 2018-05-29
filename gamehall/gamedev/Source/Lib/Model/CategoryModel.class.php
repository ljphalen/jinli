<?php
/**
 * 应用分类模型
 * @author shuhai
 */
class CategoryModel extends Model
{
	protected $trueTableName = 'category';
	const CATEGORY_GAME      = 100;
	const CATEGORY_APPS      = 200;

	/**
	 * 获取游戏栏目
	 * @return Array
	 */
	function category_game()
	{
		return $this->category_two(self::CATEGORY_GAME);
	}
	
	/**
	 * 获取应用栏目
	 * @return Array
	 */
	function category_apps()
	{
		return $this->category_two(self::CATEGORY_APPS);
	}
	
	/**
	 * 获取二级栏目
	 * @return Array
	 */
	function category_two($parent_id)
	{
		return (array)$this->where(array("parent_id"=>$parent_id))->getField("id, name", true);
	}
	
	/**
	 * 产生栏目目录树
	 * @return Array
	 */
	function category_tree()
	{
		$category = (array)$this->category_game() + (array)$this->category_apps();
		foreach ($category as $cid => $cate)
			$category[$cid] = array("name"=>$cate, "item"=>$this->category_two($cid));
		return $category;
	}
}