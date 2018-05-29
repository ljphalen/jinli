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
	public function _initSession(Yaf_Dispatcher $dispatcher) {
		Yaf_Session::getInstance()->start();
	}
	/**
	 * 
	 * init config
	 */
	public function _initConfig(Yaf_Dispatcher $dispatcher) {
		$config = Yaf_Application::app()->getConfig();
		set_include_path(get_include_path() . PATH_SEPARATOR . $config->application->library);
		Yaf_Registry::set("config", $config);
	}
	
	public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        if (! Util_Environment::isOnline()) {
            $dispatcher->registerPlugin(new UserPlugin());
        }
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
	public function _initDefaultAdapter(Yaf_Dispatcher $dispatcher){
		$config = Common::getConfig('dbConfig', 'default');
		//$defaultAdapter = Db_Pdo::factory($config);
		// set default adatpter
		Db_Adapter_Pdo::setDefaultAdapter($config);
		
		// get gamelog db config
		$GLOGconfig = Common::getConfig('dbConfig', 'glog');
		//$GLOGAdapter = Db_Pdo::factory($GLOGconfig);
		//register GLOG adapter
		Db_Adapter_Pdo::registryAdapter('GLOG', $GLOGconfig);
		
		// get acclog db config
		$ACCLOGconfig = Common::getConfig('dbConfig', 'acclog');
		//$ACCLOGAdapter = Db_Pdo::factory($ACCLOGconfig);
		//register $ACCLOG adapter
		Db_Adapter_Pdo::registryAdapter('ACCLOG', $ACCLOGconfig);
		
		// get bi db config
		$BIconfig = Common::getConfig('dbConfig', 'bi');
		//$BIAdapter = Db_Pdo::factory($BIconfig);
		//register bi adapter
		Db_Adapter_Pdo::registryAdapter('BI', $BIconfig);
		
		// get statistics db config
		$STATISTICSconfig = Common::getConfig('dbConfig', 'statistics');
		//$BIAdapter = Db_Pdo::factory($BIconfig);
		//register statistics adapter
		Db_Adapter_Pdo::registryAdapter('STATISTICS', $STATISTICSconfig);
    }
}
