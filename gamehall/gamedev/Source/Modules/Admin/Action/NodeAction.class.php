<?php
class NodeAction extends SystemAction
{
	//节点菜单类型
	protected $type = array(0=>'左侧导航', 1=>'顶部导航', 2=>'隐藏菜单');
	
	function _before()
	{
		$this->assign("node_type", $this->type);
	}
	
	public function _filter(&$map)
	{
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		
        if(!empty($_GET['group_id'])) {
            $map['group_id'] =  $_GET['group_id'];
            $this->assign('nodeName','分组');
        }elseif(empty($_POST['search']) && !isset($map['pid']) ) {
			$map['pid']	=	0;
		}
		if($_GET['pid']!=''){
			$map['pid']=$_GET['pid'];
		}
		
		$_SESSION['currentNodeId']	=	$map['pid'];
		
		//获取上级节点
		$node  = D("Node");
        if(isset($map['pid'])) {
            if($node->getById($map['pid'])) {
                $this->assign('level',$node->level+1);
                $this->assign('nodeName',$node->name);
            }else {
                $this->assign('level',1);
            }
        }
	}

	public function _before_index() {
		$model	=	D("NodeGroup");
		$list	=	$model->where('status=1')->order("id desc")->select();
		$this->assign('groupList',$list);
		
		/* 使用关联模型  */
		$this->isRelation = TRUE;
		
		/* 节点列表按指定字段排列 Add BY 4wei.cn */
		$_REQUEST ['orderField'] = "group_id,sort";
		$_REQUEST ['_sort'] = "asc";
	}

	// 获取配置类型
	public function _before_add() {
		$model	=	D("NodeGroup");
		$list	=	$model->where('status=1')->order("id desc")->select();
		$this->assign('groupList', $list);
		
		$node	=	D("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid', $node->id);
        $this->assign('parent_node_name', $node->name);
		$this->assign('level', $node->level+1);
		
		if($_REQUEST["id"])
		{
			$this->assign("vo", $node->where(array("id"=>$_REQUEST["id"]))->find());
		}
	}

    public function _before_patch() {
		$model	=	D("NodeGroup");
		$list	=	$model->where('status=1')->order("id desc")->select();
		$this->assign('groupList',$list);
		
		$node	=	D("Node");
		$node->getById($_SESSION['currentNodeId']);
        $this->assign('pid',$node->id);
		$this->assign('level',$node->level+1);
    }
    
	public function _before_edit() {
		$model	=	D("NodeGroup");
		$list	=	$model->where('status=1')->order("id desc")->select();
		$this->assign('groupList',$list);
	}
	
	function update() {
		$name=$this->getActionName();
		$model = D ( $name );
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		// 更新数据
		$list=$model->save ();

		if (false !== $list) {
			//成功提示
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('编辑成功!', 'closeCurrent');
		} else {
			//错误提示
			$this->error ('编辑失败!');
		}
	}
}
?>