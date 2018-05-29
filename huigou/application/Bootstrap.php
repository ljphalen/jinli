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
	 * init session
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initSession($dispatcher) {
		Yaf_Session::getInstance()->start();
	}
	/**
	 * 
	 * init config
	 */
	public function _initConfig() {
		$config = Yaf_Application::app()->getConfig();
		set_include_path(get_include_path() . PATH_SEPARATOR . $config->application->library);
		Yaf_Registry::set("config", $config);
	}
	
	/**
	 * 
	 * init route
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		$router = Yaf_Dispatcher::getInstance()->getRouter();
		$router->addConfig(Yaf_Registry::get("config")->routes);
	}

	/**
	 * 
	 * init default module 
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initDefaultModule(Yaf_Dispatcher $dispatcher) {
		$dispatcher->setDefaultModule(DEFAULT_MODULE);	
	}
	
	/**
	 * 
	 * init default adapter
	 */
	public function _initDefaultAdapter(){
		$config = Yaf_Application::app()->getConfig();
		$defaultAdapter = Db_Pdo::factory($config->database->default->toArray());
		// set default adatpter
		Db_Adapter_Pdo::setDefaultAdapter($defaultAdapter);
    }
}
