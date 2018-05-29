<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * 出错页面
 * @author luojiapeng
 *
*/
class ErrorController extends Front_BaseController{
	
	public function errorAction($exception) {
		$webroot = Common::getWebRoot();
		$this->assign('webroot', $webroot);
		switch ($exception->getCode()) {
			case YAF_ERR_NOTFOUND_MODULE:
			case YAF_ERR_NOTFOUND_CONTROLLER:
			case YAF_ERR_NOTFOUND_ACTION:
			case YAF_ERR_NOTFOUND_VIEW:
				echo 404,':',$exception->getMessage();
				break;
			case -1 :
				echo $this->getView()->render('error/error.phtml', array('msg'=>$exception->getMessage()));
				break;
			default :
				echo 0,':',$exception->getMessage();
				break;
		}
		
	}
	
	public function indexAction($exception) {
		
	}
}	