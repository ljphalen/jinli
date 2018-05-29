<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Bootstrap extends Yaf_Bootstrap_Abstract {
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $dispatcher
	 */
	public function _initSession($dispatcher) {
		Yaf_Session::getInstance()->start();
	}
	/**
	 * 
	 * Enter description here ...
	 */
	public function _initConfig() {
		$config = Yaf_Application::app()->getConfig();
		set_include_path(get_include_path() . PATH_SEPARATOR . $config->application->library);
		Yaf_Registry::set("config", $config);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		$router = Yaf_Dispatcher::getInstance()->getRouter();
		$router->addConfig(Yaf_Registry::get("config")->routes);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$dispatcher->registerPlugin(new UserPlugin());
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initDefaultModule(Yaf_Dispatcher $dispatcher) {
		$dispatcher->setDefaultModule(DEFAULT_MODULE);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function _initDefaultAdapter(){
		$config = Common::getConfig('dbConfig', 'default');
		// set default adatpter
		Db_Adapter_Pdo::setDefaultAdapter($config);
    }
}
