<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CategoryController extends Api_BaseController {
	
	public $actions = array(
			'listUrl' => '/client/category/index',
			'detailUrl' => '/client/category/detail/',
			'indexlUrl' => '/client/index/detail/',
			'tjUrl' => '/client/index/tj'
	);
	public $perpage = 8;
	public $cacheKey = 'Client_Category_index';
	
    /**
     * 
     */
    public function indexAction() {
    	$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		$sp = $this->getInput('sp');
		$sps = explode('_', $sp);
		$version = $sps[1];
		$source = $this->getSource();
		$tjUrl = $this->actions['tjUrl'];
		$detailUrl = $this->actions['detailUrl'];
		
		if ($page < 1) $page = 1;
		//判断游戏大厅版本
		$checkVer = $this->checkAppVersion();
		//猜你喜欢
		$game_caini =  Game_Service_Config::getValue('game_caini');
		if($game_caini && $checkVer < 3 ) {
			$game_caini = 1;
			$flag = 1;
		} else {
			$game_caini = 0;
			$flag = 0;
		}
		//游戏分类
		list($total, $categorys) = Resource_Service_Attribute::getApiList($page, $this->perpage, $game_caini, array('at_type'=>1,'status'=>1));
		$num = ($flag ? ($total + 1) : $total );
		
		//客户端
		$hasnext = (ceil((int) $num / $this->perpage) - $page) > 0 ? true : false;
    	list($data, $imgs) =  Resource_Service_Attribute::getJsonData($categorys, $checkVer, $game_caini, $sp, $tjUrl,$detailUrl, $page, $hasnext ,$intersrc, $source, $this->filter);
    	if($page < 2 && $imgs) $this->cache($imgs, 'category');
    	$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }
    
    
    
}