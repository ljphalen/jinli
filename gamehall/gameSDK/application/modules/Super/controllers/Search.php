<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class SearchController extends Super_BaseController{

	public function indexAction() {
        $webroot = Common::getWebRoot();
        $url = "{$webroot}/search/index/?source=super4";
        $this->redirect($url);
        exit;
	}
	
}
