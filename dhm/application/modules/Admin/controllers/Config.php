<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author Terry
 *
 */
class ConfigController extends Admin_BaseController {
	
	public $actions = array(
		'editUrl'=>'/admin/config/index',
		'editPostUrl'=>'/admin/config/editPost',
	);
	public $appCacheName = 'APPC_Front_Index_index';
	public $versionName = 'Config_Version';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Dhm_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}
	
	/**
	 * 更新配置
	 */
	public function editPostAction() {
		$config = $this->getInput(array(
				'dhm_page_title',
		        'dhm_page_keywords',
		       'dhm_page_description',
			));

		foreach(array_filter($config) as $key=>$value) {
			Dhm_Service_Config::setValue($key, html_entity_decode($value));
		}
		Dhm_Service_Config::setValue('Config_Version', Common::getTime());
		$this->output(0, '操作成功.');
	}
	

}
