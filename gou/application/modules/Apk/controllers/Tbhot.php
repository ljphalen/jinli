<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TbhotController extends Apk_BaseController {
	
    /**
     * 淘热卖广告
     */
    public function indexAction() {
        $model = $this->getInput('model');
        
        //广告不用了，所以不用展示广告，直接跳爱淘宝 2015-07-02
    	/* $webroot = Common::getWebRoot();    	
    	$this->assign('ajax_url', $webroot.'/api/apk_tbhot/taobaourl?model='.$model);
    	$this->assign('title', '淘热卖'); */
        
        //淘热卖跳转地址
        $url = Client_Service_Taobaourl::getBy(array('model'=>$model));
        if($model && $url) {
            Client_Service_Taobaourl::updateTJ($url['id']);
            $tbhot_url = html_entity_decode($url['url']);
        } else {
            $tbhot_url = 'http://m.taobao.com';
        }
        $this->redirect($tbhot_url);
        exit;
    }
}