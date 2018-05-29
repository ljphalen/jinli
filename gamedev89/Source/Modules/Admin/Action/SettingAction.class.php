<?php
/**
 * 后台配置首页背景图片
 * @author shuhai
 */
class SettingAction extends SystemAction
{
	protected $filename = 'index-background-slide';
	
	function index()
	{
		$file = sprintf("%s%s%s", helper("Apk")->get_path("image"), $this->filename, ".png");
		if(is_file($file))
			$this->assign("image", sprintf("%s%s%s", helper("Apk")->get_url("image"), $this->filename, ".png"));
		$this->display();
	}
	
	function upload()
	{
		
		import( 'ORG.Net.UploadFile' );
		$upload = new UploadFile();
		$upload->allowExts = array('png','PNG');
		//temp write
		$_FILES['image']['name'] = str_replace('.PNG', '.png', $_FILES['image']['name']);
		$upload->maxSize = 1024*1000*5;
		$upload->savePath = helper("Apk")->get_path("image");
		$upload->saveRule = $this->filename;
		$upload->uploadReplace = true;
		
		if(! $upload->upload()){
			$this->error($upload->getErrorMsg());
		} else {
			$info = $upload->getUploadFileInfo();
			$url = helper("Apk")->get_url("image").$info[0]['savename'];
			
			$this->assign("image", $url);
			$this->display("index");
		}
	}
}