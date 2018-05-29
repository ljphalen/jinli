<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class ReadCoinController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/ReadCoin/index',
		'addUrl' => '/Admin/ReadCoin/add',
		'addPostUrl' => '/Admin/ReadCoin/add_post',
		'editUrl' => '/Admin/ReadCoin/edit',
		'editPostUrl' => '/Admin/ReadCoin/edit_post',
		'deleteUrl' => '/Admin/ReadCoin/delete',
	);
	
	public $perpage = 20;
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$params  = $this->getInput(array('card_number', 'goods_id', 'order_id'));
		if ($page < 1) $page = 1;
		//get all subjects
		list(,$goods) = Gou_Service_LocalGoods::getCanUseLocalGoods(1, 10, array('goods_type'=>3), array('id'=>'DESC'));
		$goods = Common::resetKey($goods, 'id');
		$this->assign('goods', $goods);
		
		//get search params
		$search = array();
		if ($params['card_number']) $search['card_number'] = $params['card_number'];
		if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
		if ($params['order_id']) $search['order_id'] = $params['order_id'];
		
		//get goods list
		list($total, $readcoins) = Gou_Service_ReadCoin::getList($page, $this->perpage, $search);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$this->assign('readcoins', $readcoins);
		//get pager
		$this->cookieParams();
		$url = $this->actions['listUrl'] .'/?'. http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ReadCoin::getReadCoin(intval($id));
		$this->assign('info', $info);
	}	
	
	/**
	 * add redadcoin
	 */
	public function addAction() {
		list(,$goods) = Gou_Service_LocalGoods::getCanUseLocalGoods(1, 10, array('goods_type'=>3), array('id'=>'DESC'));
		$this->assign('goods', $goods);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array(array('card_number', '#s_zb'), 'goods_id'));
		$info = $this->_cookData($info);
		$card_number =  explode('<br />', $info['card_number']);
		$ret = self::FetchRepeatInArray($card_number);
		if($ret) {
			$this->output(-1, '卡号'.implode(',', $ret).'有重复');
		}
		$data = array();
		foreach ($card_number as $key=>$value) {
			$read_coin = Gou_Service_ReadCoin::getBy(array('card_number'=>$value));
			if($read_coin) $this->output(-1, '卡号'.$read_coin['card_number'].'已存在');
			if(!$read_coin && $value) {
				$data[$key]['id'] = '';
				$data[$key]['goods_id'] = $info['goods_id'];
				$data[$key]['card_number'] = $value;
				$data[$key]['order_id'] = '';
			}
		}
		$result = Gou_Service_ReadCoin::batchaddReadCoin($data);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id', 'card_number'));
		$info = $this->_cookData($info);
		$readcoin = Gou_Service_ReadCoin::getBy(array('card_number'=>$info['card_number']));
		if($readcoin && $readcoin['id'] != $info['id']) $this->output(-1, '卡号'.$info['card_number'].'已存在');
		$ret = Gou_Service_ReadCoin::updateReadCoin($info, intval($info['id']));
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.'); 		
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['card_number']) $this->output(-1, '卡号不能为空.'); 
		return $info;
	}
	
	/**
	 * FetchRepeatInArray
	 * @param array $array
	 * @return array:
	 */
	private function  FetchRepeatInArray($array) {
		// 获取去掉重复数据的数组
		$unique_arr = array_unique ( $array );
		$repeat_arr = array_diff_assoc ( $array, $unique_arr );
		return $repeat_arr;
	}
	
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Gou_Service_ReadCoin::getReadCoin($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Gou_Service_ReadCoin::deleteReadCoin($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
