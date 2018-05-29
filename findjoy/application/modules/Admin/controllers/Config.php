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
		'getExchangeRate'=>'/admin/config/getExchangeRate',
	);
	public $appCacheName = 'APPC_Front_Index_index';
	public $versionName = 'Config_Version';
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Fj_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	/**
	 * 获取当前实时汇率
	 * @return num|string
	 */
	public function getExchangeRateAction(){
		$currencies = $this->getInput(array('fc', 'tc'));

		$rate = Util_Exchange::getExchangeRate($currencies['fc'], $currencies['tc']);

		if($rate === false)
			$this->output(1, '获取失败');
		else
			$this->output(0, '获取成功', array('rate'=>$rate, 'ftc'=>$currencies['fc'] . ' > ' . $currencies['tc']));
	}
	
	/**
	 * 更新配置
	 */
	public function editPostAction() {
		$config = $this->getInput(array(
				'fj_currency_rate_hk',
			));

		foreach(array_filter($config) as $key=>$value) {
			Fj_Service_Config::setValue($key, html_entity_decode($value));
		}
		Fj_Service_Config::setValue('Config_Version', Common::getTime());
		$this->output(0, '操作成功.');
	}
	

}
