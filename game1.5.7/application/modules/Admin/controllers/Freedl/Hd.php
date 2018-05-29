<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Freedl_HdController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Freedl_Hd/index',
		'addUrl' => '/Admin/Freedl_Hd/add',
		'addPostUrl' => '/Admin/Freedl_Hd/add_post',
		'addGameUrl' => '/Admin/Freedl_Hd/addgame',
		'addWarnUrl' => '/Admin/Freedl_Hd/addwarn',
		'addGamePostUrl' => '/Admin/Freedl_Hd/addgame_post',
		'addWarnPostUrl' => '/Admin/Freedl_Hd/addwarn_post',
		'editUrl' => '/Admin/Freedl_Hd/edit',
		'editWarnUrl' => '/Admin/Freedl_Hd/editwarn',
		'editWarnPostUrl' => '/Admin/Freedl_Hd/editwarn_post',
		'editPostUrl' => '/Admin/Freedl_Hd/edit_post',
		'deleteUrl' => '/Admin/Freedl_Hd/delete',
		'batchUpdateUrl'=>'/Admin/Freedl_Hd/batchUpdate',
		'blistUrl' => '/Admin/Freedl_Hd/bindex',
		'baddUrl' => '/Admin/Freedl_Hd/badd',
		'baddPostUrl' => '/Admin/Freedl_Hd/badd_post',
		'bcheckUrl'=>'/admin/Freedl_Hd/bcheck',
		'ulistUrl' => '/Admin/Freedl_Hd/uindex',
		'ualistUrl' => '/Admin/Freedl_Hd/uaindex',
		'uaexportUrl'=>'/admin/Freedl_Hd/uaexport',
		'uexportUrl'=>'/admin/Freedl_Hd/uexport',
		'udetailUrl' => '/Admin/Freedl_Hd/udetail',
		'udexportUrl'=>'/admin/Freedl_Hd/udexport',
		'uzdetailUrl' => '/Admin/Freedl_Hd/uzdetail',
		'uzexportUrl'=>'/admin/Freedl_Hd/uzexport',
		'hdetailUrl' => '/Admin/Freedl_Hd/hdetail',
		'hdexportUrl'=>'/admin/Freedl_Hd/hdexport',
		'flistUrl' => '/Admin/Freedl_Hd/findex',
		'fdetailUrl' => '/Admin/Freedl_Hd/fdetail',
		'foindexUrl' => '/Admin/Freedl_Hd/foindex',
		'fexportUrl' => '/Admin/Freedl_Hd/fexport',
		'fdexportUrl' => '/Admin/Freedl_Hd/fdexport',
		'fodexportUrl' => '/Admin/Freedl_Hd/fodexport',
		'uploadUrl' => '/Admin/Freedl_Hd/upload',
		'uploadPostUrl' => '/Admin/Freedl_Hd/upload_post',
		'uploadImgUrl' => '/Admin/Freedl_Hd/uploadImg'
	);
	
	public $perpage = 20;
	public $htype = array(
			2 => '全站免流量',
			1 => '专区免流量',
	);
	public $btype = array(
			1 => 'IMSI',
			//2 => '账号',
			3 => 'IMEI',
	);
	public $stype = array(
			1 => '由高到低',
			2 => '由低到高',
	);
	public $ustatu = array(
			1 => '下载等待',
			2 => '下载进行中',
		    3 => '下载暂停',
			4 => '下载完成',
			5 => '下载失败',
			6 => '下载取消',
	);
	public $regions = array(
			1 => '北京',
			2 => '天津',
			3 => '河北',
			4 => '山西',
			5 => '内蒙古',
			6 => '辽宁',
			7 => '吉林',
			8 => '黑龙江',
			9 => '上海',
			10 => '江苏',
			11 => '浙江',
			12 => '安徽',
			13 => '福建',
			14 => '江西',
			15 => '山东',
			16 => '河南',
			17 => '湖北',
			18 => '湖南',
			19 => '广东',
			20 => '广西',
			21 => '海南',
			22 => '四川',
			23 => '贵州',
			24 => '云南',
			25 => '西藏',
			26 => '陕西',
			27 => '甘肃',
			28 => '青海',
			29 => '宁夏',
			30 => '新疆',
			31 => '重庆',
	);
	public $operators = array(
			1 => '移动',
			2 => '联通',
			3 => '电信',
	);
	public $operator = array(
			'cmcc' => '移动',
			'cu' => '联通',
			'ctc' => '电信',
	);
	/**
	 * 活动首页列表
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','status','start_time','end_time','act','step_status'));
		//检查是否要回滚
		$user = $this->userInfo['username'];
		$this->_rollIndexBack($user);
		
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['title']) $params['title'] = array('LIKE',$s['title']);
		if ($s['start_time']) $params['start_time'] = array('>=',strtotime($s['start_time']));
		if ($s['end_time']) $params['end_time'] = array('<=',strtotime($s['end_time']));
		
		$orderBy = array('start_time'=>'DESC','id'=>'DESC');
		list($total, $hds) = Freedl_Service_Hd::getList($page, $this->perpage,$params ,$orderBy);

		
		$this->assign('s', $s);
		$this->assign('hds', $hds);
		$this->assign('htype', $this->htype);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 
	 * 编辑活动
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$info = Freedl_Service_Hd::getFreedl(intval($id));
		$this->assign('info', $info);
		$this->assign('htype', $this->htype);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function addAction() {
		$this->assign('htype', $this->htype);
	}
	
	

	//批量上线，下线，排序
	function batchUpdateAction() {
		$info = $this->getPost(array('action', 'ids', 'activity_id'));
		$user = $this->userInfo['username'];
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='addbk'){
			$ret = Freedl_Service_Blacklist::updateBlacklistStatus($info['ids'], 1);
		} else if($info['action'] =='cancel'){
			$ret = Freedl_Service_Blacklist::updateBlacklistStatus($info['ids'], 0);
		} else if($info['action'] =='add'){
			$ret = Freedl_Service_Hd::batchAddByGames($info['ids'],$info['activity_id'], $user);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 
	 * 添加活动信息
	 */
	public function add_postAction() {
		$info = $this->getPost(array('htype', 'title', 'num','total', 'status', 'img', 'f_img', 'start_time', 'end_time','download','consume', 'create_time','update_time','content', 'explain', 'total_warning', 'user_warning', 'email_warning'));
		$info = $this->_cookData($info);
		$info['update_time'] = Common::getTime();
		$info['create_time'] = Common::getTime();
		$ret = Freedl_Service_Hd::addFreedl($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->output(0,'操作成功',$ret);
		exit;
	}
	
	/**
	 * 添加免流量专区游戏
	 */
	public function addgameAction() {
		$page = $this->getInput('page');
		$search = $this->getInput(array('name','id','activity_id'));
		$params = $info = $taste_games = $current_games  = $currentIds= $tmp = array();
		//临时表添加回滚数据
		$user = $this->userInfo['username'];
		$this->_rollAddBack($search['activity_id'], $user);
		
		//游戏库分类列表
		$categorys = Resource_Service_Attribute::getsBy(array('at_type'=>1,'status'=>1, 'editable'=>0));
		$this->assign('categorys', $categorys);
		
		$params['status'] = 1;
		if ($search['name']) $params['name'] = array('LIKE',$search['name']);
		if ($search['id']) $params['id'] = $search['id'];
		list($total, $games) = Resource_Service_Games::addSearch($page, $this->perpage, $params, array('id'=>'DESC'));
		unset($params);
		if($games){
			foreach($games as $k=>$v){
				$info[] = Resource_Service_Games::getGameAllInfo(array('id'=>$v['id']));
			}
		}
		
		//查找当前页添加的游戏
		$current_games = Freedl_Service_Hd::getTmpGames(array('freedl_id'=>$search['activity_id'],'user'=>$user));
		if($current_games){
			$temp = array();
			foreach($current_games as $k=>$v){
				$info = Resource_Service_Games::getGameAllInfo(array('id'=>$v['game_id']));
				$temp[$v['game_id']] = $info['name'];
				$currentIds[] = $v['game_id'];
			}
		}
		
		$this->assign('total', $total);
		$this->assign('games', $info);
		$this->assign('search', $search);
		$this->assign('currentIds', $currentIds);
		$this->assign('current_games', $temp);
		$this->assign('activity_id', $search['activity_id']);
		$this->assign('act', $search['act']);
		$this->assign('user', $user);
		$url = $this->actions['addGameUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *  添加预警
	 */
	public function addwarnAction() {
		$activity_id = $this->getInput('activity_id');
		$info = Freedl_Service_Hd::getFreedl($activity_id);
		$act = $this->getInput('act');
		$this->assign('activity_id', $activity_id);
		$this->assign('act', $act);
		$this->assign('info', $info);
	}
	
	/**
	 *  编辑预警
	 */
	public function editwarnAction() {
		$activity_id = $this->getInput('activity_id');
		$act = $this->getInput('act');
		$info = Freedl_Service_Hd::getFreedl($activity_id);
		$blacklist_rule = unserialize($info['blacklist_rule']);
		$this->assign('info', $info);
		$this->assign('activity_id', $activity_id);
		$this->assign('act', $act);
		$this->assign('blacklist_rule', $blacklist_rule);
	}
	
	/**
	 * 预警设置
	 */
	public function addwarn_postAction() {
		$info = $this->getPost(array('activity_id','total_warning', 'user_warning', 'email_warning', 'status','warn_check_1','warn_check_2','warn_check_3','warn_check_4','warn_1','warn_2','warn_3','warn_4'));
		$act = $this->getPost('act');
		$info = $this->_cookWarnData($info);
		//黑名单设置值序列化处理
		$flow_warn = array(
				    1 => $info['warn_1'],
				    2 => $info['warn_check_2'] == 2 ? $info['warn_2'] : '',
				    3 => $info['warn_check_3'] == 3 ? $info['warn_3'] : '',
				    4 => $info['warn_check_4'] == 4 ? $info['warn_4'] : '',
				);
	    $blacklist_rule = serialize($flow_warn);
	    
	    //处理第一步，第二步的数据
	    $old_info = Freedl_Service_Hd::getFreedl($info['activity_id']);
	    $user = $this->userInfo['username'];
	    $rollData = Game_Service_Config::getValue($user.'freedHd_'.$info['activity_id']);
	    $rollData = unserialize($rollData);
	    //索引表中有添加的数据
	    if($rollData['add']){
	    	Freedl_Service_Hd::addMuFreedl($rollData['add']);
	    }
	    //索引表中有删除的数据
	    if($rollData['del']){
	    	foreach($rollData['del'] as $key=>$value){
	    		Freedl_Service_Hd::deleteByGame(array('freedl_id'=>$info['activity_id'],'game_id'=>$value));
	    	}
	    }
	    //获取第一步操作的数据
	    $step_one = array();
	    $config_key = $user.'steponefreedHd_'.$info['activity_id'];
	    $step_one = Freedl_Service_Hd::getByTmpHdFreedl(array('uerkey'=>$config_key));
	    $step_one['update_time'] = Common::getTime();
	    unset($step_one['uerkey']);
	    //更新第一步操作基本信息
	    Freedl_Service_Hd::updateFreedl($step_one, $info['activity_id']);
	    //更新第二步操作信息
	    list($num, $size) = Freedl_Service_Hd::compNum($info['activity_id']);
	    $ret = Freedl_Service_Hd::updateByFreedl(array('total_warning'=>$info['total_warning'],'user_warning'=>$info['user_warning'],'email_warning'=>$info['email_warning'],'blacklist_rule'=>$blacklist_rule,'status'=>$info['status'],'num'=>$num,'total'=>$size), array('id'=> $info['activity_id']));
	    
	    //如果黑名单规则有修改，原来是黑名单的移除
	    if(($blacklist_rule != $old_info['blacklist_rule']) && $act == 'edit'){
	    	$remove_time = Common::getTime();
	    	Freedl_Service_Blacklist::updateByBlacklist(array('status'=>0,'remove_time'=>$remove_time),array('activity_id'=>$info['activity_id']));
	    	//同步刷新缓存黑名单
	    	$rs = Freedl_Service_Permission::setBlackData();
	    }
	    
	    //正常操作完，清理临时表中的数据
	    Freedl_Service_Hd::deleteTmpFreedl(array('freedl_id'=>$info['activity_id'],'user'=>$user));
	    Freedl_Service_Hd::deleteTmpHd(array('uerkey'=>$config_key));
	    Game_Service_Config::delete(array('game_key'=>$config_key));
	    
	    if (!$ret) $this->output(-1, '操作失败');
	    $this->output(0, '操作成功.');
		
	}
	
	/**
	 * 
	 * 编辑活动基本信息
	 */
	public function edit_postAction() {
		$info = $this->getPost(array('id','htype', 'title','img', 'f_img', 'start_time', 'end_time','content', 'explain'));
		$info = $this->_cookData($info);
		//先查找原来的数据，添加临时表，以防回滚
		$user = $this->userInfo['username'];
		$info['uerkey'] = $user.'steponefreedHd_'.$info['id'];
		$ret = Freedl_Service_Hd::addTmpHd($info);
		$this->output(0,'操作成功',$info['id']);
		exit;		
	}
	
	/**
	 * 黑名单列表
	 */
	public function bindexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('name','status', 'start_time','end_time','uname','utype'));
		$params =  $tmp = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['utype'] && $s['name']) {
			if($s['utype'] == 1) {
				$params['imsi'] =  array('LIKE',trim($s['name']));
			}else if($s['utype'] == 3){
				$params['imei'] =  array('LIKE',trim($s['name']));
			}
		} else if(!$s['utype'] && $s['name']){
			$params['name'] = trim($s['name']);
		} else if($s['utype'] && !$s['name']){
			$params['utype'] = $s['utype'];
		}

		list($total, $result) = Freedl_Service_Blacklist::getsearchList($page, $this->perpage, $params);
	
	
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('btype', $this->btype);
		$url = $this->actions['blistUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　添加和名单
	 * Enter description here ...
	 */
	public function baddAction() {
		$this->assign('btype', $this->btype);
	}
	
	/**
	 *　添加黑名单提交
	 * Enter description here ...
	 */
	public function badd_postAction() {
		$info = $this->getPost(array('utype', 'name','activity_id', 'content'));
		$info = $this->_cookBlacklistData($info);
		$info['create_time'] = Common::getTime();
		$parmes = array();
		if($info['utype'] == 1) {
			$info['imsi'] = $info['name'];
			$info['uname'] = "";
			$info['imei'] = "";
			$parmes['imsi'] = $info['name'];
			$parmes['utype'] = 1;
			$str = 'imsi';
		} else {
			$info['imsi'] = "";
			$info['uname'] = "";
			$info['imei'] = $info['name'];
			$parmes['imei'] = $info['name'];
			$parmes['utype'] = 3;
			$str = 'imei';
		}
		$info['status'] = 1;
		$info['num'] = 1;
		$info['create_time'] = Common::getTime();
		$info['name'] = $this->userInfo['username'];
		$ret = Freedl_Service_Blacklist::getByBlacklist($parmes);
		
		if($ret) $this->output(-1, '该'.$str.'在黑名单中已经存在，不能重复提交!'); 
		$result = Freedl_Service_Blacklist::addBlacklist($info);
		//同步刷新缓存黑名单
		$rs = Freedl_Service_Permission::setBlackData();
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 单条移除，添加黑名单操作
	 */
	public function bcheckAction(){
		$id = $this->getInput('id');
		$status = $this->getInput('status');
		$curr_status = ($status ? 0 : 1);
		$info = Freedl_Service_Blacklist::getBlacklist($id);
		$remove_time = '';
		if(!$curr_status) {
			//解除黑名单时间
			$remove_time = Common::getTime();
		}
		
		
		$params = array();
		if($info['utype'] == 1) {
			$params['imsi'] = $info['imsi'];
			$params['utype'] = 1;
		} else {
			$params['imei'] = $info['imei'];
			$params['utype'] = 3;
		}
		
		//查找该记录对应的所有IMSI或者IMEI，然后全部更新
		$blks = Freedl_Service_Blacklist::getsByBlacklist($params);
		foreach($blks as $key=>$value){
			$num = $value['num'];
			if($curr_status) $num = $value['num'] + 1;
			$ret = Freedl_Service_Blacklist::updateBlacklist(array('status'=>$curr_status,'remove_time'=>$remove_time,'num'=>$num),$value['id']);
		}
		
		//同步刷新缓存黑名单
		$rs = Freedl_Service_Permission::setBlackData();
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 用户统计列表
	 */
	public function uindexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('imsi','uname', 'imei','sort'));
		$params = array();
		if ( $s['imsi'] ) $params['imsi'] = array('LIKE',trim($s['imsi']));
		if ( $s['uname'] ) $params['uname'] = array('LIKE',trim($s['uname']));
		if ( $s['imei'] )  $params['imei'] = array('LIKE',trim($s['imei']));
		if($s['sort'] == 1 || !$s['sort']) {
			$s['sort'] = 1;
			$orderBy = array('total_consume'=>'DESC','create_time'=>'DESC');
		} else {
			$orderBy = array('total_consume'=>'ASC','create_time'=>'DESC');
		}
		list($total, $result) = Freedl_Service_Imsitotal::getList($page, $this->perpage, $params, $orderBy);
		
		
		
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('stype', $this->stype);
		$url = $this->actions['ulistUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　用户统计列表导出数据
	 * Get phrase list
	 */
	public function uexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	    $s = $this->getInput(array('imsi','uname', 'imei','sort'));
		$params = array();
		if ( $s['imsi'] ) $params['imsi'] = array('LIKE',trim($s['imsi']));
		if ( $s['uname'] ) $params['uname'] = array('LIKE',trim($s['uname']));
		if ( $s['imei'] )  $params['imei'] = array('LIKE',trim($s['imei']));
		if($s['sort'] == 1 || !$s['sort']) {
			$s['sort'] = 1;
			$orderBy = array('total_consume'=>'DESC','create_time'=>'DESC');
		} else {
			$orderBy = array('total_consume'=>'ASC','create_time'=>'DESC');
		}
		
		//excel-head
		$filename = '用户统计_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('编号','IMSI','累计消耗','IMEI','用户昵称','用户名称','机型','客户端名称', '版本', 'Anroid版本'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_Imsitotal::getList($page, $this->perpage, $params, $orderBy);
			if (!$result) break;
		
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						$value['id'],
						"\t".$value['imsi'],
						$value['total_consume'],
						$value['imei'],
						$value['nickname'],
						$value['uname'],
						$value['model'],
						($value['client_pkg'] == "gn.com.android.gamehall" ? "游戏大厅" : "艾米游戏"),
						$value['version'],
						$value['sys_version']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	/**
	 * 用户统计--用户明细
	 */
	public function udetailAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('id','imsi','game_name', 'game_id','activity_id','year_month','status','activity_name'));
		$params = array();
		
		if ( $s['imsi'] ) $params['imsi'] = array('LIKE',trim($s['imsi']));
		if ( $s['game_name'] ) $params['game_name'] = array('LIKE',trim($s['game_name']));
		if ( $s['game_id'] )  $params['game_id'] = trim($s['game_id']);
		if ( $s['activity_id'] )  $params['activity_id'] = trim($s['activity_id']);
		if ( $s['year_month'] )  $params['year_month'] = date('Y-m', strtotime($s['year_month']));
		if ( $s['status'] )  $params['status'] = $s['status'];
		if ( $s['activity_name'] ) $params['activity_name'] = array('LIKE',trim($s['activity_name']));
		
		
		$info = Freedl_Service_UserImsi::getBy(array('imsi'=>$s['imsi']),array('create_time'=>'DESC'));	
		list($total, $result) = Freedl_Service_Userinfo::getList($page, $this->perpage, $params);
		list($uinfo,$consum) = Freedl_Service_Usertotal::getCount(array('imsi'=>$s['imsi']));
		
		$this->assign('ustatu', $this->ustatu);
		$this->assign('info', $info);
		$this->assign('uinfo', $uinfo);
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('consum', $consum);
		$this->assign('s', $s);
		$url = $this->actions['udetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		
	}
	
	/**
	 * 用户统计--账号明细
	 */
	public function uzdetailAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('id','imsi'));
		$params = array();
		$params['imsi'] = array('LIKE',trim($s['imsi']));
		
		list($total, $result) = Freedl_Service_UserImsi::getList($page, $this->perpage, $params, array('create_time'=>'DESC'));
		list(,$consum) = Freedl_Service_Usertotal::getCount(array('imsi'=>trim($s['imsi'])));
		
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('consum', $consum);
		$this->assign('s', $s);
		$url = $this->actions['uzdetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 * 用户统计--账号明细导出
	 */
	public function uzexportAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('id','imsi'));
		$params = array();
		$params['imsi'] = array('LIKE',trim($s['imsi']));
		
		//excel-head
		$filename = '账号明细_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('活动ID','UUID','IMEI','用户名称','用户昵称','机型','客户端名称', '版本', 'Anroid版本'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_UserImsi::getList($page, $this->perpage, $params, array('create_time'=>'DESC'));
			if (!$result) break;
		
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						$value['activity_id'],
						$value['uuid'],
						$value['imei'],
						$value['uname'],
						$value['nickname'],
						$value['model'],
						($value['client_pkg'] == "gn.com.android.gamehall" ? "游戏大厅" : "艾米游戏"),
						$value['version'],
						$value['sys_version']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	
	/**
	 *　用户统计--用户明细导出数据
	 * Get phrase list
	 */
	public function udexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$s = $this->getInput(array('id','imsi','game_name', 'game_id','activity_id','year_month','status','activity_name'));
		$params = array();
		
		if ( $s['imsi'] ) $params['imsi'] = array('LIKE',trim($s['imsi']));
		if ( $s['game_name'] ) $params['game_name'] = array('LIKE',trim($s['game_name']));
		if ( $s['game_id'] )  $params['game_id'] = trim($s['game_id']);
		if ( $s['activity_id'] )  $params['activity_id'] = trim($s['activity_id']);
		if ( $s['year_month'] )  $params['year_month'] = date('Y-m', strtotime($s['year_month']));
		if ( $s['status'] )  $params['status'] = $s['status'];
		if ( $s['activity_name'] ) $params['activity_name'] = array('LIKE',trim($s['activity_name']));
	
		//excel-head
		$filename = '用户明细_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('月份','游戏名称','游戏ID','活动ID','活动名称','下载时间','状态','游戏大小','流量消耗'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_Userinfo::getList($page, $this->perpage, $params);
			if (!$result) break;
	
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						"\t".$value['year_month'],
						preg_replace("/,/", "，", $value['game_name']),
						$value['game_id'],
						$value['activity_id'],
						preg_replace("/,/", "，", $value['activity_name']),
						date('Y-m-d H:i:s', $value['create_time']),
						$this->ustatu[$value['status']],
						$value['size'],
						$value['consume']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	/**
	 * 用户统计 -- 全部用户
	 */
	public function uaindexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		list($total, $result) = Freedl_Service_Users::getUsersList($page, $this->perpage);
	
	
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('operator', $this->operator);
		$this->assign('regions', $this->regions);
		$url = $this->actions['ualistUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	
	/**
	 *　用户统计 -- 全部用户导出数据
	 * Get phrase list
	 */
	public function uaexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		//excel-head
		$filename = '全部用户_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('地区','人数'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_Users::getUsersList($page, $this->perpage);
			if (!$result) break;
	
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						$this->regions[$value['region']].' '.$this->operator[$value['operator']],
						$value['num']."\t");
			}
			Util_Csv::putData($tmp);
			if($total <= $this->perpage) break;
			if($total > $this->perpage) $page ++;
		}
		exit;
	}
	
	/**
	 * 免流量活动-- 活动明细
	 */
	public function hdetailAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('activity_id','imsi','game_name', 'game_id','status'));
		$params = array();
	
		if ( $s['imsi'] ) $params['imsi'] = array('LIKE',trim($s['imsi']));
		if ( $s['game_name'] ) $params['game_name'] = array('LIKE',trim($s['game_name']));
		if ( $s['game_id'] )  $params['game_id'] = trim($s['game_id']);
		if ( $s['activity_id'] )  $params['activity_id'] = trim($s['activity_id']);
		if ( $s['status'] )  $params['status'] = $s['status'];
		$params['activity_id'] = trim($s['activity_id']);
	
	
		$info = Freedl_Service_Hd::getFreedl($s['activity_id']);
	
		list($total, $result) = Freedl_Service_Userinfo::getList($page, $this->perpage, $params);
		$consum = Freedl_Service_Userinfo::getCount($params);
	
		$this->assign('ustatu', $this->ustatu);
		$this->assign('info', $info);
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('consum', $consum);
		$this->assign('s', $s);
		$url = $this->actions['hdetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　免流量活动--活动明细导出数据
	 * Get phrase list
	 */
	public function hdexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
	
		$s = $this->getInput(array('activity_id','imsi','game_name', 'game_id','status'));
		$params = array();
	
		if ( $s['imsi'] ) $params['imsi'] = array('LIKE',trim($s['imsi']));
		if ( $s['game_name'] ) $params['game_name'] = array('LIKE',trim($s['game_name']));
		if ( $s['game_id'] )  $params['game_id'] = trim($s['game_id']);
		if ( $s['activity_id'] )  $params['activity_id'] = trim($s['activity_id']);
		if ( $s['status'] )  $params['status'] = $s['status'];
	
		//excel-head
		$filename = '活动明细_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('序号','IMSI','游戏名称','游戏ID','下载时间','状态','游戏大小','流量消耗','IMEI','用户昵称','用户名称','机型','客户端名称', '版本', 'Anroid版本'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_Userinfo::getList($page, $this->perpage, $params);
			if (!$result) break;
			
	
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						$value['id'],
						"\t".$value['imsi'],
						preg_replace("/,/", "，", $value['game_name']),
						$value['game_id'],
						date('Y-m-d H:i:s', $value['create_time']),
						$this->ustatu[$value['status']],
						$value['size'],
						$value['consume'],
						$value['imei'],
						$value['nickname'],
						$value['uname'],
						$value['model'],
						($value['client_pkg'] == "gn.com.android.gamehall" ? "游戏大厅" : "艾米游戏"),
						$value['version'],
						$value['sys_version']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	/**
	 * 流量统计-- 列表 
	 */
	public function findexAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('start_time','end_time'));
		$params = array();
	
		if ($s['start_time']) $params['year_month'][0] = array('>=', date('Y-m',strtotime($s['start_time'])));
		if ($s['end_time'] && $s['start_time']) $params['year_month'][1] = array('<=', date('Y-m',strtotime($s['end_time'])));

		list($total, $result) = Freedl_Service_Monthtotal::getList($page, $this->perpage, $params);
		$consum = Freedl_Service_Monthtotal::getCount($params);

		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('consum', $consum);
		$this->assign('s', $s);
		$url = $this->actions['flistUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　流量统计-- 列表导出数据
	 * Get phrase list
	 */
	public function fexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('start_time','end_time'));
		$params = array();
	
		if ($s['start_time']) $params['year_month'][0] = array('>=', date('Y-m',strtotime($s['start_time'])));
		if ($s['end_time'] && $s['start_time']) $params['year_month'][1] = array('<=', date('Y-m',strtotime($s['end_time'])));
		
		//excel-head
		$filename = '流量统计_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('月份','本月消耗'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_Monthtotal::getList($page, $this->perpage, $params);
			if (!$result) break;
	
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						"\t".$value['year_month'],
						$value['month_consume']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	/**
	 * 流量统计-- 内容明细
	 * Get phrase list
	 */
	public function fdetailAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('year_month','month_consume','game_id','game_name'));
		$params = array();
		
		if ( $s['game_name'] ) $params['game_name'] = array('LIKE',trim($s['game_name']));
		if ( $s['game_id'] )  $params['game_id'] = trim($s['game_id']);
		$params['year_month'] = $s['year_month'];
		
		list($total, $result) = Freedl_Service_Gametotal::getList($page, $this->perpage, $params);
		foreach($result as $key=>$value){
			$info = $act_info = array();
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
			$act_info = Freedl_Service_Hd::getFreedl($value['activity_id']);
			$info['start_time'] = $act_info['start_time'];
			$info['end_time'] = $act_info['end_time'];
			$tmp[$value['id']] = $info;
		}
		
		$mon_info = Freedl_Service_Monthtotal::getBy(array('year_month'=>$s['year_month']),$orderBy = array());
		
		
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('month_consume', $month_consume);
		$this->assign('s', $s);
		$this->assign('games', $tmp);
		$this->assign('mon_info', $mon_info);
		$url = $this->actions['fdetailUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	} 
	
	/**
	 *　流量统计-- 内容明细导出数据
	 * Get phrase list
	 */
	public function fdexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('year_month','month_consume','game_id','game_name'));
		$params = array();
		
		if ( $s['game_name'] ) $params['game_name'] = array('LIKE',trim($s['game_name']));
		if ( $s['game_id'] )  $params['game_id'] = trim($s['game_id']);
		$params['year_month'] = $s['year_month'];
	
		//excel-head
		$filename = '内容明细_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('游戏ID','游戏名称','版本','大小','生效时间','消耗流量'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_Gametotal::getList($page, $this->perpage, $params);
			if (!$result) break;
			
			$tmp = array();
			foreach ($result as $key=>$value) {
				$info = $act_info = array();
				$info = Resource_Service_Games::getGameAllInfo(array('id'=>$value['game_id']));
				$act_info = Freedl_Service_Hd::getFreedl($value['activity_id']);
				
				$tmp[] = array(
						$value['game_id'],
						preg_replace("/,/", "，", $value['game_name']),
						$info['version'],
						$info['size'],
						date('Y-m-d H:i:s', $act_info['start_time']).'\r'.date('Y-m-d H:i:s', $act_info['end_time']),
						$value['month_consume']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	/**
	 * 流量统计-- 运营商明细
	 * Get phrase list
	 */
	public function foindexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$s = $this->getInput(array('year_month','month_consume','region','operator'));
		$params = array();
		
		if ( $s['operator'] ) $params['operator'] = trim($s['operator']);
		if ( $s['region'] )  $params['region'] = trim($s['region']);
		$params['year_month'] = $s['year_month'];
		
		list($total, $result) = Freedl_Service_Operatortotal::getList($page, $this->perpage, $params);

		$mon_info = Freedl_Service_Monthtotal::getBy(array('year_month'=>$s['year_month']),$orderBy = array());
		
		
		$this->assign('result', $result);
		$this->assign('total', $total);
		$this->assign('s', $s);
		$this->assign('mon_info', $mon_info);
		$this->assign('operator', $this->operator);
		$this->assign('regions', $this->regions);
		$url = $this->actions['foindexUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		
	}
	
	/**
	 * 流量统计-- 运营商导出
	 */
	public function fodexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		
		$s = $this->getInput(array('year_month','month_consume','region','operator'));
		$params = array();
		
		if ( $s['operator'] ) $params['operator'] = trim($s['operator']);
		if ( $s['region'] )  $params['region'] = trim($s['region']);
		$params['year_month'] = $s['year_month'];

		//excel-head
		$filename = '运营商明细_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('地区','运营商','本月消耗'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Freedl_Service_Operatortotal::getList($page, $this->perpage, $params);
			if (!$result) break;
		
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						$this->regions[$value['region']],
						$this->operator[$value['operator']],
						$value['month_consume']."\t");
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		exit;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '活动名称不能为空.'); 
		if(!$info['explain']) $this->output(-1, '活动简介不能为空.');
		if($info['explain'] && strlen($info['explain']) > 60 ) $this->output(-1, '活动简介不能超过50个字符.');
		if(!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能大于或者等于结束时间.');
		if(!$info['img']) $this->output(-1, '图片不能为空.');
		if($info['htype'] == 1 && !$info['f_img']) $this->output(-1, '免流量专区图片不能为空.');
		if(!$info['content']) $this->output(-1, '活动规则不能为空.');
		return $info;
	}
	
	/**
	 * 
	 * @param array $info
	 * @return array $info
	 */
	private function _cookWarnData($info) {
		if(!$info['total_warning']) $this->output(-1, '整体预警额度不能为空.');
		if(($info['total_warning'] < 0 || !preg_match("/^[0-9]*[1-9][0-9]*$/",$info['total_warning'])) && $info['total_warning']){
			$this->output(-1, '整体预警额度必须填写正整数.');
		}
		if(!$info['user_warning']) $this->output(-1, '用户预警额度不能为空.');
		if(($info['user_warning'] < 0 || !preg_match("/^[0-9]*[1-9][0-9]*$/",$info['user_warning'])) && $info['user_warning']){
			$this->output(-1, '用户预警额度必须填写正整数.');
		}
		if(!$info['email_warning']) $this->output(-1, '预警邮箱不能为空.');
		if(!$info['warn_1']) $this->output(-1, '1天内下载同一个内容不能为空.');
		if(!$info['warn_2'] && $info['warn_check_2'] == 2) $this->output(-1, '1小时消耗免流量额度不能为空.');
		if(!$info['warn_3'] && $info['warn_check_3'] == 3) $this->output(-1, '1天内消耗免流量额度不能为空.');
		if(!$info['warn_4'] && $info['warn_check_4'] == 4) $this->output(-1, '1月内消耗免流量额度不能为空.');
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookBlacklistData($info) {
		if($info['utype'] == 1  && !$info['name']) $this->output(-1, 'IMSI不能为空.');
		if($info['utype'] == 3  && !$info['name']) $this->output(-1, 'IMEI不能为空.');
		if(!$info['activity_id']) $this->output(-1, '活动ID不能为空.');
		if($info['activity_id']) {
			$ret = Freedl_Service_Hd::getFreedl($info['activity_id']);
			if(!$ret) $this->output(-1, '活动ID不存在.');
		}
		if(!$info['content']) $this->output(-1, '添加原因不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$activity_id = intval($this->getInput('activity_id'));
		$game_id = intval($this->getInput('game_id'));
		$user = $this->getInput('user');
		$ret = Freedl_Service_Hd::getTmpGames(array('freedl_id'=>$activity_id,'game_id'=>$game_id,'user'=>$user));
		
		//设置删除回滚的数据
		$rollData = Game_Service_Config::getValue($user.'freedHd_'.$activity_id);
		$rollData = unserialize($rollData);
		if($rollData['del']){
			array_push($rollData['del'], $game_id);
		} else {
			$rollData['del'] = array();
			array_push($rollData['del'], $game_id);
		}
		$rollData['del'] = array_unique($rollData['del']);
		$rollData = serialize($rollData);
		Game_Service_Config::setValue($user.'freedHd_'.$activity_id, $rollData);
		
		$result = Freedl_Service_Hd::deleteTmpFreedl(array('freedl_id'=>$activity_id,'game_id'=>$game_id,'user'=>$user));
		$rets = Freedl_Service_Hd::getTmpGames(array('freedl_id'=>$activity_id,'user'=>$user));
		//全部删除完
		if(!$rets){
			Game_Service_Config::setValue($user.'addfreedHd_'.$activity_id, 1);
		}
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function upload_postAction() {
		$ret = Common::upload('img', 'hd');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 编辑器中上传图片
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'hd');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}
	
	/**
	 * 添加回滚操作
	 * @param string $act
	 * @param boolen $step_status
	 * @param int $activity_id
	 */
	private function _rollIndexBack($user) {
			//回滚完，清除临时表数据
			Game_Service_Config::delete(array('game_key'=>array('LIKE',$user.'freedHd')));
			Game_Service_Config::delete(array('game_key'=>array('LIKE',$user.'addfreedHd')));
			Freedl_Service_Hd::deleteTmpHd(array('uerkey'=>array('LIKE',$user.'steponefreedHd')));
			Freedl_Service_Hd::deleteTmpFreedl(array('user'=>$user));
			//活动信息未完成删除
			$rets = Freedl_Service_Hd::getsByFreedl(array('total_warning'=>0));
			if($rets){
				foreach($rets as $key=>$value){
					Freedl_Service_Hd::deleteFreedl($value['id']);
					Freedl_Service_Hd::deleteByGame(array('freedl_id'=>$value['id']));
				}
			}
	}
	
	/**
	 * 批量添加游戏回滚操作
	 * @param string $act
	 * @param boolen $step_status
	 * @param int $activity_id
	 */
	private function _rollAddBack($activity_id, $user) {
		//把当前数据插入临时表以防回滚
		$curr_games = Freedl_Service_Hd::getGames(array('freedl_id'=>$activity_id));
		//查找临时表中的数据
		$tmp_games = Freedl_Service_Hd::getTmpGames(array('freedl_id'=>$activity_id,'user'=>$user));
		$flag = Game_Service_Config::getValue($user.'addfreedHd_'.$activity_id);
		//当前数据如果有，添加到临时表，以备回滚
		if(!$flag && $curr_games && !$tmp_games){
			foreach($curr_games as $key=>$value){
				$tmp[] = array(
						'id'=>'',
						'user' => $user,
						'sort' => '',
						'status'=> $value['status'],
						'freedl_id' => $activity_id,
						'game_id'=> $value['game_id'],
				);
			}
			if($tmp) Freedl_Service_Hd::addTmpFreedl($tmp);
		}
	}
	
}
