<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏缓存观察者
 * Resource_Service_AttributeCacheObserver
 * @author fanch
 *
 */
class Resource_Service_AttributeCacheObserver extends Common_Service_Observer {

	/**
	 * 执行方法体
	 */
	public function run($data){
		switch ($data['type']){
			case 1 :
				Resource_Service_AttributeCache::saveAtrributeToCache();
				break;
			case 2 :
				Resource_Service_AttributeCache::saveLabelToCache();
				break;
		}
	}
}
