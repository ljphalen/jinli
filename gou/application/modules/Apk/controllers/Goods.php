<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GoodsController extends Apk_BaseController {
	
	public $actions =array(
				'index' => '/goods/index',
				'goodsUrl' => '/goods/goods/',
			);
	
	public function indexAction() {
		$this->display('notice');exit;
		list(, $goods) = Gou_Service_LocalGoods::getCanUseLocalGoods(0, 100, array('status'=>1), array('end_time'=>'DESC'));
		$tmp = array();
		foreach($goods as $key=>$value) {
			if (!is_array($tmp[$value['end_time']])) $tmp[$value['end_time']] = array();
			array_push($tmp[$value['end_time']], $value);
		}
		$this->assign('goods', $tmp);
		$this->setLoginFromUrl();
		$this->assign('hasfooter', false);
		$this->assign('title', '积分换购');
	}
	
	public function afterAction() {
		$this->display('notice');exit;
		list(, $goods) = Gou_Service_LocalGoods::getAfterLocalGoods(0, 10, array('status'=>1), array('start_time'=>'ASC'));
		$tmp = array();
		foreach($goods as $key=>$value) {
			if (!is_array($tmp[$value['start_time']])) $tmp[$value['start_time']] = array();
			array_push($tmp[$value['start_time']], $value);
		}
		$this->assign('goods', $tmp);
		$this->setLoginFromUrl();
		$this->assign('hasfooter', false);
		$this->assign('title', '积分换购');
	}
	
	public function detailAction() {
		$this->display('notice');exit;
		$id = intval($this->getInput('id'));
		$refer = $this->getInput('refer');
		$info = Gou_Service_LocalGoods::getLocalGoods($id);
		
		$imgs = Gou_Service_LocalGoodsImg::getImagesByGoodsId($id);
		//兼容老版本
		if(!$imgs) $imgs = array(array('img'=>$info['img']));
		
		$this->assign('disable', $info['start_time'] > Common::getTime());
		$this->assign('info', $info);
		$this->assign('imgs', $imgs);
		$this->assign('refer', $refer);
		$this->assign('title', '商品详情');
	}
	
	public function ruleAction() {
		
	}
}
