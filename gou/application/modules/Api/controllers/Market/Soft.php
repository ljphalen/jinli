<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author rainkid
 *
 */
class Market_SoftController extends Api_BaseController {
	
	public function indexAction() {
		/* $data = array(
			0=>array('package'=>'com.taobao.taobao', 'apk'=>'taobao.apk' ,'version'=>'90', 'download_url'=>'http://goudl.gionee.com/apps/shoppingmall/taobao.apk')	
		); */
		
		$data = Gou_Service_Marketsoft::getAllSoft();
		$this->clientOutput(0, '', array('list'=>$data));
	}
}
