<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class IndexController extends Game_BaseController{
	
	public $actions = array(
		
	);

	public $perpage = 100;

	/**
	 * 
	 * index page view
	 */
	public function indexAction() {
		
		
		$cache = Common::getCache();
		
		var_dump($cache);
		$cache->set('test', 1);
		var_dump(ddd);
		exit;
		
		
	}

	
}
