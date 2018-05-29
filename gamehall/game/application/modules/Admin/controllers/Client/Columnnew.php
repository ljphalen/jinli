<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * Enter description here ...
 * @author luojiapeng
 *
 */
class Client_ColumnnewController extends Admin_BaseController {

	public $actions= array( 
			'listUrl' => '/Admin/Client_Columnnew/index',
			'addColumnUrl'=> '/Admin/Client_Columnnew/addColumn',
			'addColumnPostUrl' => '/Admin/Client_Columnnew/addColumnPost',
			'channelSetUrl'=> '/Admin/Client_Columnnew/channelSet',
			'channelSetPostUrl' => '/Admin/Client_Columnnew/channelSetPost',
			'channelSortUrl'=> '/Admin/Client_Columnnew/channelSort',
			'channelSortPostUrl' => '/Admin/Client_Columnnew/channelSortPost',
			'cloumnAttriUrl'=> '/Admin/Client_Columnnew/cloumnAttri',
			'cloumnAttriPostUrl' => '/Admin/Client_Columnnew/cloumnAttriPost',
			'editColumnUrl'=> '/Admin/Client_Columnnew/editColumn',
			'editColumnPostUrl' => '/Admin/Client_Columnnew/editColumnPost',
			'editChannelSetUrl'=> '/Admin/Client_Columnnew/editChannelSet',
			'editChannelSetPostUrl' => '/Admin/Client_Columnnew/editChannelSetPost',
			'editChannelSortUrl'=> '/Admin/Client_Columnnew/editChannelSort',
			'editChannelSortPostUrl' => '/Admin/Client_Columnnew/editChannelSortPost',
			'editCloumnAttriUrl'=> '/Admin/Client_Columnnew/editCloumnAttri',
			'editCloumnAttriPostUrl' => '/Admin/Client_Columnnew/editCloumnAttriPost',
			
			'viewColumnUrl'=> '/Admin/Client_Columnnew/viewColumn',
			'viewChannelSetUrl'=> '/Admin/Client_Columnnew/viewChannelSet',
			'viewChannelSortUrl'=> '/Admin/Client_Columnnew/viewChannelSort',
			'viewCloumnAttriUrl'=> '/Admin/Client_Columnnew/viewCloumnAttri',
			
			'uploadUrl' => '/Admin/Client_Columnnew/upload',
			'uploadPostUrl' => '/Admin/Client_Columnnew/upload_post',
			'infoUrl' =>  '/Admin/Client_Columnnew/info'
	);

	public $perpage = 20;	
	public $channel_type = array(1=>'默认',2=>'扩展');
	

	public function indexAction() {
	 	$log_param['step'] = array('<',4);
		$ret = Client_Service_ColumnLog::getsBy($log_param);
		$temp = array();
		if($ret){
			foreach ($ret as $val){
				if(strtotime($val['create_time']) >= strtotime($val['create_time']." -20 minute")){
					$temp[] = $val['id'];
				}
			}
			foreach ($temp as $val){
				Client_Service_ColumnNew::deleteBy(array('log_id'=>$val));
				Client_Service_ColumnLog::deleteBy(array('id'=>$val));
			}
		} 
		$page = intval($this->getInput('page'));
		if($page < 1) $page = 1;
		
		$column_version = $this->getInput('column_version');
		$column_version = $column_version?$column_version:'1.5.2';
		
		$column_name   = trim($this->getInput('column_name'));
		$status = $this->getInput('status');
		
		$search = array();
		$params = array();
		if ($column_name) {
			$search['column_name'] = $column_name ;
			$params['column_name'] = array('LIKE', $column_name);
		}
		
		if ($status) {
			$search['status'] = $status;
			$params['status'] = intval($status) - 1;
		}
		
		if($column_version){
			$params['column_version'] = $column_version;
			$search['column_version'] = $column_version;
		}
		
		//$params['step'] = 4;
		
		list($total, $data) = Client_Service_ColumnLog::getList($page, $this->perpage, $params,array('id'=>'DESC','start_time'=>'DESC'));
		
		$this->assign('column_version', $column_version);
		$this->assign('search', $search);
		$this->assign('info', $data);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}

	/**
	 *
	 * Enter description here ...
	 */
	public function addColumnAction() {
		
		$column_version = $this->getInput('column_version');
		//查询条件
		$params['pid'] = 0;
		$params['is_deafault'] = 0;
		$params['column_version'] = $column_version;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 20, $params);
		$this->assign('info', $data);
		$this->assign('column_version', $column_version);
		
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editColumnAction() {
	
		$log_id = intval($this->getInput('log_id'));
		$column_version = $this->getInput('column_version');
		if(!$log_id){
			$this->output(-1,  '非法操作.');
		}
		//查询条件
		$params['pid'] = 0;
		$params['is_deafault'] = 1;
		$params['column_version'] = $column_version;
		$params['log_id'] = $log_id;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 20, $params);
		$this->assign('info', $data);
		$this->assign('column_version', $column_version);
		$this->assign('log_id', $log_id);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function viewColumnAction() {
	
		$log_id = intval($this->getInput('log_id'));
		$column_version = $this->getInput('column_version');
		if(!$log_id){
			$this->output(-1,  '非法操作.');
		}
		//查询条件
		$params['pid'] = 0;
		$params['log_id'] = $log_id;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 20, $params);
		$this->assign('info', $data);
		$this->assign('log_id', $log_id);
		$this->assign('column_version', $column_version);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function addColumnPostAction() {
		
		$column_version = $this->getInput('column_version');
		$tmp = array();
		//验证名称的合法行
		for($i = 1 ; $i <= 5;$i++ ){
			$tmp[] = trim($this->getInput('name'.$i));
		}

		//比较数组的长度
		$ori_length = count($tmp);
		$end_length = count(array_unique($tmp));
		if($ori_length != $end_length){
			$this->output(-1, '名称重复，请重新填写');
		}
		
		$data = array();
		for($i = 1 ; $i <= 5;$i++ ){
			$info['name'] = trim($this->getInput('name'.$i));
			$info['position'] = $i;
			$info['status'] = $this->getInput('status'.$i);
			$info['icon_choose'] = $this->getInput('icon_choose'.$i);
			$info['icon_default'] = $this->getInput('icon_default'.$i);
			$info['relevance'] = $this->getInput('relevance'.$i);
			$info['default_open'] = $this->getInput('default_open');
			$default_open = $info['default_open'];
			$info['level'] = trim($this->getInput('level'));
			$info['is_deafault'] = 1;
			$info['column_version'] = $column_version;
			//哪个频道是默认打开的
			if ($info['default_open'] == $i){
				$info['default_open'] = 1;
			}else{
				$info['default_open'] = 0;
			}
	 
			if (!$info['name']){
				$this->output(-1,  '名称不能为空.');
			}
			
			if($info['position'] > 5 || $info['position'] < 1){
					$this->output(-1, '位置只能1-5之间');
			}
			if (!$info['icon_choose'] || !$this->_checkImg($info['icon_choose'])){
				$this->output(-1, '位置为'.$i.'的选中图片不能为空.');
			}
			if (!$info['icon_default'] || !$this->_checkImg($info['icon_default'])){
				$this->output(-1, '位置为'.$i.'的默认图片不能为空.');
			}
			
			if(($info['status'] == 0) && ($default_open == $i) ){
				$this->output(-1, '默认打开不能关闭');
			}
		
			$tmp['status'][] = $info['status'];
				
			//判断是否开启四个栏目以上
			if(count($tmp['status']) == 5){
				$status_arr = array_count_values($tmp['status']);
				if($status_arr[1] < 4){
					$this->output(-1, '至少要开启4个栏目');
				}
			}

			$info['update_time']=Common::getTime();
			$data[] = $info;
		}
		
		//日志表
		$log_data['column_version'] = $column_version;
		$log_data['column_num'] = 5;
		$log_data['admin_id'] = $this->userInfo['uid'];
		$log_data['admin_name'] = $this->userInfo['username'];
		$log_data['update_time'] = Common::getTime();
		$log_data['step'] = 1;
		$log_data['is_deafault'] = 1;
		Common_Service_Base::beginTransaction();
		$ret = Client_Service_ColumnLog::addColumn($log_data);
		if (!$ret) $this->output(-1, '操作失败1');
		$ret_channel = array();
		if($ret){
			foreach ($data as $val){
				$channel_data= array('name'=>$val['name'],
						                'position'=>$val['position'],
										'status'=>$val['status'],
										'icon_choose'=>$val['icon_choose'],
						                'icon_default'=>$val['icon_default'],
						                'relevance'=>$val['relevance'],
						                'default_open'=>$val['default_open'],
						                'level'=>$val['level'],
						                'update_time'=>$val['update_time'],
						                'is_deafault'=>$val['is_deafault'],
										'column_version'=>$val['column_version'],
						                'log_id'=>$ret,
						);
				$ret_channel[] = Client_Service_ColumnNew::addColumn($channel_data);
			}
		}
		if($ret_channel[0] && $ret_channel[1] && $ret_channel[2] && $ret_channel[3] && $ret_channel[4] ){
			Common_Service_Base::commit();
			$this->output(0, '操作成功',array('column_version'=>$column_version,'log_id'=>$ret,'default_open'=>$this->getInput('default_open')));
		}else{
			Common_Service_Base::rollBack();
			$this->output(-1, '操作失败2');
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editColumnPostAction() {
	
		$log_id = intval($this->getInput('log_id'));
		$column_version = $this->getInput('column_version');
		if(!$log_id){
			$this->output(-1,  '非法操作.');
		}
		$tmp = array();
		//验证名称的合法行
		for($i = 1 ; $i <= 5;$i++ ){
			$tmp[] = trim($this->getInput('name'.$i));
		}
	
		//比较数组的长度
		$ori_length = count($tmp);
		$end_length = count(array_unique($tmp));
		if($ori_length != $end_length){
			$this->output(-1, '名称重复，请重新填写');
		}
	
		$data = array();
		for($i = 1 ; $i <= 5;$i++ ){
			$info['column_id'] = trim($this->getInput('column_id'.$i));
			$info['name'] = trim($this->getInput('name'.$i));
			$info['position'] = $i;
			$info['status'] = $this->getInput('status'.$i);
			$info['icon_choose'] = $this->getInput('icon_choose'.$i);
			$info['icon_default'] = $this->getInput('icon_default'.$i);
			$info['relevance'] = $this->getInput('relevance'.$i);
			$info['default_open'] = $this->getInput('default_open');
			$default_open = $info['default_open'];
			$info['level'] = trim($this->getInput('level'));
			$info['is_deafault'] = 1;
			$info['column_version'] = $column_version;
			//哪个频道是默认打开的
			if ($info['default_open'] == $i){
				$info['default_open'] = 1;
			}else{
				$info['default_open'] = 0;
			}
	
			if (!$info['name']){
				$this->output(-1,  '名称不能为空.');
			}
				
			if($info['position'] > 5 || $info['position'] < 1){
				$this->output(-1, '位置只能1-5之间');
			}
			if (!$info['icon_choose'] || !$this->_checkImg($info['icon_choose'])){
				//$this->output(-1, '位置为'.$i.'的选中图片不能为空.');
			}
			if (!$info['icon_default'] || !$this->_checkImg($info['icon_default'])){
				//$this->output(-1, '位置为'.$i.'的默认图片不能为空.');
			}
				
			if(($info['status'] == 0) && ($default_open == $i) ){
				$this->output(-1, '默认打开不能关闭');
			}
	
			$tmp['status'][] = $info['status'];
	
			//判断是否开启四个栏目以上
			if(count($tmp['status']) == 5){
				$status_arr = array_count_values($tmp['status']);
				if($status_arr[1] < 4){
					$this->output(-1, '至少要开启4个栏目');
				}
			}
			$info['update_time']=Common::getTime();
			$data[] = $info;
		}
		//保存到临时字段中temp1
		$log_data['temp1'] = serialize($data);
		$ret = Client_Service_ColumnLog::updateByID($log_data, $log_id);
		if($ret){
			$this->output(0, '操作成功',array('column_version'=>$column_version, 'log_id'=>$log_id, 'default_open'=>$this->getInput('default_open')));
		}else{
			$this->output(-1, '操作失败2');
		}
		
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function channelSetAction() {
	    //header("Content-type:text/html;charset=utf-8");
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		//查询条件
		$params['pid'] = array('>', 0);
		$params['is_deafault'] = 0;
		$params['column_version'] = $column_version;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 50, $params, array('pid' => 'ASC','position'=>'ASC'));
		$this->assign('info', $data);
		$this->assign('column_version', $column_version);
		$this->assign('default_open', $default_open);
		$this->assign('log_id', $log_id);
		$this->assign('channel_type', $this->channel_type);
		$show_type =  Common::getConfig('apiConfig','ext_type');
		$this->assign('show_type',$show_type );
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editChannelSetAction() {
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		//查询条件
		$params['pid'] = array('>', 0);
	    $params['log_id'] = $log_id;
		$params['column_version'] = $column_version;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 50, $params, array('pid' => 'ASC','position'=>'ASC'));
		$this->assign('info', $data);
		$this->assign('column_version', $column_version);
		$this->assign('log_id', $log_id);
		$this->assign('default_open', $default_open);
		$this->assign('channel_type', $this->channel_type);
		$show_type =  Common::getConfig('apiConfig','ext_type');
		$this->assign('show_type',$show_type );
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function viewChannelSetAction() {
		$log_id = intval($this->getInput('log_id'));
		$column_version = $this->getInput('column_version');
		//查询条件
		$params['pid'] = array('>', 0);
		$params['log_id'] = $log_id;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 50, $params, array('pid' => 'ASC','position'=>'ASC'));
		$this->assign('info', $data);
		$this->assign('log_id', $log_id);
		$this->assign('column_version', $column_version);
		$this->assign('channel_type', $this->channel_type);
		$show_type =  Common::getConfig('apiConfig','ext_type');
		$this->assign('show_type',$show_type );
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function channelSetPostAction() {
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		$channel_total= $this->getInput('channel_total');
		
		if (!$log_id) $this->output(-1,  '非法操作1.');	
			
		$ret = Client_Service_ColumnLog::getBy(array('id'=>$log_id));
		if(!$ret){
			$this->output(-1,  '非法操作2.');
		}
		
		$temp1 = $temp2 = array();
		$temp1 =  Client_Service_ColumnNew::getListBywhere(array('column_version'=>$column_version,'pid'=>array('>',0),'is_deafault'=>0));
		$temp2 =  Client_Service_ColumnNew::getListBywhere(array('log_id'=>$log_id,'pid'=>array('>',0)));
		if(count($temp1) <= count($temp2)){
			$this->output(-1,  '非法操作3.');
		}
		
		$tmp = array();
		//验证名称的合法行
		for($i = 1 ; $i <= $channel_total;$i++ ){
			$tmp[] = trim($this->getInput('name'.$i));
		}
		//比较数组的长度
		$ori_length = count($tmp);
		$end_length = count(array_unique($tmp));
		if($ori_length != $end_length){
			$this->output(-1, '名称重复，请重新填写');
		}
		
		//拼接数据
		for($i = 1 ; $i <= $channel_total;$i++ ){
			$info['name'] = trim($this->getInput('name'.$i));
			$info['status'] = 1; //$this->getInput('status'.$i);
			$info['channel_type'] = $this->getInput('channel_type'.$i);
			$info['icon_default'] = $this->getInput('icon_default'.$i);
			$info['show_type'] = $this->getInput('show_type'.$i);
			$info['relevance'] = $this->getInput('relevance'.$i);
			$info['link'] = $this->getInput('link'.$i);
			$info['level'] = $this->getInput('level'.$i);
			$info['pid'] = $this->getInput('pid'.$i);
			$info['position'] = $this->getInput('position'.$i);
			
			$info['is_deafault'] = 1;
			$info['column_version'] = $column_version;
			$info['log_id'] = 1;
			$info['update_time'] = Common::getTime();
			$info['is_disabled'] = $this->getInput('channel_disabled'.$i);
			if (!$info['name']){
				$this->output(-1,  '名称不能为空.');
			}
			$data[] = $info;
		}
		//更新日志表
		$log_data['update_time'] = Common::getTime();
		$log_data['step'] = 2;
		$log_data['admin_id'] = $this->userInfo['uid'];
		$log_data['admin_name'] = $this->userInfo['username'];
		Common_Service_Base::beginTransaction();
		$ret = Client_Service_ColumnLog::updateByID($log_data, $log_id);
		if (!$ret) $this->output(-1, '操作失败1');
		if($ret){
			foreach ($data as $val){
				$channel_data= array('name'=>$val['name'],
						'status'=>$val['status'],
						'channel_type'=>$val['channel_type'],
						'icon_default'=>$val['icon_default'],
						'show_type'=>$val['show_type'],
						'relevance'=>$val['relevance'],
						'link'=>$val['link'],
						'level'=>$val['level'],
						'pid'=>$val['pid'],
						'position'=>$val['position'],
						'is_deafault'=>$val['is_deafault'],
						'column_version'=>$val['column_version'],
						'log_id'=>$log_id,
                                                'is_disabled' => $val['is_disabled'],
						'update_time'=>$val['update_time'],
				);
				$ret_channel[] = Client_Service_ColumnNew::addColumn($channel_data);
			}
		}
		//事务提交
		if( !in_array(false, $ret_channel) ){
			Common_Service_Base::commit();
			$this->output(0, '操作成功',array('column_version'=>$column_version ,'log_id'=>$log_id ,'default_open'=>$default_open));
		}else{
			Common_Service_Base::rollBack();
			$this->output(-1, '操作失败2');
		}
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editChannelSetPostAction() {
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		$channel_total= $this->getInput('channel_total');
	
		if (!$log_id) $this->output(-1,  '非法操作1.');
			
		$ret = Client_Service_ColumnLog::getBy(array('id'=>$log_id));
		if(!$ret){
			$this->output(-1,  '非法操作2.');
		}
	
		$tmp = array();
		//验证名称的合法行
		for($i = 1 ; $i <= $channel_total;$i++ ){
			$tmp[] = trim($this->getInput('name'.$i));
		}
		//比较数组的长度
		$ori_length = count($tmp);
		$end_length = count(array_unique($tmp));
		if($ori_length != $end_length){
			$this->output(-1, '名称重复，请重新填写');
		}
	
		//拼接数据
		for($i = 1 ; $i <= $channel_total;$i++ ){
			$info['column_id'] = trim($this->getInput('column_id'.$i));
			$info['name'] = trim($this->getInput('name'.$i));
			$info['status'] = 1; //$this->getInput('status'.$i);
			$info['channel_type'] = $this->getInput('channel_type'.$i);
			$info['icon_default'] = $this->getInput('icon_default'.$i);
			$info['show_type'] = $this->getInput('show_type'.$i);
			$info['relevance'] = $this->getInput('relevance'.$i);
			$info['link'] = $this->getInput('link'.$i);
			$info['level'] = $this->getInput('level'.$i);
			$info['pid'] = $this->getInput('pid'.$i);
			$info['position'] = $this->getInput('position'.$i);
			$info['is_deafault'] = 1;
                        $info['is_disabled'] = $this->getInput('disabled'.$i);
			$info['column_version'] = $column_version;
			$info['log_id'] = 1;
			$info['update_time'] = Common::getTime();
			if (!$info['name']){
				$this->output(-1,  '名称不能为空.');
			}
			$data[] = $info;
		}
		
		//保存到临时字段中temp1
		$log_data['temp2'] = serialize($data);
		$ret = Client_Service_ColumnLog::updateByID($log_data, $log_id);
		if($ret){
			$this->output(0, '操作成功',array('column_version'=>$column_version ,'log_id'=>$log_id ,'default_open'=>$default_open));
		}else{
			$this->output(-1, '操作失败2');
		}
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function channelSortAction() {
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		
		$parent_params['pid'] = 0;
		$parent_params['log_id'] = $log_id;
		$parent_params['column_version'] = $column_version;
		list(,$parent_arr) = Client_Service_ColumnNew::getColumnList(1, 5, $parent_params);
		$parent_arr = Common::resetKey($parent_arr,'position');
		$params['pid']  = array('>',0);
		$params['name'] = array('!=','');
		$params['log_id'] = $log_id;
		$params['column_version'] = $column_version;
		
		
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 50, $params);
		
		$params['status'] = 1;
		list($total, $temp) = Client_Service_ColumnNew::getColumnList(1, 50, $params);
		foreach ($temp as $val){
			if($val['name']){
				$positon_arr[$val['pid'].'-'.$val['position']] = $val['name'] ;
			}
		}

		$this->assign('parentinfo', $parent_arr);
		$this->assign('info', $data);
		$this->assign('positon_arr', $positon_arr);
		$this->assign('column_version', $column_version);
		$this->assign('default_open', $default_open);
		$this->assign('log_id', $log_id);
		$this->assign('channel_type', $this->channel_type);
		$show_type =  Common::getConfig('apiConfig','ext_type');
		$this->assign('show_type',$show_type );
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editChannelSortAction() {
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
	

		$log_info = Client_Service_ColumnLog::getBy(array('id'=>$log_id));
		$parent_arr = unserialize($log_info['temp1']);
		
		header("Content-type:text/html;charset=utf-8");
		$nameArr = unserialize($log_info['temp2']);
        foreach ($nameArr as $val){
        	$columnName[$val['column_id']] = $val['name'];
        }
		
		
		$params['pid']  = array('>',0);
		$params['log_id'] = $log_id;
		$params['column_version'] = $column_version;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 50, $params);
	
		$params['status'] = 1;
		list($total, $temp) = Client_Service_ColumnNew::getColumnList(1, 50, $params);
		foreach ($temp as $val){
			if($val['name']){
				$positon_arr[$val['pid'].'-'.$val['position']] = array_key_exists($val['id'],$columnName)?$columnName[$val['id']]:$val['name'] ;
			}
		}
	
		$info = array();
		foreach ($data as $val){
			$info[] = array('id'=>$val['id'],
					'position'=>$val['position'],
					'channel_type'=>$val['channel_type'],
					'pid'=>$val['pid'],
					'name'=>array_key_exists($val['id'],$columnName)?$columnName[$val['id']]:$val['name'],
					'link'=>$val['link'],
					'icon_choose'=>$val['icon_choose'],
					'status'=>$val['status'],
					'create_time'=>$val['create_time'],
					'default_open'=>$val['default_open'],
					'show_type'=>$val['show_type'],
					'relevance'=>$val['relevance'],
					'icon_default'=>$val['icon_default'],
					'level'=>$val['level'],
					'column_version'=>$val['column_version'],
					'is_deafault'=>$val['is_deafault'],
                                        'is_disabled'=>$val['is_disabled'],
					'log_id'=>$val['log_id'],
					'update_time'=>$val['update_time']					         
					);
		}
		
		$this->assign('parentinfo', $parent_arr);
		$this->assign('info', $info);
		$this->assign('positon_arr', $positon_arr);
		$this->assign('default_open', $default_open);
		$this->assign('column_version', $column_version);
		$this->assign('log_id', $log_id);
		$this->assign('channel_type', $this->channel_type);
		$show_type =  Common::getConfig('apiConfig','ext_type');
		$this->assign('show_type',$show_type );
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function viewChannelSortAction() {
		$log_id = intval($this->getInput('log_id'));
		$column_version = $this->getInput('column_version');
		$parent_params['pid'] = 0;
		$parent_params['log_id'] = $log_id;
		list(,$parent_arr) = Client_Service_ColumnNew::getColumnList(1, 5, $parent_params);
		
		$params['pid']  = array('>', 0);
		$params['log_id'] = $log_id;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 50, $params);

	
		$params['status'] = 1;
		list($total, $temp) = Client_Service_ColumnNew::getColumnList(1, 50, $params);
		foreach ($temp as $val){
			if($val['name']){
				$positon_arr[$val['pid'].'-'.$val['position']] = $val['name'] ;
			}
		}
		$this->assign('parentinfo', $parent_arr);
		$this->assign('info', $data);
		$this->assign('positon_arr', $positon_arr);
	
		$this->assign('log_id', $log_id);
		$this->assign('column_version', $column_version);
		$this->assign('channel_type', $this->channel_type);
		$show_type =  Common::getConfig('apiConfig','ext_type');
		$this->assign('show_type',$show_type );
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function channelSortPostAction() {
		
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		$info = $this->getInput(array('name11','name12','name13','name14','name21','name22','name23','name24','name31','name32','name33','name34','name41','name42','name43','name44','name51','name52','name53','name54','name61','name62','name63','name64'));
		
		if(!$column_version || !$log_id) $this->output(-1, '非法参数！！');
	
		
		
		$result_sub    = Client_Service_ColumnNew::getListBywhere(array('log_id'=>$log_id,'pid'=>array('>',0)));
		$result_parent = Client_Service_ColumnNew::getListBywhere(array('log_id'=>$log_id,'pid'=>0));
		if(!$result_sub || !$result_parent) $this->output(-1, '非法请求。');
		$result_parent_ids = Common::resetKey($result_parent, 'position');
		$result_sub_ids    = Common::resetKey($result_sub, 'id');
		
		$tmp = array();
		$positon_arr = array();
		$choose_flag =  0 ; //精选是否选择的标志
		foreach ($info as $key=>$val) {
			if($val){
				$positon_arr[$key] = substr($key, 4,1).substr($key, 5,1);
				$temp_postion = explode('-', $val);
				$tmp[$key]   = $temp_postion[0];
				if( $temp_postion[2] == 'chosen' ){
					$choose_flag = 1;
				}
			}
		}
		
		//判断名称是否重复
		//比较数组的长度
		$ori_length = count($tmp);
		$end_length = count(array_unique($tmp));
		if($ori_length != $end_length){
			$this->output(-1, '位置中名称选择重复，请重新选择');
		}
		
		//判断位置是否选择有误
		for ($i = 1 ; $i <= 6; $i++){
			if( ($info['name'.$i.'2'] && empty($tmp['name'.$i.'1'])) || ($info['name'.$i.'3'] && ( empty($tmp['name'.$i.'1']) || empty($tmp['name'.$i.'2']) )) || ($info['name'.$i.'4'] && ( empty($tmp['name'.$i.'1']) || empty($tmp['name'.$i.'2']) || empty($tmp['name'.$i.'3']) ))  ){
				$this->output(-1, '频道位置('.$i.'-1,'.$i.'-2,'.$i.'-3,'.$i.'-4)配置不连续，需要配置连续，请重新调整!!!');
				break;
			}
				
		}
		 
		//验证个数
		$hot_count = 0 ;
		
		$hot_count = $this->checkChannelCount($default_open, $info, $hot_count);
	
		$temp_postion = array();
		//验证精选只能是第一个
		foreach ($info as $key=>$val) {
			if($val){
				$temp_postion = explode('-', $val);
				if( $temp_postion[2] == 'chosen' && (substr($key, 5,1) != 1) ){
					$this->output(-1, '精选的位置只能是频道第一个');
				}
			}
		}
		if($hot_count < 3){
			$this->output(-1, '热点导航不能小于3个');
		}
		
		//判断那些热点导航一定上传图片
		$hot_flag = 0;
		for ($i = 1 ; $i <= 4; $i++){
			if($info['name6'.$i]){
				$tmp = explode('-', $info['name6'.$i]);
				$name = $tmp[0];
				$id = $tmp[1];
				$ret = Client_Service_ColumnNew::getListBywhere(array('id'=>$id));
				if(!$ret[0]['icon_default'] || !$this->_checkImg($ret[0]['icon_default'])){
					$str = $name.',';
					$hot_flag = 1;
					break;
				}
			}
		}
		
		if($hot_flag){
			$this->output(-1, '精选的位置中"'.rtrim($str,',').'"没有上传图片，请先去上传该频道的图片');
		}
		
		Common_Service_Base::beginTransaction();
		//更改频道的位置
		$parent_position = array();
		foreach ($info as $key=>$val) {
			$data = array();
			$temp = array();
			$temp = explode('-', $val);
			if($val){
				$data['name']= $temp[0];
				$id = $temp[1];
				$data['pid']= intval(substr($key, 4,1));
				$data['position']= intval(substr($key, 5,1));
				$data['level']= $temp[4];
				$data['status']= 1;
				$temp_id[$id] = $id;
				$ret = Client_Service_ColumnNew::updateColumn($data, $id);
				//关闭相同位置的选项
			}else{
				//关闭未配置的选项
				$data['pid']= intval(substr($key, 4,1));
				$data['position']= intval(substr($key, 5,1));
				$data['status']= 0;
				$parent_position[$data['pid']][] = $data['position'] ;
				$ret = Client_Service_ColumnNew::updateColumnBywhere($data, array('log_id'=>$log_id,'pid'=>$data['pid'],'position'=>$data['position']));        
			}
			$id = $temp[1];
			//已经操作的ID
			 $channelId[] = $id;
			if(!ret){
				//事务回滚
				Common_Service_Base::rollBack();
				$this->output(-1, '操作失败.');
			}
		}
		 if($channelId){
			$ret =Client_Service_ColumnNew::updateColumnBywhere(array('status'=>0), array('log_id'=>$log_id , 'id'=> array('NOT IN', $channelId)));
			if(!ret){
				//事务回滚
				Common_Service_Base::rollBack();
				$this->output(-1, '操作失败.');
			}
		} 
		
		//自动开启关闭一级栏目
		foreach ($parent_position as $key=>$val){
			if(count($val) == 4){
				$ret = Client_Service_ColumnNew::updateColumnBywhere(array('status'=>0), array('log_id'=>$log_id,'pid'=>0,'position'=>$key));
			}else{
				$ret = Client_Service_ColumnNew::updateColumnBywhere(array('status'=>1), array('log_id'=>$log_id,'pid'=>0,'position'=>$key));
			}
			if(!ret){
				//事务回滚
				Common_Service_Base::rollBack();
			}
		}
		
		//更新日志表
		$log_data['update_time'] = Common::getTime();
		$log_data['step'] = 3;
		$log_data['channel_num'] = count($temp_id);
		$log_data['admin_id'] = $this->userInfo['uid'];
		$log_data['admin_name'] = $this->userInfo['username'];
		$ret = Client_Service_ColumnLog::updateByID($log_data, $log_id);
		if($ret){
			Common_Service_Base::commit();
			$this->output(0, '操作成功',array('column_version'=>$column_version,'log_id'=>$log_id));
		}else{
			Common_Service_Base::rollBack();
			$this->output(-1, '操作失败.');
		}
		
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function editChannelSortPostAction() {
		
		$default_open = $this->getInput('default_open');
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		$info = $this->getInput(array('name11','name12','name13','name14','name21','name22','name23','name24','name31','name32','name33','name34','name41','name42','name43','name44','name51','name52','name53','name54','name61','name62','name63','name64'));
	
		if(!$column_version || !$log_id) $this->output(-1, '非法参数！！');
		
	
		$result_sub    = Client_Service_ColumnNew::getListBywhere(array('log_id'=>$log_id,'pid'=>array('>',0)));
		$result_parent = Client_Service_ColumnNew::getListBywhere(array('log_id'=>$log_id,'pid'=>0));
		if(!$result_sub || !$result_parent) $this->output(-1, '非法请求。');
		$result_parent_ids = Common::resetKey($result_parent, 'position');
		$result_sub_ids    = Common::resetKey($result_sub, 'id');
	
		$tmp = array();
		$positon_arr = array();
		$choose_flag =  0 ; //精选是否选择的标志
		foreach ($info as $key=>$val) {
			if($val){
				$positon_arr[$key] = substr($key, 4,1).substr($key, 5,1);
				$temp_postion = explode('-', $val);
				$tmp[$key]   = $temp_postion[0];
				if( $temp_postion[2] == 'chosen' ){
					$choose_flag = 1;
				}
			}
		}
	
		//判断名称是否重复
		//比较数组的长度
		$ori_length = count($tmp);
		$end_length = count(array_unique($tmp));
		if($ori_length != $end_length){
			$this->output(-1, '位置中名称选择重复，请重新选择');
		}
	
		//判断位置是否选择有误
		for ($i = 1 ; $i <= 6; $i++){
			if( ($info['name'.$i.'2'] && empty($tmp['name'.$i.'1'])) || ($info['name'.$i.'3'] && ( empty($tmp['name'.$i.'1']) || empty($tmp['name'.$i.'2']) )) || ($info['name'.$i.'4'] && ( empty($tmp['name'.$i.'1']) || empty($tmp['name'.$i.'2']) || empty($tmp['name'.$i.'3']) ))  ){
				$this->output(-1, '频道位置('.$i.'-1,'.$i.'-2,'.$i.'-3,'.$i.'-4)配置不连续，需要配置连续，请重新调整!!!');
				break;
			}
	
		}
			
		//验证个数
		$hot_count = 0 ;
		$hot_count = $this->checkChannelCount ( $default_open, $info, $hot_count );

		$temp_postion = array();
		//验证精选只能是第一个
		foreach ($info as $key=>$val) {
			if($val){
				$temp_postion = explode('-', $val);
				if( $temp_postion[2] == 'chosen' && (substr($key, 5,1) != 1) ){
					$this->output(-1, '精选的位置只能是频道第一个');
				}
			}
		}
		if($hot_count < 3){
			$this->output(-1, '热点导航不能小于3个');
		}
	

		
		$result_cloumn = Client_Service_ColumnLog::getBy(array('id'=>$log_id));
		$cloumnArr = unserialize($result_cloumn['temp2']);
		
		
		foreach ($cloumnArr as $val){
			if($val['icon_default']){
				$columnPic[$val['column_id']] = $val['icon_default'];
			}
			
		}
		//判断那些热点导航一定上传图片
		$hot_flag = 0;
		for ($i = 1 ; $i <= 4; $i++){
			if($info['name6'.$i]){
				$tmp = explode('-', $info['name6'.$i]);
				$name = $tmp[0];
				$id = $tmp[1];
				if( !array_key_exists($id, $columnPic) || !$this->_checkImg($columnPic[$id])){
					$str = $name.',';
					$hot_flag = 1;
					break;
				}
			}
		}
	
		//判断热点导航是否上传图片
		if($hot_flag){
			$this->output(-1, '精选的位置中"'.rtrim($str,',').'"没有上传图片，请先去上传该频道的图片');
		}
	
		//保存到临时字段中temp3
		$log_data['temp3'] = serialize($info);
		$ret = Client_Service_ColumnLog::updateByID($log_data, $log_id);
		if($ret){
			$this->output(0, '操作成功',array('column_version'=>$column_version,'log_id'=>$log_id));
		}else{
			$this->output(-1, '操作失败2');
		}
		
	}
	
	/**
	 * @param default_open
	 * @param info
	 * @param hot_count
	 */
	 private function checkChannelCount($default_open, $info, $hot_count) {
		//各个频道选择的个数
		$channel_count_name1 = $channel_count_name2 = $channel_count_name3 = $channel_count_name4 = $channel_count_name5 = 0;
		for ($i = 1 ; $i <= 4; $i++){
			if($info['name6'.$i]){
				$hot_count++ ;
			}
			if($info['name1'.$i]){
				$channel_count_name1++;
			}
			if($info['name2'.$i]){
				$channel_count_name2++;
			}
			if($info['name3'.$i]){
				$channel_count_name3++;
			}
			if($info['name4'.$i]){
				$channel_count_name4++;
			}
			if($info['name5'.$i]){
				$channel_count_name5++;
			}
		}
	
		//看选择栏目至少要四个
		if( ($channel_count_name1 < 1 && $channel_count_name2 < 1) || ($channel_count_name1 < 1 && $channel_count_name3 < 1) || ($channel_count_name1 < 1 && $channel_count_name4 < 1) || ($channel_count_name1 < 1 && $channel_count_name5 < 1)){
			$this->output(-1, '至少要选择四个栏目');
		}
	
		if( ($channel_count_name2 < 1 && $channel_count_name3 < 1) || ($channel_count_name2 < 1 && $channel_count_name4< 1) || ($channel_count_name2 < 1 && $channel_count_name5 < 1) ){
			$this->output(-1, '至少要选择四个栏目');
		}
		if( ($channel_count_name3 < 1 && $channel_count_name4 < 1) || ($channel_count_name3 < 1 && $channel_count_name5< 1)  ){
			$this->output(-1, '至少要选择四个栏目');
		}
		if( ($channel_count_name4 < 1 && $channel_count_name5 < 1) ){
			$this->output(-1, '至少要选择四个栏目');
		}
		
		//判断默认频道是否有配置二级频道
		if($channel_count_name1 == 0 && $default_open == 1  ){
			$this->output(-1, '默认打开栏目必须要配置频道');
		}
		
		if($channel_count_name2 == 0 && $default_open == 2  ){
			$this->output(-1, '默认打开栏目必须要配置频道');
		}
		
		if($channel_count_name3 == 0 && $default_open == 3  ){
			$this->output(-1, '默认打开栏目必须要配置频道');
		}
		
		if($channel_count_name4 == 0 && $default_open == 4  ){
			$this->output(-1, '默认打开栏目必须要配置频道');
		}
		
		if($channel_count_name5 == 0 && $default_open == 5  ){
			$this->output(-1, '默认打开栏目必须要配置频道');
		}
		return $hot_count;
	}

	
	/**
	 *
	 * 添加栏目的属性
	 */
	public function cloumnAttriAction() {
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		$this->assign('column_version', $column_version);
		$this->assign('log_id', $log_id);
		
	}
	
	/**
	 *
	 * 编辑栏目的属性
	 */
	public function editCloumnAttriAction() {
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
	    $info = Client_Service_ColumnLog::getBy(array('id'=>$log_id));
	    
		$this->assign('column_version', $column_version);
		$this->assign('log_id', $log_id);
		$this->assign('info', $info);
	
	}
	
	/**
	 *
	 * 查看栏目的属性
	 */
	public function viewCloumnAttriAction() {
		$column_version = $this->getInput('column_version');
		$log_id = intval($this->getInput('log_id'));
		$info = Client_Service_ColumnLog::getBy(array('id'=>$log_id));
		$this->assign('log_id', $log_id);
		$this->assign('column_version', $column_version);
		$this->assign('info', $info);
	
	}
	
	/**
	 *
	 * 添加栏目属性的post
	 */
	public function cloumnAttriPostAction() {
		$log_id = intval($this->getInput('log_id'));
		$column_version = $this->getInput('column_version');
		$column_name = trim($this->getInput('column_name'));
		$status= $this->getInput('status');
		$effect= $this->getInput('effect');
		
		if(!$column_name){
			$this->output(-1, '名称不能为空.');
		}
		if($effect){
			$start_time = Common::getTime();
		}else{
			$start_time = strtotime($this->getInput('start_time'));
		}
		if(!$start_time){
			$this->output(-1, '生效时间不能为空.');
		}
		
		$params['column_name']    = $column_name;
		$params['column_version'] = $column_version;
		if($log_id){
			$params['id']  = array('!=', $log_id);
		}
		$ret = Client_Service_ColumnLog::getBy($params);
		if($ret){
			$this->output(-1, '名称不能重复.');
		}
		
		$log_data['column_name'] = $column_name;
		$log_data['status'] = $status;
		$log_data['start_time'] = $start_time;
		$log_data['step'] = 4;
		$log_data['admin_id'] = $this->userInfo['uid'];
		$log_data['admin_name'] = $this->userInfo['username'];
		//更新日志表
		$ret = Client_Service_ColumnLog::updateByID($log_data, $log_id);
		if(!$ret) $this->output(-1, '操作非法.');
		//更新数据版本
		$this->_updateVersion();
		$this->output(0, '操作成功.',array('column_version'=>$column_version));
	
	}
	
	/**
	 *
	 * 编辑栏目属性的post
	 */
	public function editCloumnAttriPostAction() {
		$log_id = intval($this->getInput('log_id'));
		$column_version = $this->getInput('column_version');
		$column_name = trim($this->getInput('column_name'));
		$status= $this->getInput('status');
		$effect= $this->getInput('effect');
	
		if(!$column_name){
			$this->output(-1, '名称不能为空.');
		}
		if($effect){
			$start_time = Common::getTime();
		}else{
			$start_time = strtotime($this->getInput('start_time'));
		}
		if(!$start_time){
			$this->output(-1, '生效时间不能为空.');
		}
	
		//验证名称是否重复
		$params['column_name']    = $column_name;
		$params['column_version'] = $column_version;
		if($log_id){
			$params['id']  = array('!=', $log_id);
		}
		$rs = Client_Service_ColumnLog::getBy($params);
		if($rs){
			$this->output(-1, '名称不能重复.');
		}
		$rs_log = Client_Service_ColumnLog::getBy(array('id'=>$log_id));
		

		
		Common_Service_Base::beginTransaction();
		
		//更新栏目
		$temp1 = unserialize($rs_log['temp1']);
		foreach ($temp1 as $key => $val){
			$channel_data= array('name'=>$val['name'],
					'position'=>$val['position'],
					'status'=>$val['status'],
					'icon_choose'=>$val['icon_choose'],
					'icon_default'=>$val['icon_default'],
					'relevance'=>$val['relevance'],
					'default_open'=>$val['default_open'],
					'level'=>$val['level'],
					'update_time'=>$val['update_time'],
					'is_deafault'=>$val['is_deafault'],
					'column_version'=>$val['column_version']
			);
			$ret = Client_Service_ColumnNew::updateColumn($channel_data, $val['column_id']);
		    if(!ret){
		    	Common_Service_Base::rollBack();
		    	$this->output(-1, '操作失败.');
		    }
		}
	
		
		//更新频道设置
		$temp2 = unserialize($rs_log['temp2']);
		foreach ($temp2 as $val){
			$channel_data= array('name'=>$val['name'],
								 'status'=>$val['status'],
								 'channel_type'=>$val['channel_type'],
								 'icon_default'=>$val['icon_default'],
								 'show_type'=>$val['show_type'],
								 'relevance'=>$val['relevance'],
								 'link'=>$val['link'],
								 'level'=>$val['level'],
								 'pid'=>$val['pid'],
								 'position'=>$val['position'],
								 'is_deafault'=>$val['is_deafault'],
								 'column_version'=>$val['column_version'],
								 'update_time'=>$val['update_time'],
			);
			$ret = Client_Service_ColumnNew::updateColumn($channel_data,$val['column_id']);
			if(!ret){
				Common_Service_Base::rollBack();
				$this->output(-1, '操作失败.');
			}
		}
		
		//更新频道排序
		$temp3 = unserialize($rs_log['temp3']);
		$parent_position = array();
		foreach ($temp3 as $key=>$val) {
			$data = array();
			if($val){
				$temp = array();
				$temp = explode('-', $val);
				$data['name']= $temp[0];
				$id = $temp[1];
				$data['pid']= intval(substr($key, 4,1));
				$data['position']= intval(substr($key, 5,1));
				$data['level']= $temp[4];
				$data['status']= 1;
				$temp_id[$id] = $id;
				//关闭当前位置的其它的频道
				$ret1 = Client_Service_ColumnNew::updateColumnBywhere(array('status'=>0), array('log_id'=>$log_id,'pid'=>$data['pid'],'position'=>$data['position'],'id'=> array('!=',$id)));
				//更改当前位置当前状态
				$ret = Client_Service_ColumnNew::updateColumn($data, $id);
				
			}else{
				//关闭未配置的选项
				$data['pid']= intval(substr($key, 4,1));
				$data['position']= intval(substr($key, 5,1));
				$data['status']= 0;
				$parent_position[$data['pid']][] = $data['position'] ;
				$ret = Client_Service_ColumnNew::updateColumnBywhere($data, array('log_id'=>$log_id,'pid'=>$data['pid'],'position'=>$data['position']));
			}
			if(!ret || !$ret1){
				//事务回滚
				Common_Service_Base::rollBack();
				$this->output(-1, '操作失败.');
			}
		}
		

		$log_data['column_name'] = $column_name;
		$log_data['status'] = $status;
		$log_data['start_time'] = $start_time;
		$log_data['step'] = 4;
		$log_data['channel_num'] = count($temp_id);
		$log_data['admin_id'] = $this->userInfo['uid'];
		$log_data['admin_name'] = $this->userInfo['username'];
		$log_data['update_time'] = Common::getTime();
		$log_data['temp1'] = '';
		$log_data['temp2'] = '';
		$log_data['temp3'] = '';
		//更新日志表
		Common_Service_Base::beginTransaction();
		$ret = Client_Service_ColumnLog::updateByID($log_data, $log_id);
		if(!$ret){
			Common_Service_Base::rollBack();
			$this->output(-1, '操作失败.');
		}

		//事务提交
		Common_Service_Base::commit();	
		//自动开启关闭一级栏目
		foreach ($parent_position as $key=>$val){
			if(count($val) == 4){
				$ret = Client_Service_ColumnNew::updateColumnBywhere(array('status'=>0), array('log_id'=>$log_id,'pid'=>0,'position'=>$key));
			}else{
				$ret = Client_Service_ColumnNew::updateColumnBywhere(array('status'=>1), array('log_id'=>$log_id,'pid'=>0,'position'=>$key));
			}
		}
		//更新数据版本
		$this->_updateVersion();
		$this->output(0, '操作成功.',array('column_version'=>$column_version));
	
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	public function infoAction() {
		
		
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
		$ret = Common::upload('img', 'column');
		$imgId = $this->getPost('imgId');
		$this->assign('code' , $ret['data']);
		$this->assign('msg' , $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}
	
	/**
	 * 维护数据版本
	 */
	private function _updateVersion() {
		$time = Common::getTime();
		Game_Service_Config::setValue("Column_New_Nav_Version",$time );
	}
	
	/**
	 *验证图片是否存在
	 */
	private function _checkImg($path) {
		$img_path = Common::getConfig('siteConfig', 'attachPath').$path ;
		return Util_File::isFile($img_path);
	}


	/**
	 * 构造栏目列表
	 * @param array $items
	 * @return array
	 */
	private function _buildList($items){
		$tmp = array();
		foreach ($items as $key => $value){
			$tmp[$key] = $value;
			$childs = Client_Service_ColumnNew::getListBywhere(array('pid'=>$value['id'],'status'=>1));
			if (!empty($childs)){
				$tmp[$key]['items'] = $childs;
			}
		}
		return $tmp;
	}
	

}