<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class FjuserController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Fjuser/index',
		'editUrl' => '/Admin/Fjuser/edit',
		'editPostUrl' => '/Admin/Fjuser/edit_post',
		'cartUrl' => '/Admin/Fjuser/cart',
		'cartPostUrl' => '/Admin/Fjuser/cart_post',
	);
	
	public $perpage = 20;
	public $status = array(
				1 => '未审核',
				2 => '已审核',
				3 => '已锁定' 
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('id', 'phone', 'realname','status', 'open_id', 'start_time', 'end_time'));
		
		if ($param['id']) $search['id'] = $param['id'];
		if ($param['phone']) $search['phone'] = $param['phone'];
		if ($param['realname']) $search['realname'] = $param['realname'];
		if ($param['status']) $search['status'] = intval($param['status']);
		if ($param['open_id']) $search['open_id'] = $param['open_id'];
		
		$perpage = $this->perpage;
		list($total, $users) = Fj_Service_User::getList($page, $perpage, $search);
		
		$this->assign('users', $users);
		$this->assign('status', $this->status);
		$this->assign('param', $search);
		
		$this->cookieParams();
		$url = $this->actions['indexUrl'] .'/?'. http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('total', $total);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$userInfo = Fj_Service_User::getUser(intval($id));
		
		$this->assign('userInfo', $userInfo);
		$this->assign('status', $this->status);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status'));
		$ret = Fj_Service_User::updateUser($info, intval($info['id']));
		if (!$ret) $this->output(-1, '更新用户失败');
		$this->output(0, '更新用户成功.'); 		
	}

	/**
	 * 购物车
	 */
	public function cartAction() {
		$id = $this->getInput('id');
		$userInfo = Fj_Service_User::getUser(intval($id));
		if($userInfo === false)
			$this->output(1, '该用户不存在.');

		list($total, $cart) = Fj_Service_Cart::getsBy(array('uid'=>$userInfo['id']), array('id'=>'DESC'));

		$goods = array();
		if($total > 0){
			$goodsID = array_keys(Common::resetKey($cart, 'goods_id'));
			list(, $goods) = Fj_Service_Goods::getsBy(array('id'=>array('IN', $goodsID)), array('id'=>'DESC'));
			$goods = Common::resetKey($goods, 'id');
		}

		foreach($cart as &$item){
			$item['goods'] = isset($goods[$item['goods_id']])?$goods[$item['goods_id']]:array();
		}

		$this->assign('id', $id);
		$this->assign('open_id', $userInfo['open_id']);
		$this->assign('phone', $userInfo['phone']);
		$this->assign('username', $userInfo['username']);
		$this->assign('cart', $cart);
	}

	/**
	 * 编辑购物车
	 */
	public function cart_postAction() {
		$user_id = $this->getPost('user_id');
		$user_open_id = $this->getPost('user_open_id');
		$cartPost = $this->getPost(array('goods_id', 'goods_price', 'num', 'goods_descrip'));

		foreach($cartPost['goods_id'] as $key=>$val){
			if($val == 0) $this->output(-1, '商品ID为空.');
			if($cartPost['num'][$key] < 0) $this->output(-1, '商品数量不能小于0.');
		}

		foreach($cartPost['goods_id'] as $key=>$val){
			$cart = Fj_Service_Cart::getBy(array('goods_id'=>$val, 'uid'=>$user_id));
			if($cart) {
				$result = $this->updateCart($cart, 1, abs(intval($cartPost['num'][$key])), html_entity_decode($cartPost['goods_descrip'][$key]));
				if(!$result) $this->output(-1, '操作失败1.');
			} else {
				if($cartPost['num'][$key] == 0) $this->output(-1, '添加新商品数量不能等于0.');
				$data = array(
					'uid'			=> $user_id,
					'open_id'		=> $user_open_id,
					'goods_id'		=> $val,
					'goods_num'		=> abs(intval($cartPost['num'][$key])),
					'price'			=> $cartPost['goods_price'][$key],
					'descrip'		=> html_entity_decode($cartPost['goods_descrip'][$key]),
					'create_time'	=> Common::getTime()
				);
				$ret = Fj_Service_Cart::add($data);
				if(!$ret) $this->output(-1, '操作失败2.');
			}
		}

		$this->output(0, '购物车添加成功.');
	}

	/**
	 * 更新购物车商品数量和备注
	 */
	private function updateCart($cart, $type, $goods_num = 0, $descrip = '') {
		if($type == 1) {
			//增加数量
			$update_data = array('goods_num'=>$cart['goods_num'] + $goods_num, 'descrip'=>$descrip);
		} else {
			//减数量
			if($cart['goods_num'] - $goods_num < 1) return false;
			$update_data = array('goods_num'=>$cart['goods_num'] - $goods_num, 'descrip'=>$descrip);
		}
		$ret = Fj_Service_Cart::update($update_data, $cart['id']);
		if(!$ret) return false;
		return true;
	}
}