<?php
/**
 * 后台内容管理
 * @author jiazhu
 *
 */
class ArticleAction extends SystemAction
{
	public $model = 'Article';
	
	public function _filter(&$map)
	{
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		
		//按时间搜索
		if(!empty($_search["startDay"]) || !empty($_search["endDay"]))
		{
			$s = empty($_search["startDay"]) ? 0 : $_search["startDay"].' 00:00:00';
			$e = empty($_search["endDay"]) ? 0 : $_search["endDay"].' 23:59:59';
			$map["pubdate"] = array("between", array($s, $e)); 
		}
		
		if($_search['mold'] != 1)
			unset($_search['category']);
		$_search['status'] = array('NEQ',ArticleModel::STATUS_DEL);
		$map = $_search;
		$this->assign("_search", $_search);
	}
	
	public function _before_index()
	{
		$this->_after_index();
	}
	
	public function _after_index()
	{
		$category_list = D($this->model)->getCategory();
		$this->assign('category_list',$category_list);
		$mold_list = D($this->model)->getMold();
		$this->assign('mold_list',$mold_list);
	}
	
	
	public function add()
	{
		$category_list = D('Dev://Article')->getCategory();
		$this->assign('category_list',$category_list);
		$this->display();
	}
	
	public function sdkadd()
	{
		$this->display();
	}
	
	public function addSave()
	{
		$id = $this->_post('id');
		$_POST['admin_id'] = $_SESSION['authId'];
		$uploadList = Helper("Upload")->_upload("doc");
		if (is_array ( $uploadList )) {
			$_POST['file_path'] = $uploadList[0]['filepath'];
		}
		//var_dump($uploadList,$_POST);
		$model = D ($this->model);
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
		$category_list = D('Dev://Article')->getCategory();
		
		$this->assign('category_list',$category_list);
		$this->assign('info',$info);
		if ($info['mold'] == ArticleModel::MUST_VALIDATE)
		{
			$this->display('add');
		}else
		{
			$this->display('sdkadd');
		}
	}
	
	public function editStatus()
	{
		$id = $this->_get('id');
		$status = $this->_get('status');
		$res = D($this->model)->where('id='.$id)->save(array('status'=>$status, 'update_time'=>time()));
		if ($res)
		{
			$this->success('操作成功');
		}else 
		{
			$this->error('操作失败');
		}
	}
}