<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Bootstrap
 * @author rainkid
 */
class Bootstrap extends Yaf_Bootstrap_Abstract {

	/**
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initSession($dispatcher) {
		Yaf_Session::getInstance()->start();
	}

	public function _initConfig() {
		$config = Yaf_Application::app()->getConfig();
		set_include_path(get_include_path() . PATH_SEPARATOR . $config->application->library);
		Yaf_Registry::set("config", $config);
	}

	/**
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initRoute(Yaf_Dispatcher $dispatcher) {
		$router = Yaf_Dispatcher::getInstance()->getRouter();
		$router->addConfig(Yaf_Registry::get("config")->routes);
	}

	/**
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$dispatcher->registerPlugin(new UserPlugin());
	}

	/**
	 * @param Yaf_Dispatcher $dispatcher
	 */
	public function _initDefaultModule(Yaf_Dispatcher $dispatcher) {
		$dispatcher->setDefaultModule(DEFAULT_MODULE);
	}

	public function _initDefaultAdapter() {
		$config = Common::getConfig('dbConfig', 'default');
		Db_Adapter_Pdo::setDefaultAdapter($config);

		//$config = Common::getConfig('dbConfig', 'stat');
		//Db_Adapter_Pdo::registryAdapter('stat', $config);

	}

}
