<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Kingstone_CategoryController extends Api_BaseController {
	
	public $actions = array(
			'listUrl' => '/kingstone/category/index',
			'detailUrl' => '/kingstone/category/detail/',
			'indexlUrl' => '/kingstone/index/detail/',
			'tjUrl' => '/kingstone/index/tj'
	);
	public $perpage = 8;
	public $cacheKey = 'Kingstone_Category_index';
	
    /**
     * 
     */
    public function indexAction() {
    	$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		$source = $this->getSource();
		$tjUrl = $this->actions['tjUrl'];
		$detailUrl = $this->actions['detailUrl'];
		
		if ($page < 1) $page = 1;
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		//猜你喜欢
		$game_caini = 0;
		//游戏分类
		list($total, $categorys) = Resource_Service_Attribute::getApiList($page, $this->perpage, $game_caini, array('at_type'=>1,'status'=>1));
		//客户端
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
    	list($data, $imgs) =  Resource_Service_Attribute::getJsonData($categorys, $checkVer, $game_caini, $sp, $tjUrl,$detailUrl, $page, $hasnext ,$intersrc, $source);
    	if($page < 2 && $imgs) $this->cache($imgs, 'category');
    	$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
    
    
    
}