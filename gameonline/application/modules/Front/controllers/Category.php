<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class CategoryController extends Front_BaseController{
	
	public $actions = array(
		'listUrl' => '/Front/Category/index/',
		
	);

	public $perpage = 35;

	/**
	 * 
	 * 分类页面
	 */
	public function indexAction() {
		Common::addSEO($this,'分类页面');
		$page = intval($this->getInput('page'));
		$category_id = intval($this->getInput('category'));
		
		//默认查询出所有游戏
		$category_id = ($category_id)? $category_id : 100;
		$flag = intval($this->getInput('flag'));
		
		//分页的参数
		if ($page < 1) $page = 1;
		$params=array('category'=>$category_id,'flag'=>$flag);

	    //游戏分类名称
		list($categorys_total, $categorys) = Resource_Service_Attribute::getsortList(1, 100, array('at_type'=>1,'status'=>1));
		$category_list = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $category_list);
		$this->assign('category_id', $category_id);
		
		//排序的字段 最新或最热
		if($flag){
			$orderBy = array( 'downloads'=>'DESC','sort'=>'DESC','id'=>'DESC');
		}else{
			$orderBy = array( 'online_time'=>'DESC','sort'=>'DESC','id'=>'DESC');
		}

		//查找游戏的参数
		$games_params['status'] = 1;
		//全部与最新
	    if($category_id == 100 || $category_id == 101){
	    	//过滤
	    	if($this->filter){
	    		$games_params['id'] = array('NOT IN', $this->filter);
	    	}
	    	list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $games_params,$orderBy);
	    }else{
	    	//查找游戏索引表的参数
	    	$category_params['game_status'] = 1;
	        $category_params['category_id'] = $category_id;
	        //过滤
	        if($this->filter){
	        	$category_params['game_id'] = array('NOT IN', $this->filter);
	        }
	   
	    	list($total, $games_all) = Resource_Service_Games::getCategoryGames($category_params);
	    	$game_ids = Common::resetKey($games_all, 'game_id');
	    	$game_ids = array_unique(array_keys($game_ids));
	    	$games_params['id'] = array('IN',$game_ids);	    	   	
	    	list($total, $games) = Resource_Service_Games::getList($page, $this->perpage, $games_params,$orderBy);
	    }
	
	
		
		//取出游戏详细信息
		$game_list = array();
		foreach ($games as $key=>$val){ 
		     $info= Resource_Service_Games::getGameAllInfo(array("id"=>$val['id']));
		     if($info) {
		     	$game_list[$key] =  $info;
		     }
		}
		$this->assign('gamelist', $game_list);
		//分页
		$url = $this->actions['listUrl'].'?'. http_build_query($params).'&';
		$paper = Common::getPages($total, $page, $this->perpage, $url,'',1,'paddingright10');
		$this->assign('pager',$paper );
	
		//下载排行
		$this->assign('downgames', $this->getDowloadRank());

		$this->assign('flag',$flag);
		
	}		
}
