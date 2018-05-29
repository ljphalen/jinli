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
		$config = $this->getInput(array('game_client_upfreq',
                                        'game_service_qq',
                                        'game_service_worktime',
                                        'game_service_tel',
                                        'game_set_img',
                                        'game_set_download',
                                        'game_set_del',
                                        'game_set_tips',
                                        'game_set_uptime',
                                        'pushIntervalDays',
                                        'game_third_push',
                                        'thirdPartyPush',
                                        'thirdPartyPushGn',
                                        'game_third_Invalidpush',
                                        'pushInvalidDays',
                                        'flowReportSize',
                                        'game_client_gobackfilter',
                                        'game_client_deffilter',
                                        'game_client_cache',
                                        'news_source',
                                        'strategy_source',
				                        'game_feedback_user_question',
				                        'game_feedback_input',
				                        'xunleiEnabled',
										'game_webp_switch'
                                        ));
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
			$this->saveConfigToCache($key, $value, 5*60);
		}
		
		$this->output(0, '操作成功.');
	}
	
	private function saveConfigToCache($key, $data, $expire=60){
		$ckey = ":" . $key;
		$cache = Cache_Factory::getCache();
		$cache->set($ckey, $data, $expire);
	}
	
}
