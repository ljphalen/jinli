<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * Class UlotteryController
 * 用户中心抽奖管理
 */
class UlotteryController extends Admin_BaseController {

	public $levels = array(
		'1' => '一等奖',
		'2' => '二等奖',
		'3' => '三等奖',
		'4' => '四等奖',
		'5' => '五等奖',
		'6' => '六等奖',
		'7' => '七等奖',
		'8' => '八等奖',
	);
	
	public  $actions  = array(
		'indexUrl'						=>'/Admin/Ulottery/index',
		'editUrl'							=>'/Admin/Ulottery/edit',
		'editPostUrl'					=>'/Admin/Ulottery/editPost',
		'deleteUrl'						=>'/Admin/Ulottery/delete',
		'uploadUrl'						=>'/Admin/Ulottery/upload',
		'winnerUrl'						=>'/Admin/Ulottery/winner',
		'uploadPostUrl'				=>'/Admin/Ulottery/upload_post',
		'snatchConfigUrl'			=>'/Admin/Ulottery/snatchConfig',
		'sindexUrl'						=>'/Admin/Ulottery/sindex',
		'seditUrl'							=>'/Admin/Ulottery/sedit',
		'seditPostUrl'					=>'/Admin/Ulottery/seditPost',
		'sdeleteUrl'					=>'/Admin/Ulottery/sdelete',
		'prizelistUrl'					=>'/Admin/Ulottery/prizelist',
		'conponUrl'					=>'/Admin/Ulottery/coupon',
		'couponListUrl'				=>'/Admin/Ulottery/couponlist',
		'exportUrl'						=>'/Admin/Ulottery/export',
		'manualUrl'					=>'/Admin/Ulottery/manual',
		'quizConfigUrl'				=>'/Admin/Ulottery/quizConfig',
		'quizUrl'							=>'/Admin/Ulottery/quiz',
		'editQuizUrl'					=>'/Admin/Ulottery/editQuiz',
		'postQuizUrl'					=>'/Admin/Ulottery/postQuiz',
		'importQuizUrl'				=>'/Admin/Ulottery/importQuiz',
		'deleteQuizUrl'				=>'/Admin/Ulottery/deleteQuiz',
		'quizlogUrl'						=>'/Admin/Ulottery/quizlog',
		'quizdetailUrl'				=>'/Admin/Ulottery/quizdetail',
	);

	public $pageSize = 20;
	
	public 	$options = array('1'=>"A",'2'=>"B",'3'=>"C",'4'=>'D');

	//基本配置
	public function configAction() {
		$postData                          = $this->getInput(array(
			'ulottery_per_cosume',
			'ulottery_max_times',
			'ulottery_status',
			'ulottery_free_times'
		));
		$postData['ulottery_rule_content'] = $_POST['ulottery_rule_content'];
		if ($postData['ulottery_per_cosume'] || $postData['ulottery_max_times']) {
			if (!intval($postData['ulottery_per_cosume']) || !intval($postData['ulottery_max_times'])) {
				$this->output('-1', '请输入正确的数！');
			}
			foreach ($postData as $k => $v) {
				Gionee_Service_Config::setValue($k, $v);
			}
			$this->output('0', '编辑成功！');
		}
		$data             = $temp = array();
		$params['3g_key'] = array(
			'IN',
			array(
				'ulottery_per_cosume',
				'ulottery_max_times',
				'ulottery_rule_content',
				'ulottery_status',
				'ulottery_free_times'
			)
		);
		$temp             = Gionee_Service_Config::getsBy($params);
		foreach ($temp as $m => $n) {
			$data[$n['3g_key']] = $n['3g_value'];
		}
		$this->assign('data', $data);
	}

	/**
	 * 列表
	 */
	public function indexAction() {
		$postData = $this->getInput(array('page', 'status'));
		$page     = max($postData['page'], 1);
		$where    = array();
		if (isset($postData['status']) && $postData['status'] >= 0) {
			$where['status'] = $postData['status'];
		}
		list($total, $dataList) = User_Service_Lottery::getList($page, $this->pageSize, $where, array('level' => 'ASC'));
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?status={$postData['status']}&"));
		$this->assign('statusList', array('0' => '关闭', '1' => '开启'));
		$this->assign('status', $postData['status']);
		$this->assign('levels', $this->levels);
	}

	/**
	 * 编辑
	 */
	public function editAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$data = User_Service_Lottery::get($id);
		}
		$prizeTypes = Common::getConfig('userConfig', 'prize_types');
		$this->assign('data', $data);
		$this->assign('levels', $this->levels);
		$this->assign('prizeTypes', $prizeTypes);
	}

	public function editPostAction() {
		$postData = $this->getInput(array(
			'name',
			'number',
			'scores',
			'type',
			'val',
			'level',
			'id',
			'image',
			'ratio',
			'sort',
			'status'
		));
		if (!intval($postData['val'] || !intval($postData['number']))) {
			$this->output('-1', '数量或金币不能为空!');
		}
		if (intval($postData['id'])) {
			$ret = User_Service_Lottery::edit($postData, $postData['id']);
		} else {
			unset($postData['id']);
			$postData['created_time'] = time();
			$ret                      = User_Service_Lottery::add($postData);
		}
		if ($ret) {
			$this->output('0', '操作成功！');
		} else {
			$this->output('-1', '操作失败!');
		}
	}

	public function deleteAction() {
		$id  = $this->getInput('id');
		$ret = User_Service_Lottery::delete($id);
		$this->output('0', '删除成功!');
	}


	/**
	 * 中奖用户列表
	 */
	public function winnerAction() {
		$postData = $this->getInput('page');
		$page     = max(1, $postData['page']);
		list($total, $dataList) = User_Service_ScoreLog::getList($page, $this->pageSize, array('score_type' => '203'), array('add_time' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$userInfo                 = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['username'] = $userInfo['username'];

			$goods                     = User_Service_Lottery::get($v['fk_earn_id']);
			$dataList[$k]['goodsName'] = $goods['name'];
		}
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['winner'] . "?"));
	}
	/**
	 * 夺宝奇兵配置信息
	 */
	public function snatchConfigAction() {
		$postData                        = $this->getInput(array(
			'snatch_max_times',
			'snatch_status',
			'snatch_free_times',
			'snatch_level'
		));
		$postData['snatch_rule_content'] = trim($_POST['snatch_rule_content']);

		if (!empty($postData['snatch_level'])) {
			$postData['snatch_level'] = implode(',', $postData['snatch_level']);
			foreach ($postData as $k => $v) {
				Gionee_Service_Config::setValue($k, $v);
			}
		}
		$data             = $temp = array();
		$params['3g_key'] = array(
			'IN',
			array(
				'snatch_max_times',
				'snatch_status',
				'snatch_free_times',
				'snatch_level',
				'snatch_rule_content'
			)
		);
		$temp             = Gionee_Service_Config::getsBy($params);
		foreach ($temp as $m => $n) {
			$data[$n['3g_key']] = $n['3g_value'];
		}
		if (!empty($data['snatch_level'])) {
			$data['snatch_level'] = explode(',', $data['snatch_level']);
		}
		$this->assign('data', $data);
		$this->assign('levels', $this->levels);
	}

	public function quizConfigAction(){
		$postData = $this->getInput(array('quiz_per_reward_scores','quiz_all_right_reward_scores','quiz_per_day_num','quiz_status'));
		$postData['quiz_rule_content'] =$_POST['quiz_rule_content'];
		
		if(!empty($postData['quiz_per_reward_scores'])){
			foreach ($postData as $k=>$v){
				Gionee_Service_Config::setValue($k, $v);
			}
		} 
		$data= $temp = array();
		$params['3g_key'] = array('IN',array('quiz_per_reward_scores','quiz_all_right_reward_scores','quiz_rule_content','quiz_per_day_num','quiz_status'));
		$temp = Gionee_Service_Config::getsBy($params);
		foreach ($temp as $m=>$n){
			$data[$n['3g_key']] = $n['3g_value'];
		}
		$this->assign('data', $data);
	}
	/**
	 * 夺宝奇兵列表
	 */
	public function sindexAction() {
		$postData = $this->getInput(array('page', 'status'));
		$page     = max($postData['status'], 1);
		$where    = array();
		if (isset($postData['status'])) {
			$where['status'] = $postData['status'];
		}
		list($total, $dataList) = User_Service_Snatch::getList($page, $this->pageSize, $where);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['sindexUrl'] . "?status={$postData['status']}&"));
		$this->assign('dataList', $dataList);
	}

	public function seditAction() {
		$id = $this->getInput('id');
		if (intval($id)) {
			$info               = User_Service_Snatch::get($id);
			$info['prize_info'] = json_decode($info['prize_info'], true);
			$this->assign('data', $info);
		}

		$levels = Gionee_Service_Config::getValue('snatch_level');
		$this->assign('availLevels', explode(',', $levels));
		$this->assign('levels', $this->levels);
		$this->assign('prizeTypes', Common::getConfig('userConfig', 'prize_types'));
	}

	public function seditPostAction() {
		$postData = $this->getInput(array(
			'name',
			'cost_scores',
			'status',
			'image',
			'sort',
			'prize_info',
			'prize_type',
			'number',
			'id',
			'subtitle'
		));
		if (empty($postData['name']) || !intval($postData['cost_scores']) || empty($postData['prize_info'])) {
			$this->output('-1', '参数不错!');
		}
		$postData['prize_info'] = json_encode($postData['prize_info']);
		if (intval($postData['id'])) {
			$ret = User_Service_Snatch::update($postData, $postData['id']);
		} else {
			$postData['add_time'] = time();
			$ret                  = User_Service_Snatch::add($postData);
		}
		$this->output('0', '操作成功！');
	}


	public function sdeleteAction() {
		$id  = $this->getInput('id');
		$ret = User_Service_Snatch::delete($id);
		$this->output('0', '删除成功!');
	}


	public function prizelistAction() {
		$postData = $this->getInput('page');
		$page     = max($postData, 1);
		list($total, $dataList) = User_Service_ScoreLog::getList($page, $this->pageSize, array('score_type' => '207'), array('id' => 'DESC'));
		foreach ($dataList as $k => $v) {
			$user                     = Gionee_Service_User::getUser($v['uid']);
			$dataList[$k]['username'] = $user['username'];
		}
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['prizelistUrl'] . "?"));
	}


	/**
	 * 书券管理
	 */

	public function couponlistAction() {
		$postData = $this->getInput(array('page', 'card_num', 'status'));
		$page     = max($postData['page'], '1');
		$where    = array();
		if ($postData['card_num']) {
			$where['card_num'] = $postData['card_num'];
		}

		if (!empty($postData['status'])) {
			if ($postData['status'] == 1) {
				$where['uid'] = array('>', 0);
			} else {
				$where['uid'] = 0;
			}
		}

		$statusList = array('0' => '全部', '1' => '已发送', '2' => '未发放');

		list($total, $dataList) = User_Service_BookCoupon::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['couponlist'] . "?" . http_build_query($postData) . "&"));
		$this->assign('params', $postData);
		$this->assign('statusList', $statusList);

	}

	public function couponAction() {
		if (!empty($_FILES['data']['tmp_name'])) {
			$this->_import($_FILES['data']['tmp_name'],array('card_num'));
			$this->output('0', '导入成功！');
		}
	}

	public function exportAction() {
		$data = User_Service_BookCoupon::getAll();
		$this->_export($data, '书签数据','coupon');
		exit();
	}

	public function manualAction() {
	}

	public function ajaxGiveCouponAction() {
		$username = $this->getInput('username');
		$user     = Gionee_Service_User::getBy(array('username' => $username));
		if (empty($user)) {
			$this->output('-1', '用户不存在');
		}
		$coupon = User_Service_BookCoupon::getBy(array('uid' => 0, 'get_time' => 0));
		User_Service_BookCoupon::update($coupon['id'], array('uid' => $user['id'], 'get_time' => time()));
		Common_Service_User::sendInnerMsg(array(
			'status'   => 1,
			'uid'      => $user['id'],
			'classify' => 12,
			'coupon'   => $coupon['card_num']
		), 'user_book_coupon');
		$this->output('0', '操作成功!');
	}

	//答题
	
	public function quizAction(){
		 $postData = $this->getInput(array('page','title'));
		 $page = max($postData['page'],1);
		 $where =array();
		 if(!empty($postData['title'])){
		 	$where['title'] = array("LIKE",$postData['title']);
		 }
		list($total, $dataList) = User_Service_Quiz::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
		$this->assign('dataList', $dataList);
		$this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['quizUrl'] . "?title={$postData['title']}&" )); 
		$this->assign('options', $this->options);
		$this->assign('text', $postData['title']);
	} 
	
	public function editQuizAction(){
	
		$id = $this->getInput('id');
		if(intval($id)){
			$data = User_Service_Quiz::get($id);
			$this->assign('data', $data);
		}
		$this->assign('options', $this->options);
		
	}
	
	public function postQuizAction(){
		$postData = $this->getInput(array('title','option1','option2','option3','option4','answer','id','keywords'));
		if(intval($postData['id'])){
			User_Service_Quiz::edit($postData, $postData['id']);
		}else{
			$postData['add_time'] = time();
			User_Service_Quiz::add($postData);
		}
		$this->output('0','操作成功');	
	}
	
	public function deleteQuizAction(){
		$id = $this->getInput('id');
		User_Service_Quiz::delete($id);
		$this->output('0','操作成功');
	}
	public function importQuizAction(){
		if (!empty($_FILES['data']['tmp_name'])) {
			$fields = array('title','keywords', 'option1','option2','option3','option4','answer');
			$this->_import($_FILES['data']['tmp_name'],$fields);
			$this->output('0', '导入成功！');
		}
	}
	
	public function quizlogAction(){
			$postData = $this->getInput(array('page','sdate','edate','mobile'));
			$page = max($postData['page'],1);
			!$postData['sdate']&& $postData['sdate'] = date('Y-m-d',strtotime('-7 day'));
			!$postData['edate']&& $postData['edate'] = date('Y-m-d',strtotime('now'));
			$where =array();
			if(!empty($postData['mobile'])){
				$user = Gionee_Service_User::getUserByName($postData['mobile']);
				$where['uid'] = $user['id'];
			}
			$where['add_time'] = array(
				array('>=',strtotime($postData['sdate'])),
				array('<=',strtotime($postData['edate']." 23:59:59")),
			);
			$where['selected'] = array('>',0);
			$dataList = User_Service_QuizResult::getAnswerUserData($where);
			$this->assign('dataList', $dataList);
			$this->assign('params', $postData);
	}
	
	public function quizdetailAction(){
		$postData = $this->getInput(array('date','num','page'));
		$page = max(1,$postData['page']);
		$where = array();
		if(!empty($postData['num'])){
			$user = Gionee_Service_User::getUser($postData['num']);
			if(empty($user)){
				$user = Gionee_Service_User::getUserByName($postData['num']);
			}
			$where['uid'] = $user['id'];
		}
		$where['answer_time'] = array(array(">=",strtotime($postData['date'])),array("<=",strtotime($postData['date']." 23:59:59")));
		$dataList = User_Service_QuizResult::getDayAnswerData($where,array('uid','is_right'));
		foreach ($dataList as $k=>$v){
			$temp  = array();
			$temp['date'] = date('Ymd',strtotime($postData['date']));
			$temp['score_type'] = array("IN",array('211','212'));
			$temp['group_id'] = 2;
			$earnScores= User_Service_ScoreLog::sum('affected_score',$temp);
			$dataList[$k]['scores'] = $earnScores;
		}
		$this->assign('dataList', $dataList);
		$this->assign('params', $postData);
	}
	
	public function exportQuizAction(){
		$data = User_Service_Quiz::getAll();
		$this->_export($data, '答题列表','quiz');
		exit();
	}
	//上传图片
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
		$ret   = Common::upload('img', 'products');
		$imgId = $this->getPost('imgId');
		$this->assign('imgId', $imgId);
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->getView()->display('common/upload.phtml');
		exit;
	}


	private function _import($file,$fields =array()) {
		$row    = 1;//初始值
		$num    = count($fields);
		if (($handle = fopen($file, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				if ($row > 1) {
					$content = array();
					for ($i = 0; $i < $num; $i++) {
						$content[$fields[$i]] = iconv('GBK', 'UTF8', $data[$i]);
					}
					$content['add_time'] = time();
					$ret = User_Service_Quiz::add($content);
				}
				$row++;
			}
		}
		fclose($handle);
	}


	private function _export($data, $title,$type) {
		ini_set('memory_limit', '1024M');
		header('Content-Type: application/vnd.ms-excel;charset=GB2312');
		$filename = $title . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=' . iconv('utf8', 'gb2312', $filename));
		$out = fopen('php://output', 'w');
		fputcsv($out, array(chr(0xEF) . chr(0xBB) . chr(0xBF)));
		switch ($type){
			case 'coupon':{
				fputcsv($out, array('ID', '卡号', '添加时间', '用户ID', '获得时间'));
				foreach ($data as $k => $v) {
					fputcsv($out, array(
					$v['id'],
					$v['card_num'],
					date('Y-m-d H:i:s', $v['add_time']),
					$v['uid'],
					date('Y-m-d H:i:s', $v['get_time'])
					));
				}
				break;
			}
			case 'quiz':{
				fputcsv($out, array('ID', '题目名', '选项A', '选项B','选项C','选项D','正确答案', '添加时间'));
				$options = $this->options;
				foreach ($data as $k=>$v){
					fputcsv($out, array(
						$v['id'],$v['title'],$v['option1'],$v['potion2'],$v['option3'],$v['option4'],$options[$v['answer']],date('Y-m-d H:i:s',$v['add_time']),
					));
				}
				break;
			}
		}
		
		fclose($out);
	}
}