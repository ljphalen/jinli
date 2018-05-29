<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class CategoryController extends Super_BaseController{

	public function indexAction() {
        $webroot = Common::getWebRoot();
        $url = "{$webroot}/category/index/?source=super2";
        $this->redirect($url);
        exit;
	}
}
