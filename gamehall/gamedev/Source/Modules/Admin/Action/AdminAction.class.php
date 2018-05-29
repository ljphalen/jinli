<?php
// 后台用户模块
class AdminAction extends SystemAction {
	public function _filter(&$map){
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		$map['id'] = array('egt',2);
	}

	public function _before_index() {
		/* 使用关联模型  */
		$this->isRelation = TRUE;
	}

	function edit($name="") {
		$name = $name ? $name : $this->model ;
		$name = $name ? $name : $this->getActionName();
		
		$model = D ( $name );
		$id = $_REQUEST [$model->getPk ()];
		$vo = $model->getById ( $id );
		
		//记录不存在
		if(!is_array($vo))
		{
			$this->error ('您要修改的记录不存在');
		}
		
		//管理员用户名禁止修改
		unset($_POST["account"]);
		
		$this->assign ( 'vo', $vo );
		$this->display ();
	}
	
	public function _before_update()
	{
		//管理员用户名禁止修改
		unset($_POST["account"]);
	}
	
	public function _before_foreverdelete()
	{
		if(D("Entry")->where(array("admin_id"=>$_REQUEST["id"]))->find())
		{
			$this->error("用户名下含有录入数据，不能删除");
		}
	}
		
	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
            $this->error( '用户名必须是字母，且5位以上！');
        }
		$User = D("Admin");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }
    
	// 插入数据
	public function insert()
	{
		// 创建数据对象
		$User	 =	 D("Admin");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->addRole($result);
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
	}

	protected function addRole($userId) {
		//新增用户自动加入相应权限组
		$RoleUser = D("RoleUser");
		$RoleUser->user_id	=	$userId;
        // 默认加入网站编辑组
        $RoleUser->role_id	=	3;
		$RoleUser->add();
	}

    //重置密码
    public function resetPwd()
    {
    	if($_POST['password'] != $_POST['repassword'])
    		$this->error('密码确认不一致，请认真检查！');
    	
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        
        if(empty($id) || !D("Admin")->find($id))
        	$this->error('用户不存在');
        
        $User = D("Admin");
		$User->password	=	md5($password);
		$User->id			=	$id;
		$result	=	$User->save();
        if(false !== $result) {
            $this->success("密码修改为:$password");
        }else {
        	$this->error('重置密码失败，请联系管理员！');
        }
    }
    
    //更新用户缓存
    function clearcache()
    {
		//更新用户缓存
		$data = D("Admin")->field(array('id','nickname'))->where("status=1")->select();
		foreach ($data as $row) $AdminData[$row['id']] = $row['nickname'];
		S("AdminData", $AdminData);
    	$this->success("操作完成");
    }
}