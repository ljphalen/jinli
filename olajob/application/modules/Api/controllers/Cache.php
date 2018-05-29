<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CacheController extends Api_BaseController {

	
    /**
     * 
     */
    public function indexAction() {
    	$cn = $this->getInput('cn');
    	Yaf_Dispatcher::getInstance()->disableView();
    	header('Content-Type: text/cache-manifest');
    	echo "CACHE MANIFEST\n";
    	$v = Game_Service_Config::getValue('APPC_'.$cn);
    	echo "\n#version:".$v."\n\n";
		
    	$caches = Common::getConfig('cacheConfig', $cn);
    	foreach($caches as $key=>$value) {
    		if ($key == 'CACHE') {
    			$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $cn);
    			if(file_exists($file)) {
    				$files = include $file;
    				if (is_array($files)) {
	    				$value = array_merge($files, $value);
    				}
    			}
    			if (Game_Service_Config::getValue('game_cache')) {
		    		echo sprintf("\n\n%s:\n", $key);
			    	echo implode("\n", $value);
    			}
    		} else {
    			echo sprintf("\n\n%s:\n", $key);
    			echo implode("\n", $value);
    		}
    	}
    }
}