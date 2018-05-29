<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ProductController extends Front_BaseController {
	
	public $actions = array(
			'listUrl' => '/product/index',
	);
	public $perpage = 10;
	
	
	public function indexAction() {
		$title = "积分换购";
		$page = intval($this->getInput('page'));
		$st = intval($this->getInput('st'));
		if ($page < 1) $page = 1;
		$perpage = $this->perpage;
		if ($st) {
			list(, $goods) = Gc_Service_LocalGoods::getAfterLocalGoods($page, $this->perpage,array());
		} else {
			list(, $goods) = Gc_Service_LocalGoods::getNomalLocalGoods($page, $this->perpage,array());
			list(, $buygoods) = Gc_Service_LocalGoods::getCanUseLocalGoods(0, 1,array());
			$this->assign('buygoods', $buygoods[0]);
		}
		$this->assign('st', $st);
		$this->assign('goods', $goods);
		$this->assign('title', $title);
	}
	
	public function detailAction() {
		$id = intval($this->getInput('id'));
		$title = "产品详情";
		$info = Gc_Service_LocalGoods::getLocalGoods($id);
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
		if ($log) $this->output(0, '商品已在心愿清单!');
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