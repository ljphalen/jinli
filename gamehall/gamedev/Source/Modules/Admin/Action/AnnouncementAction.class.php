<?php
/**
 * 后台内容管理
 * @author jiazhu
 *
 */
class AnnouncementAction extends SystemAction
{
	public $model = 'Announcement';
	
	public function _filter(&$map)
	{
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see SystemAction::index()
	 */
	public function index()
	{
		$model = D ($this->model);
		
		$_search = MAP();
		
		if (!$order = $this->_request("orderField", "trim", "")) {
			$order = $model->getPk ();
			$_REQUEST['orderField'] = $order;
		}
		
		if (isset($_REQUEST['orderDirection'])) {
			$sort = (strtolower($_REQUEST ['orderDirection']) == 'asc') ? 'asc' : 'desc';
		} else {
			$sort = TRUE ? 'asc' : 'desc';
			$_REQUEST['orderDirection'] = $sort;
		}
		
		$map = array();
		$nowTime = strtotime(date ( 'Y-m-d H:i:s' ));
		//按时间搜索
		if(!empty($_search["start_s"]) || !empty($_search["start_e"]))
		{
			$s = empty($_search["start_s"]) ? '0000-00-00 00:00:00' : $_search["start_s"];
			$e = empty($_search["start_e"]) ? date ( 'Y-m-d H:i:s' ) : $_search["start_e"];
				
			if(strtotime($e) <= strtotime($s))
				$this->error("结束时间不能晚于开始时间");
		
			$map["start_time"] = array("between", array($s, $e));
		}
		
		if(!empty($_search["status"])){
			if ($_search['status'] == AnnouncementModel::STATUS_RELEASE) {
				$map['start_time'] = array('elt', date('Y-m-d H:i:s', $nowTime));
				$map['end_time'] = array('egt', date('Y-m-d H:i:s', $nowTime));
			} elseif ($_search['status'] == AnnouncementModel::STATUS_DOWN){
				$map['end_time'] = array('elt', date('Y-m-d H:i:s', $nowTime));
			} elseif ($_search['status'] == AnnouncementModel::STATUS_PRE_RELEASE){
				$map['start_time'] = array('egt', date('Y-m-d H:i:s', $nowTime));
			} else {
				$map["status"] = $_search['status'];
			}
		}
		
		if(!empty($_search["keyword"])){
			$map["content"] = array("like", '%'.$_search['keyword'].'%');
		}
		
		if(!empty($_search["author"])){
			$map["author"] = array("like", '%'.$_search['author'].'%');
		}
		
		$_search['is_deleted'] = array('NEQ',AnnouncementModel::STATUS_IS_DELETE);
// 		dump($map);echo '</br>';
// 		dump($_search);
		$volist = $model->where($map)->select();
		$count = count($volist);
		if ($count > 0) {
				
			/*
			 * 数据导出断点
			*/
			if ($_REQUEST['do_export'])
			{
				$this->do_export($map);
			}
				
			import ( "ORG.Util.Page" );
				
			//创建分页对象
			if(!empty( $_REQUEST['numPerPage'] ))
			{
				$listRows = intval($_REQUEST ['numPerPage']) ? intval($_REQUEST ['numPerPage']) : C('PAGE_LISTROWS');
				Cookie::set("numPerPage", intval($_REQUEST ['numPerPage']));
			}
			if(Cookie::get("numPerPage") > 0)
			{
				$listRows = Cookie::get("numPerPage");
				Cookie::set("numPerPage", $listRows);
			}
			$_GET[C('VAR_PAGE')] = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
			$listRows = empty($listRows) ? C('PAGE_LISTROWS') : $listRows;
				
			$p = new Page ( $count, $listRows );
			
			//分页查询数据，增加关联模型，Add By 4wei.cn
			$voList = $this->isRelation == TRUE
					?
						$model	->relation(TRUE)
								->where($map)
								->order($order . " " . $sort)
								->limit($p->firstRow . ',' . $p->listRows)
								->select ( )
					:	
					  	$model	->where($map)
					  			->order($order . " " . $sort)
								->limit($p->firstRow . ',' . $p->listRows)
								->select ( );
			
			Log::write($model->getlastsql(), Log::DEBUG);
			
			//后续完善列表信息 edit by jiazhu 
			if(method_exists($this, '_after_index'))
			{
				call_user_func_array(array($this, '_after_index'), array(&$voList));		
			}
				
			//追加信息where
			if (is_array($voList))
			{
				foreach ($voList as $key => $val)
				{
					$startTime = strtotime($val['start_time']);
					$endTime = strtotime($val['end_time']);
					if ($val['status'] == AnnouncementModel::STATUS_PRE_RELEASE) {
					//已发布
						if ($startTime <= $nowTime && $nowTime <= $endTime) {
							$voList[$key]['status'] = '2';
						}
						//已下线
						if ($nowTime >= $endTime) {
							$voList[$key]['status'] = '3';
						}
					}
				}
			}
		
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val )
			{
				if (! is_array ( $val ))
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
			}
				
			//分页显示
			$page = $p->show ();
				
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
				
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $listRows );
		$this->assign ( 'currentPage', !empty($_GET[C('VAR_PAGE')])?$_GET[C('VAR_PAGE')]:1);
			
		Cookie::set ( '_currentUrl_', __SELF__ );
// 		dump($info);
		
		$this->assign("_search", $_search);
		$this->display();
	}
	
	public function _before_index()
	{
		$this->_after_index();
	}
	
	public function _after_index()
	{
		$status_list = D($this->model)->getStatus();
		$this->assign('status_list',$status_list);
	}
	
	public function add()
	{
		$admin = D("Admin")->find( admin_id() );
		$this->assign('admin', $admin);

		$this->display();
	}
	
	public function addSave()
	{
		$id = $this->_post('id');

		//过滤一些文档中不规范的格式
		$_POST["content"] = editor_format($_POST["content"]);
		$nowTime = date ( 'Y-m-d H:i:s' );
		$_POST['created_time'] = $nowTime;
		
		$startTime = $_POST['start_time'];
		if(strtotime($nowTime) > strtotime($startTime)){
			$this->error ('开始时间必须大于当前时间!');
		}
		
		$endTime = $_POST['end_time'];
		if(strtotime($startTime) > strtotime($endTime)){
			$this->error ('结束时间必须大于开始时间!');
		}
		
		$model = D ($this->model);
		if(strtotime($nowTime) < strtotime($startTime)){//开始时间未到,状态未预发布
			if($_POST['status'] == AnnouncementModel::STATUS_PRE_RELEASE){
				$where = array();
				$sql = "SELECT id,start_time,end_time FROM announcement WHERE 
				((start_time <= '$startTime' AND end_time >= '$startTime') 
				OR (start_time <= '$endTime' AND end_time >= '$endTime')) 
				AND status=1;";
				$rows = $model->query($sql);
				if(count($rows) >= 1){
					$error = '与已有公告ID'.$rows[0]['id'].'时间'.'('.$rows[0]['start_time'].'~'.$rows[0]['end_time']
					.')冲突,是否以当前配置为准?确定则之前ID结束';
					$this->error ($error);
				}
// 				$_POST['status'] = AnnouncementModel::STATUS_PRE_RELEASE;
			}
		}
		
// 		Log::write ( 'addSave', Log::DEBUG );
		
		$res =  $model->create();

		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		//保存当前数据对象
		if ($id)
		{
			$list = $model->save();
		}else 
		{
			$list=$model->add();
		}
		if ($list !== false)
		{
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('操作成功!', 'closeCurrent');
		} else {
			$this->log_error($model->getDbError());
			$this->error ('操作失败!');
		}
	}
	
	public function edit()
	{
		$id = $this->_get('id');
		$info = D($this->model)->find($id);
// 		$status_list = D('Dev://Announcement')->getStatus();
		
// 		$this->assign('status_list',$status_list);
		$this->assign('info',$info);
		$this->display('add');
	}
	
	public function editStatus()
	{
		$id = $this->_get('id');
		$status = $this->_get('status');
		$updateList = array('status'=>1);
		
		$model = D ($this->model);
		$info = $model->find($id);
		$nowStatus = $info['status'];
		$nowTime = date ( 'Y-m-d H:i:s' );
		if ($status == AnnouncementModel::STATUS_RELEASE) {
			$updateList['start_time'] = $nowTime;
			$map = array();
			$map['start_time'] = array('elt', $nowTime);
			$map['end_time'] = array('egt', $nowTime);
			$count = $model->where($map)->count('*');
			if ($count >= 1) {
				$this->error('已发布公告，请重新编辑或下线已发布公告');;
			}
		}
		
		if ($status == AnnouncementModel::STATUS_DOWN) {
			$updateList['end_time'] = $nowTime;
		}
		
		if ($nowStatus == AnnouncementModel::STATUS_SAVE) {
			$startTime = $info['start_time'];
			$endTime = $info['end_time'];
			$sql = "SELECT id,start_time,end_time FROM announcement WHERE 
				((start_time <= '$startTime' AND end_time >= '$startTime') 
				OR (start_time <= '$endTime' AND end_time >= '$endTime')) 
				AND status in (1,2);";
				$rows = $model->query($sql);
				if(count($rows) >= 1){
					$this->error('与已有状态冲突，请重新编辑');
				}
		}
		
// 		$res = D($this->model)->where('id='.$id)->save($updateList);
		$res = $model->where('id='.$id)->save($updateList);
		if ($res)
		{
			$this->success('操作成功');
		}else 
		{
			$this->error('操作失败');
		}
	}
	
	public function del()
	{
		$id = $this->_get('id');
		$res = D($this->model)->where('id='.$id)->save(array('is_deleted'=>2));
		if ($res)
		{
			$this->success('操作成功');
		}else
		{
			$this->error('操作失败');
		}
	}
}