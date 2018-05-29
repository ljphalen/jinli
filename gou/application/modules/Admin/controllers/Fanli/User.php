<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Fanli_UserController extends Admin_BaseController {
	
	public $actions = array(
		'indexUrl' => '/Admin/Fanli_User/index',
		'editUrl' => '/Admin/Fanli_User/edit',
		'editPostUrl' => '/Admin/Fanli_User/edit_post',
	);
	
	public $perpage = 20;
	public $status = array(
				1 => '未审核',
				2 => '已审核',
				3 => '已锁定' 
			);
	public $sex = array(
				1 => '女',
				2 => '男' 
			);
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));		
		$param = $this->getInput(array('id', 'phone', 'truename', 'username', 'start_time', 'end_time'));
		
		if ($param['id'] != '') $search['id'] = $param['id'];
		if ($param['username'] != '') $search['username'] = $param['username'];
		if ($param['truename'] != '') $search['truename'] = $param['truename'];
		if ($param['phone']) $search['phone'] = $param['phone'];
		if ($param['start_time']) $search['start_time'] = $param['start_time'];
		if ($param['end_time']) $search['end_time'] = $param['end_time'];
		
		$perpage = $this->perpage;
		list($total, $users) = Fanli_Service_User::search($page, $perpage, $search);
		
		$this->assign('users', $users);
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
		$userInfo = Gou_Service_User::getUser(intval($id));
		
		$coinInfo = Api_Gionee_Pay::getCoin(array('out_uid'=>$userInfo['out_uid']));
		$coinInfo['gold_coin'] = Common::money($coinInfo['gold_coin']);
		$coinInfo['silver_coin'] = Common::money($coinInfo['silver_coin']);
		$coinInfo['freeze_gold_coin'] = Common::money($coinInfo['freeze_gold_coin']);
		$coinInfo['freeze_silver_coin'] = Common::money($coinInfo['freeze_silver_coin']);
		
		$userInfo = array_merge($userInfo, $coinInfo);
		//查询用户的收货地址
		$address = Gou_Service_UserAddress::getDefaultAddress($userInfo['id']);
		
		$this->assign('userInfo', $userInfo);
		$this->assign('sex', $this->sex);
		$this->assign('status', $this->status);
		$this->assign('address', $address);
		//更多个人质料
		$extends = Gou_Service_UserExtend::getUserExtendByUserId(intval($id));
		$tmp = array(
				'job'=> array(
						0 => '请选择',
						1 => '销售/采购',
						2 => 'IT/通讯',
						3 => '房地产/建筑',
						4 => '保险/金融',
						5 => '销售/采购',
						6 => '管理/人事行政',
						7 => '设计/媒体',
						8 => '生产/物流',
						9 => '机关单位/公务员',
						10 => '其他'
				),
				'love'=> array(
						0 => '请选择',
						1 => '游戏',
						2 => '购物',
						3 => '旅游',
						4 => '健身',
						5 => '美食',
						6 => '电子产品',
						7 => '电影',
						8 => '美妆',
						9 => '养生',
						10 => '宅',
						11 => '看书',
						12 => '写作',
						13 => '其他'
				),
				'age'=> array(
						0 => '请选择',
						1 => '18岁以下',
						2 => '19—25岁',
						3 => '26—35岁',
						4 => '36—45岁',
						5 => '46—55岁',
						6 => '56岁以上'
				)
		);
		$extends['job'] = array_search($extends['job'],array_flip($tmp['job']));
		$extends['love'] = array_search($extends['love'],array_flip($tmp['love']));
		$extends['age'] = array_search($extends['age'],array_flip($tmp['age']));
		$this->assign('extends',$extends);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'status'));
		$ret = Gou_Service_User::updateUser($info, intval($info['id']));
		if (!$ret) $this->output(-1, '更新用户失败');
		$this->output(0, '更新用户成功.'); 		
	}
	
	/**
	 * update user order_num
	 */
	public function updateOrderNumAction () {
		$page = 1;
		do {
			list($total, $users) = Gou_Service_User::getList($page, 100);
			foreach ($users as $key=>$value) {
				list($order_num, ) = Gou_Service_Order::getList(1, 1, array('uid'=>$value['id']));
				//if($total) {
					Gou_Service_User::updateUser(array('order_num'=>intval($order_num)), $value['id']);
				//}
			}
			$page++;
			sleep(2);
		} while ($total>($page * 100));
		exit('done');
	}
}