<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GoodsController extends Front_BaseController {
	
	/**
	 * goods detail page
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$info = Gc_Service_TaokeGoods::getGoods($id);
		$title = "产品详情";
		$info['title'] = Util_String::substr($info['title'], 0, 18);
		//taoke goods info
		$topApi  = new Api_Top_Service();
		$taoke_info  = $topApi->getTaobaoke(array('num_iids'=>$info['num_iid'], 'is_mobile'=>'true', 'outer_code'=>$this->getOuterCode()));
		
		//taobao goods info
		$taobao_info = $topApi->getItemInfo($info['num_iid']);
		$taoke_info['click_url'] = $this->getTaobaokeUrl($taoke_info['click_url']);
		
		$info['item_imgs'] = $taobao_info['item_imgs']['item_img'];
		
		$rate = Gc_Service_Config::getValue('gc_silver_coin_rate');
		$taoke_info['commission'] = Common::money($taoke_info['commission'] * ($rate / 100));
		$this->assign('taoke_info', $taoke_info);
		$this->assign('info', $info);
		$this->assign('title', $title);
	}
	
	/**
	 * 
	 */
	public function wantAction() {
		$id = intval($this->getInput('id'));
		$user = Gc_Service_User::isLogin();
		if (!$user) $this->output(-1, '你还没有登录哦，请先登录!');
		$goods = Gc_Service_TaokeGoods::getGoods($id);
		if (!$goods) $this->output(-1, '商品信息不存在.');
		$log = Gc_Service_WantLog::getByUidAndGoodsId($user['id'], $id);
		if ($log) $this->output(-1, '商品已在心愿清单!');
		$ret = Gc_Service_TaokeGoods::want(array(
					'uid'=> $user['id'],
					'username'=> $user['username'],
					'goods_id'=> $goods['id'],
					'goods_name'=> $goods['title'],
				));
		if (!$ret) $this->output(-1, '加入失败,再试试吧!');
		$this->output(0, '商品加入心愿清单了!'); 
	}
}
