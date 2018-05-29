<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {
	
	public $perpage = 20;

    public $actions = array(
        'adClickUrl' => '/index/adclick',
    );
	
    public function indexAction() {
        $this->nocache_headers();

       $id = Common::encrypt($this->userInfo['id'],'ENCODE');
       $this->assign('id', $id);        
       $this->assign('title', '大红帽 - 正品港货 香港原价直销，让你轻松淘遍香港');
    }

    /**
     * 增加广告点击量 +1
     */
    public function adClickAction() {
    	$id = $this->getInput('ad');
        $url = $this->getInput('url');

        //广告点击次数增加
        Fj_Service_Ad::clickIncrement(array('id'=>intval($id)));

        $this->redirect(html_entity_decode(urldecode($url)));
    }
}