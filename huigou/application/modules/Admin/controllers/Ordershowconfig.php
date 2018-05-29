<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author tiansh
 *
 */
class OrdershowconfigController extends Admin_BaseController {
	
	public $actions = array(
		'editUrl'=>'/admin/Ordershowconfig/index',
		'editPostUrl'=>'/admin/Ordershowconfig/edit_post',
	);
	
	public $key = 'gou_ordershow_checked_number';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$info = Gc_Service_Config::getValue($this->key);
		$this->assign('checked_number', info ? $info['gou_value'] : '');
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {		
		$checked_number = $this->getPost('checked_number');
		$checked_number = $this->_cookData($checked_number);
		$ret = Gc_Service_Config::setValue($this->key, intval($checked_number));
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0, '操作成功.'); 
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($checked_number) {
		if(is_null($checked_number)) $this->output(-1, '已核实数量不能为空.');
		if(intval($checked_number) != $checked_number) $this->output(-1, '已核实数量要为整数.');
		if($checked_number > 5 || $checked_number < 0) $this->output(-1, '已核实数量只能在0-5之间.');
		return $checked_number;
	}
}
