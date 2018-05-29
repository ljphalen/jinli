<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class FreeflowController extends Client_BaseController{
	
	
	/**
	 *
	 * 免流量活动详情
	 */
	public function detailAction() {
		$id = intval($this->getInput('id'));
	   if(!$id){
	    	$str  = $this->redirect('/Client/Error/index/');
	    	exit;
	    }
		$info = Freedl_Service_Hd::getFreedl($id);
		$this->assign('info', $info);
		
	}
}