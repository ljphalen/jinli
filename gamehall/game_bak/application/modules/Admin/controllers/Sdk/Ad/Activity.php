<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author rainkid
 *
 */
class Sdk_Ad_ActivityController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Sdk_Ad_Activity/index',
		'addUrl' => '/Admin/Sdk_Ad_Activity/add',
		'addPostUrl' => '/Admin/Sdk_Ad_Activity/add_post',
		'editUrl' => '/Admin/Sdk_Ad_Activity/edit',
		'editPostUrl' => '/Admin/Sdk_Ad_Activity/edit_post',
		'deleteUrl' => '/Admin/Sdk_Ad_Activity/delete',
		'batchUpdateUrl'=>'/Admin/Sdk_Ad_Activity/batchUpdate',
		'uploadImgUrl' => '/Admin/Sdk_Ad_Activity/uploadImg',
		'addGameUrl'	  => '/Admin/Sdk_Ad_Activity/addGame',
		'addGamePostUrl' => '/Admin/Sdk_Ad_Activity/addGamePost',
		'editGameUrl'	  => '/Admin/Sdk_Ad_Activity/editGame',
		'editGamePostUrl' => '/Admin/Sdk_Ad_Activity/editGamePost',
		'deleteGameUrl' => '/Admin/Sdk_Ad_Activity/deleteGame',	
		'uploadUrl' => '/Admin/Sdk_Ad_Activity/upload',
		'uploadPostUrl' => '/Admin/Sdk_Ad_Activity/upload_post',
	);
	
	public $perpage = 20;
	public $show_type_name = array(
			1 => '公告',
			2 => '活动',
	);
	
	
	public $versionName = 'Sdk_Ad_Version';
	    
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		
		//清除数据
		$dele_params['is_finish'] = 0;
		$ret = Sdk_Service_Ad::getsBy($dele_params);
		$temp = array();
		if($ret){
			foreach ($ret as $val){
				if(strtotime($val['create_time']) >= strtotime($val['create_time']." -10 minute")){
					$temp[] = $val['id'];
				}
			}
			foreach ($temp as $val){
				Sdk_Service_Ad::deleteBy(array('id'=>$val));
			}
		}
		//参数
		$perpage = $this->perpage;
		$page = intval($this->getInput('page'));
		$status = intval($this->getInput('status'));
		$title = trim($this->getInput('title'));
		$start_time = $this->getInput('start_time');
		$end_time = $this->getInput('end_time');
		$game_ids = intval($this->getInput('game_ids'));
		$name     = trim($this->getInput('name'));
		$show_type = trim($this->getInput('show_type'));
		
		if ($page < 1) $page = 1;
		$params = array();
		$search = array();
		if($start_time && $end_time){
			$search['start_time'] = $start_time;
			$search['end_time'] = $end_time;
			$params['start_time'] = array(array('>=',strtotime($start_time)),array('<=',strtotime($end_time)));
			$params['end_time'] = array(array('>=',strtotime($start_time)),array('<=',strtotime($end_time)));
		}
		if($status) {
			$search['status'] = $status;
			$params['status'] = $status - 1;
		}
		if($title) {
			$search['title'] = $title;
			$params['title'] = array('LIKE',$title);
		}
	 
		if($show_type){
			$search['show_type'] = $show_type;
			if($show_type ==  3){
				$show_type ='1,2';
			}
			$params['show_type'] = array('LIKE',$show_type);
		}
		$params['is_finish'] = 1;
		
		if($game_ids) {
			$search['game_ids'] = $game_ids;
			$params['game_ids'] = array('LIKE',$game_ids);
			$ret = Sdk_Service_Ad::getsBy($params);
			$ids = array();
			foreach ($ret as $val){
				$temp = explode(',', $val['game_ids']);
				if( in_array($game_ids, $temp)){
					$ids[$val['id']] = $val['id'];
				}
			}
			if(!$ids){
				$ids = array('0');
			}
		}
		if($name){
			$search['name']  = $name;
			$game_params['name']  = array('LIKE',$name);
			$games = Resource_Service_Games::getGamesByGameNames($game_params);
			$games = Common::resetKey($games, 'id');
			$games = array_unique(array_keys($games));
			$ret = Sdk_Service_Ad::getsBy($params);
			foreach ($ret as $val){
				$temp = array_intersect($games, explode(',', $val['game_ids']) );
				if($temp){
					$name_ids[$val['id']] = $val['id'];
				}
			}
			if(!$name_ids){
				$name_ids = array('0');
			}
		}

		if($game_ids && $name){
			$params['id'] = array('IN',array_intersect($ids,$name_ids)?array_intersect($ids,$name_ids):array('0'));
		}elseif ($game_ids){
			$params['id'] = array('IN', $ids);
		}elseif ($name){
			$params['id'] = array('IN', $name_ids);
		}
		
		list($total, $ads) = Sdk_Service_Ad::getList($page, $perpage, $params,  array('sort'=>'DESC' ,'start_time'=>'DESC' ,'id'=>'DESC'));
		$this->assign('search', $search);
		$this->assign('ads', $ads);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign("total", $total);
		$this->assign("show_type_name", $this->show_type_name);
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
	}
	
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function add_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', array('ad_content', '#s_z'), 'start_time', 'end_time', 'is_payment','status','show_type','img'));
		$info['ad_type'] = Sdk_Service_Ad::AD_TYPE_TEXT;
		$info['ad_content'] = str_replace("<br />","",html_entity_decode($info['ad_content']));
		$info = $this->_cookData($info);
		$info['sort']  = intval($info['sort']);
		$info['title'] = trim($info['title']);
		$temp_show_type  = $info['show_type'];
		$info['show_type'] = implode(',' , $info['show_type']);
		
        //验证是否有图片
		if(in_array('2', $temp_show_type)){
			if(!$info['img']){
				$this->output(-1, '没有上传图片');
			}
		}
		//判断是否重复
		$params['title'] = $info['title'];
		$ret = Sdk_Service_Ad::getBy($params);
		if($ret)  $this->output(-1, '名称不能重复');
		//插入数据
		$result = Sdk_Service_Ad::addAd($info);
		if (!$result) $this->output(-1, '操作失败');
		
		$this->output(0, '操作成功',array('id'=>$result,'show_type'=>$info['show_type']));

	}
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='sort'){
			$ret = Sdk_Service_Ad::sortAd($info['sort']);
		}elseif($info['action'] =='open'){
			$data['status'] = 1 ;
			$this->checkTimeOverlapForBatchOpen($info['ids']);
			foreach ($info['ids'] as $key=>$val){
				$ret = Sdk_Service_Ad::updateAd($data, $val);
			}
		}elseif($info['action'] =='close'){
			$data['status'] = 0;
			foreach ($info['ids'] as $key=>$val){
				$ret = Sdk_Service_Ad::updateAd($data, $val);
			}
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	

	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$id = intval($this->getInput('id'));
		if (!$id) $this->output(-1, '操作失败');
		$info = Sdk_Service_Ad::getAd($id);
		$this->assign('show_type_name', $this->show_type_name);
		$this->assign('info', $info);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('sort', 'title', 'ad_type', array('ad_content', '#s_z'), 'start_time', 'end_time', 'is_payment','status','show_type','img'));
		$info['ad_type'] = Sdk_Service_Ad::AD_TYPE_TEXT;
		$info['ad_content'] = str_replace("<br />","",html_entity_decode($info['ad_content']));
		$info = $this->_cookData($info);
		$info['sort']  = intval($info['sort']);
		$info['title'] = trim($info['title']);
		$id =  intval($this->getPost('id'));
		$temp_show_type  = $info['show_type'];
		$info['show_type'] = implode(',' , $info['show_type']);
		if (!$id) $this->output(-1, '操作失败');
		
		if(in_array('2', $temp_show_type)){
			if(!$info['img']){
				$this->output(-1, '没有上传图片');
			}
		}
		//判断是否重复
		$params['title'] = $info['title'];
		$params['id'] = array('!=', $id);
		$ret = Sdk_Service_Ad::getBy($params);
		if($ret)  $this->output(-1, '名称不能重复');
		
	    $data['temp_content'] = serialize($info);
	    $ret = Sdk_Service_Ad::updateAd($data, $id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.',array('id'=>$id,'show_type'=>$info['show_type'])); 		
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editGameAction() {
	
		$ad_id = $this->getInput('adid'); 
		$show_type = $this->getInput('show_type');
		if(!$ad_id) $this->output(-1, '非法操作.');
		$ad_info = Sdk_Service_Ad::getAd($ad_id);
		if(!$ad_info) $this->output(-1, '非法操作.');
		
		$action = $this->getInput('action');
	
		$page = $this->getInput('page');
		$s = $this->getInput(array('name','cooperate','game_id'));
		$params = $search = $info  = $current_games  = array();
		$s['cooperate'] = $s['cooperate'] ? $s['cooperate'] : 1 ;
		$s['adid'] =  $ad_id;
		
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1, 'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
	
		$search['status'] = 1;
		if ($s['name']) $search['name'] = array('LIKE',$s['name']);
		if ($s['cooperate']) $search['cooperate'] = $s['cooperate'];
		if ($s['game_id']) $search['id'] = $s['game_id'];
		

		list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $search, array('id'=>'DESC'));
		unset($search);
		if($games){
			foreach($games as $k=>$v){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$v['id']));
			}
		}
		
		//查询已添加的游戏id,让页面复选框选中的状态
		$game_info  = Sdk_Service_Ad::getAd($ad_id);
		if($game_info){
			//第一次进来
			if($action == 'step1' ){
				$ids = explode(',', $game_info['game_ids']);
				Sdk_Service_Ad::updateAd(array('game_temp_ids'=>$game_info['game_ids'],'is_add'=>0), $ad_id);
			}elseif($action == 'del'){
				$ids = explode(',', $game_info['game_temp_ids']);
				$temp_content = unserialize($game_info['temp_content']);
				$show_type = $temp_content['show_type'];
			}else{
				$game_info  = Sdk_Service_Ad::getAd($ad_id);
				//判断是否有添加的标志
				if(intval($game_info['is_add']) ==  1 ){
					$ids = explode(',', $game_info['game_temp_ids']);
				}else{
					$ids = explode(',', $game_info['game_temp_ids']);
				}
			}
			$current_games = array();
			$current_game_name = array();
			foreach ($ids as $val){
				if($val){
					$current_games[$val] = $val;
					$current_name = Resource_Service_Games::getBy(array('id'=>$val));
					$current_game_name[$val] = $current_name['name'];
				}
			}
		}
		
		$this->assign('current_games', $current_games);
		$this->assign('current_game_name', $current_game_name);
		$this->assign('total', $total);
		$this->assign('games', $info);
		$this->assign('s', $s);
		$this->assign('adid', $ad_id);
		$this->assign('show_type', $show_type);
		$url = $this->actions['editGameUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('page', $page);
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editGamePostAction() {
		$info = $this->getInput(array('ids','adid','action','name','cooperate','show_type'));
		if(!$info['adid']) $this->output(-1, '非法操作.');

		$ad_info = Sdk_Service_Ad::getAd($info['adid']);
		if(!$ad_info) $this->output(-1, '非法操作.');
		if($info['show_type'] != 2){
			//算出已加入公告的游戏id的条数
			$time = common::getTime();
			$params['start_time'] = array('<=',$time);
			$params['end_time'] = array('>=',$time);
			$params['is_finish'] = 1;
			$params['status'] = 1;
			$params['show_type'] = array('like','1');
			//算出已加入公告的游戏id的条数
			$result = Sdk_Service_Ad::getsBy($params);
			$temp = array();
			$temp_count =  array();
			foreach ($result as $val){
				$temp =  explode(',', $val['game_ids']) ;
				foreach ($temp as $va){
					if($va){
						$temp_count[$va] = $temp_count[$va] + 1;
					}
				}
			}
			//比较游戏id加入公告中的大于10
			$temp = array();
			$flag = 0 ;
			foreach ($info['ids'] as $val){
				if($temp_count[$val] >= 11){
					$temp[$val] = $val;
					$flag = 1;
				}
			}
			if($flag){
				$this->output(-1, '以下游戏的id为（'.implode(',', $temp).'）公告条数大于10，不能再添加',implode(',', $temp));
			}
		}
		
		//添加按钮
		if($info['action'] =='add'){
			if (!$info['ids']) $this->output(-1, '没有可操作的项.');
			$temp = $ad_info['game_temp_ids'];
			if($temp){
				$data['game_temp_ids'] = $temp.','.implode(',', $info['ids']);
			}else{
				$data['game_temp_ids'] = implode(',', $info['ids']);
			}
			$data['is_add'] = 1 ;
			$ret = Sdk_Service_Ad::updateAd($data, $info['adid']);
		//完成按钮
		}elseif($info['action'] =='finish'){
			//更新数据
			$data = unserialize($ad_info['temp_content']);
			$data['is_finish'] = 1;
			$data['is_add'] = 0;
			$data['game_ids'] = $ad_info['game_temp_ids'];
			$data['game_temp_ids'] = '';
			$data['game_num'] = count(explode(',', $data['game_ids']));
			$data['version_time'] = Common::getTime();
			$data['temp_content'] = '';
		    $this->checkTimeOverlap($info['adid'], $data, explode(',', $data['game_ids']));
			
			$ret = Sdk_Service_Ad::updateAd($data, $info['adid']);
		}
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.',$info);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addGameAction() {
	
		$ad_id = $this->getInput('adid');
		$show_type = $this->getInput('show_type');
		$page = $this->getInput('page');
		$s = $this->getInput(array('name','cooperate','game_id'));
		$params = $search = $info  = $current_games  = array();
		$s['cooperate'] = $s['cooperate'] ? $s['cooperate'] : 1 ;
		$s['adid'] =  $ad_id;
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1, 'editable'=>0));
		$categorys_id = Common::resetKey($categorys, 'id');
		$this->assign('categorys', $categorys);
	
		$search['status'] = 1;
		if ($s['name']) $search['name'] = array('LIKE',$s['name']);
		if ($s['cooperate']) $search['cooperate'] = $s['cooperate'];
		if ($s['game_id']) $search['id'] = $s['game_id'];
		
		list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $search, array('id'=>'DESC'));
		unset($search);
		if($games){
			foreach($games as $k=>$v){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$v['id']));
			}
		}
		
		//查询当前已添加的游戏
		$game_info  = Sdk_Service_Ad::getAd($ad_id);
		if($game_info && $game_info['game_temp_ids']){
			$ids = explode(',', $game_info['game_temp_ids']);
			$current_games = array();
			$current_game_name = array();
			foreach ($ids as $val){
				if($val){
					$current_games[$val] = $val;
					$current_name = Resource_Service_Games::getBy(array('id'=>$val));
					$current_game_name[$val] = $current_name['name'];
				}
			}
			$show_type = $game_info['show_type'];
		}
	
		
		$this->assign('current_games', $current_games);
		$this->assign('current_game_name', $current_game_name);
		$this->assign('total', $total);
		$this->assign('games', $info);
		$this->assign('s', $s);
		$this->assign('adid', $ad_id);
		$this->assign('show_type', $show_type);
		$url = $this->actions['addGameUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('page', $page);
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addGamePostAction() {
		$info = $this->getInput(array('ids','adid','action','name','cooperate','show_type'));
		if(!$info['adid']) $this->output(-1, '非法操作.');
		
		$ad_info = Sdk_Service_Ad::getAd($info['adid']);
		if(!$ad_info) $this->output(-1, '非法操作.');
		
		if($info['show_type'] != '2'){
			//算出已加入公告的游戏id的条数
			$time = common::getTime();
			$params['start_time'] = array('<=',$time);
			$params['end_time'] = array('>=',$time);
			$params['is_finish'] = 1;
			$params['status'] = 1;
			$params['show_type'] = array('like','1');
			//算出已加入公告的游戏id的条数
			$result = Sdk_Service_Ad::getsBy($params);
			$temp = array();
			$temp_count =  array();
			foreach ($result as $val){
				$temp =  explode(',', $val['game_ids']) ;
				foreach ($temp as $va){
					if($va){
						$temp_count[$va] = $temp_count[$va] + 1;
					}
				}
			}
			//比较游戏id加入公告中的大于10
			$temp = array();
			$flag = 0 ;
			foreach ($info['ids'] as $val){
				if($temp_count[$val] >= 10){
					$temp[$val] = $val;
					$flag = 1;
				}
			}
			//判断游戏id添加公告的条数
			if($flag){
				$this->output(-1, '以下游戏的id为（'.implode(',', $temp).'）公告条数大于10，不能再添加',implode(',', $temp));
			}
		}
		
		//添加按钮
		if($info['action'] =='add'){
			if (!$info['ids']) $this->output(-1, '没有可操作的项.');
			$temp = $ad_info['game_temp_ids'];
			if($temp){
				$data['game_temp_ids'] = $temp.','.implode(',', $info['ids']);
			}else{
				$data['game_temp_ids'] = implode(',', $info['ids']);
			}
			$data['is_add'] = 1;
			$ret = Sdk_Service_Ad::updateAd($data, $info['adid']);
		//完成按钮
		}elseif($info['action'] =='finish'){
		    $this->checkTimeOverlap($info['adid'], $ad_info, explode(',', $ad_info['game_temp_ids']));
			//更新数据
			$data['is_add'] = 0;
			$data['is_finish'] = 1;
			$data['game_ids'] = $ad_info['game_temp_ids'];
			$data['game_temp_ids'] = '';
			$data['version_time'] = Common::getTime();
			$data['game_num'] = count(explode(',', $data['game_ids']));
			$ret = Sdk_Service_Ad::updateAd($data, $info['adid']);
		}
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.',$info);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function deleteGameAction() {
		$adid = intval($this->getInput('adid'));
		$game_id = intval($this->getInput('gameid'));
		$info = Sdk_Service_Ad::getAd($adid);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');	
		
		$game_temp_ids = $info['game_temp_ids'];
		$temp = explode(',', $game_temp_ids);
		//移除掉
		foreach ($temp as $key=>$val){
			if($val == $game_id ){
				unset($temp[$key]);
			}
		}
	    $data['game_temp_ids'] = implode(',', $temp);
		$ret = Sdk_Service_Ad::updateAd($data, $adid);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功' ,array('adid'=>$adid) );
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function gameNumCheckAction() {
		$ids = $this->getPost('ids');
		$info = explode(',', html_entity_decode($ids));
		/* if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
			foreach ($info as $val){
			
		} */
		$act = $this->getPost('act');
		$num = 10;
		if($act == 'edit'){
			$num = 11;
		}
	
	    
		$time = common::getTime();
		$params['start_time'] = array('<=',$time);
		$params['end_time'] = array('>=',$time);
		$params['is_finish'] = 1;
		$params['status'] = 1;
		$params['show_type'] = array('like','1');
	
		//算出已加入公告的游戏id的条数
		$result = Sdk_Service_Ad::getsBy($params);
		$temp = array();
		$temp_count =  array();
		foreach ($result as $val){
			$temp =  explode(',', $val['game_ids']) ;
			foreach ($temp as $va){
				if($va){
					$temp_count[$va] = $temp_count[$va] + 1;
				}
			}
		}
		//比较游戏id加入公告中的大于10
		$temp = array();
		$flag = 0 ;
		foreach ($info as $val){
			if($temp_count[$val] >= $num){
				$temp[$val] = $val;
				$flag = 1;
			}
		}
	
		if($flag){
			$this->output(-1, '以下游戏的id为（'.implode(',', $temp).'）公告条数大于10，不能再添加',implode(',', $temp));
		}
	
		$this->output(0, '操作成功');
		exit;
	
	}
	

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['show_type'])$this->output(-1, '显示范围要选择.');
		if(!trim($info['title'])) $this->output(-1, '名称不能为空.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.'); 
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.'); 
		$info['start_time'] = strtotime($info['start_time']);
		if($info['start_time'] <= Common::getTime())$this->output(-1, '不能小于当前时间.'); 
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或等于结束时间.');
		if(!trim($info['ad_content'])) $this->output(-1, '内容不能为空.');
		return $info;
	}
	
	private function checkTimeOverlapForBatchOpen($ids) {
	    $dailogMsg = '';
	    foreach ($ids as $id) {
	        $info = Sdk_Service_Ad::getAd($id);
	        $info['status'] = 1;
	        $gameIds = explode(',', $info['game_ids']);
	        $isTimeOverLap = $this->getTimeOverLapGameId($id, $info, $gameIds);
	        if ($isTimeOverLap) {
	        	$onPass[$id] = $info['title'];
	        	$dailogMsg .= 'ID'.$id.'，';
	        }
	    }
	    
	    if ($dailogMsg) {
	    	$dailogMsg.='公告时间重叠，请去掉勾选后提交。';
	    	$this->output(-1, $dailogMsg);
	    }
	}
	
	/**
	 * 检查生效时间是否重复
	 * @author yinjiayan
	 * @param unknown $info
	 */
	private function checkTimeOverlap($adId, $info, $gameIds) {
	    $timeOverLapGameId = $this->getTimeOverLapGameId($adId, $info, $gameIds);
	    if ($timeOverLapGameId) {
	        $dailogMsg = '';
	        foreach ($timeOverLapGameId as $gameId) {
    	        $gameInfo = Resource_Service_Games::getGameAllInfo(array('id'=>$gameId));
    	        $dailogMsg .= '游戏ID'.$gameId.'('.$gameInfo['name'].')，';
	        }
	        $dailogMsg.='公告时间重叠，请去掉勾选后提交。';
	        $this->output(-1, $dailogMsg);
	    }
	}
	
	private function getTimeOverLapGameId($adId, $info, $gameIds) {
	    $timeOverLapGameId = array();
	    foreach ($gameIds as $gameId) {
	        if ($info['status'] == 1 && in_array('1', explode(',', $info['show_type']))) {
	            $params = array(
	                            'id' => array('<>', $adId),
	                            'status' => $info['status'],
	                            'show_type' => array('LIKE', '1'),
	                            'game_ids' => array('LIKE', $gameId),
	                            'start_time' => array('<=', $info['end_time']),
	                            'end_time' => array('>=', $info['start_time'])
	            );
	            $result = Sdk_Service_Ad::getsBy($params);
	            if ($result) {
	                $timeOverLapGameId[] = $gameId;
	            }
	        }
	    }
	    return $timeOverLapGameId;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Sdk_Service_Ad::getAd($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		//Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
		$result = Sdk_Service_Ad::deleteAd($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}


    
    /**
     * 编辑器中上传图片
     */
    public function uploadImgAction() {
    	$ret = Common::upload('imgFile', 'sdkimg');
    	if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
    	exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
    }
    
    /**
     *
     * Enter description here ...
     */
    public function uploadAction() {
    	$imgId = $this->getInput('imgId');
    	$this->assign('imgId', $imgId);
    	$this->getView()->display('common/upload1.phtml');
    	exit;
    }
    
    /**
     *
     * Enter description here ...
     */
    public function upload_postAction() {
    	$ret = Common::upload('img', 'sdkimg');
    	$imgId = $this->getPost('imgId');
    	$this->assign('code' , $ret['data']);
    	$this->assign('msg' , $ret['msg']);
    	$this->assign('data', $ret['data']);
    	$this->assign('imgId', $imgId);
    	$this->getView()->display('common/upload1.phtml');
    	exit;
    }
}
