<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Kingstone_CacheController extends Api_BaseController {

	
    /**
     * 
     */
    public function indexAction() {
    	$cn = $this->getInput('cn');
    	$apk_version = $this->getInput('apk_version');
    	Yaf_Registry::set("apk_version", $apk_version);
    	Yaf_Dispatcher::getInstance()->disableView();
    	header('Content-Type: text/cache-manifest');
    	echo "CACHE MANIFEST\n";
    	$v = Game_Service_Config::getValue('APPC_'.$cn);
    	echo "\n#version:".$v."\n\n";
		
    	$caches = Common::getConfig('cacheConfig', $cn);
    	$attachPath = Common::getAttachPath();
        foreach($caches as $key=>$value) {
    		echo sprintf("\n\n%s:\n", $key);
    		if ($key == 'CACHE') {
    			$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $cn);
    			if(file_exists($file)) {
    				$files = include $file;
    				foreach($files as $k=>$v) {
    					foreach($v as $pic) {
		    				array_push($value, $attachPath.$pic);
    					}
    				}
    			}
    		}
	    	echo implode("\n", $value);
    	}
    }
}