<?php
/*
 * 全部应用
 */
class AppManageAction extends SystemAction {
	
	
	//ApkID 	AppID 	包名 	版本 	包大小 	注册帐号 	应用名称 	应用状态 	发布日期 	版本
	protected $_export_config = array(
		'apk_id' => array('ApkID','apkInfo','callback','{{field_val}},id','id'),
		'id' => array('AppID'),
		'package' => array('包名','apkInfo','callback','{{field_val}},package','id'),
		'version_name' => array('版本','apkInfo','callback','{{field_val}},version_name','id'),
		'file_size' => array('包大小','apkInfo','callback','{{field_val}},file_size','id'),
		'email' => array('注册帐号','getAccountField','callback','{{field_val}},email','author_id'),
		'app_name' => array('应用名称','apkInfo','callback','{{field_val}},app_name','id'),
		'status' => array('应用状态','apkInfo','callback','{{field_val}},status','id'),
		'created_at' => array('发布日期','date','function','"Y-m-d H:i:s",{{field_val}}'),
		'apk_count' => array('版本数量','apkInfo','callback','{{field_val}},apk_count','id'),
	);
	
	function _filter(&$map){
		$search = $map = MAP();
		$_search = $_REQUEST['_search'];
		
		unset($map['status']);
		if(strlen($_search['status']) > 0)
			$map['status'] = floatval($_search['status']);

		$author = $_search['author'];
		if(isset($author) && $author)
		{
			$account = D("Dev://Accounts")->getUserByEmail($author);
			$map['author_id'] = $account['id'];
		}
		$account = $_search['_search']['account'];
		if(isset($account) && $account)
		{
			$admin_info = D("Admin")->where(array('account'=>$account ))->find();
			if(!empty($admin_info)){
				$map['admin_id'] = intval($admin_info['id']);
			}
		}
		if(isset($_search['timeStart']) && $_search['timeStart']){
			$timeStart = strtotime($_search["timeStart"]." 00:00:00");
			$map['onlined_at'] = array('EGT',$timeStart);
		}
		if(isset($_search['timeEnd']) && $_search['timeEnd']){
			$timeEnd = strtotime($_search["timeEnd"]." 23:59:59");
			$map['onlined_at'] = array('EGT',$timeStart);
		}	
		if($_search['timeStart'] && $_search['timeEnd']){
			if ($timeEnd <= $timeStart) {
				$this->error("结束时间不能小于等于开始时间");
			}
			$map['onlined_at'] = array("between", array($timeStart, $timeEnd));
		}
		$_REQUEST['orderField'] = 'id';
		$_REQUEST['orderDirection'] = 'desc';
	}

	function bspackage()
	{
		$apk_id = $this->_get("apk_id", "intval", 0);
		$this->patchs = D("Bspackage")->where(sprintf("b_apk_id=%d or s_apk_id=%d", $apk_id, $apk_id))->select();
		$this->assign("apk_id", $apk_id);
		$this->display();
	}
	
	function bs_status()
	{
		$pid = $this->_get("pid", "intval", 0);
		$status = $this->_get("status", "intval", 0);
		
		if(!$vo = D("Bspackage")->where(array("id"=>$pid))->find())
			$this->error("记录不存在");

		if($status)
			$data["checked_at"] = time();
		
		$data["updated_at"] = time();
		$data["admin_id"] = admin_id();
		$data["status"] = $status;
		
		D("Bspackage")->where(array("id"=>$pid))->save($data);
		
		//同步数据，上线的应用通知应用同步接口
		if(D("Apks")->where(array("app_id"=>$vo['app_id'], "status"=>3))->count())
			helper("Sync")->done($vo['app_id'], 'online');
		
		$this->success("操作完成");
	}
	
	function apks()
	{
		$app_id = $this->_get("app_id", "intval", 0);
		$this->apks = D("Apks")->where(array("app_id"=>$app_id))->order(array("id"=>"desc"))->select();
		$this->display();
	}

	function _before_index(){
		$this->model = "Apps";
	}
	
	function _before_export(){
		$this->model = "Apps";
	}
	
	/*
	 * 应用统计
	 */
	public function stat()
	{
		$d_apk = D('Dev://apks');
		$d_union = D('Union');
		$is_join = (int) $this->_get('join');
		$game_add =  $game_check =$date_arr = array();
		//游戏统计
		$max_day = 30;
		$day_len = 24*3600;

		for ($i=0;$i < $max_day;$i++)
		{
			$day_time = mktime(0,0,0,date('m'),Date('d')-$i);
			$date_arr[$i] = '"'.Date('Y-m-d',$day_time).'"';
			$time_cond = array('between',array($day_time,$day_time+$day_len));
			
			if ($is_join == 1)	//联运
			{
				$add_map = array('created_at' => $time_cond);
				$game_add[$i] = $d_union->where($add_map)->count();
				
				$up_map = array('authorized_at' => $time_cond,'status' => UnionModel::ALLOW);
				$game_check[$i] = $d_union->where($up_map)->count();
				
			}else 
			{
				//游戏增加数
				$add_map = array('is_join' => $is_join,'created_at' => $time_cond);
				$game_add[$i] = $d_apk->where($add_map)->count();
				//var_dump(Date('Y-m-d H:i:s',$day_time),$d_apk->getlastsql());exit;
				
				//游戏审核通过
				$up_map = array('is_join' => $is_join, 'checked_at' => $time_cond,'status' => array('in',array(ApksModel::APK_TEST_PASS, ApksModel::APK_ONLINE)));
				$game_check[$i] = $d_apk->where($up_map)->count();
				//var_dump($game_add,$game_update,$game_check);//exit();
			}
			//通过率
			$pass_rate[$i] = $game_add[$i]==0?0:floor($game_check[$i]*100/$game_add[$i]);
		}
		//var_dump($date_arr,$game_add,$game_check);exit;
		$this->assign('date_arr',$date_arr);
		$this->assign('game_add',$game_add);
		$this->assign('game_check',$game_check);
		$this->assign('pass_rate',$pass_rate);
		$this->display();
	}
	
	//显示apk详情
	function detail(){
		$apk_id = $this->_get("apk_id","intval",0);
		$apkinfo = D("Dev://Apks")->find($apk_id);
		
		$this->_getLebals();
		$this->assign("vo", $apkinfo);
		$this->display();
	}
	
	/**
	 * 安全日志
	 */
	public function safelog() {
		$apk_id= $this->_get('apk_id');
		$d_apksafe = D('ApkSafe');
		
		$list = $d_apksafe->field('apk_safe.*, apks.app_id, apks.app_name, apks.version_name, apks.package ')->join('apks on apks.id= apk_safe.apk_id ')->where(array('apks.id' => $apk_id))->select();
		$this->assign('list',$list);
		$this->display();
	}
	
	/*
	 * 根据app_id获得apk信息
	 * @param int $app_id
	 * @param string $field
	 */
	public function apkInfo($app_id,$field='*')
	{
		$vo = D('Apps')->find($app_id);
		if(!empty($vo['main_apk_id']))
			$apkWhere = array("id"=>$vo['main_apk_id']);
		else
			$apkWhere = array("app_id"=>$vo['id']);
		$apkModel = D("Apks");
		$apk = $apkModel->field($field)->where($apkWhere)->order("id desc")->find();
		if ($field != '*') {
			if ($field == 'file_size')
			{
				return implode('', showsize($apk[$field]));
			}elseif ($field == 'apk_count')
			{
				return $apkModel->where(array("app_id"=>$app_id))->count();;
			}elseif ($field == 'status')
			{
				return $apkModel->getApkStatusByStatus($apk['status']);
			}
			return $apk[$field];
		}else 
		{
			return $apk;
		}
	}
	
}