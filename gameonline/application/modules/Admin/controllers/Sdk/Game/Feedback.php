<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Sdk_Game_FeedbackController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Sdk_Game_Feedback/index',
		'addLablePostUrl'=> '/Admin/Sdk_Game_Feedback/addLablePost',
		'replytUrl'=> '/Admin/Sdk_Game_Feedback/reply',
		'replyPosttUrl'=> '/Admin/Sdk_Game_Feedback/reply_post',
		'viewReplytUrl'=> '/Admin/Sdk_Game_Feedback/reply',
		'exportUrl'=> '/Admin/Sdk_Game_Feedback/export',	
	    
	);
	
	public $perpage = 20;
	public $feedback_status = array(
			0 => '未回复',
			1 => '已回复'
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
		$s = $this->getInput(array('status', 'label_name','uname','content','name','uuid'));
		
		//查询条件
		$params =  $tmp = $search = $game_names = array();
		if ($s['status'])      $params['status'] =  $s['status'] - 1;
		if ($s['label_name'] != '')  $params['label_name'] =  $s['label_name'] ;
		if ($s['uuid'])       $params['uuid'] = array('LIKE',trim($s['uuid']));
		if ($s['uname'])       $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['content'])     $params['content'] = array('LIKE',trim($s['content']));
		if($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
		    $games = Resource_Service_Games::getGamesByGameNames($search); 
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			}else{
				$params['game_id'] = -1;
			}
		}
		
		list($total, $result) = Sdk_Service_Feedback::getList($page, $this->perpage, $params);
		foreach($result as $k=>$v){
			$temp = array();
			$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
			$game_names[$v['game_id']] = $temp['name'];
		}
		
		//获取标签的名字
		$label_name  = json_decode(Game_Service_Config::getValue('sdk_label_type'));
		$label_name = html_entity_decode($label_name);
		$label_arr = explode(',', $label_name);

		
		$url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->assign('result', $result);
		$this->assign('s', $s);
		$this->assign('total', $total);
		$this->assign('lable_arr', $label_arr);
		$this->assign('game_names', $game_names);
		$this->assign('feedback_status', $this->feedback_status);
	}
		
	
	
	
	/**
	 *　
	 * Enter description here ...
	 */
	public function lableAction() {
		//获取标签的名字
		$label_name  = json_decode(Game_Service_Config::getValue('sdk_label_type'));
		$this->assign('lable_name', $label_name);
	}
	
	/**
	 *　
	 * Enter description here ...
	 */
	public function addLablePostAction(){
		$lable = trim($this->getPost('lable'));
		if ($lable == '') $this->output(-1, '标签不能为空');
	    $temp = explode(',', html_entity_decode($lable));
	    foreach ($temp as $val){
	    	if(!trim($val)) $this->output(-1, '标签不能为空');
	    	$count = Util_String::strlen(trim($val));
	    	if($count > 5){
	    		$this->output(-1, '标签在5个字符之内'.$count);
	    	}
	    }
		$ret  = Game_Service_Config::setValue('sdk_label_type',json_encode($lable) );
	    $this->output(0, '操作成功');
	}
	
	/**
	 *　
	 * Enter description here ...
	 */
	public function updateLableAction(){
		$info = $this->getInput(array('id', 'label_name'));
		if(!intval($info['id'])) $this->output(-1, '非法操作');
		//if(!$info['label_name']) $this->output(-1, '非法操作');
		
		$data['label_name']  = $info['label_name'];
		$ret = Sdk_Service_Feedback::updateByID($data, $info['id']);
		if(!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
		
		
	}
	

	/**
	 *　
	 * Enter description here ...
	 */
	public function replyAction(){
	    $id = $this->getInput('id');
	    if(!intval($id)) $this->output(-1, '非法操作');
		$info = Sdk_Service_Feedback::getByID( $id );

	    $game_names =  Resource_Service_Games::getResourceByGames($info['game_id']);
		

		//获取标签的名字
		$label_name  = json_decode(Game_Service_Config::getValue('sdk_label_type'));
		$label_name = html_entity_decode($label_name);
		$label_arr = explode(',', $label_name);
		$this->assign('label_arr', $label_arr);
		$this->assign('feedback_status', $this->feedback_status);
		$this->assign('game_names', $game_names['name']);
		$this->assign('info', $info);
		
	}
	
	/**
	 *　
	 * Enter description here ...
	 */
	public function reply_postAction(){
		$info = $this->getInput(array('id','label_name','reply_content'));
		if(!intval($info['id'])) $this->output(-1, '非法操作');
		$data['status'] = 1;
		$data['label_name'] = trim($info['label_name']);
		$data['reply_content'] = trim($info['reply_content']);
		$data['reply_name']    = $this->userInfo['username'];
		$data['reply_time']    = Common::getTime();
		$data['version_time']    = Common::getTime();
		//if($data['label_name'] == '0')   $this->output(-1, '标签未选择！！');
		if($data['reply_content'] == '') $this->output(-1, '回复内容不能为空！！');
		$ret = Sdk_Service_Feedback::updateByID($data, $info['id']);
		if(!$ret)  $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	

	/**
	 *　
	 * Enter description here ...
	 */
	public function viewReplyAction(){
		$id = $this->getInput('id');
		if(!intval($id)) $this->output(-1, '非法操作');
		$info = Sdk_Service_Feedback::getByID( $id );
		$this->assign('info', $info);
		//获取标签的名字
		$label_name  = json_decode(Game_Service_Config::getValue('sdk_label_type'));
		$label_name = html_entity_decode($label_name);
		$label_arr = explode(',', $label_name);
		$this->assign('label_arr', $label_arr);
		$this->assign('feedback_status', $this->feedback_status);
	}
	
	
	/**
	 *　导出
	 * 
	 */
	public function exportAction() {
	    $s = $this->getInput(array('status', 'label_name','tel','content','name'));
		//查询条件
		$params =  $tmp = $search = $game_names = array();
		if ($s['status'])      $params['status'] =  $s['status'] - 1;
		if ($s['label_name'])  $params['label_name'] =  $s['label_name'] ;
		if ($s['uname'])         $params['uname'] = array('LIKE',trim($s['uname']));
		if ($s['content'])     $params['content'] = array('LIKE',trim($s['content']));
		if($s['name']){
			$search['name']  = array('LIKE',trim($s['name']));
		    $games = Resource_Service_Games::getGamesByGameNames($search); 
			$games = Common::resetKey($games, 'id');
			$ids = array_unique(array_keys($games));
			if($ids){
				$params['game_id'] = array('IN',$ids);
			}
		}
		
		list($total, $result) = Sdk_Service_Feedback::getList(1, 500000, $params);
		foreach($result as $k=>$v){
			$temp = array();
			$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
			$game_names[$v['game_id']] = $temp['name'];
		}

	
		$file_content = "";
		//词组记录
		$file_content .= "\"编号\",";
		$file_content .= "\"游戏名称\",";
		$file_content .= "\"帐号\",";
		$file_content .= "\"反馈时间\",";
		$file_content .= "\"状态\",";
		$file_content .= "\"标签\",";
		$file_content .= "\r\n";
		
		foreach($result as $k=>$v){
			$temp = array();
			$temp  = Resource_Service_Games::getResourceByGames($v['game_id']);
			$game_names[$v['game_id']] = $temp['name'];
		}

		foreach ($result as $key=>$value) {
			$create_time = $value['create_time'] ? date('Y-m-d H:i:s', $value['create_time']) : '';
			$label_name = ($value['label_name'] == '0' ) ? '无' : $value['label_name'];
			$file_content .= "\"" . $value['id'] . "\",";
			$file_content .= "\"	" .$game_names[$value['game_id']] . "\",";
			$file_content .= "\"" . $value['uname'] . "\",";
			$file_content .= "\"" . $create_time . "\",";
			$file_content .= "\"" . $this->feedback_status[$value['status']] . "\",";
			$file_content .= "\"" . $label_name . "\",";
			$file_content .= "\r\n";
		}
		
	
		$file_content = mb_convert_encoding($file_content, 'gb2312', 'UTF-8');
		$filename = date('Ymd', Common::getTime());
		Util_DownFile::downloadFile($filename . '.csv', $file_content);
	}


	
	
}
