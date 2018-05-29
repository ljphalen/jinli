<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ElifeController extends Front_BaseController {
	
	public $actions =array(
				'index' => '/elife/index',
				'tjUrl'=>'/index/tj'
			);
	
	public $perpage = 4;

	public function indexAction() {
		$cid = intval($this->getInput('cid'));
		$webroot = Common::getWebRoot();
		
		//mallcategorys
		list(, $categorys) = Mall_Service_Category::getCanUseMallCategorys(0, 20, array('area_id'=>2));
		$this->assign('categorys', $categorys);
		$this->assign('title', 'ELIFE专区');
		$this->assign('cid', $cid);
	}
	
	/**
	 * goods detail page
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$cid = intval($this->getInput('cid'));
		$info = Mall_Service_Goods::getMallGoods($id);
	
		//taoke goods info
		$topApi  = new Api_Top_Service();
		$taoke_info  = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$info['num_iid'], 'is_mobile'=>'true', 'outer_code'=>$this->getOuterCode()));
		if(!$taoke_info) Mall_Service_Goods::updateMallGoods(array('status'=>0), $info['id']);
		//taobao goods info
		$taobao_info = $topApi->getItemInfo($info['num_iid']);
		
		if($taoke_info && ($taoke_info['promotion_price'] != $info['price'])) {
			Mall_Service_Goods::updateMallGoods(array('price'=>$taoke_info['promotion_price']), $info['id']);
		}
		
		//$taoke_info['click_url'] = $this->getTaobaokeUrl($taoke_info['click_url']);
		$info['item_imgs'] = $taobao_info['item_imgs']['item_img'];
		
		//$rate = Gou_Service_Config::getValue('gou_silver_coin_rate') / 100;
		$this->assign('taoke_info', $taoke_info);
		//$this->assign('taoke_rate', $rate);
		$this->assign('info', $info);
		$this->assign('cid', $cid);
		$this->assign('title', '商品详情');
	}
}
