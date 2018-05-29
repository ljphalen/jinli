<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CacheController extends Common_BaseController {

	
    /**
     * 
     */
    public function indexAction() {
    	$cn = $this->getInput('cn');
    	Yaf_Dispatcher::getInstance()->disableView();
    	header('Content-Type: text/cache-manifest');
    	echo "CACHE MANIFEST\n";
    	$v = Lock_Service_Config::getValue('APPC_'.$cn);
    	echo "\n#version:".$v."\n\n";
		
    	$caches = Common::getConfig('cacheConfig');
    	$caches = $caches[$cn];
    	foreach($caches as $key=>$value) {
    		echo sprintf("\n\n%s:\n", $key);
    		 if ($key == 'CACHE') {
    			$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $cn);
    			if(file_exists($file)) {
    				$files = include $file;
    				if (is_array($files)) {
	    				$value = array_merge($files, $value);
    				}
    			}
    		}
	    	echo implode("\n", $value);
    	}
    }
    
}
