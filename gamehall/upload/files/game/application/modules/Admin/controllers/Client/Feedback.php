<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghau
 *
 */
class Client_FeedbackController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Feedback/index',
		'rlistUrl' => '/Admin/Client_Feedback/react',
		'addUrl' => '/Admin/Client_Feedback/add',
		'addLablePostUrl'=> '/Admin/Client_Feedback/addLablePost',
		'addPostUrl' => '/Admin/Client_Feedback/add_post',
		'updateUrl' => '/Admin/Client_Feedback/updateLable',
		'exportUrl'=>'/admin/Client_Feedback/export',
		'replytUrl'=> '/Admin/Client_Feedback/reply',
		'replyPosttUrl'=> '/Admin/Client_Feedback/reply_post',
		'viewReplytUrl'=> '/Admin/Client_Feedback/viewReply',
		'gameFeedbackUrl'	=>'/Admin/Sdk_Game_Feedback/index',
		'setUrl'	=>'/Admin/Client_Feedback/set',
		'setPostUrl'	=>'/Admin/Client_Feedback/setPost',
		'uploadImgUrl' => '/Admin/Client_Feedback/uploadImg',
	);
	
	public $perpage = 20;
	
	public $mfeedbackStatus = array(
			0 => '未回复',
			1 => '已回复'
	);
	public $mUserType = array(
			1 => '账号',
			2 => 'IMEI',
	);
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function indexAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$params = $this->getInput(array('status', 'label_name','uname','content','uuid','imei','start_time','end_time'));
		
		//查询条件
		$search = array();
		if ($params['status'])      $search['status'] =  $params['status'] - 1;
		if ($params['label_name'] != '')  $search['label_name'] =  $params['label_name'] ;
		if ($params['uuid'])       $search['uuid'] = array('LIKE',trim($params['uuid']));
		if ($params['uname'])       $search['uname'] = array('LIKE',trim($params['uname']));
		if ($params['content'])     $search['content'] = array('LIKE',trim($params['content']));
		if ($params['imei']) $search['imei'] = $params['imei'];
		if ($params['start_time']) {
			$search['create_time'][0] = array('>=', strtotime($params['start_time']));
		}
		if($params['end_time']){
			$search['create_time'][1] = array('<=', strtotime($params['end_time']));
		}
		
		
		list($total, $result) = Feedback_Service_Feedback::getList($page, $this->perpage, $search);
	
		//获取标签的名字
		$labelName  = json_decode(Game_Service_Config::getValue('client_feedback_label_type'));
		$labelArr = explode(',', html_entity_decode($labelName));
		

		foreach ($result as $val){
			//获取附件
			$attachInfo = Feedback_Service_FeedbackAttach::getByID($val['id']);
			if($attachInfo){
				$attach[$val['id']] = $attachInfo['image_path'];
			}
		}
		
		
		$url = $this->actions['listUrl'].'/?' . http_build_query($params) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
		$this->cookieParams();
		$this->assign('result', $result);
		$this->assign('params', $params);
		$this->assign('labelArr', $labelArr);
		$this->assign('attach', $attach);
		$this->assign('total', $total);
		$this->assign('feedbackStatus', $this->mfeedbackStatus);
	}
	
	/**
	 *　
	 * Enter description here ...
	 */
	public function lableAction() {
		//获取标签的名字
		$lableName  = json_decode(Game_Service_Config::getValue('client_feedback_label_type'));
		$this->assign('lableName', $lableName);
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
		$ret  = Game_Service_Config::setValue('client_feedback_label_type',json_encode($lable) );
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
		$ret = Feedback_Service_Feedback::updateByID($data, $info['id']);
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
		$info = Feedback_Service_Feedback::getByID( $id );
	
		
		$contentInfo = Feedback_Service_FeedbackReply::getByID($info['id']);
		$info['reply_content'] = $contentInfo['reply_content'];
		$info['reply_name'] = $contentInfo['reply_name'];
		
		//获取附件
		$attachInfo = Feedback_Service_FeedbackAttach::getByID($info['id']);
		$info['attach'] = $attachInfo['image_path'];
		
		//获取标签的名字
		$labelName  = json_decode(Game_Service_Config::getValue('client_feedback_label_type'));
		$labelName = html_entity_decode($labelName);
		$labelArr = explode(',', $labelName);
		$this->assign('labelArr', $labelArr);
		$this->assign('feedbackStatus', $this->mfeedbackStatus);
	
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
		$data['version_time']    = Common::getTime();
		if(trim($info['reply_content']) == '') $this->output(-1, '回复内容不能为空！！');
		
		
		Common_Service_Base::beginTransaction();
		$ret = Feedback_Service_Feedback::updateByID($data, $info['id']);
		
		
		$replyData['reply_content'] = trim($info['reply_content']);
		$replyData['reply_name']    = $this->userInfo['username'];
		$replyData['reply_time']    = Common::getTime();
		$replyResult = Feedback_Service_FeedbackReply::getByID($info['id']);
		
		if($replyResult){
			$replyRet  = Feedback_Service_FeedbackReply::updateByID($replyData, $info['id']);
		}else{
			$replyData['feedback_id'] = $info['id'];
			$replyRet  = Feedback_Service_FeedbackReply::add($replyData);
		}

		if($ret && $replyRet){
			Common_Service_Base::commit();
		}else{
			Common_Service_Base::rollBack();
		} 
		if(!$ret || !$replyRet)  $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	/**
	 *　
	 * Enter description here ...
	 */
	public function viewReplyAction(){
		$id = $this->getInput('id');
		if(!intval($id)) $this->output(-1, '非法操作');
		$info = Feedback_Service_Feedback::getByID( $id );
		
		
		$contentInfo = Feedback_Service_FeedbackReply::getByID($info['id']);
		$info['reply_content'] = $contentInfo['reply_content'];
		$info['reply_name'] = $contentInfo['reply_name'];
		
		//获取附件
		$attachInfo = Feedback_Service_FeedbackAttach::getByID($info['id']);
		$info['attach'] = $attachInfo['image_path'];
		
		$this->assign('info', $info);
		//获取标签的名字
		$labelName  = json_decode(Game_Service_Config::getValue('client_feedback_label_type'));
		$labelName = html_entity_decode($labelName);
		$labelArr = explode(',', $labelName);
		$this->assign('labelArr', $labelArr);
		$this->assign('feedbackStatus', $this->mfeedbackStatus);
	}
	
	/**
	 *　客户端反馈导出数据
	 * Get phrase list
	 */
	public function exportAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$s =  $this->getInput(array('status', 'label_name','uname','content','uuid','imei','start_time','end_time'));
		$params = array();
		
		if ($s['status']) $params['status'] = $s['status'] - 1;
		if ($s['content']) $params['content'] = array('LIKE',$s['content']);
		if ($s['uname']) $params['uname'] = array('LIKE',$s['uname']);
		if ($s['label_name'] != '') $params['label_name'] = $s['label_name'];
		if ($s['uuid']) $params['uuid'] = $s['uuid'];
		if ($s['imei']) $params['imei'] = $s['imei'];
		if ($s['start_time']) {
			$params['create_time'][0] = array('>=', strtotime($s['start_time']));
		}
		if($s['end_time']){
			$params['create_time'][1] = array('<=', strtotime($s['end_time']));
		}
		
		
		//excel-head
		$filename = '意见反馈_' . date('Ymdhis');
		Util_Csv::putHead($filename);
		$title = array(array('编号','UUID','imei','联系方式','反馈时间','反馈内容','状态','标签','用户名称','机型','客户端名称', '版本', '系统版本'));
		Util_Csv::putData($title);
		//循环分页查询输出
		while(1){
			list($total, $result) = Feedback_Service_Feedback::getList($page, $this->perpage, $params);
			if (!$result) break;
	
			$tmp = array();
			foreach ($result as $key=>$value) {
				$tmp[] = array(
						$value['id'],
						$value['uuid'],
						$value['imei'],
						$value['contact'],
						date('Y-m-d H:i:s', $value['create_time']),
						$value['content'],
						($value['status'] ? "已回复" : "未回复"),
						$value['label_name']=='0'?'无':$value['label_name'],
						$value['uname'],
						$value['model'],
						 "游戏大厅" ,
						$value['client_version'],
						$value['sys_version']."\t");
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
	public function reactAction() {
		$this->redirect('/Admin/React/index');
		exit;
	}
	

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function add_postAction() {
		$labels = $this->getPost('game_free_label');
		$ret = Game_Service_Config::setValue('game_free_label', $labels);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookData($info) {
		if(!$info['title']) $this->output(-1, '游戏分类名称不能为空.'); 
		return $info;
	}
		
	/**
	 * 
	 * Enter description here ...
	 */
	public function deleteAction() {
		$id = intval($this->getInput('id'));
		$info = Game_Service_Label::getLabel($id);
		//get IdxLabel ByLabelId
		$game_ids = Game_Service_Game::getIdxLabelByLabelId(array('label_id'=>$id));
		if($game_ids ) $this->output(-1, '请先删除游戏列表里面含有该标签的游戏');
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		$result = Game_Service_Label::deleteLabel($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	
	/**
	 * Enter description here ...
	 */
	public function setAction() {
		$configs['game_feedback_faq'] = Game_Service_Config::getValue('game_feedback_faq');
		$this->assign('configs', $configs);
	}
	
	/**
	 * Enter description here ...
	 */
	public function setPostAction() {
		$config = $this->getPost('game_feedback_faq');
		if(!$config) $this->output(-1, '常见问题不能为空.');
		Game_Service_Config::setValue('game_feedback_faq', $config);
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 编辑器中上传图片
	 */
	public function uploadImgAction() {
		$ret = Common::upload('imgFile', 'feedback');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
	}

	
	
}
