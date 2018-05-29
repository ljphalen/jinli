<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ActivityController extends Amigo_BaseController {
	
	/**
	 * amigo activity
	 */
	public function indexAction() {
	    $this->assign('title', '淘宝热门');
	}

    public function longAction(){
        $this->assign('title', '热门活动');
    }
    public function redirectAction(){
        $id = $this->getInput('id');
        Amigo_Service_Activity::updateTJ($id);
        $item = Amigo_Service_Activity::getOne($id);
        if(empty($item['link'])){
            header('Location: ' .Util_Http::getServer('HTTP_REFERER'));
            exit ;
        }
        header('Location:'.html_entity_decode($item['link']));
        exit;
    }
}
