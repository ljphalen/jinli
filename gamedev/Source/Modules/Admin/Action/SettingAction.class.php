<?php
/**
 * 后台配置首页背景图片
 * @author shuhai
 */
class SettingAction extends SystemAction
{
	public $model = 'SettingImage';
	
	function _filter(&$map)
	{
		$search = MAP();
		$map = array_merge($search, $map);
	}
	
	function resume() 
	{
		$model = D ($this->model);
		$id = $this->_get("id", 'intval', 0);
		if (false !== $model->query("update setting_image set status = status ^ 1 where id=$id")) {
			$this->success ( '状态恢复成功！' );
		} else {
			$this->error ( '状态恢复失败！' );
		}
	}

	function forbid()
	{
		$this->resume();	
	}

	function upload()
	{
		$isupdate = $this->_post('isupdate', 'intval', "0");
		
		import( 'ORG.Net.UploadFile' );
		$upload = new UploadFile();
		$upload->allowExts = array('png','PNG', 'jpg');

		$_FILES['image']['name'] = str_replace('.PNG', '.png', $_FILES['image']['name']);
		$upload->maxSize = 1024*1000*5;
		$upload->savePath = helper("Apk")->get_path("image");
		$upload->uploadReplace = true;
		
		//发布新记录或者更新记录有上传图片时处理
		if(!$isupdate || !empty($_FILES["image"]["size"]))
		{
			if(!$upload->upload())
				$this->error($upload->getErrorMsg());
		
			$info = $upload->getUploadFileInfo();
			$_POST["image"] = helper("Apk")->get_url("image").$info[0]['savename'];
		}
		
		$_POST["admin_id"] = admin_id();
		$_POST["start_time"] = strtotime($_POST["start_time"]);
		$_POST["end_time"] = strtotime($_POST["end_time"]);
		
		if($this->_post('isupdate'))
			parent::update();
		else
		{
			$_POST["create_at"] = time();
			parent::insert();
		}
	}
}