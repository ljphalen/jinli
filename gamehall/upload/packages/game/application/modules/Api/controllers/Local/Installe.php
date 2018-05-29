<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Local_InstalleController extends Api_BaseController{

	public $perpage = 10;
	
	
	/**
	 * 装机必备列表
	 */
	public function installeListAction() {
		$page = intval($this->getInput('page'));
		$intersrc = $this->getInput('intersrc');
		if(!$intersrc) $intersrc = 'ness';
		$sp = $this->getInput('sp');
		$imei = array_shift(explode('_',$sp));
		
		$data = $this->_installeList($page, $intersrc, $imei);
		$this->localOutput('','',$data);
	}
	
	
	public function _installeList($page, $intersrc, $imei){
		
		$webroot = Common::getWebRoot();
		if ($page < 1) $page = 1;
		$model = $imei; 
		$params  = $search = $tmp = $group_ids = array();
		$params['status'] = 1;
		
	    $group = Resource_Service_Pgroup::getPgroupBymodels($model);  //根据机型找出分组

	    if($group){ 
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
   	
    	//判断游戏大厅版本
    	$tmp = array();
    	$checkVer = $this->checkAppVersion();
		$tmp = Resource_Service_Games::getClientGameData($game_ids, $intersrc, $checkVer, 1);
	
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$data = array('list'=>$tmp, 'hasnext'=>$hasnext, 'curpage'=>$page, 'totalCount'=>$total);
		return $data;
	}
}
