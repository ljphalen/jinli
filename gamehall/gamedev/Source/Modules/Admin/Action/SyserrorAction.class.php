<?php
class SyserrorAction extends SystemAction
{
	public $model = "Syserror";
	
	public function _filter(&$map){
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
	}
	
	function sovled()
	{
		$model = D($this->model);
		$model->create(array("id"=>$_GET["id"], "status"=>1));
		$model->save();
		
		$this->success("操作成功");
	}
}