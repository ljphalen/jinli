<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class HotController extends Super_BaseController{

	public function indexAction() {
        $webroot = Common::getWebRoot();
        $url = "{$webroot}?source=super1";
        $this->redirect($url);
        exit;
	}
}
