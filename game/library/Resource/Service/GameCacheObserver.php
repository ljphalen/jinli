<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 游戏缓存观察者
 * Resource_Service_GameCacheObserver
 * @author fanch
 *
 */
class Resource_Service_GameCacheObserver extends Common_Service_Observer {

	/**
	 * 执行方法体
	 */
	public function run($data){
		switch ($data['status']){
			case 1 :
				Resource_Service_GameCache::saveGameDataToCache($data['gameId']);
				break;
			case 0 :
				Resource_Service_GameCache::deleteGameCache($data['gameId']);
				break;
		}
	}
}
