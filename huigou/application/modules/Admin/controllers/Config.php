<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class ConfigController extends Admin_BaseController {
	
	public $actions = array(
		'editUrl'=>'/admin/config/index',
		'editPostUrl'=>'/admin/config/edit_post',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Gc_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 * 
	 */
	public function edit_postAction() {
		$config = $this->getInput(array('gc_search_keyword', 'gc_silver_coin_rate'));
		$keys = explode(',',html_entity_decode($config['gc_search_keyword']));
		foreach($keys as $key=>$val) {
			if(empty($val))  $this->output(-1, '关键字不能为空.');
		}
		if(count($keys) >= 10) $this->output(-1, '关键词不超过9个.');
		foreach($config as $key=>$value) {
			Gc_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
}
