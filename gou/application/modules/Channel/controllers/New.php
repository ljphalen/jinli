<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class NewController extends Channel_BaseController {
	
	public $actions =array(
				'index' => '/new/index',
				'tjUrl'=>'/index/tj',
				'shopUrl'=>'/shop/detail'
			);
	
	public $perpage = 4;

	public function indexAction() {
		$this->assign('title', '新品女装');
	}
	
	/**
	 * goods detail page
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$info = Mall_Service_Goods::getMallGoods($id);
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;
	
		//taoke goods info
		$topApi  = new Api_Top_Service();
		$taoke_info = $topApi->getTbkItemInfo(array('num_iids'=>$info['num_iid']));
		
		//convert
		if($taoke_info) {
			$convert = $topApi->tbkMobileItemsConvert(array('num_iids'=>$info['num_iid']));
			$taoke_info['click_url'] = $convert['click_url'];
		} else {
			Mall_Service_Goods::updateMallGoods(array('status'=>0), $info['id']);
		}
		
		//$this->assign('taoke_rate', $rate);
		$this->assign('taoke_info', $taoke_info);
		//$this->assign('shop_info', $shop_info);
		$this->assign('info', $info);
		$this->assign('title', '商品详情');
	}
}
