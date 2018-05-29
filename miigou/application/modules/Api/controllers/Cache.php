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
    	$v = Gou_Service_Config::getValue('APPC_'.$cn);
    	echo "\n#version:".$v."\n\n";
		
    	$webroot = Common::getWebRoot();
    	$caches = Common::getConfig('cacheConfig', $cn);
    	foreach($caches as $key=>$value) {
    		echo sprintf("\n\n%s:\n", $key);
    		if ($key == 'CACHE') {
    			$file = sprintf("%scache/APPC_%s.php", Common::getConfig('siteConfig', 'dataPath'), $cn);
    			if(file_exists($file)) {
    				$files = include $file;
    				foreach($files as $k=>$v) {
    					foreach($v as $pic) {
		    				array_push($value, $webroot.$pic);
    					}
    				}
    			}
    		}
	    	echo implode("\n", $value);
    	}
    }
}