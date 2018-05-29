<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class WantlogController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Wantlog/index',
		'editUrl' => '/Admin/Wantlog/edit',
		'editPostUrl' => '/Admin/Wantlog/edit_post',
		'orderFreeUrl' => '/Admin/Wantlog/orderfree',
		'orderFreePostUrl' => '/Admin/Wantlog/orderfree_post',
		'orderFreeLogUrl' => '/Admin/Orderfreelog/index',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('username', 'goods_id', 'start_time', 'end_time'));
		$search = array();
		if ($params['username']) $search['username'] = $params['username'];
		if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
		if ($params['start_time']) $search['start'] = strtotime($params['start_time']);
		if ($params['end_time']) $search['end'] = strtotime($params['end_time']);
		
		//get logs
		list($total, $logs) = Gc_Service_WantLog::getWantLogsByTime($page, $this->perpage, $search);
		
		$this->assign('search', $params);		
		$this->assign('logs', $logs);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *抽奖
	 */
	public function orderfreeAction() {
		$page = intval($this->getInput('page'));
		$params = $this->getInput(array('goods_id'));
		$total = 0;
		$logs = $users = array();
		
		if ($params['goods_id']) {
			$search = array();
			$search['goods_id'] = $params['goods_id'];		
			
			//get logs
			list($total, $logs) = Gc_Service_WantLog::getWantLogsByTime($page, $this->perpage, $search);
			$uids = array();
			foreach ($logs as $k=>$value) {
				$uids[] = $value['uid'];
			}
			//get users
			$users = Gc_Service_User::getUserByUids($uids);
			$users = Common::resetKey($users, 'id');
			
		}
	
		$this->assign('search', $params);
		$this->assign('logs', $logs);
		$this->assign('users', $users);
		$this->assign('goods_id', $params['goods_id']);
		$url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gc_Service_WantLog::getWantLog(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('order_id', 'id'));
		if (!$info['order_id']) $this->output(-1, '订单号不能为空.'); 
		$ret = Gc_Service_WantLog::updateWantLog(array('order_id'=>$info['order_id'], 'status'=>1), intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.'); 
	}
	
	/**
	 *
	 */
	public function orderfree_postAction() {
		$info = $this->getPost(array('number', 'goods_id'));
		if (!$info['number']) $this->output(-1, '请输中奖人数.');
		list($total, $logs) = Gc_Service_WantLog::getByGoodsId($info['goods_id']);
		if ($info['number'] > $total) $this->output(-1, '中奖人数不能大于喜欢总数.');
		//上次的编号
		$OrderFreeNumber = Gc_Service_OrderFreeNumber::getLast();
		$number = $OrderFreeNumber['number'] + 1;
		//插入期数
		$ret = Gc_Service_OrderFreeNumber::addOrderFreeNumber(array('number'=>$number));
		if(!$ret) $this->output(-1, '抽奖失败.'); 
		
		//商品信息
		$goods = Gc_Service_TaokeGoods::getGoods($info['goods_id']);		
		$rand = array_rand($logs, intval($info['number']));
		if (!is_array($rand)) $rand = array($rand);
		$data = array();
		$uids = array();
		foreach ($rand as $key=>$value) {
			$data[$key]['id'] = '';
			$data[$key]['number'] = $number;
			$data[$key]['user_id'] = $logs[$value]['uid'];
			$data[$key]['username'] = $logs[$value]['username'];
			$data[$key]['goods_id'] = $logs[$value]['goods_id'];
			$data[$key]['goods_title'] = $logs[$value]['goods_name'];
			$data[$key]['goods_price'] = $goods['price'];
			$data[$key]['create_time'] = Common::getTime();
			$data[$key]['create_status'] = 0;
			$data[$key]['remark'] = '';
			$uids[] = $logs[$value]['uid'];
		}
		$result = Gc_Service_OrderFreeLog::batchAddOrderFreeLog($data);
		//更新会员免单数
		$ret = Gc_Service_User::updateFreeNumberByIds($uids);
		if (!$result || !$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
