<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class BrandController extends App_BaseController {
	
	public function indexAction(){
		$cate_id = intval($this->getInput('cate_id'));
		$this->assign('cate_id', $cate_id);
		$this->assign('title', '品牌汇');
	}
	
	public function detailAction(){
		$brand_id = intval($this->getInput('brand_id'));
		$cate_id = intval($this->getInput('cate_id'));
		$this->assign('title', '品牌汇');
		$this->assign('brand_id', $brand_id);
		$this->assign('cate_id', $cate_id);
	}
	

	/**
	 *
	 * @return redirect
	 */
	public function redirectAction() {
		$id = intval($this->getInput('id'));
		$url = $this->getInput('_url');
	
		//goods
		$goods = Gou_Service_BrandGoods::getBrandgoods($id);
		if (!$id || !$goods || !$url) return false;
		Gou_Service_BrandGoods::updateHits($id);
		$this->redirect($url);
	}
}