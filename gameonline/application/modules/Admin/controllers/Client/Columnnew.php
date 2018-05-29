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
			'listChannelUrl'=> '/Admin/Client_Columnnew/channel_index',
			'editUrl' => '/Admin/Client_Columnnew/edit',
			'editPostUrl' => '/Admin/Client_Columnnew/edit_post',
			'addChanneUrl'=> '/Admin/Client_Columnnew/channel_add',
			'addChannePostUrl' => '/Admin/Client_Columnnew/channel_add_post',
			'editChanneAllUrl' => '/Admin/Client_Columnnew/channel_editall',
			'editChanneAllPostUrl' => '/Admin/Client_Columnnew/channel_editall_post',
			'editChanneUrl' => '/Admin/Client_Columnnew/channel_edit',
			'editChannePostUrl' => '/Admin/Client_Columnnew/channel_edit_post',
			'uploadUrl' => '/Admin/Client_Columnnew/upload',
			'uploadPostUrl' => '/Admin/Client_Columnnew/upload_post',
			'infoUrl' =>  '/Admin/Client_Columnnew/info'
	);

	public $perpage = 20;	
	public $channel_type = array(1=>'默认',2=>'扩展');
	

	public function indexAction() {	
		$page = intval($this->getInput('page'));
		$name = trim($this->getInput('name'));
		$status = $this->getInput('status');
		$perpage = $this->perpage;
		
		$search = array();
		$params = array();
		$params['pid'] = 0;
		if ($name) {
			$search['name'] = $name ;
			$params['name'] = array('LIKE', $name);
		}
		
		if ($status) {
			$search['status'] = $status;
			$params['status'] = intval($status);
		}
		
		
		list($total, $data) = Client_Service_ColumnNew::getColumnList($page, $perpage, $params);
		$columns = $this->_buildList($data);

		$this->assign('search', $search);
		$this->assign('info', $columns);
		$this->assign('total', $total);
		$url = $this->actions['listUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}



	/**
	 *
	 * Enter description here ...
	 */
	public function editAction() {
		$params['pid'] = 0;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1,20,$params);	
		$this->assign('info', $data);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function edit_postAction() {
		$ret = array();
		$tmp = array();
				
		for($i = 1 ; $i <= 5;$i++ ){
			$info['id'] = intval($this->getInput('id'.$i));	
			$info['name'] = trim($this->getInput('name'.$i));
			$info['position'] = intval($this->getInput('position'.$i));
			$info['status'] = $this->getInput('status'.$i);
			$info['icon_path'] = $this->getInput('icon_path'.$i);
			$info['icon_default'] = $this->getInput('icon_default'.$i);
			$info['default_open'] = $this->getInput('default_open');
			$default_open = $info['default_open'];
			$info['level'] = trim($this->getInput('level'));
			//初始那个频道是默认打开的
			if ($info['default_open'] == $info['id']){
				$info['default_open'] = 1;
			}else{
				$info['default_open'] = 0;
			}
			
			
			//验证首页位置
		    /* 	if($this->getInput('position1') != 1){
				$this->output(-1, '第一个栏目位置只能是1.');
			}  */
             
			if (!$info['name']){
				$this->output(-1,  '名称不能为空.');
			}
			if(in_array($info['name'], $tmp['name'])){
				$this->output(-1,  '名称不能重复.');
			}
			//验证位置是否重复
			if(in_array($info['position'], $tmp['position'])){
				$this->output(-1, '位置不能重复.');
			}
			if($info['position'] > 5 || $info['position'] < 1){
				$this->output(-1, '位置只能1-5之间');
			}
			if(($info['status'] == 2) && ($default_open == $info['id']) ){
				$this->output(-1, '默认打开不能关闭');
			}
			if (!$info['icon_path']){
				$this->output(-1, '选中图片不能为空.');
			} 
			if (!$info['icon_default']){
				$this->output(-1, '默认图片不能为空.');
			} 

			$tmp['status'][] = $info['status'];
			
			//判断是否开启四个栏目以上
			if(count($tmp['status']) == 5){
				$status_arr = array_count_values($tmp['status']);
				if($status_arr[1] < 4){
					$this->output(-1, '至少要开启4个栏目');
				}
			}

			$rs_list = Client_Service_ColumnNew::getListBywhere(array('pid'=>$info['id'],'status'=>1));
			if( ($info['status']) == 2 && count($rs_list) > 0){
				$this->output(-1, $info['name'].'下的频道还有未关闭，你要先关闭频道');
			}
			
			$info['update_time']=Common::getTime();
			$ret[$i] = Client_Service_ColumnNew::updateColumn($info, intval($info['id']));
		}
		
		if (!$ret[1] || !$ret[2] || !$ret[3]  || !$ret[4]  || !$ret[5] ) $this->output(-1, '操作失败');
		$this->_updateVersion();
		$this->output(0, '操作成功.');      
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */	
	public function channel_indexAction() {
		$page = intval($this->getInput('page'));
		$name = trim($this->getInput('name'));
		$status = $this->getInput('status');
		$perpage = $this->perpage;
		
		$search = array();
		$params = array();
		$params['pid'] = array('>',0);
		if ($name) {
			$search['name'] = $name ;
			$params['name'] = array('LIKE', $name);
		}
		
		if ($status) {
			$search['status'] = $status;
			$params['status'] = intval($status);
		}
		
		
		list($total, $data) = Client_Service_ColumnNew::getColumnList($page, $perpage, $params,array('pid' => 'ASC','position'=>'ASC'));
		$parent_list = Client_Service_ColumnNew::getParenList();
		$parent_list = Common::resetKey($parent_list, 'id');

		
		$this->assign('search', $search);
		$this->assign('info', $data);
		$this->assign('parent_list', $parent_list);
		$this->assign('channel_type', $this->channel_type);
		$this->assign('total', $total);
		$url = $this->actions['listChannelUrl'].'/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	/**
	 *
	 * 单个频道编辑
	 */
	public function channel_editAction() {
		$id = $this->getInput('id');
		$type = $this->getInput('channel_type');
		$data = Client_Service_ColumnNew::getColumn($id);
		//扩展类型
        if($type == 2){		
			$api_config = Common::getConfig('apiConfig','layoutnew');
			$this->assign('ext_list_arr', $api_config);
        }
       
        $show_type =  Common::getConfig('apiConfig','ext_type');
        
		$this->assign('channel_type', $this->channel_type);
		$this->assign('show_type',$show_type );
		$this->assign('info', $data);
	}
	
	
	/**
	 *
	 * 频道编辑post
	 */
	public function channel_edit_postAction() {
		$id = intval($this->getInput('id'));
		$info['name'] = trim($this->getInput('name'));
		$info['link'] = trim($this->getInput('link'));
		$info['channel_type'] = $this->getInput('channel_type');
		if($info['channel_type'] == 2 ){
			$info['show_type'] = intval($this->getInput('show_type'));	
			$show_type =  $api_config = Common::getConfig('apiConfig','ext_type');
			$info['relevance'] = $show_type[$info['show_type']]['value'];
		}
		
		$info['position'] = $this->getInput('position');
		$info['status'] = intval($this->getInput('status'));
		$info['icon_default'] = $this->getInput('icon_default');
		$tmp = explode('-', $info['position']);
		$info['pid'] = $tmp[0];
		$info['position'] = $tmp[1];
		$data = $this->_cookData($info);
		$relevance = $this->getInput('relevance');
		
		 if($relevance == 'chosen' && $info['status'] == 2){
			$this->output(-1,  '精选不能关闭');
		}
		 
	
		if (in_array($info['pid'], array('6')) && ($info['icon_default'] == '') ){
			$this->output(-1,  '图片不能为空.');
		} 
		//扩展
		if($info['channel_type'] == 2 && !trim($info['link']) &&  ($info['pid'] != 6) ){
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}
		}
		
		 //查找名称是否已经存在
		 $ret = Client_Service_ColumnNew::getListBywhere(array('pid'=>array('>',0),'name'=>trim($info['name']),'id'=>array('!=',$id)));
		if(!empty($ret)){
		$this->output(-1, '频道的名称已经存在，请重新输入');
		} 
		//查找出原来更改前的的状态信息
		$column_info = Client_Service_ColumnNew::getColumn($id);
		if(empty($column_info)){
			$this->output(-1, '非法操作');
		}
		
		
		$ret = Client_Service_ColumnNew::getListBywhere(array('position'=>$info['position'],'pid'=>$info['pid'],'status'=>1,'id'=>array('!=',$id)));
		if(!empty($ret)){
			$this->output(-1, '此位置已经被占用，请关闭此位置的频道，再开启');
		}
		
		$orial_sub_arr = Client_Service_ColumnNew::getColumn($id);
		//$this->output(-1, 'test'.$orial_sub_arr['status']);
		//查找出一级栏目已经开启的数量
		$parent_total = Client_Service_ColumnNew::getColumnCount(array('pid'=>0,'status'=>1));
		//二级栏目开启的数量
		$sub_total = Client_Service_ColumnNew::getColumnCount(array('pid'=>$info['pid'],'status'=>1));
		
		//查找出这个二级栏目是否是最后一个关闭的
		if( ($info['status'] == 2) && ($parent_total == 4) && ($sub_total == 1) && ($info['status'] != $orial_sub_arr['status'] ) ){
			$this->output(-1, '你不能再关闭，栏目至少为四个');
		}
		
		//关闭之前，查看其一级栏目是否是默认打开的
		$rs  = Client_Service_ColumnNew::getColumn($info['pid']);
		if( ($info['status'] == 2) && ($rs['default_open'] == 1) && ($sub_total == 1) ){
			$this->output(-1, '你不能再关闭，此频道对应的一级栏目是默认开');
		}
	    
		//更新数据
		$info['update_time']=Common::getTime();
		$ret = Client_Service_ColumnNew::updateColumn($info, $id);
		if (!$ret ) $this->output(-1, '操作失败');
	
		//当是最后一个关闭的，把一级栏目的状态也关掉,第一个开启，也把对用的一级栏目开启
		if(($sub_total == 1) && ($info['status'] == 2) && ($info['status'] != $orial_sub_arr['status']) ){
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>2),$info['pid']);
		}else if(intval($sub_total) <= 1 && ($info['status'] == 1)){
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>1),$info['pid']);
		}
		//自动提升下面的频道位置。
		if($info['status'] == 2 && $column_info['status'] == 1){
			$upgrade_info = Client_Service_ColumnNew::getListBywhere(array('position'=>array('>',$info['position']),'pid'=>$info['pid'],'status'=>1,'id'=>array('!=',$id)));
			foreach ($upgrade_info as $val){
				$tmp = $val['position']-1 ;
				$ret = Client_Service_ColumnNew::updateColumn(array('position'=>$tmp),$val['id'] );
				
			}
		}
		$this->_updateVersion();
		$this->output(0, '操作成功.');
	}
	
	/**
	 *扩展添加
	 * Enter description here ...
	 */
	public function channel_addAction() {
	    //取得所有二级频道，三级频道
		//$params['pid'] = array(array('>',0),array('!=',6));
		$params['pid'] = array(array('>',0));
		$params['status'] = 1;
		list($total, $data) = Client_Service_ColumnNew::getColumnList(1, 50, $params,array('pid' => 'ASC'));
		
		$channel_arr = array();
		foreach ($data as $val){
			$channel_arr[] = $val['pid'].$val['position'];
		}
		//找出不存在的频道位置
		$channel_arr_list = array();
		for($j = 1 ; $j <= 6 ; $j++){
			for ($i = 1 ; $i <= 4 ; $i++){
				if(in_array($j.$i, $channel_arr)){
					continue;
				}else{
					$channel_arr_list[$j][$i]= $i;
				}
			}
			
		}
		//展示列表
		$show_type =  $api_config = Common::getConfig('apiConfig','ext_type');
		$this->assign('show_type',$show_type );
		$this->assign('channel_arr_list', $channel_arr_list);
		$api_config = Common::getConfig('apiConfig','layoutnew');
		$this->assign('ext_list_arr', $api_config);
		
		
	}
	
	/**
	 *扩展添加post
	 * Enter description here ...
	 */
	public function channel_add_postAction() {
		$info['channel_type'] = 2;
		$info['show_type'] = $this->getInput('show_type');
		$info['name'] = trim($this->getInput('name'));
		$info['position'] = trim($this->getInput('position'));
		$info['status'] = $this->getInput('status');
		$info['icon_default'] = $this->getInput('icon_default');
		$info['link'] = $this->getInput('link');
		$info['level'] = intval($this->getInput('level'));		

		$tmp = explode('-', $info['position']);
		$info['pid'] = $tmp[0];
		$info['position'] = $tmp[1];
		
		$show_type =  $api_config = Common::getConfig('apiConfig','ext_type');
		$info['relevance'] = $show_type[$info['show_type']]['value'];

		$data = $this->_cookData($info,2);
		if(!trim($info['link'])){
			if (strpos($info['link'], 'http://') === false || !strpos($info['link'], 'https://') === false) {
				$this->output(-1, '链接地址不规范.');
			}  
		}
		
		//位置6必须要上传图片
		if (($info['pid'] == 6) && (!$info['icon_default'])) {
			$this->output(-1, '热点导航必须要上传图片.');
		}
		
		//查找名称是否已经存在
		$ret = Client_Service_ColumnNew::getListBywhere(array('pid'=>array('>',0),'name'=>trim($info['name'])));
		if(!empty($ret)){
			$this->output(-1, '名称已经存在');
		}
		
		//查找出当前最大的位置
		$max_postion = 0 ;
		$result = Client_Service_ColumnNew::getListBywhere(array('pid'=>$info['pid']));
		foreach ($result as $val){
			if($val['position'] > $max_postion){
				$max_postion = $val['position'];
			}
		}
		if($info['position'] > ($max_postion+1)){
			$this->output(-1, '你选择的位置有错，要先添加比此位置小');
		}
		if($info['link']){
			//查找url是否已经存在
			$ret = Client_Service_ColumnNew::getListBywhere(array('link'=>trim($info['link'])));
			if(!empty($ret)){
				$this->output(-1, 'url已经存在');
			}
		}
		$info['create_time']=Common::getTime();
		$info['update_time']=Common::getTime();
		$ret = Client_Service_ColumnNew::addColumn($info);
		//$ret = Client_Service_ColumnNew::updateColumnBywhere($info, array('pid'=>$info['pid'],'position'=>$info['position']));
		if (!$ret ) $this->output(-1, '操作失败');
		//$this->_updateVersion();
		$this->output(0, '操作成功.');
	
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function channel_editallAction() {
		
		list(,$parent_arr) = Client_Service_ColumnNew::getColumnList(1, 5, array('pid'=>0));
		$params['pid']  = array('>',0);
		$params['name'] = array('!=','');
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
		
	}
	
	public function channel_editall_postAction() {
		$info = $this->getInput(array('name11','name12','name13','name14','name21','name22','name23','name24','name31','name32','name33','name34','name41','name42','name43','name44','name51','name52','name53','name54','name61','name62','name63','name64'));
	 
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

		//看选择频道至少要四个
		if( ($channel_count_name1 < 1 && $channel_count_name2 < 1) || ($channel_count_name1 < 1 && $channel_count_name3 < 1) || ($channel_count_name1 < 1 && $channel_count_name4 < 1) || ($channel_count_name1 < 1 && $channel_count_name5 < 1)){
			$this->output(-1, '至少要选择四个频道');
		}
		
		if( ($channel_count_name2 < 1 && $channel_count_name3 < 1) || ($channel_count_name2 < 1 && $channel_count_name4< 1) || ($channel_count_name2 < 1 && $channel_count_name5 < 1) ){
			$this->output(-1, '至少要选择四个频道');
		}
		if( ($channel_count_name3 < 1 && $channel_count_name4 < 1) || ($channel_count_name3 < 1 && $channel_count_name5< 1)  ){
			$this->output(-1, '至少要选择四个频道');
		}
		if( ($channel_count_name4 < 1 && $channel_count_name5 < 1) ){
			$this->output(-1, '至少要选择四个频道');
		}
		
		//验证是否有填写精选
		if(!$choose_flag){
			$this->output(-1, '精选必须要选择');
		}
		
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
				if(!$ret[0]['icon_default']){
					$str = $name.',';
					$hot_flag = 1;
				}
			}
		}
		
		if($hot_flag){
			$this->output(-1, '精选的位置中"'.rtrim($str,',').'"没有上传图片，请先去上传该频道的图片');
		}
		
		$temp = array();
		$temp_arr1 = array();
		$temp_arr2 = array();
		foreach ($info as $key=>$val) {
			if($val){
				$temp_arr1[$key]=$key;
				$temp = explode('-', $val);
				$data['name']= $temp[0];
				$id = $temp[1];
				$data['pid']= intval(substr($key, 4,1));
				$data['position']= intval(substr($key, 5,1));
				$data['relevance']= $temp[2];
				//$data['channel_type']= $temp[3];
				$data['level']= $temp[4];
				$data['status']= 1;
				//判断位置是否被占用
				$ret = Client_Service_ColumnNew::getListBywhere(array('pid'=>$data['pid'],'position'=>$data['position'],'status'=>1));
				if(count($ret) && !in_array(($data['pid'].$data['position']), $positon_arr)){
					$this->output(-1, "位置".$data['pid'].$data['position']."已经被占用，请先关闭");
				}
				//关闭此位置其他选项
				$ret = Client_Service_ColumnNew::updateColumnBywhere(array('status'=>2), array('id'=>array('!=',$id),'pid'=>$data['pid'],'position'=>$data['position'],'status'=>1));
				$ret = Client_Service_ColumnNew::updateColumn($data, $id);
			}else{
				$temp_arr2[$key]=$key;
			}
		}

		$data = array();
		//关闭此位置其他选项
		foreach ($temp_arr2 as $val){
			if(in_array($val, $temp_arr1)){
				continue ;
			}
			$data['pid']= intval(substr($val, 4,1));
			$data['position']= intval(substr($val, 5,1));
			$data['status']= 2;
			$ret = Client_Service_ColumnNew::getListBywhere(array('pid'=>$data['pid'],'position'=>$data['position']));
			if($ret){
				$rs = Client_Service_ColumnNew::updateColumnBywhere($data,array('pid'=>$data['pid'],'position'=>$data['position']));
			}	
		}
		//如果编辑频道都是空，则把其父栏目也关闭
		if($channel_count_name1 < 1){
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>2),1);
		}else{
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>1),1);
		}
		if($channel_count_name2 < 1){
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>2),2);
		}else{
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>1),2);
		}
		if($channel_count_name3 < 1){
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>2),3);
		}else {
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>1),3);
		}
		if($channel_count_name4 < 1){
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>2),4);
		}else {
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>1),4);
		}
		if($channel_count_name5 < 1){
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>2),5);
		}else{
			$ret = Client_Service_ColumnNew::updateColumn(array('status'=>1),5);
		}
		$this->_updateVersion();
		
		$this->output(0, '操作成功.');

	}
	
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
		$this->_buildColumnLog($time);
	}
	private function _buildColumnLog($version){

		$data = array();
		$info = Client_Service_ColumnNew::getAllColumn();
		$api_config = Common::getConfig('apiConfig','layoutnew');
		$ext_view   = Common::getConfig('apiConfig','ext_type');
		//支持扩展的类型
		$ext_arr = array();
		foreach ($ext_view as $val){
			$ext_arr[] = $val['value'];
		}
		$attachroot = Common::getAttachPath();
		 
		//一级
		$info = Client_Service_ColumnNew::getListBywhere(array('pid'=>0,'status'=>1));
		$info_count = count($info);
		//把二级栏目组合
		$tmp = array();
		$default_open = 0;
		$default_arr = array();
		foreach ($info as $key => $val){
			$tmp[$key] = $val;
			$childs = Client_Service_ColumnNew::getListBywhere(array('pid'=>$val['id'],'status'=>1));
			if (!empty($childs)){
				$tmp[$key]['items'] = $childs;
			}
			//默认打开
			if($val['default_open']){
				$default_open = $val['position'];
			}
			$default_arr[] = $val['position'];
		}
		//关闭那个位置
		for ($i = 1; $i <= 5; $i++ ){
			if(!in_array($i, $default_arr)){
				$close_flag = $i ;
				break;
			}
		}
		 
		//默认打开大于取出条数
		if( ( $info_count < 5 ) && ( $close_flag < $default_open )  ){
			$default_open = $default_open - 1;
		}
		//默认配置
		$data['data']['defaultTabIndex'] = $default_open;
		//精品推荐下
		$channel_list = Client_Service_ColumnNew::getListBywhere(array('pid'=>6,'status'=>1));
		$channel = array();
		foreach ($channel_list as $val){
	    	//判断是默认还是扩展 1默认2扩展
	    	if($val['channel_type'] == 1){
	    		if(in_array($val['relevance'], array('rankweek','rankmonth'))){
	    			$channel [] = array('name'    => $val['name'],
	    					'viewType'=> $api_config['channel'][$val['relevance']]['viewType'],
	    					'source'  => $api_config['channel'][$val['relevance']]['source'],
	    					'url'     => $api_config['channel'][$val['relevance']]['url'],
	    					'imageUrl'  => $attachroot.$val['icon_default']
	    			);
	    		}else{
	    			$channel [] = array('name'    => $val['name'],
	    					'viewType'=> $api_config['channel'][$val['relevance']]['viewType'],
	    					'source'  => $api_config['channel'][$val['relevance']]['source'],
	    					'imageUrl'  => $attachroot.$val['icon_default']
	    			);
	    		}
	    	}else{
	    		$channel [] = array('name'    => $val['name'],
	    				'viewType'=> $api_config[$val['relevance']][$val['link']]['viewType'],
	    				'source'  => $api_config[$val['relevance']][$val['link']]['source'],
	    				'url'  => $api_config[$val['relevance']][$val['link']]['url'],
	    				'imageUrl'  => $attachroot.$val['icon_default']
	    		);
	    	}
	    }
		$data['data']['channelItems'] = $channel;
		
	    //拼接一级二级频道数据
	    foreach ($tmp as $val ){
	    	// 二级频道大于1个 
	    	if(count($val['items']) > 1){
	    		$channel_arr = array();
	    		foreach ($val['items'] as $va){
	    			//判断二级频道是扩展还是默认
	    			if($va['channel_type'] == 2){
	    				$viewtype = $va['relevance'];
	    			}else{
	    				$viewtype = $api_config['channel'][$va['relevance']]['viewType'];
	    			}
	    			//月榜周榜是默认的时候
	    			if(in_array($va['relevance'], array('rankweek','rankmonth'))){
	    				$channel_arr[] = array('title'=>$va['name'],
	    						'viewType'=>$viewtype,
	    						'source'=>$api_config['channel'][$va['relevance']]['source'],
	    						'url'=>$api_config['channel'][$va['relevance']]['url']
	    				);
	    			}elseif(in_array($va['relevance'], $ext_arr)){
	    				$channel_arr[] = array('title'=>$va['name'],
	    									   'viewType'=>$api_config[$va['relevance']][$va['link']]['viewType'],
	    						               'source'  =>$api_config[$va['relevance']][$va['link']]['source'],
	    						                'url'    =>$api_config[$va['relevance']][$va['link']]['url']
	    				);
	    			}else{
	    				$channel_arr[] = array('title'=>$va['name'],
	    						'viewType'=>$viewtype,
	    						'source'=>$api_config['channel'][$va['relevance']]['source']
	    				);
	    			}
	    			
	    		}
    			$data['data']['items'][] =array( 'title'=>$val['name'],
    					'source'=>$api_config['column'][$val['relevance']]['source'],
    					'normalIcon'=>$attachroot.$val['icon_default'],
    					'selectIcon'=>$attachroot.$val['icon_path'],
    					'items'=>$channel_arr
    			) ;
    		// 二级频道等于1个
	    	}else{ 	    		
	    		if(count($val['items']) < 1){
	    			continue ;
	    		}
	    			
    			if($val['items'][0]['channel_type'] == 2){
    				$view_type = $val['items'][0]['relevance'];
    			}else{
    				if(in_array( $api_config['channel'][$val['items'][0]['relevance']]['viewType'], array('RankView'))){
    					$view_type = $val['items'][0]['relevance'];
    				}else{
    				    $view_type = $api_config['channel'][$val['items'][0]['relevance']]['viewType'];
    				}
    			}
    			//月榜周榜是默认的时候
	    		if(in_array($view_type, array('rankweek','rankmonth'))){
	    				$data['data']['items'][] =array( 'title'=>$val['name'],
	    					'source'=>$api_config['channel'][$val['items'][0]['relevance']]['source'],
	    					'viewType' => $api_config['channel'][$val['items'][0]['relevance']]['viewType'],
	    					'url'=>$api_config['channel'][$val['items'][0]['relevance']]['url'],
	    					'normalIcon'=>$attachroot.$val['icon_default'],
	    					'selectIcon'=>$attachroot.$val['icon_path']
	    			) ;
	    		}elseif(in_array($view_type, $ext_arr) ){
	    			$data['data']['items'][] =array( 'title'=>$val['name'],
	    					'source'=>$api_config[$val['items'][0]['relevance']][$val['items'][0]['link']]['source'],
	    					'viewType' => $api_config[$val['items'][0]['relevance']][$val['items'][0]['link']]['viewType'],
	    					'url'=>$api_config[$val['items'][0]['relevance']][$val['items'][0]['link']]['url'],
	    					'normalIcon'=>$attachroot.$val['icon_default'],
	    					'selectIcon'=>$attachroot.$val['icon_path']
	    			) ;
	    		}else{	    			
	    			$data['data']['items'][] =array( 'title'=>$val['name'],
	    					'source'=>$api_config['channel'][$val['items'][0]['relevance']]['source'],
	    					'viewType' => $view_type,
	    					'normalIcon'=>$attachroot.$val['icon_default'],
	    					'selectIcon'=>$attachroot.$val['icon_path']
	    			) ;
	    		}
    					
	    	}
	    } 
		Client_Service_ColumnLog::addColumn(array('column_version'=>$version,'content'=>json_encode($data)));
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
	
	/**
	 *
	 * Enter description here ...
	 */
	private function _cookData($info,$level = 1) {
		if (!trim($info['name'])) $this->output(-1,  '名称不能为空.');
		if (!intval($info['position'])) $this->output(-1,  '位置只能是数字或者不能为空.');
		return $info;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	private function _cookColumn($info, $pid = '', $level){
		$tmp = array();
		foreach($info['addsort'] as $key => $value){
				if(isset($info['ids'][$key])) {
					$tmp[$key]['id'] = $info['ids'][$key];
				} else {
					$tmp[$key]['id'] = '';
				} 
				$tmp[$key]['sort'] = $info['addsort'][$key];
				$tmp[$key]['pid'] = $pid;
				$tmp[$key]['name'] = $info['addname'][$key];
				$tmp[$key]['link'] = $info['addlink'][$key];
				$tmp[$key]['status'] = $info['status'];
				$tmp[$key]['update_time'] = time();
				if(empty($info['ids'][$key])) $tmp[$key]['create_time'] = time();
				self::_cookData($tmp[$key], $level);
		}
		return $tmp;
	}
}