<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GoodsController extends Amigo_BaseController {
	
	/**
	 * goods detail
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		//$refer = $this->getInput('refer');
		$info = Gou_Service_LocalGoods::getLocalGoods($id);
		$time = Common::getTime();
		$webroot = Common::getWebRoot();
		//$refer = Util_Http::getServer('HTTP_REFERER');
		//if(!$info || !$id || $info['start_time'] > $time || $info['end_time'] < $time ) $this->redirect($webroot);
		
		if($info['keywords']) $keywords = explode(',', html_entity_decode($info['keywords']));
		
		$this->assign('info', $info);
		$this->assign('keywords', $keywords);
		$this->assign('has_stock_desc', Gou_Service_Config::getValue('has_stock_desc'));
		$this->assign('null_stock_desc', Gou_Service_Config::getValue('null_stock_desc'));
		//$this->assign('refer', $refer);
		$this->assign('title', '商品详情');
	}
	
	
	/**
	 * buy
	 */
	public function buyAction() {
		$webroot = Common::getWebRoot();
		$cookie_data = json_decode(Util_Cookie::get('AMIGO_CART', true), true);
		
		if(!$cookie_data['goods_id'] || !$cookie_data['number']) $this->redirect($webroot);
		$time = Common::getTime();		
		
		$info = Gou_Service_LocalGoods::getLocalGoods($cookie_data['goods_id']);
		if($info['start_time'] > $time || $info['end_time'] < $time ) $this->redirect($webroot);
		
		if($cookie_data['number'] > $info['limit_num'])  $this->redirect($webroot.'/amigo/goods/buy_setp?id='.$cookie_data['goods_id']);
	
		$this->assign('info', $info);
		$this->assign('number', $cookie_data['number']);
		$this->assign('title', '创建订单');
	}
	
	/**
	 * buy_step
	 */
	public function buy_stepAction() {
		$goods_id = intval($this->getPost('goods_id'));
		$number = intval($this->getPost('number'));
		$time = Common::getTime();
		$webroot = Common::getWebRoot();
	
		$info = Gou_Service_LocalGoods::getLocalGoods($goods_id);
		if(!$info || !$goods_id || $info['start_time'] > $time || $info['end_time'] < $time ) $this->output(-1, '参数异常.');
		
		if(!$number)  $this->output(-1, '请填写购买数量.');
		if($number > $info['limit_num'])  $this->output(-1, '您购买的数量超过最大限购数.');
		
		$cookie_data = array('number'=>$number, 'goods_id'=>$goods_id);
		Util_Cookie::set('AMIGO_CART', json_encode($cookie_data), true, Common::getTime() + 86400, '/', $this->getDomain());
		
		$this->output(0,'', array('type'=>'redirect', 'url'=>$webroot.'/amigo/goods/buy'));
	}
	
	/**
	 * goods images
	 */
	public function imagesAction() {
		$id = intval($this->getInput('id'));
		$page = intval($this->getInput('page'));
		$perpage = 4;
		if(!$page) $page = 1;
		list($total, $imgs) = Gou_Service_LocalGoodsImg::getList($page, $perpage, array('goods_id'=>$id), array('id'=>'ASC'));
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($imgs as $key=>$value) {
			$data[] = Common::getAttachPath() .$value['img'];
		}		
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
}
