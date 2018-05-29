<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 积分抽奖
 * @author fanch
 *
 */
class Point_PrizeController extends Admin_BaseController {
	public $actions = array(
			'pointShopIndexUrl' => '/Admin/Mall_Goods/index',
			'pointCJIndexUrl' => '/Admin/Point_Prize/index',
			'presendIndexUrl' => '/Admin/Mall_Goods/presendIndex',
			'detailUrl' => '/Admin/Point_Prize/detail',
			'addUrl' => '/Admin/Point_Prize/add',
			'addPostUrl' => '/Admin/Point_Prize/addPost',
			'editUrl' => '/Admin/Point_Prize/edit',
			'editPostUrl' => '/Admin/Point_Prize/editPost',
			'configUrl' => '/Admin/Point_Prize/config',
			'configPostUrl' => '/Admin/Point_Prize/configPost',
			'logUrl' => '/Admin/Point_Prize/log',
			'sendUrl' => '/Admin/Point_Prize/send',
			'exportUrl' => '/Admin/Point_Prize/export',
			'uploadUrl' => '/Admin/Point_Prize/upload',
			'uploadPostUrl' => '/Admin/Point_Prize/uploadPost',
	);
	
	public $perpage = 10;
	
	public function indexAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$info = $this->getInput(array('title','status','start_time','end_time'));
		$params = array();
		$startTime = $endTime =  Common::getTime();
		if($info['title']) $params['title'] = array('LIKE', trim($info['title']));
		if($info['start_time']){
			$params['start_time'] = strtotime($info['start_time']);
			$startTime = strtotime($info['start_time']);
		}
		if($info['end_time']) {
			$params['end_time'] = strtotime($info['end_time']);
			$endTime = strtotime($info['end_time']);
		}
		if($info['status']){
			switch($info['status']){
				case '1': //未开始
					$params['start_time'] = array('>', $startTime);
					break;
				case '2': //进行中
					$params['start_time'] = array('<=', $startTime);
					$params['end_time'] = array('>=', $endTime);
					break;
				case '3': //已结束
					$params['end_time'] = array('<', $endTime);
					break;
			}
		}

		list($total, $data) = Point_Service_Prize::getList($page, $this->perpage, $params);
		$this->assign('data', $data);
		$this->assign('total', $total);
		$this->assign('search', $info);
		$url = $this->actions['indexUrl'].'/?'. http_build_query($info).'&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	public function addAction(){
		$this->assign('type', $this->type);
	}
	
	public function editAction(){
		$prizeId = $this->getInput('id');
		$prizeData = Point_Service_Prize::getPrize($prizeId);
		$configData = Point_Service_Prize::getConfig($prizeId);
		$this->assign('prize', $prizeData);
		$this->assign('config', $configData);
	}
	
	public function detailAction(){
		$prizeId = $this->getInput('id');
		$prizeData = Point_Service_Prize::getPrize($prizeId);
		$configData = Point_Service_Prize::getConfig($prizeId);
		$this->assign('prize', $prizeData);
		$this->assign('config', $configData);
	}
	
	
	public function addPostAction(){
		$prizeInfo = $this->getInput(array('title','start_time','end_time','img','version','point','descript','status'));
		$configInfo = $this->getInput(array(
				'prize_pos_1', 'prize_type_1', 'prize_amount_1', 'prize_day_1', 'prize_title_1', 'prize_img_1', 'prize_probability_1', 'prize_min_space_1', 'prize_max_win_1',
				'prize_pos_2', 'prize_type_2', 'prize_amount_2', 'prize_day_2', 'prize_title_2', 'prize_img_2', 'prize_probability_2', 'prize_min_space_2', 'prize_max_win_2',
				'prize_pos_3', 'prize_type_3', 'prize_amount_3', 'prize_day_3', 'prize_title_3', 'prize_img_3', 'prize_probability_3', 'prize_min_space_3', 'prize_max_win_3',
				'prize_pos_4', 'prize_type_4', 'prize_amount_4', 'prize_day_4', 'prize_title_4', 'prize_img_4', 'prize_probability_4', 'prize_min_space_4', 'prize_max_win_4',
				'prize_pos_5', 'prize_type_5', 'prize_amount_5', 'prize_day_5', 'prize_title_5', 'prize_img_5', 'prize_probability_5', 'prize_min_space_5', 'prize_max_win_5',
				'prize_pos_6', 'prize_type_6', 'prize_amount_6', 'prize_day_6', 'prize_title_6', 'prize_img_6', 'prize_probability_6', 'prize_min_space_6', 'prize_max_win_6',
				'prize_pos_7', 'prize_type_7', 'prize_amount_7', 'prize_day_7', 'prize_title_7', 'prize_img_7', 'prize_probability_7', 'prize_min_space_7', 'prize_max_win_7',
				'prize_pos_8', 'prize_type_8', 'prize_amount_8', 'prize_day_8', 'prize_title_8', 'prize_img_8', 'prize_probability_8', 'prize_min_space_8', 'prize_max_win_8',
		));
		$prizeData = $this->cookData($prizeInfo);
		//重叠区间判断
		$items  = Point_Service_Prize::getAllPrize(array('id'=>'DESC'));
		$check = $this->_checkRegion($prizeData, $items);
		if (!$check) $this->output(-1, '添加的时间区间，不能出现重叠。');
		
		$configData = $this->cookConfigData($configInfo);	
		$ret = Point_Service_Prize::save($prizeData, $configData);
		if(!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	public function editPostAction(){
		$prizeInfo = $this->getInput(array('id','title','start_time','end_time','img','version','point','descript','status'));
		$configInfo = $this->getInput(array(
				'prize_cid_1', 'prize_pos_1', 'prize_type_1', 'prize_amount_1', 'prize_day_1', 'prize_title_1', 'prize_img_1', 'prize_probability_1', 'prize_min_space_1', 'prize_max_win_1',
				'prize_cid_2', 'prize_pos_2', 'prize_type_2', 'prize_amount_2', 'prize_day_2', 'prize_title_2', 'prize_img_2', 'prize_probability_2', 'prize_min_space_2', 'prize_max_win_2',
				'prize_cid_3', 'prize_pos_3', 'prize_type_3', 'prize_amount_3', 'prize_day_3', 'prize_title_3', 'prize_img_3', 'prize_probability_3', 'prize_min_space_3', 'prize_max_win_3',
				'prize_cid_4', 'prize_pos_4', 'prize_type_4', 'prize_amount_4', 'prize_day_4', 'prize_title_4', 'prize_img_4', 'prize_probability_4', 'prize_min_space_4', 'prize_max_win_4',
				'prize_cid_5', 'prize_pos_5', 'prize_type_5', 'prize_amount_5', 'prize_day_5', 'prize_title_5', 'prize_img_5', 'prize_probability_5', 'prize_min_space_5', 'prize_max_win_5',
				'prize_cid_6', 'prize_pos_6', 'prize_type_6', 'prize_amount_6', 'prize_day_6', 'prize_title_6', 'prize_img_6', 'prize_probability_6', 'prize_min_space_6', 'prize_max_win_6',
				'prize_cid_7', 'prize_pos_7', 'prize_type_7', 'prize_amount_7', 'prize_day_7', 'prize_title_7', 'prize_img_7', 'prize_probability_7', 'prize_min_space_7', 'prize_max_win_7',
				'prize_cid_8', 'prize_pos_8', 'prize_type_8', 'prize_amount_8', 'prize_day_8', 'prize_title_8', 'prize_img_8', 'prize_probability_8', 'prize_min_space_8', 'prize_max_win_8',
		));
		$prizeData = $this->cookData($prizeInfo);
		//重叠区间判断
		$items  = Point_Service_Prize::getsByPrize(array('id'=>array('!=', $prizeInfo['id'])), array('id'=>'DESC'));
		$check = $this->_checkRegion($prizeData, $items);
		if (!$check) $this->output(-1, '添加的时间区间，不能出现重叠。');
		$configData = $this->cookConfigData($configInfo);	
		$ret = Point_Service_Prize::update($prizeData, $configData);
		if(!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	public function logAction(){
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 1;
		$info = $this->getInput(array('id', 'uname', 'prize_status', 'type', 'send_status','prize_start_time','prize_end_time','send_start_time','send_end_time'));
		$params = array();
		$params['prize_id'] = $info['id'];
		if($info['prize_status']) $params['prize_status'] = $info['prize_status']-1;
		if($info['type']){
			$configIds = $this->getConfigIds($info['id'], ($info['type']-1));
			if(!empty($configIds)){
				$params['prize_cid'] = array('IN', $configIds);
			}else{
				$params['prize_cid'] = 0;
			}
		}
		if($info['send_status']){
			$params['send_status'] = $info['send_status']-1;
		}
		if($info['prize_start_time']) {
			$params['create_time'] = array('>=', strtotime($info['prize_start_time']));
		}
		if($info['prize_end_time']) {
			$params['create_time'] = array('<=', strtotime($info['prize_end_time']));
		}
		
		if($info['prize_start_time'] && $info['prize_end_time']){
			$params['create_time'] = array(
					array('>=', strtotime($info['prize_start_time'])),
					array('<=', strtotime($info['prize_end_time']))
			);
		}
		
		if($info['send_start_time']) {
			$params['send_time'] = array('>=', strtotime($info['send_start_time']));
		}
		if($info['send_end_time']) {
			$params['send_time'] = array('<=', strtotime($info['send_end_time']));
		}
		
		if($info['send_start_time'] && $info['send_end_time']){
			$params['send_time'] = array(
					array('>=', strtotime($info['send_start_time'])),
					array('<=', strtotime($info['send_end_time']))
			);
		}
		
		if($info['uname']) 	$params['uname'] = array('LIKE', trim($info['uname']));

		
		$configData = Point_Service_Prize::getConfig($info['id']);
		$configData  = Common::resetKey($configData, 'id'); 
		list($total, $data) = Point_Service_Prize::getListLog($page, $this->perpage, $params);
		
		$this->assign('config', $configData);
		$this->assign('data', $data);
		$this->assign('total', $total);
		$this->assign('search', $info);
		$url = $this->actions['logUrl'].'/?'. http_build_query($info).'&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	private function getConfigIds($prizeId, $type){
		$temp = array();
		$data = Point_Service_Prize::getsByConfig(array('prize_id' => $prizeId, 'type' => $type));
		foreach ($data as $value){
			if($value['type'] == $type) $temp[] = $value['id'];
		}
		return $temp;
	}
	
	/**
	 * 发放操作
	 */
	public function sendAction(){
		$logId = $this->getInput('id');
		$result = Point_Service_Prize::updateLog(array('send_status' => 1,'send_time' => time()), array('id' => $logId));
		if(!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	/**
	 * 配置页面
	 */
	public function configAction(){
		$data = Game_Service_Config::getValue('point_prize_close');
		$this->assign('closeImg', $data);
	}
	
	/**
	 * 配置提交
	 */
	public function configPostAction(){
		$closeImg = $this->getInput('closeImg');
		$result = Game_Service_Config::setValue('point_prize_close', $closeImg);
		if(!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	
	/**
	 * 导出操作
	 */
	public function exportAction() {
		$page = intval($this->getInput('page'));
		$info = $this->getInput(array('id', 'uname', 'prize_status', 'type', 'send_status','prize_start_time','prize_end_time','send_start_time','send_end_time'));
		if ($page < 1) $page = 1;
		$params = array();
		$params['prize_id'] = $info['id'];
		if($info['prize_status']) $params['prize_status'] = $info['prize_status']-1;
		if($info['type']){
			$configIds = $this->getConfigIds($info['id'], ($info['type']-1));
			if(!empty($configIds)){
				$params['prize_cid'] = array('IN', $configIds);
			}else{
				$params['prize_cid'] = 0;
			}
		}
		if($info['send_status']){
			$params['send_status'] = $info['send_status']-1;
		}
		if($info['prize_start_time']) {
			$params['create_time'] = array('>=', strtotime($info['prize_start_time']));
		}
		if($info['prize_end_time']) {
			$params['create_time'] = array('<=', strtotime($info['prize_end_time']));
		}
		if($info['prize_start_time'] && $info['prize_end_time']){
			$params['create_time'] = array(
					array('>=', strtotime($info['prize_start_time'])),
					array('<=', strtotime($info['prize_end_time']))
			);
		}
		if($info['send_start_time']) {
			$params['send_time'] = array('>=', strtotime($info['send_start_time']));
		}
		if($info['send_end_time']) {
			$params['send_time'] = array('<=', strtotime($info['send_end_time']));
		}
		if($info['send_start_time'] && $info['send_end_time']){
			$params['send_time'] = array(
					array('>=', strtotime($info['send_start_time'])),
					array('<=', strtotime($info['send_end_time']))
			);
		}
		if($info['uname']) 	$params['uname'] = trim($info['uname']);
		
		$configData = Point_Service_Prize::getConfig($info['id']);
		$configData  = Common::resetKey($configData, 'id');
	
		//excel-head
		$filename = "抽奖数据_".date('YmdHis', Common::getTime());
		Util_Csv::putHead($filename);
		$title = array(array('账号', '抽奖时间', '中奖状态','奖项', '发放状态', '发放时间', '收货人', '联系电话', '收获地址'));
		Util_Csv::putData($title);
		//循环分页查询输出
		
		while(1){
			list(, $rs) = Point_Service_Prize::getListLog($page, $this->perpage, $params);
			if (!$rs) break;
			$tmp = array();
			foreach ($rs as $key=>$item) {
				$tmp[] = array(
						$item['uname'],
						date('Y-m-d  H:i:s', $item["create_time"]),
						($item['prize_status']) ? '已中奖' : '未中',
						($item['prize_status']==1) ? $configData[$item['prize_cid']]['title'] : '-',
						($item['prize_status']==1) ? (($item['send_status']) ? '已发' : '未发') : '-',
						($item["send_time"]) ? date('Y-m-d  H:i:s', $item["send_time"]): '-',
						($item['receiver']) ? $item['receiver'] : '-',
						($item['mobile']) ? $item['mobile'] : '-',
						($item['address']) ? $item['address'] : '-',
					);
			}
			Util_Csv::putData($tmp);
			$page ++;
		}
		
		exit;
	}
	
	/**
	 * 
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
	public function uploadPostAction() {
		$ret = Common::upload('img', 'prize');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
		
	/**
	 * 抽奖活动参数过滤
	 * @param array $info
	 * @return array
	 */
	private function cookData($info){
		if(!$info['title']) $this->output(-1, '抽奖活动名称不能为空.');
		if(!$info['start_time']) $this->output(-1, '抽奖活动开始时间不能为空.');
		if(!$info['end_time']) $this->output(-1, '抽奖活动结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time'] = strtotime($info['end_time']);
		if($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		if(!$info['img']) $this->output(-1, '抽奖活动图片不能为空.');
		if(!$info['point']) $this->output(-1, '抽奖活动消耗积分数不能为空.');
		if(!$info['descript']) $this->output(-1, '抽奖活动描述不能为空.');
		return $info;
	}
	
	private function _checkRegion($info, $items){
		$flag = true;
		if(!$items) return $flag;
		foreach ($items as $value){
			if((intval($info['start_time']) <= intval($value['end_time'])) && (intval($value['start_time']) <= intval($info['end_time']))){
				$flag = false;
				break;
			}
		}
		return $flag;
	}
	
	private function cookConfigData($info){
		$temp = array();
		$flag = 0;
		for($i=1;$i<=8;$i++){
			$temp[]= $this->cookConfigItem($info, $i);
			if($info['prize_type_' . $i] == 0) $flag ++;
		}
		if($flag == 0) $this->output(-1, "奖项至少要配置一个未中的奖品.");
		if($flag == 8) $this->output(-1, "奖项不能全部都是未中的奖品.");
		return $temp;
	}
	
	private function cookConfigItem($info, $i){
		$temp = array();
		if($info['prize_cid_' . $i]) $temp['id'] = $info['prize_cid_' . $i];
		$temp['pos'] = $info['prize_pos_' . $i];
		$temp['type'] = $info['prize_type_' . $i];
		switch($info['prize_type_' . $i]){
			case '1':
				if(!$info['prize_title_' . $i]) $this->output(-1, "位置{$i} 奖品名称不能为空.");
				$temp['title'] = $info['prize_title_' . $i];
				if(!$info['prize_img_' . $i]) $this->output(-1, "位置{$i} 奖品图片不能为空.");
				$temp['img'] = $info['prize_img_' . $i];
				if($info['prize_probability_' . $i] < 0) $this->output(-1, "位置{$i} 奖品概率不能小于0.");
				$temp['probability'] = $info['prize_probability_' . $i];
				if($info['prize_min_space_' . $i] < 0) $this->output(-1, "位置{$i} 奖品最小间隔不能小于0.");
				$temp['min_space'] = $info['prize_min_space_' . $i];
				if($info['prize_max_win_' . $i] < 0) $this->output(-1, "位置{$i} 奖品每日发放最大数量不能小于0.");
				$temp['max_win'] = $info['prize_max_win_' . $i];
				$temp['amount'] = 0;
				$temp['day'] = 0;
				break;
			case '2':
				if(!isset($info['prize_amount_' . $i])) $this->output(-1, "位置{$i} A券数量不能为空.");
				if($info['prize_amount_' . $i] <= 0) $this->output(-1, "位置{$i} A券数量不能小于等于0.");
				$temp['amount'] = $info['prize_amount_' . $i];
				if(!isset($info['prize_day_' . $i])) $this->output(-1, "位置{$i} A券有效期不能为空.");
				if($info['prize_day_' . $i] <= 0) $this->output(-1, "位置{$i} A券有效期不能小于等于0.");
				$temp['day'] = $info['prize_day_' . $i];
				if(!$info['prize_title_' . $i]) $this->output(-1, "位置{$i} 奖品名称不能为空.");
				$temp['title'] = $info['prize_title_' . $i];
				if(!$info['prize_img_' . $i]) $this->output(-1, "位置{$i} 奖品图片不能为空.");
				$temp['img'] = $info['prize_img_' . $i];
				if($info['prize_probability_' . $i] < 0) $this->output(-1, "位置{$i} 奖品概率不能小于0.");
				$temp['probability'] = $info['prize_probability_' . $i];
				if($info['prize_min_space_' . $i] < 0) $this->output(-1, "位置{$i} 奖品最小间隔不能小于0.");
				$temp['min_space'] = $info['prize_min_space_' . $i];
				if($info['prize_max_win_' . $i] < 0) $this->output(-1, "位置{$i} 奖品每日发放最大数量不能小于0.");
				$temp['max_win'] = $info['prize_max_win_' . $i];
				break;
			case '3':
				if(!isset($info['prize_amount_' . $i])) $this->output(-1, "位置{$i} 积分数量不能为空.");
				if($info['prize_amount_' . $i] <= 0) $this->output(-1, "位置{$i} 积分数量不能小于等于0.");
				$temp['amount'] = $info['prize_amount_' . $i];
				if(!$info['prize_title_' . $i]) $this->output(-1, "位置{$i} 奖品名称不能为空.");
				$temp['title'] = $info['prize_title_' . $i];
				if(!$info['prize_img_' . $i]) $this->output(-1, "位置{$i} 奖品图片不能为空.");
				$temp['img'] = $info['prize_img_' . $i];
				if($info['prize_probability_' . $i] < 0) $this->output(-1, "位置{$i} 奖品概率不能小于0.");
				$temp['probability'] = $info['prize_probability_' . $i];
				if($info['prize_min_space_' . $i] < 0) $this->output(-1, "位置{$i} 奖品最小间隔不能小于0.");
				$temp['min_space'] = $info['prize_min_space_' . $i];
				if($info['prize_max_win_' . $i] < 0) $this->output(-1, "位置{$i} 奖品每日发放最大数量不能小于0.");
				$temp['max_win'] = $info['prize_max_win_' . $i];
				$temp['day'] = 0;
				break;
			default:
				if(!$info['prize_img_'.$i]) $this->output(-1, "位置{$i} 奖品图片不能为空.");
				$temp['img'] = $info['prize_img_' . $i];
				$temp['amount'] = 0;
				$temp['day'] = 0;
				$temp['title'] = '';
				$temp['probability'] = 0;
				$temp['min_space'] = 0;
				$temp['max_win'] = 0;
		}
		return $temp;
	}
	
}