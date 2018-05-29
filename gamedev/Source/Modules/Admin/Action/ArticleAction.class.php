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
		
		//按时间搜索
		if(!empty($_search["startDay"]) || !empty($_search["endDay"]))
		{
			$s = empty($_search["startDay"]) ? 0 : strtotime($_search["startDay"]);
			$e = empty($_search["endDay"]) ? $s+ 83600 : strtotime($_search["endDay"]) + 83600;
			
			if($e <= $s)
				$this->error("结束时间不能晚于开始时间");

			$map["add_time"] = array("between", array($s, $e));
		}
		
		if($_search['mold'] != 1)
			unset($_search['category']);
		$_search['status'] = array('NEQ',ArticleModel::STATUS_DEL);
		
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
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
		$admin = D("Admin")->find( admin_id() );
		$this->assign('admin', $admin);

		$category_list = D('Dev://Article')->getCategory();
		$this->assign('category_list',$category_list);
		$this->display();
	}
	
	public function sdkadd()
	{
		$admin = D("Admin")->find( admin_id() );
		$this->assign('admin', $admin);
		
		$this->display();
	}
	
	public function addSave()
	{
		$id = $this->_post('id');
		$_POST['admin_id'] = $_SESSION['authId'];
		if(!empty($_FILES["file_path"]["tmp_name"])) {
			$uploadList = Helper("Upload")->_upload("doc");
			if (is_array ( $uploadList )) {
				$_POST['file_path'] = $uploadList[0]['filepath'];
			}elseif (is_string($uploadList) && strlen($uploadList) > 0){
				return $this->error("文件上传出错:".$uploadList);
			}
		}

		//过滤一些文档中不规范的格式
		$_POST["content"] = editor_format($_POST["content"]);
		
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