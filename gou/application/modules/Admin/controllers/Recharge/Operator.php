<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 话费充值运营商管理功能
 * @author huangsg
 *
 */

class Recharge_OperatorController extends Admin_BaseController {
	public $actions = array(
			'indexUrl' => '/Admin/Recharge_Operator/index',
			'editPostUrl' => '/Admin/Recharge_Operator/edit_post',
	);
	
	/**
	 * 运营商价格列表
	 */
	public function indexAction(){
		$opid = $this->getInput('opid');
		if (empty($opid)) $opid = 1;
		$list = Recharge_Service_Operator::getList($opid);
		$price_list = Recharge_Service_Price::getList();
		$price_list = Common::resetKey($price_list, 'id');
		
		$this->assign('price', $price_list);
		$this->assign('list', $list);
		$this->assign('opid', $opid);
	}
	
	/**
	 * 修改数据，AJAX方式
	 */
	public function edit_postAction(){
		$params = $this->getInput(array('id', 'value'));
		if (empty($params['id'])){
			echo 0;
			exit();
		}
		
		$ret = Recharge_Service_Operator::updatePrice(
				array('offset'=>$params['value']), 
				$params['id']
		);
		
		if (!$ret){
			echo 0;
		} else {
			echo 1;
		}
		exit();
	}
	
}