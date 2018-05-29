<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class SubjectController extends Super_BaseController {

	public function indexAction() {
        $webroot = Common::getWebRoot();
        $url = "{$webroot}/subject/index/?source=super3";
        $this->redirect($url);
        exit;
	}
}
