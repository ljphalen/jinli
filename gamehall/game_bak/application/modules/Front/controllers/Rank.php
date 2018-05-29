<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author fanch
 *
 */
class RankController extends Front_BaseController{
	
	/**
	 * 排行首页
	 */
	public function indexAction(){
		//获取排序好的排行代码
		$ranks = Game_Service_Config::getConfigRank('web',true);
		$data = array();
		foreach ($ranks as $value){
			$data[] = Client_Service_Rank::$value['key']();
		}
		$this->assign($data, $data);
	}
	
	/**
	 * 排行列表页
	 */
	public function listAction(){
		
	}
}