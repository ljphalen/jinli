<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ThemeController extends Market_BaseController {
	
	public $actions =array(
			'tjUrl'=>'/index/tj'
	);
	
    /**
     * 
     */
    public function indexAction() {
    	$id = intval($this->getInput('id'));
    	
    	$category = Store_Service_Category::getBy(array('id'=>$id, 'version_type'=>4, 'status'=>1), array('id'=>'DESC'));
    	
    	//活动
    	$act_params = array(
    		'cate_id'=>$id,
    		'info_type'=>1,
    		'version_type'=>4,
    		'status'=>1,
    		'start_time'=>array('<', Common::getTime()),
    		'end_time'=>array('>', Common::getTime()),
    	);
    	$activity = Store_Service_Info::getBy($act_params, array('sort'=>'DESC', 'id'=>'DESC'));
    	
    	//平台
    	$plat_params = array(
    			'cate_id'=>$id,
    			'info_type'=>2,
    			'version_type'=>4,
    			'status'=>1,
    			'start_time'=>array('<', Common::getTime()),
    			'end_time'=>array('>', Common::getTime()),
    	);
    	list(,$plats) = Store_Service_Info::getsBy($plat_params, array('sort'=>'DESC', 'id'=>'DESC'));
    	
    	$this->assign('activity', $activity);
    	$this->assign('plats', $plats);
    	$this->assign('category', $category);
    	$this->assign('title', $category['title']);
    }
}