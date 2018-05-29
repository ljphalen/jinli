<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class InstalleController extends Channel_BaseController{
	
	public $actions = array(
		'listUrl' => '/channel/installe/index',
		'detailUrl' => '/channel/index/detail/',
		'tjUrl' => '/channel/index/tj'
	);

	public $perpage = 10;
	/**
	 * 
	 * index page view
	 */
   public function indexAction() {
		$page = intval($this->getInput('page'));
		$t_bi = $this->getInput('t_bi');
		if (!$t_bi) $this->setSource();
		if(!$imei){
			$sp = $this->getInput('sp');
			$imei = array_shift(explode('_',$sp));
			Util_Cookie::set("imei", $imei, true, Common::getTime() + strtotime("+1 days"));
		} else {
			$imei = Util_Cookie::get('imei', true);
		}
		$model = $imei; 
		$intersrc = $this->getInput('intersrc');
		if ($page < 1) $page = 1;
		$params  = $search = $tmp = $group_ids = array();
		$params['status'] = 1;
		
	    $group = Resource_Service_Pgroup::getPgroupBymodels($model);  //根据机型找出分组
    	$ids = Common::resetKey($group, 'id');
    	$ids = array_unique(array_keys($ids));
    	if($ids){ 
    		foreach($group as $key=>$value){
    			$temp = array();
    			$temp = explode(',',$value['p_title']);
    			if(in_array($model,$temp)){
    				$group_ids[] = $value['id'];
    			}
    		}
    		if($group_ids){
    			$params['gtype'] = array('IN',$group_ids);
    		} else {
    			$params['gtype'] = 1;
    		}
    	} else {
    		$params['gtype'] = 1;
    	}
    	$info = Client_Service_Installe::getInstalleByGtypes($params);  //找出分组信息
    	if(!$info){
    		$info = Client_Service_Installe::getInstalleByGtypes(array('gtype'=>1,'status'=>1));  //找出分组信息
    	}   
    	list($total, $game_ids) = Client_Service_Installe::getIdxList(1, $this->perpage, array('status'=>1,'game_status'=>1,'installe_id'=>$info['id']),array('sort'=>'DESC','game_id'=>'DESC'));
    	
    	$game_ids = Common::resetKey($game_ids, 'game_id');
    	$game_ids = array_unique(array_keys($game_ids));
	    foreach($game_ids as $key=>$value) {
    			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
	    		if ($info) $games[] = $info; 
    	}
		$this->assign('games', $games);
		$hasnext = (ceil((int) $total / $this->perpage) - 1) > 0 ? true : false;
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
		$this->assign('source', $this->getSource());
		$this->assign('intersrc', $intersrc);
	}
	
	/**
	 * get game list as more
	 */
	public function moreAction(){
	    $page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$intersrc = $this->getInput('intersrc');
		if(!$intersrc) $intersrc = 'ness';
		$webroot = Common::getWebRoot();
		
	    $imei = Util_Cookie::get('imei', true);
		$model = $imei; 
		if ($page < 1) $page = 1;
		$params  = $search = $tmp = $group_ids = array();
		$params['status'] = 1;
		
	    $group = Resource_Service_Pgroup::getPgroupBymodels($model);  //根据机型找出分组
    	$ids = Common::resetKey($group, 'id');
    	$ids = array_unique(array_keys($ids));
	    if($ids){ 
    		foreach($group as $key=>$value){
    			$temp = array();
    			$temp = explode(',',$value['p_title']);
    			if(in_array($model,$temp)){
    				$group_ids[] = $value['id'];
    			}
    		}
    		if($group_ids){
    			$params['gtype'] = array('IN',$group_ids);
    		} else {
    			$params['gtype'] = 1;
    		}
    	} else {
    		$params['gtype'] = 1;
    	}
    	$info = Client_Service_Installe::getInstalleByGtypes($params);  //找出分组信息
    	if(!$info){
    		$info = Client_Service_Installe::getInstalleByGtypes(array('gtype'=>1,'status'=>1));  //找出分组信息
    	}
    	list($total, $game_ids) = Client_Service_Installe::getIdxList($page, $this->perpage, array('status'=>1,'game_status'=>1,'installe_id'=>$info['id']),array('sort'=>'DESC','game_id'=>'DESC'));
    	
    	$game_ids = Common::resetKey($game_ids, 'game_id');
    	$game_ids = array_unique(array_keys($game_ids));
	    foreach($game_ids as $key=>$value) {
    			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value));
	    		if ($info) $games[] = $info; 
    	}
    	
		foreach($games as $key=>$value) {
			$href = urldecode($webroot.$this->actions['detailUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource());
			$tmp[] = array(
						'id'=>$value['id'],
						'name'=>$value['name'],
						'resume'=>$value['resume'],
						'size'=>$value['size'].'M',
						'link'=>Common::tjurl($this->actions['tjUrl'], $value['id'], $intersrc, $value['link']),
						'alink'=>urldecode($webroot.$this->actions['indexlUrl']. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$this->getSource()),
						'img'=>urldecode($value['img']),
						'profile'=>$value['name'].','.$href.','.$value['id'].','.$value['link'].','.$value['package'].','.$value['size'].','.$value['min_sys_version_title'].','.$value['min_resolution_title'].'-'.$value['max_resolution_title'],
			);
		}
		
		$hasnext = (ceil((int) $total / $this->perpage) - $page) > 0 ? true : false;
		$this->output(0, '', array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->assign('hasnext', $hasnext);
		$this->assign('page', $page);
	}
}
