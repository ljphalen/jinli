<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ProdController extends Apk_BaseController {
	
	public $actions =array(
				'index' => '/shop/index',
			);
	
	public $perpage = 10;


	
	/**
	 * shop list
	 */
	public function indexAction() {
        $uid = Common::getAndroidtUid();
	    $this->assign('uid',$uid);
	    $this->assign('title', '每日新品');
	}
	
	/**
	 * goods detail page
	 */
	public function detailAction() {
        $id = intval($this->getInput('id'));
        Mall_Service_Goods::inc('hits',array('num_iid' => $id));
        $topApi  = new Api_Top_Service();
        $info = $topApi->tbkMobileItemsConvert(array('num_iids' => $id));
        $this->redirect($info['click_url']);
        exit();
	}
}
