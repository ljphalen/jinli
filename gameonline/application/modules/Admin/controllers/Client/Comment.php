<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_CommentController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Comment/index',
		'manageUrl' => '/Admin/Client_Comment/manage',
		'plistUrl' => '/Admin/Client_Comment/pindex',
		'paddUrl' => '/Admin/Client_Comment/padd',
		'paddPostUrl' => '/Admin/Client_Comment/padd_post',
		'peditUrl' => '/Admin/Client_Comment/pedit',
		'peditPostUrl' => '/Admin/Client_Comment/pedit_post',
		'slistUrl' => '/Admin/Client_Comment/sindex',
		'saddUrl' => '/Admin/Client_Comment/sadd',
		'saddPostUrl' => '/Admin/Client_Comment/sadd_post',
		'seditUrl' => '/Admin/Client_Comment/sedit',
		'seditPostUrl' => '/Admin/Client_Comment/sedit_post',
		'sexportUrl'=>'/admin/Client_Comment/sexport',
		'addUrl' => '/Admin/Client_Comment/add',
		'addPostUrl' => '/Admin/Client_Comment/add_post',
		'editUrl' => '/Admin/Client_Comment/edit',
		'editPostUrl' => '/Admin/Client_Comment/edit_post',
		'pexportUrl'=>'/admin/Client_Comment/pexport',
		'importUrl'=>'/admin/Client_Comment/import',
		'exportUrl'=>'/admin/Client_Comment/export',
		'mexportUrl'=>'/admin/Client_Comment/mexport',
		'logUrl'=>'/admin/Client_Comment/log',
		'lexportUrl'=>'/admin/Client_Comment/lexport',
		'manageUrl' => '/Admin/Client_Comment/manage',
		'blistUrl' => '/Admin/Client_Comment/bindex',
		'baddUrl' => '/Admin/Client_Comment/badd',
		'baddPostUrl' => '/Admin/Client_Comment/badd_post',
		'beditUrl' => '/Admin/Client_Comment/bedit',
		'beditPostUrl' => '/Admin/Client_Comment/bedit_post',
		'bexportUrl'=>'/admin/Client_Comment/bexport',
		'batchUpdateUrl'=>'/Admin/Client_Comment/batchUpdate',
		'singleUpdateUrl'=>'/Admin/Client_Comment/singleUpdate',
		'importPostUrl'=>'/admin/Client_Comment/import_post',
		'checkUrl' => '/Admin/Client_Comment/check',
		'singleUrl'=>'/Admin/Client_Comment/single',
		'batchdelUrl'=>'/Admin/Client_Comment/batchdel',
	);
	
	public $perpage = 20;
	public $comment_status = array(
			1 => '未审核',
			2 => '审核不通过',
			3 => '审核通过',
	);
	public $utype = array(
			1 => '账号',
			2 => 'IMEI',
	);
	
	/**
	 * 评论审核列表
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('id','status', 'start_time','end_time','uname','name','nickname','is_sensitive','utype','is_blacklist'));
		$params =  $tmp = $search = $game_names = array();
		if (!$s['status'])  $params['status'] = 1;
		if($s['status'] == 3){
			 $params['status'] = array('IN',array(1,2));
		} else if($s['status'] && $s['status'] != 3){
			$params['status'] = $s['status'];
		}
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['nickname']) $params['nickname'] = array('LIKE',trim($s['nickname']));
		if ($s['id']) $params['game_id'] = $s['id'];
		if ($s['is_sensitive']) $params['is_sensitive'] = $s['is_sensitive'] -1;
		if ($s['utype']) $params['utype'] = $s['utype'];
		
		if($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
		    $games = Resource_Service_Games::getGamesByGameNames($search); 
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
			if($s['id']) $params['game_id'] = $s['id'];
		}
		$params['is_del'] = 0;
		$params['is_blacklist'] = 0;
		if ($s['is_blacklist']) unset($params['is_blacklist']);
		$sensitives = Client_Service_Sensitive::getsBySensitives(array('status'=>1));
		
		list(, $phrases) = Client_Service_Phrase::getsByPhrase(array('status'=>1),array('id'=>'DESC','create_time'=>'DESC'));
		$phrases = Common::resetKey($phrases, 'id');
		
		list($total, $result) = Client_Service_Comment::getList($page, $this->perpage, $params);
		
		foreach($result as $k=>$v){
			$temp = array();
			$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
			$game_names[$v['game_id']] = $temp['name'];
		}
		
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('result', $result);
		$this->assign('phrases', $phrases);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('sensitives', $sensitives);
		$this->assign('comment_status', $this->comment_status);
		$this->assign('utype', $this->utype);
		$this->assign('game_names', $game_names);
	}
	
	/**
	 *评论管理列表
	 * Enter description here ...
	 */
	public function manageAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('id','start_time','end_time','uname','name','is_top','utype','nickname'));
		$params =  $tmp = $search = $game_names = array();
		if ($s['start_time']) $params['check_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['check_time'][1] = array('<=', strtotime($s['end_time']));
		if($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if($s['nickname']) $params['nickname'] = array('LIKE',trim($s['nickname']));
		if($s['id']) $params['game_id'] = $s['id'];
		if($s['is_top']) $params['is_top'] = $s['is_top'] - 1;
		if($s['utype']) $params['utype'] = $s['utype'];
	
		if($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
			$games = Resource_Service_Games::getGamesByGameNames($search);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
			if($s['id']) $params['game_id'] = $s['id'];
		}
		$params['status'] = 3;
		$params['is_del'] = 0;
		list($total, $result) = Client_Service_Comment::getList($page, $this->perpage, $params);
	
		foreach($result as $k=>$v){
			$temp = array();
			$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
			$game_names[$v['game_id']] = $temp['name'];
		}
	
		$url = $this->actions['manageUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('result', $result);
		$this->assign('phrases', $phrases);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('comment_status', $this->comment_status);
		$this->assign('utype', $this->utype);
		$this->assign('game_names', $game_names);
	}
	
	/**
	 * 检查是否有重复置顶
	 */
	public function checkAction() {
		$tmp = $game_info = $temp = $cmm_ids = array();
		$flag = 0;
		$ids = $this->getInput('ids');
		$cm_ids = explode(',',html_entity_decode($ids));
		
		foreach($cm_ids as $k=>$v){
			if($v){
				$temp[] = $v;
			}
		}
		$cm_ids = $temp;
		foreach($cm_ids as $k=>$v){
			$info = Client_Service_Comment::getComment($v);
			$ret = Client_Service_CommentLog::getsByLog(array('game_id'=>$info['game_id'],'status'=>3,'is_del'=>0));
			$top_ret = Client_Service_CommentLog::getBy(array('game_id'=>$info['game_id'],'status'=>3,'is_del'=>0,'is_top'=>1));
			$cmm_ids[$info['game_id']][] = $v;
			if(count($ret) >= 2 && count($cmm_ids[$info['game_id']]) >=2) {
				$flag = 1;
				$str = '同一个游戏只能有1条置顶评论.';
				break;
			}
			if($top_ret){
				$game_info  = Resource_Service_Games::getResourceByGames($info['game_id']);
				$tmp[] = $game_info['name'];
			}
		}
		$ids = implode(',',$cm_ids);
		if($flag == 1){
			$this->_output('-1', $str, $ids, $flag);
		} else if($tmp){
			$flag = 3;
			$str = '你选择了 '.count($cm_ids).' 条评论置顶，其中选择评论对应的 '.implode(',',array_unique($tmp)).' 已有置顶评论，是否更新置顶评论？';
			$this->_output('0', $str, $ids, $flag);
		} else {
			$flag = 2;
			$str = '';
			$this->_output('0', $str, $ids, $flag);
		}
		
	}
	
	/**
	 * 批量置顶
	 */
	public function singleAction() {
		$tmp = $game_info = $game_ids = $exist_ids = array();
		$ids = $this->getInput('ids');
	
		$cmm_ids = explode(',',html_entity_decode($ids));
		if($cmm_ids) {
			$ret = Client_Service_Comment::topUpdateComments($cmm_ids,1);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $code
	 * @param unknown_type $msg
	 * @param unknown_type $data
	 */
	private function _output($code, $msg = '', $data = array(), $flag = array()) {
		header("Content-type:text/json");
		exit(json_encode(array(
				'success' => $code == 0 ? true : false ,
				'msg' => $msg,
				'data' => $data,
				'flag' => $flag
		)));
	}
	
	/**
	 *　词库管理列表
	 * Enter description here ...
	 */
	public function pindexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','status', 'start_time','end_time','uname'));
		$params =  $tmp = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if($s['title']) $params['title'] = array('LIKE',trim($s['title']));
		if($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));

		list($total, $result) = Client_Service_Phrase::getList($page, $this->perpage, $params);
		foreach($result as $key=>$value){
			list($tot,) = Client_Service_Sensitive::getsBySensitive(array('stype'=>$value['id']));
			$tmp[$value['id']] = $tot;
		}

		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('sensitives', $tmp);
		$url = $this->actions['plistUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　
	 * Enter description here ...
	 */
	public function paddAction() {
	}
	
	/**
	 *　添加词库
	 * Enter description here ...
	 */
	public function padd_postAction() {
		$info = $this->getPost(array('title', 'status'));
		$info = $this->_cookPhraseData($info);
		$info['uname'] = $this->userInfo['username'];
		$info['create_time'] = Common::getTime();
		$result = Client_Service_Phrase::addPhrase($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 *　编辑词库
	 * edit an Phrase
	 */
	public function peditAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Phrase::getPhrase(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *　编辑词库提交
	 * Enter description here ...
	 */
	public function pedit_postAction() {
		$info = $this->getPost(array('id', 'title', 'status'));
		$info = $this->_cookPhraseData($info);
		$info['uname'] = $this->userInfo['username'];
		$ret = Client_Service_Phrase::updatePhrase($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 导入敏感词
	 */
	public function importAction() {
	}
	
	/**
	 * 导入敏感词提交
	 */
	public function import_postAction() {
		header("Content-Type: text/html; charset=UTF-8");
		$ext = strtolower(end(explode(".", $_FILES['pimportcsv']['name'])));
		if($ext != "csv") $this->output(-1, '导入文件格式非法，必须是csv.');
		if (!empty($_FILES['pimportcsv']['tmp_name'])) {
			$file  = $_FILES["pimportcsv"]['tmp_name'];
			$uname = $this->userInfo['username'];
			//bom判断
			$contents=file_get_contents($file);
			$charset[1]=substr($contents, 0, 1);
			$charset[2]=substr($contents, 1, 1);
			$charset[3]=substr($contents, 2, 1);
			if (ord($charset[1])==239 && ord($charset[2])==187 && ord($charset[3])==191) $this->output(-1, '导入文件数据非法，包含bom头.');
				
			list(, $phrases) = Client_Service_Phrase::getsByPhrase(array('status'=>1),array('id'=>'DESC','create_time'=>'DESC'));
			$phrases = Common::resetKey($phrases, 'id');
			$phrases = array_unique(array_keys($phrases));

			$spl_obj = new SplFileObject($file, 'rb');
			$spl_obj->seek(filesize($file));
			$count = $spl_obj->key();//总行数
			$perPage = 100;
			$maxPage = ceil($count / $perPage);
			for($page = 1; $page <= $maxPage; $page++){
				$this->_getFilePartData($phrases, $uname, $spl_obj, $page, $perPage);//每次读取100行
			}
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败');
		}
	}
	
	/*
	 * 分段获取内容
	*/
	private function _getFilePartData($phrases, $uname, $spl_obj, $page = 1, $limit=100) {
		$start = ($page - 1) * $limit;
		$spl_obj->seek($start);// 转到第N行, seek方法参数从0开始计数
		for($i = 0; $i < $limit; $i++) {
			$content= $spl_obj->current();//获取当前行
			list($type, $word) = explode(',', $content);
			if (empty($type) || !in_array($type, $phrases)) continue; //数据非法跳过
			$row = Client_Service_Sensitive::getBySensitive(array('title' => $word));
			if($row) continue; //添加过的数据跳过。
			$tmp[] = array(
					'id'=>'',
					'title'=>$word,
					'stype'=>$type,
					'status'=>1,
					'create_time'=>Common::getTime(),
					'num'=> 0,
					'uname'=> $uname,
			);
			$spl_obj->next();// 下一行
		}
		Client_Service_Sensitive::addMoreSensitive($tmp);
	}
	
	
	/**
	 *　导出词库
	 * Get phrase list
	 */
	public function pexportAction() {
		$page = intval($this->getInput('page'));
		$s = $this->getInput(array('start_time','end_time','status','uname','title'));
		if ($page < 1) $page = 1;
	
		$params = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['title']) $params['title'] = array('LIKE',trim($s['title']));
		
		list($total, $result) = Client_Service_Phrase::getList($page, 10000, $params);
		foreach($result as $key=>$value){
			list($tot,) = Client_Service_Sensitive::getsBySensitive(array('stype'=>$value['id']));
			$tmp[$value['id']] = $tot;
		}
	
		$file_content = "";
		//词组记录
		$file_content .= "\"词组\",";
		$file_content .= "\"数量\",";
		$file_content .= "\"创建时间\",";
		$file_content .= "\"状态\",";
		$file_content .= "\"维护人\",";
		$file_content .= "\r\n";

		foreach ($result as $key=>$value) {
			$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			$file_content .= "\"	" . $value['title'] . "\",";
			$file_content .= "\"" . $tmp[$value['id']] . "\",";
			$file_content .= "\"" . $create_time . "\",";
			$file_content .= "\"" . ($value['status'] ? '开启' : '关闭') . "\",";
			$file_content .= "\"" . $value['uname'] . "\",";
			$file_content .= "\r\n";
		}
		
	
		$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		$filename = "词库管理_".date('Ymd', Common::getTime());
		Util_DownFile::downloadFile($filename . '.csv', $file_content);
	}
	
	/**　评论审核导出
	 * get comment list
	 */
	public function exportAction() {
		$s = $this->getInput(array('name','id','nickname','uname','status','is_sensitive','utype', 'start_time','end_time','is_blacklist'));
		$params =  $tmp = $search = $game_names = array();
	    if (!$s['status'])  $params['status'] = 1;
		if($s['status'] == 3){
			 $params['status'] = array('IN',array(1,2));
		} else if($s['status'] && $s['status'] != 3){
			$params['status'] = $s['status'];
		}
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if($s['nickname']) $params['nickname'] = array('LIKE',trim($s['nickname']));
		if($s['id']) $params['game_id'] = $s['id'];
		if ($s['is_sensitive']) $params['is_sensitive'] = $s['is_sensitive'] -1;
		if ($s['utype']) $params['utype'] = $s['utype'];
		
		if($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
			$games = Resource_Service_Games::getGamesByGameNames($search);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
			if($s['id']) $params['game_id'] = $s['id'];
		}
		$params['is_del'] = 0;
		$params['is_blacklist'] = 0;
		if ($s['is_blacklist']) unset($params['is_blacklist']);
		
		$filename = "评论审核_".date('Ymd', Common::getTime());
		$file = realpath(dirname(Common::getConfig('siteConfig', 'attachPath'))) .'/attachs/'.$filename.".csv";
		$header = array('游戏ID','游戏名称','评论时间', '审核时间', '评论状态', '敏感词','评论内容', '用户名称','用户昵称','机型', '版本', 'SDK版本');
			
		$header = sprintf("%s\r\n", implode(",", $header));
		$header = mb_convert_encoding($header, 'gb2312', 'UTF-8');
		$ret = Util_File::write($file, $header);
					
					
		// 一次最多拿100000条数据。分100次获取
		$per = 1000;
		$page = $total = 0;
			
		do {
			list($total, $rs) = Client_Service_Comment::getList($page, $per, $params);
			if ($total == 0) break;
			foreach($rs as $k=>$v){
			  $temp = array();
			  $temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
			  $game_names[$v['game_id']] = $temp['name'];
			}
	
			
		    $rs_str = "";
		    foreach ($rs as $key=>$value) {
			  $create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			  $check_time = $value['check_time'] ? date('Y-m-d H:i:s', $value['check_time']) : '';
		      $rs_array = array(
								 $value['game_id'],
								 $game_names[$value['game_id']],
						         $create_time,
						         $check_time,
		                         $this->comment_status[$value['status']],
				                 ($value['is_sensitive'] ? '包含' : '不包含'),
				                 $value['title'],
			                     ($value['utype'] == 1 ? $value['uname'] : "IMEI:".$value['imei']),
				                 $value['nickname'],
				                 $value['model'],
						         $value['version'],
						         "Anroid".$value['sys_version']."\t");
						         $rs_str .= sprintf("%s\r\n", implode(",", $rs_array));
		}
			 $page++;
			 $rs_str = mb_convert_encoding($rs_str, 'gb2312', 'UTF-8');
			 Util_File::write($file, $rs_str, Util_File::APPEND_WRITE);
		} while ($total>($page * $per));
		$filname = $filename.".csv";
		$this->redirect(Common::getAttachPath() . '/'.$filname);
	}
	

	/**　评论管理导出
	 * get comment list
	*/
	public function mexportAction() {
		$s = $this->getInput(array('name','id','nickname','uname','is_top','utype', 'start_time','end_time'));
		$params =  $tmp = $search = $game_names = array();
		$params['status'] = 3;
		if ($s['start_time']) $params['check_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['check_time'][1] = array('<=', strtotime($s['end_time']));
		if($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if($s['nickname']) $params['nickname'] = array('LIKE',trim($s['nickname']));
		if($s['id']) $params['game_id'] = $s['id'];
		if ($s['is_top']) $params['is_top'] = $s['is_top'] - 1;
		if ($s['utype']) $params['utype'] = $s['utype'];
	
		if($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
			$games = Resource_Service_Games::getGamesByGameNames($search);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
			if($s['id']) $params['game_id'] = $s['id'];
		}
		$params['is_del'] = 0;
		
		$filename = "用户评论_".date('Ymd', Common::getTime());
		$file = realpath(dirname(Common::getConfig('siteConfig', 'attachPath'))) .'/attachs/'.$filename.'.csv';
		$header = array('游戏ID','游戏名称','评论时间', '审核时间', '是否置顶','评论内容', '用户名称','用户昵称','机型', '版本', 'SDK版本');
		
			
		$header = sprintf("%s\r\n", implode(",", $header));
		$header = mb_convert_encoding($header, 'gb2312', 'UTF-8');
		$ret = Util_File::write($file, $header);
			
			
		// 一次最多拿100000条数据。分100次获取
		$per = 1000;
		$page = $total = 0;
			
		do {
			list($total, $rs) = Client_Service_Comment::getList($page, $per, $params);
			if ($total == 0) break;
			foreach($rs as $k=>$v){
				$temp = array();
				$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
				$game_names[$v['game_id']] = $temp['name'];
			}
	
				
			$rs_str = "";
			foreach ($rs as $key=>$value) {
				$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
				$check_time = $value['check_time'] ? date('Y-m-d H:i:s', $value['check_time']) : '';
				$rs_array = array(
						$value['game_id'],
						$game_names[$value['game_id']],
						$create_time,
						$check_time,
						($value['is_top'] ? '是，有效期至：'.date('Y-m-d H:i:s', $value['top_time']) : '否'),
						$value['title'],
						($value['utype'] == 1 ? $value['uname'] : "IMEI:".$value['imei']),
						$value['nickname'],
						$value['model'],
						$value['version'],
						"Anroid".$value['sys_version']."\t");
				$rs_str .= sprintf("%s\r\n", implode(",", $rs_array));
			}
			$page++;
			$rs_str = mb_convert_encoding($rs_str, 'gb2312', 'UTF-8');
			Util_File::write($file, $rs_str, Util_File::APPEND_WRITE);
		} while ($total>($page * $per));
		$filname = $filename.".csv";
		$this->redirect(Common::getAttachPath() . '/'.$filname);
	}
	
	/**
	 *　敏感词列表
	 * Enter description here ...
	 */
	public function sindexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','status', 'start_time','end_time','uname','id','stype'));
		$params =  $search = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if($s['title']) $params['title'] = array('LIKE',trim($s['title']));
		if($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['id']) $params['id'] = $s['id'];
		if ($s['stype']) $params['stype'] = $s['stype'];
		
		list($total, $result) = Client_Service_Sensitive::getList($page, $this->perpage, $params);
		$this->assign('result', $result);
		list(, $phrases) = Client_Service_Phrase::getsByPhrase(array('status'=>1),array('id'=>'DESC','create_time'=>'DESC'));
		$phrases = Common::resetKey($phrases, 'id');
		$this->assign('phrases', $phrases);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$url = $this->actions['slistUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	/**
	 *　添加敏感词
	 * Enter description here ...
	 */
	public function saddAction() {
		list(, $phrases) = Client_Service_Phrase::getsByPhrase(array('status'=>1),array('id'=>'DESC','create_time'=>'DESC'));
		$this->assign('phrases', $phrases);
	}
	
	/**
	 *　编辑敏感词
	 * edit an Phrase
	 */
	public function seditAction() {
		$id = $this->getInput('id');
		list(, $phrases) = Client_Service_Phrase::getsByPhrase(array('status'=>1),array('id'=>'DESC','create_time'=>'DESC'));
		$phrases = Common::resetKey($phrases, 'id');
		$this->assign('phrases', $phrases);
		$info = Client_Service_Sensitive::getSensitive(intval($id));
		$this->assign('info', $info);
	}
	
	/**
	 *　添加敏感词提交
	 * Enter description here ...
	 */
	public function sadd_postAction() {
		$info = $this->getPost(array('title', 'stype', 'status'));
		$info = $this->_cookData($info);
		$info['create_time'] = Common::getTime();
		$info['uname'] = $this->userInfo['username'];
		$result = Client_Service_Sensitive::addSensitive($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->_getSensitiveData();
		$this->output(0, '操作成功');
	}
	
	/**
	 *　编辑敏感词提交
	 * Enter description here ...
	 */
	public function sedit_postAction() {
		$info = $this->getPost(array('id', 'title', 'stype', 'status'));
		$info['uname'] = $this->userInfo['username'];
		$ret = Client_Service_Sensitive::updateSensitive($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败');
		$this->_getSensitiveData();
		$this->output(0, '操作成功');
	}
	
	/**
	 * 重新生成敏感词库文件
	 */
	private function _getSensitiveData(){
		$dataPath = Common::getConfig('siteConfig', 'dataPath');
		$badtrie = $dataPath.'/badtrie.dic';
		$badtrie1 = $dataPath.'/badtrie1.dic';
		$result = unlink ($badtrie1);
		$resTrie = trie_filter_new(); //create an empty trie tree
		$sensitives = Client_Service_Sensitive::getsBySensitives(array('status'=>1));
		foreach ($sensitives as $k => $v) {
				trie_filter_store($resTrie, $v['title']);
			}
	    trie_filter_save($resTrie, $badtrie1);
	    trie_filter_free($resTrie);
	    @unlink ($badtrie);
	    @rename($badtrie1,$badtrie);
	}
	
	//敏感词批量删除操作
	function batchdelAction() {
		$info = $this->getPost(array('action', 'ids'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='del'){
			$ret = Client_Service_Sensitive::deleteByComments($info['ids']);
		} 
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	//批量操作
	function batchUpdateAction() {
		$id = $this->getInput('id');
		$info = $this->getPost(array('action', 'ids', 'sort'));
		if (!count($info['ids'])) $this->output(-1, '没有可操作的项.');
		if($info['action'] =='pass'){
			$ret = Client_Service_Comment::updateCommentStatus($info['ids'], 3, $this->userInfo['username']);
		} else if($info['action'] =='npass'){
			$ret = Client_Service_Comment::updateCommentStatus($info['ids'], 2, $this->userInfo['username']);
		} else if($info['action'] =='del'){
			$ret = Client_Service_Comment::deleteByComments($info['ids'], $this->userInfo['username']);
		} else if($info['action'] =='top'){
			$ret = Client_Service_Comment::topComments($info['ids'],1);
		} else if($info['action'] =='ntop'){
			$ret = Client_Service_Comment::topComments($info['ids'],0);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	//批量操作
	function singleUpdateAction() {
		$flag = $this->getInput('flag');
		$id = $this->getInput('id');
		if($flag =='pass'){
			$ret = Client_Service_Comment::updateCommentStatus(array($id), 3, $this->userInfo['username']);
		} else if($flag =='npass'){
			$ret = Client_Service_Comment::updateCommentStatus(array($id), 2, $this->userInfo['username']);
		}
		if (!$ret) $this->output('-1', '操作失败.');
		$this->output('0', '操作成功.');
	}
	
	/**
	 * 审核记录列表
	 */
	public function logAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('game_id','status', 'start_time','end_time','nickname','name','check_name','uname'));
		$params =  $tmp = $search = $game_names = array();
		
	    if (!$s['status'])  $params['status'] = 3;
		if ($s['status'] == 3){
			 $params['status'] = array('IN',array(2,3));
		} else if($s['status'] && $s['status'] != 3){
			$params['status'] = $s['status'];
		}
		if ($s['start_time']) $params['check_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['check_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['check_name']) $params['check_name'] = array('LIKE',trim($s['check_name']));
		if ($s['nickname']) $params['nickname'] = array('LIKE',trim($s['nickname']));
		if ($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['game_id']) $params['game_id'] = $s['game_id'];
		
		
		
		
		if ($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
			$games = Resource_Service_Games::getGamesByGameNames($search);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
			if($s['game_id']) $params['game_id'] = $s['game_id'];
		}
		
		list($total, $result) = Client_Service_CommentLog::getList($page, $this->perpage, $params);
				
		//找出评论
		unset($search);
		foreach($result as $key=>$value){
			$info = array();
			$search['comment_log_id'] = $value['id'];
			$info = Client_Service_CommentOperatLog::getsByLog($search);
			$tmp[$value['id']] = $info;
		}
		
		//找出游戏名称
		foreach($result as $k=>$v){
			$temp = array();
			$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
			$game_names[$v['game_id']] = $temp['name'];
		}
		
		$url = $this->actions['logUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('comments', $tmp);
		$this->assign('comment_status', $this->comment_status);
		$this->assign('game_names', $game_names);
	}
	
	/**　
	 * 　导出审核记录
	 * 　get comment log list
	 */
	public function lexportAction() {
		$s = $this->getInput(array('game_id','status', 'start_time','end_time','nickname','name','check_name','uname'));
		$params =  $tmp = $search = $game_names = array();
		
	    if (!$s['status'])  $params['status'] = 3;
		if ($s['status'] == 3){
			 $params['status'] = array('IN',array(2,3));
		} else if($s['status'] && $s['status'] != 3){
			$params['status'] = $s['status'];
		}
		if ($s['start_time']) $params['check_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['check_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['check_name']) $params['check_name'] = array('LIKE',trim($s['check_name']));
		if ($s['nickname']) $params['nickname'] = array('LIKE',trim($s['nickname']));
		if ($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['game_id']) $params['game_id'] = $s['game_id'];
		
		
		
		
		if ($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
			$games = Resource_Service_Games::getGamesByGameNames($search);
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			} else {
				$params['game_id'] = 0;
			}
			if ($s['game_id']) $params['game_id'] = $s['game_id'];
		}
	
		$filename = "审核记录_".date('Ymd', Common::getTime());
		$file = realpath(dirname(Common::getConfig('siteConfig', 'attachPath'))) .'/attachs/'.$filename.'.csv';
		$header = array('评论序号','游戏ID','游戏名称','评论内容', '用户名称','用户昵称','机型', '版本', 'SDK版本','审核记录');
		
			
		$header = sprintf("%s\r\n", implode(",", $header));
		$header = mb_convert_encoding($header, 'gb2312', 'UTF-8');
		$ret = Util_File::write($file, $header);
			
			
		// 一次最多拿100000条数据。分100次获取
		$per = 1000;
		$page = $total = 0;
		$tmp = array();
			
		do {
			list($total, $rs) = Client_Service_CommentLog::getList($page, $per, $params);
			if ($total == 0) break;
			
			//找出评论
			$search = array();
			foreach($rs as $key=>$value){
			   $info = array();
			   $search['comment_log_id'] = $value['id'];
			   $info = Client_Service_CommentOperatLog::getsByLog($search);
			   $tmp[$value['id']] = $info;
			}
			
			//找出游戏名称
			foreach($rs as $k=>$v){
				$temp = array();
				$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
				$game_names[$v['game_id']] = $temp['name'];
			}
	
				
			$rs_str = "";
			foreach ($rs as $k2=>$v2) {
				$str = "";
				foreach($tmp[$v2['id']] as $k=>$v){ 
					 $str.= "审核时间: ".date('Y-m-d H:i:s', $v['check_time'])."   ".$v['check_name']."   ".$this->comment_status[$v['status']]."\t";
				}
				$rs_array = array(
						$v2['id'],
						$v2['game_id'],
						$game_names[$v2['game_id']],
						$v2['title'],
						$v2['uname'],
						$v2['nickname'],
						$v2['model'],
						$v2['version'],
						"Anroid".$v2['sys_version'],
						$str."\t");
				$rs_str .= sprintf("%s\r\n", implode(",", $rs_array));
			}
			$page++;
			$rs_str = mb_convert_encoding($rs_str, 'gb2312', 'UTF-8');
			Util_File::write($file, $rs_str, Util_File::APPEND_WRITE);
		} while ($total>($page * $per));
		$filname = $filename.'.csv';
		$this->redirect(Common::getAttachPath() . '/'.$filname);
	
	
	}
	
	/**
	 *　黑名单列表
	 * Enter description here ...
	 */
	public function bindexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('name','status', 'start_time','end_time','uname','utype'));
		$params =  $tmp = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if($s['name']) $params['name'] = $s['name'];
		if($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['utype']) $params['utype'] = $s['utype'];
	
		list($total, $result) = Client_Service_Blacklist::getsearchList($page, $this->perpage, $params);
		
	
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('utype', $this->utype);
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $this->actions['blistUrl'].'?'));
	}
	
	
	/**
	 * 编辑黑名单
	 * edit an Blacklist
	 */
	public function beditAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Blacklist::getBlacklist(intval($id));
		$this->assign('utype', $this->utype);
		$this->assign('info', $info);
	}
	
	/**
	 *　编辑黑名单提交
	 * Enter description here ...
	 */
	public function bedit_postAction() {
		$info = $this->getPost(array('id', 'name', 'status', 'utype'));
		$info = $this->_cookBlacklistData($info);
		$info['uname'] = $this->userInfo['username'];
		$parmes = array();
		$parmes['uname']= $info['name'];
		if($info['utype'] == 2) {
			$parmes = array();
			$info['imei'] = $info['name'];
			$info['imcrc'] = crc32($info['name']);
			$parmes['imcrc']= crc32($info['name']);
			$info['name'] = "";
		}
		Client_Service_Comment::updateByComment(array('is_blacklist'=>$info['status']),$parmes);
		Client_Service_CommentLog::updateLog(array('is_blacklist'=>$info['status']),$parmes);
		$ret = Client_Service_Blacklist::updateBlacklist($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	/**
	 *　添加和名单
	 * Enter description here ...
	 */
	public function baddAction() {
		$this->assign('utype', $this->utype);
	}
	
	/**
	 *　添加黑名单提交
	 * Enter description here ...
	 */
	public function badd_postAction() {
		$info = $this->getPost(array('name', 'status', 'utype'));
		$info = $this->_cookBlacklistData($info);
		$info['uname'] = $this->userInfo['username'];
		$info['create_time'] = Common::getTime();
		if($info['utype'] == 2) {
			$info['imei'] = $info['name'];
			$info['imcrc'] = crc32($info['name']);
			$info['name'] = "";
		}
		$result = Client_Service_Blacklist::addBlacklist($info);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	/**
	 *　敏感词导出
	 * Get Sensitive list
	 */
	public function sexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('title','status', 'start_time','end_time','uname','stype','id'));
		$params =  $tmp = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['title']) $params['title'] = array('LIKE',trim($s['title']));
		if ($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['stype']) $params['stype'] = $s['stype'];
		if ($s['id']) $params['id'] = $s['id'];
	
		list($total, $result) = Client_Service_Sensitive::getList($page, 100000, $params);
		list(, $phrases) = Client_Service_Phrase::getsByPhrase(array('status'=>1),array('id'=>'DESC','create_time'=>'DESC'));
		$phrases = Common::resetKey($phrases, 'id');
	
		$file_content = "";
		//名单记录
		$file_content .= "\"ID\",";
		$file_content .= "\"敏感词\",";
		$file_content .= "\"词组\",";
		$file_content .= "\"添加时间\",";
		$file_content .= "\"状态\",";
		$file_content .= "\"维护人\",";
		$file_content .= "\"过滤次数\",";
		$file_content .= "\r\n";
	
		foreach ($result as $key=>$value) {
			$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			$file_content .= "\"" . $value['id'] . "\",";
			$file_content .= "\"" . $value['title'] . "\",";
			$file_content .= "\"	" . $phrases[$value['stype']]['title'] . "\",";
			$file_content .= "\"" . $create_time . "\",";
			$file_content .= "\"" . ($value['status'] ? '开启' : '关闭') . "\",";
			$file_content .= "\"" . $value['uname'] . "\",";
			$file_content .= "\"" . $value['num'] . "\",";
			$file_content .= "\r\n";
		}
	
	
		$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		$filename = "敏感词库_".date('Ymd', Common::getTime());
		Util_DownFile::downloadFile($filename . '.csv', $file_content);
	}
	
	/**
	 *　导出和名单
	 * Get phrase list
	 */
	public function bexportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s = $this->getInput(array('name','status', 'start_time','end_time','uname','utype'));
		$params =  $tmp = array();
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['start_time']) $params['create_time'][0] = array('>=', strtotime($s['start_time']));
		if ($s['end_time'] && $s['start_time']) $params['create_time'][1] = array('<=', strtotime($s['end_time']));
		if ($s['name']) $params['name'] = trim($s['name']);
		if ($s['uname']) $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['utype']) $params['utype'] = $s['utype'];
		
		list($total, $result) = Client_Service_Blacklist::getsearchList($page, 100000, $params);
		
	
		$file_content = "";
		//名单记录
		$file_content .= "\"类型\",";
		$file_content .= "\"号码\",";
		$file_content .= "\"添加时间\",";
		$file_content .= "\"状态\",";
		$file_content .= "\"维护人\",";
		$file_content .= "\r\n";
	
		foreach ($result as $key=>$value) {
			$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			$file_content .= "\"	" . $this->utype[$value['utype']] . "\",";
			$file_content .= "\"" . ($value['utype'] == 1 ? $value['name'] : $value['imei']) . "\",";
			$file_content .= "\"" . $create_time . "\",";
			$file_content .= "\"" . ($value['status'] ? '开启' : '关闭') . "\",";
			$file_content .= "\"" . $value['uname'] . "\",";
			$file_content .= "\r\n";
		}
	
	
		$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		$filename = "黑名单_".date('Ymd', Common::getTime());
		Util_DownFile::downloadFile($filename . '.csv', $file_content);
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '敏感词不能为空.'); 
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookPhraseData($info) {
		if(!$info['title']) $this->output(-1, '词组不能为空.');
		return $info;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookBlacklistData($info) {
		if($info['utype'] == 1  && !$info['name']) $this->output(-1, '账号不能为空.');
		if($info['utype'] == 2  && !$info['name']) $this->output(-1, 'IMEI不能为空.');
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Client_Service_Comment::getGamePrice($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Client_Service_Comment::deleteGamePrice($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
}
