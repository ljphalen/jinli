<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GoodsController extends Front_BaseController {
	
	public $actions =array(
				'index' => '/goods/index',
				'goodsUrl' => '/goods/goods/',
			);
	
	public function indexAction() {
		self::redirectAction();
		list(, $goods) = Gou_Service_LocalGoods::getCanUseLocalGoods(0, 100, array('status'=>1, 'show_type'=>1), array('end_time'=>'DESC'));
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
		self::redirectAction();
		list(, $goods) = Gou_Service_LocalGoods::getAfterLocalGoods(0, 10, array('status'=>1, 'show_type'=>1), array('start_time'=>'ASC'));
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
		self::redirectAction();
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
	
	
	/**
	 * redirect by 鲁梅
	 */
	public function redirectAction() {
	    if (Gou_Service_User::getToday()) {
	        Common::getCache()->increment('gou_uv');
	    }
	    $this->redirect('http://ai.m.taobao.com/bu.html?id=1&pid=mm_31749056_5906928_23050021');
	}
}
