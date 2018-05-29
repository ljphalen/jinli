<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class OrderfreeController extends Front_BaseController {
	
	public $actions =array(
				'freelogUrl' => '/orderfree/freelog',
				'ruleUrl' => '/orderfree/rule'
			);
	
	public $perpage = 1;

	public function indexAction() {
		list($total,) = Gou_Service_OrderFreeNumber::getCanUseOrderFreeNumber(1, 1);
		$this->assign('total', $total);
	}
	
	/**
	 * 
	 */
	public function freelogAction() {
		$page = intval($this->getInput('page'));
		
		if ($page < 1) $page = 1;
		
		list($total, $number) = Gou_Service_OrderFreeNumber::getCanUseOrderFreeNumber($page, $this->perpage);
		
		$tmp = array();
		$webroot = Common::getWebRoot();
		$value = $number[0];
		$free_log = Gou_Service_OrderFreeLog::getOrderFreeLogByNumber($value['number']);
		$goods = Gou_Service_Goods::getGoods(intval($free_log['goods_id']));
		//want_count
		$count = Gou_Service_WantLog::getCount(array('goods_id'=>intval($free_log['goods_id'])));
		//成功免单的记录
		list($free_total, $free_list) = Gou_Service_OrderFreeLog::getList(0, 20, array('number'=>$value['number']));
		
		$uids = array();
		foreach ($free_list as $k=>$v) {
			$uids[] = $v['user_id'];
		}
		//该用户喜欢商品数 成功免单数
		$users = Gou_Service_User::getListByUids($uids);
		$users = Common::resetKey($users, 'id');
		
		$frees = array();
		foreach ($free_list as $keys=>$val) {
			$frees[$keys]['username'] = substr_replace($val['username'], '******', 3, 6);
			$frees[$keys]['want_num'] = $users[$val['user_id']]['want_num'];
			$frees[$keys]['free_num'] = $users[$val['user_id']]['free_num'];
		}
		$tmp['number'] = $value['number'];
		$tmp['img'] = (strpos($goods['img'], 'http://') === false) ? Common::getAttachPath() . $goods['img'] : $goods['img'] . '_200x200.' . end(explode(".", $goods['img']));
		$tmp['create_time_1'] = date('Y-m-d', $free_log['create_time']);
		$tmp['create_time_2'] = date('H:i', $free_log['create_time']);
		$tmp['title'] = $free_log['goods_title'];
		$tmp['price'] = $free_log['goods_price'];
		$tmp['want_count'] = $count + $goods['default_want'];
		$tmp['link'] = urldecode('/subject/detail/?id='.$free_log['goods_id']);
		$tmp['order_free_num'] = count($frees);
		$tmp['order_free'] = $frees;
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	/**
	 * 规则
	 */
	public function ruleAction(){
		
	}
}
