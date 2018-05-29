<?php
class SystemAction extends Action
{
	public $isRelation = FALSE;		//使用关联模型
	public $ViewModel = NULL;		//使用视图模型
	public $model = NULL;			//公用模块可以跨模型调用
	public $allow_action = array();
	public $not_allow_action = array();
	public $workflow = 3;
	
	//admin 载入 dev 中 model，多个以逗号分隔
	public $dev_model_name = 'Accounts,Accountinfo,Apps,Blocklog,Article,ApkSafe';
	
	/*
	 * 内容导出配置
	 *  arrray(
	 *  	'字段名' => array('字段中文名','字段规则','附加规则','输入参数')，
	 *  	'dateline' => array('审核日期','date','function','"Y-m-d H:i:s",{{field_val}}'),
	 *  )  //模式和model中 auto类似
	 */
	protected $_export_config = array();
	
	//-4:封号, -3:认领下线, -2:已下线, -1:审核不通过, 0:未提交, 1:审核中, 2:审核通过, 3:已上线, 4:自动上线
	public $apkStatus = array (
				'3' => '已上线',
				'2' => '审核通过',
				'0' => '未提交',
				'1' => '审核中',
				'-1' => '未通过',
				'-2' => '已下线',
				'-3' => '认领下线',
				'-4' => '封号',
				'4'	 => '自动上线',
		);
	
	public $feeMode = array("101" => "移动MM",
			"102" => "移动基地",
			"103" => "联通短信",
			"104" => "电信短信",
			"105" => "第三方支付",
			"107" => "金立支付",
			"106" => "无"
	);
	
	public function getApkStatus($status=null)
	{ 
		if ($status !== null)
		{
			return @$this->apkStatus[$status];
		}else 
		{
			return $this->apkStatus;
		}
	}
	
	function _initialize()
	{
		//只允许指定的操作
		if(!empty($this->allow_action) && !in_array(strtolower(ACTION_NAME), array_map('strtolower',$this->allow_action)))
			$this->error('不允许执行'.ACTION_NAME.'操作');
		//只允许指定的操作
		if(!empty($this->not_allow_action) && in_array(strtolower(ACTION_NAME), array_map('strtolower',$this->not_allow_action)))
			$this->error('不允许执行'.ACTION_NAME.'操作');

		import ('ORG.Util.RBAC');
		import ('ORG.Util.Cookie');
		import ('ORG.Util.Session');
		import ('ORG.Util.Page');
		
		//载入DEV模块中model
		$this->dev_model($this->dev_model_name);
		
		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
			//此处和IndexAction文件中检验默认项目入口权限一样，可能存在bug
			if (! RBAC::AccessDecision ()) {
				//检查认证识别号
				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					
					//如果处于登陆状态，则弹出301超时提示
					if($this->isAjax())
					{
						$this->ajaxReturn(array("info"=>"登陆超时，请重新登陆", "status"=>301));
						exit;
					}
					
					//跳转到认证网关
					redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
				}
				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE' ) );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					// 提示错误信息
					$this->assign('jumpUrl', U("Public/logout"));
					$this->assign('waitSecond', 5);
					$this->error (L ( '_VALID_ACCESS_' ) . "，请联系管理员授权!<br />当前操作：".MODULE_NAME . " -> " . ACTION_NAME);
				}
			}
		}
		
		//共用前置操作
		if(method_exists($this, "_before")) call_user_func(array($this, "_before"));
		
		$this->assign ('_search', $_POST["_search"]);
		$this->assign("waitSecond", 60);
		$this->assign("apkStatus", $this->apkStatus);
		$this->assign("feeMode", $this->feeMode);
		$this->get_admin_info();
	}
	
	public function ajaxAssign(&$result)
	{
		$result['statusCode']  =  $result['status'];
		$result['navTabId']  =  $_REQUEST['navTabId'];
		$result['callbackType']  =  $_REQUEST['callbackType'];
		$result['message'] =  $result['info'];
	}
	
	public function index($name="")
	{
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		if(!empty($this->ViewModel)) $name = $this->ViewModel;
		//列表过滤器，生成查询Map对象
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}

		$this->assign ('map', $map);
		$this->display ();
		return;
	}
	
	/**
	 * 数据内容导出
	 * @param string $name model名称
	 */
	public function export()
	{
		set_time_limit(100);

		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$limit_max = 100000;
		if(!empty($this->ViewModel)) $name = $this->ViewModel;
		//检测是否有导出配置
		if (empty($this->_export_config)) 
		{
			$this->error('缺少导出配置');
		}
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ($name);
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$model = D ($name);
		if (! empty ( $model )) {
			//$this->_list ( $model, $map );
					//if (isset($_REQUEST['orderField']) && in_array($_REQUEST['orderField'], $model->getDbFields())) {
		if (isset($_REQUEST['orderField']) ) {
			$order = $_REQUEST['orderField'];
		} else {
			$order = $model->getPk ();
		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST['orderDirection'])) {
			$sort = (strtolower($_REQUEST ['orderDirection']) == 'asc') ? 'asc' : 'desc';
		} else {
			$sort =  'asc' ;
		}

		
		//取得满足条件的记录数
		$count = $model->where ( $map )->count ( '*' );
		
		
		if ($count > 0) {
			
			//查询字段
			$data = array();
			$field_arr = array();
			foreach ($this->_export_config as $key => $val)
			{
				$field_name[] = $val[0];
				$field_arr[] = $key;
			}
			$field_str = implode(',', $field_arr);
			$data = array (1 => $field_name );
			$voList = 	$model
								->where($map)
					  			->order($order . " " . $sort)
								->limit($limit_max)
								->select ( );
			$i = 2;
			foreach ($voList as $key => $val)
			{
				$one_row = array();
				$j = 0;
				// 'dateline' => array('审核日期','date','function','"Y-m-d H:i:s",{{field_val}}','真实字段名'),
				foreach ($this->_export_config as $row => $auto)
				{
					$one_val =  $val[$row];
					if (isset($auto[4]) && !empty($auto[4]))
					{
						$one_val =  $val[$auto[4]];
					}else 
					{
						$one_val =  $val[$row];
					}
					//var_dump($one_val);exit;
					//处理数值
					if (isset($auto[1]))
					{
						switch ($auto[2])
						{
							case 'function' :
							case 'callback':
								$function = $auto[2]=='function'?$auto[1]:array(&$this,$auto[1]);
								if (isset($auto[3]))
								{
									$param_str = str_replace('{{field_val}}', $one_val, $auto[3]);
									$one_val = call_user_func_array($function, explode(',', $param_str));
								}else 
								{
									$one_val = call_user_func_array($function, array($one_val));
								}
								break;
							case 'm_callback': //支持传多个自有参数
								$function = $auto[2]=='function'?$auto[1]:array(&$this,$auto[1]);
								if (isset($auto[3]))
								{
									$params = explode(',',$auto[3]) ;
									$param_str = array();
									foreach ($params as $col => $row)
									{
										
										if (isset($val[$row]))
										{
											$param_str[] = $val[$row];
										}else 
										{
											$param_str[] = $row;
										}
									}
									$one_val = call_user_func_array($function,$param_str);
								}else 
								{
									$one_val = call_user_func_array($function, array($one_val));
								}
								break;
							case 'string':
							default:
								$one_val = $auto[1];
						}
					}
					
					$one_row[$j] = $one_val;
					$j++;
				}
				$data[$i] = $one_row;
				$i++;
			}					
			
	    	}
	    	
	    	//var_dump($data);exit;
	    	import('ORG.Util.PhpExcel',LIB_PATH);
			$xls = new PhpExcel ( 'UTF-8', false, 'My Sheet' );
			$xls->addArray ( $data );
			$cname = 'export_'.$name . date ( 'YmdHis' );
			$xls->generateXML ( $cname );
		
		}
		
	}
	
	/*
	 * 用户数据，为导出准备
	 */
	public function getAccountField($id,$field)
	{
		$res = D('Dev://Accounts')->field($field)->find($id);
		return $res[$field];
	}
	
	public function getAccountInfoFiled($id, $field)
	{
		$res = D('Accountinfo')->getAccInfo( $id );
		return $res[$field];
	}
	
	public function getAdminInfo($id)
	{
		$admin_info = D("Admin")->where( array('id'=>$id) )->find();
		return $admin_info['account'];
	}
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = $_REQUEST [$val];
			}
		}
		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _list($model, $map, $sortBy = '', $asc = TRUE)
	{
		//排序字段 默认为主键名 FOR DWZ, Add BY 4wei.cn
		//if (isset($_REQUEST['orderField']) && in_array($_REQUEST['orderField'], $model->getDbFields())) {
		$_REQUEST = array_merge($_REQUEST, $_GET, $_POST);
		if (!$order = $this->_request("orderField", "trim", "")) {
			$order = ! empty( $sortBy) ? $sortBy : $model->getPk ();
			$_REQUEST['orderField'] = $order;
		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset($_REQUEST['orderDirection'])) {
			$sort = (strtolower($_REQUEST ['orderDirection']) == 'asc') ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
			$_REQUEST['orderDirection'] = $sort;
		}

		/* DWZ Ajax 分页配置 FOR DWZ, Add BY 4wei.cn */
		$_GET[C('VAR_PAGE')] = !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1;
		
		//取得满足条件的记录数
		$count = $model->where ( $map )->count ( '*' );

		Log::write($model->getlastsql(), Log::DEBUG);
		
		if ($count > 0) {
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
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			
			//分页显示
			$page = $p->show ();
			
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			
			/*/exit (dump($voList));/**/
			
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
		return;
	}

	function insert($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ($name);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		
		//保存当前数据对象
		$list=$model->add();
		if ($list!==false)
		{
			if(method_exists($this, '_after_insert'))
				call_user_func_array(array($this, '_after_insert'), array($model));
			
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('新增成功!', 'closeCurrent');
		} else {
			$this->log_error($model->getDbError());
			$this->error ('新增失败!');
		}
	}

	public function add() {
		$this->display ();
	}

	function read() {
		$this->edit ();
	}

	function edit($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ( $name );
		$id = $this->_request($model->getPk(), 'intval', 0);
		
		if(empty($id) ||!$vo = $model->find( $id ))
			$this->error ('您要修改的记录不存在');
		
		$this->assign ('vo', $vo);
		$this->display ();
	}

	function update($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D( $name );
		$pk = $model->getPk();
		$map = array($pk=>$_POST[$pk]);
		$data = $model->create($_POST);
		if (false === $data) {
			$this->error ( $model->getError () );
		}
		//默认只更新一条记录
		$result = $model->where($map)->limit(1)->save($data);
		if (false !== $result) {
			if(method_exists($this, '_after_update'))
				call_user_func_array(array($this, '_after_update'), array($model));
			
			//成功提示
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('编辑成功!', 'closeCurrent');
		} else {
			$this->log_error($model->getDbError());
			$this->error ('编辑失败!');
		}
	}
	/**
     +----------------------------------------------------------
	 * 默认删除操作
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function delete($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$data = array('displayorder'=>-1,'is_delete'=>1);
				$list=$model->where ( $condition )->save($data);
				if ($list!==false) {
					$this->success ('删除成功！' );
				} else {
					$this->log_error($model->getDbError());
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	public function foreverdelete($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					$this->success ('删除成功！');
				} else {
					$this->log_error($model->getDbError());
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
		$this->forward ();
	}

	public function clear($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ($name);
		if (! empty ( $model )) {
			if (false !== $model->where ( 'status=-1' )->delete ()) { // zhanghuihua@msn.com change status=1 to status=-1
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ( L ( '_DELETE_SUCCESS_' ) );
			} else {
				$this->error ( L ( '_DELETE_FAIL_' ) );
			}
		}
		$this->forward ();
	}
	/**
     +----------------------------------------------------------
	 * 默认禁用操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		if ($list!==false) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态禁用成功' );
		} else {
			$this->error  (  '状态禁用失败！' );
		}
	}
	
	public function checkPass() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->checkPass( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态批准成功！' );
		} else {
			$this->error  (  '状态批准失败！' );
		}
	}

	public function recycle() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->recycle ( $condition )) {

			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态还原成功！' );

		} else {
			$this->error   (  '状态还原失败！' );
		}
	}

	public function recycleBin() {
		$map = $this->_search ();
		$map ['status'] = - 1;
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}

	/**
     +----------------------------------------------------------
	 * 默认恢复操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	function resume() {
		//恢复指定记录
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态恢复成功！' );
		} else {
			$this->error ( '状态恢复失败！' );
		}
	}

    /**
     +----------------------------------------------------------
     * 节点AJAX排序操作，Add BY 4wei.cn
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function sort($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ($name);
		
        $model->data($_GET)->save();
        $this->success ('操作成功');
    }
	
    /**
     +----------------------------------------------------------
     * 多字段重复数据检查
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    public function exist($where, $model="") {
		$model = $model ? $model : $this->model ;
		$model = $model ? $model : $this->getActionName();
		
		$model = D ($model);
		
		$result = $model->where($where)->find();
		//$this->error($model->getLastSql());
		
        return is_array($result) ? $result : FALSE;
    }
    
    /**
     +----------------------------------------------------------
     * 将排序的字段生成下拉菜单
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return String
     +----------------------------------------------------------
     */
    public function getSelect($name, $list, $selected="", $model="") {
		import("ORG.Util.Tree");
		
		$tree = new tree($list);
		
		$html = "<select class=\"combox\" name=\"{$name}\"><option value=\"\">请选择</option>";
		$format = "<option value='\$id'\$selected>\$spacer\$name</option>";
		$html .= $tree->get_tree(0, $format, $selected);
		$html .= "</select>";
		
		$this->assign($name, $html);
		return $html;
    }
    
    /**
     +----------------------------------------------------------
     * 获取管理员用户信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
     */
    public function get_admin_info()
    {
		if(!$uid = session(C('USER_AUTH_KEY')))
			return array();
		
		static $admin_info;
		if(!isset($admin_info))
		{
			$admin_info = D("Admin")->relation(true)->where( array('id'=>$uid) )->find();
			$admin_info['admin'] = session(C('ADMIN_AUTH_KEY'));
		}
		
		return $admin_info;
    }
    
    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data,$type='')
    {
    	$data['message'] = $data['info'];
    	parent::ajaxReturn($data,$type);
    }
    
    /**
     +----------------------------------------------------------
     * 操作成功跳转的快捷方法
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function success($message,$jumpUrl='',$ajax=false)
    {
    	$_REQUEST['callbackType'] = isset($_REQUEST['callbackType']) ? $_REQUEST['callbackType'] : '';
    	if($jumpUrl == 'closeCurrent')
    	{
    		$_REQUEST['callbackType'] = 'closeCurrent';
    		$jumpUrl='';
    	}

    	$this->assign("uid", null);
    	parent::success($message,$jumpUrl='',$ajax=false);
    	exit;
    }
    
    /**
     +----------------------------------------------------------
     * 操作错误跳转的快捷方法
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
     */
    protected function error($message,$jumpUrl='',$ajax=false)
    {
    	parent::error($message,$jumpUrl='',$ajax=false);
    	exit;
    }
    
    protected function log_error($error)
    {
    	Log::write('SQL EXECUTE EMERG ERROR: %s', $error, Log::EMERG);
    	APP_DEBUG && halt($error);
    }
    
    function display($templateFile='',$charset='',$contentType='',$content='',$prefix='')
    {
    	if($templateFile=='' && !empty($this->_tpl))
    		$templateFile = $this->_tpl;
    	
    	parent::display($templateFile,$charset,$contentType,$content,$prefix);
    }
    
    function base64_encode($str)
    {
    	$encode = base64_encode($str);
    	return str_replace(array('+', '/'), array("*", "-"), $encode);
    }
    
    function base64_decode($str)
    {
    	$str = str_replace(array("*", "-"), array('+', '/'), $str);
    	return base64_decode($str);
    }
    
    
    private function dev_model($name)
    {
    	if (empty($name)) return false;
    	$name_list = explode(',', $name);
    	foreach ($name_list as $key => $val)
    	{
    		import('Dev/Model/'.$val.'Model',realpath(APP_PATH).DIRECTORY_SEPARATOR.C('APP_GROUP_PATH'));
    	}
    	return true;
    }
    
    // 标签信息
    function _getLebals() {
    	$labelInfo = array ();
    	$Label = D ( "Label" );
    	$baseLabel = $Label->where ( array (
    			'parent_id' => 0
    	) )->select ();
    	if (! empty ( $baseLabel )) {
    		foreach ( $baseLabel as $item ) {
    			$parentId = $item ['id'];
    			if ($item ['name'] == '资费方式' || $item ['name'] == '游戏评级')
    				continue;
    			$childrenLabel = $Label->where ( array (
    					'parent_id' => $parentId
    			) )->select ();
    			$labelInfo [$parentId] = array (
    					'name' => $item ['name'],
    					'child' => $childrenLabel
    			);
    		}
    	}
    
    	$this->assign ( 'labelInfo', $labelInfo );
    }
    
}
