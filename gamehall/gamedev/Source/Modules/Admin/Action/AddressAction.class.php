<?php
// 后台测试模块
class AddressAction extends SystemAction {
	public function _filter(&$map){
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
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
	
}