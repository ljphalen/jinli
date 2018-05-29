<?php
class IndexAction extends SystemAction
{
	public function index()
	{
		/* 根据用户权限显示左侧菜单 */
		if(empty($_SESSION[C('USER_AUTH_KEY')]) && empty($_SESSION['administrator']))
		{
			$this->error('您没有任何可操作权限');
		}
		
		$group = D("NodeGroup");
		$node_group = $group->where("id > 1")->field(array('id','name',))->order("sort")->select();
		
		$data = array();
		
        foreach ($node_group as $group)
        {
            //显示菜单项
            $menu  = array();
            
        	//读取数据库模块列表生成菜单项
			$node    =   D("Node");
			$id	=	$node->getField("id");
			$where['level']=2;
			$where['type']=0;
			$where['status']=1;
			$where['pid']=$id;
			$where['group_id']=$group['id'];
			$list	=	$node->where($where)->field('id,name,action,params,group_id,title')->order('sort asc')->select();
			
			import ('ORG.Util.RBAC');
			$accessList = RBAC::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
			foreach($list as $key=>$module) {
				//此处可能存在bug隐患
				if(isset($accessList[strtoupper(APP_NAME)][strtoupper($module['name'])]) || $_SESSION['administrator'])
				{
					$params = empty($module['params']) ? array() : $this->params($module['params']);
					$module['url'] = U("{$module['name']}/{$module['action']}/", $params);
					
					//设置模块访问权限
					$module['access'] =   1;
					$menu[$key]  = $module;
			    }
			}

            !empty($menu) && $data[$group['id']] = array('name'=>$group['name'], 'menu'=>$menu);
		}

		if(!empty($_GET['tag'])){
			$this->assign('menu',$_GET['tag']);
		}
		
		//调用统计信息
		$this->stat();
		
		$userDetail = D("Admin")->where(array("id"=>admin_id()))->find();
		$this->assign('userDetail', $userDetail);
		$this->assign('menu', $data);
		$this->display();
	}
	
	/* 格式化参数 */
	protected function params($params)
	{
		if (empty($params)) return array();
		
		$array = explode("\n", $params);
		foreach ($array as $value)
		{
			if(empty($value)) continue;
			list($k, $v) = explode("=", $value);
			$param[$k] = $v;
		}
		
		return $param;
	}
	
	/*
	 * 统计数据报表
	 */
	public function stat()
	{
		$d_apk = D('Dev://apks');
		$d_accounts = D('Dev://Accounts');
		
		//获得所有注册用户数
		$user_count = D('Dev://Accounts')->count();
		
		//累计通过审核数量
		$check_count = $d_apk->where(array('status' => array('in',array(ApksModel::APK_TEST_PASS, ApksModel::APK_ONLINE))))->count();
		
		$game_add = $game_update = $game_check =$date_arr = array();
		
		
		//游戏统计
		
		$day_len = 24*3600;

		for ($i=0;$i < 12;$i++)
		{
			$day_time = mktime(0,0,0,date('m'),Date('d')-$i);
			$now_date = Date('Y-m-d',$day_time);
			$date_arr[$i] = '"'.$now_date.'"';
			$time_cond = array('between',array($day_time,$day_time+$day_len));
			
			//游戏增加数
			$add_map = array('created_at' => $time_cond);
			$game_add[$i] = $d_apk->where($add_map)->count();
			//var_dump(Date('Y-m-d H:i:s',$day_time),$d_apk->getlastsql());
			
			//游戏更新数
			$up_map = array('onlined_at' => $time_cond);
			$game_update[$i] = $d_apk->where($up_map)->count();
			
			//游戏审核通过
			$up_map = array('checked_at' => $time_cond,'status' => array('in',array(ApksModel::APK_TEST_PASS, ApksModel::APK_ONLINE)));
			$game_check[$i] = $d_apk->where($up_map)->count();
			//var_dump($game_add,$game_update,$game_check);//exit();
			
			//通过率
			$pass_rate[$i] = $game_add[$i]==0?0:floor($game_check[$i]*100/$game_add[$i]);
			
			//注册数
			$reg_count[$i] = $d_accounts->where('left(`created_at`,10)="'.$now_date.'"')->count();
			
			//审核数
			$check_count[$i] = $d_accounts->join(' account_infos ON accounts.id = account_infos.account_id')->where('account_infos.status='.AccountinfoModel::STATUS_SUC.' and left(`audit_at`,10)="'.$now_date.'"')->count ( '*' );
			
			//通过率
			$accounts_pass_rate[$i] = $reg_count[$i]==0?0:floor($check_count[$i]*100/$reg_count[$i]);
		}
		//var_dump($date_arr);exit;
		$this->assign('user_count',$user_count);
		$this->assign('check_count',$check_count);
		$this->assign('date_arr',$date_arr);
		$this->assign('game_add',$game_add);
		$this->assign('game_update',$game_update);
		$this->assign('game_check',$game_check);
		$this->assign('pass_rate',$pass_rate);
		
		$this->assign('reg_count',$reg_count);
		$this->assign('check_count',$check_count);
		$this->assign('accounts_pass_rate',$accounts_pass_rate);
	}
}
?>
