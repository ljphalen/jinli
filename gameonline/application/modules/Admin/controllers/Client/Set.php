<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_SetController extends Admin_BaseController {
	
	public $actions = array(
		'editUrl'=>'/admin/Client_Set/index',
		'editPostUrl'=>'/admin/Client_Set/edit_post',
	);

	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$configs = Game_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	
	/**
	 * 
	 */
	public function edit_postAction() {
		$config = $this->getInput(array('game_set_img','game_set_download','game_set_del','game_set_tips','game_set_uptime','pushIntervalDays','game_third_push','thirdPartyPush','thirdPartyPushGn','game_third_Invalidpush','pushInvalidDays'));
		$config['game_set_uptime'] = Common::getTime();
		if($config['pushIntervalDays'] == 'tp'){
			if($config['game_third_push'] < 0 || $config['game_third_push'] == 30 || !preg_match("/^[0-9]*[1-9][0-9]*$/",$config['game_third_push'])){
				$this->output(-1, '必须填写正整数，且不能等于30.');
			}
			$config['pushIntervalDays'] = $config['game_third_push'];
		}
		
		if($config['pushInvalidDays'] == 'ti'){
			if($config['game_third_Invalidpush'] < 0 || $config['game_third_Invalidpush'] == 3 || !preg_match("/^[0-9]*[1-9][0-9]*$/",$config['game_third_Invalidpush'])){
				$this->output(-1, '必须填写正整数，且不能等于3.');
			}
			$config['pushInvalidDays'] = $config['game_third_Invalidpush'];
		}
		
		

	    foreach($config as $key=>$value) {
			Game_Service_Config::setValue($key, $value);
		}
		$this->output(0, '操作成功.');
	}
}
