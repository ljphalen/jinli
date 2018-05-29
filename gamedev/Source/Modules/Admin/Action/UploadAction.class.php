<?php
class UploadAction extends SystemAction
{
	//缩略图参数配置
	protected function thumb(&$upload){}
	
	public function save()
	{
		if(!IS_POST)
			$this->success('post null');
		
		$type = $this->_get('type');
		if(!in_array($type, array('image', 'file')))
			$this->success('不允许的上传请求');
		
		import( 'ORG.Net.UploadFile' );
		$upload = new UploadFile();
		
		if($type == 'image')
		{
			$this->thumb($upload);
			$upload->allowExts = array('jpg','gif','png','jpeg');
			$upload->maxSize = 1024*1000*5;
		}else{
			$upload->allowExts = array('zip','rar','pdf','xls','xlsx','doc','docx', 'ppt', 'pptx', 'tar.gz', 'tar', '7z');
			$upload->maxSize = 1024*1000*20;
		}
		
		$upload_path = $this->upload_path_generator();
		$save_path = Helper("Apk")->get_path("doc").$upload_path;
		
		if(!is_dir($save_path) && !$this->mkdir($save_path))
		{
			Log::write("无法创建目录{$save_path}");
			$this->success("无法创建目录文件上传目录，请联系管理员");
		}
		
		$upload->savePath = $save_path;
		$upload->saveRule = 'uniqid';
		
		if(! $upload->upload()){
			$this->success($upload->getErrorMsg());
		} else {
			$info = $upload->getUploadFileInfo();
		}
		
		$FileName = $info[0]['name'];
		$url = $info[0]['savename'];
		
		$fileUrl = Helper("Apk")->get_url("doc").$upload_path.$url;
		$this->success('', $fileUrl, $FileName);
	}
	
	/**
	 * 产生文件上传的文件目录
	 * 格式：
	 * 模块名/6位年月/2位日/两位随机数/随机文件名.扩展名
	 */
	protected function upload_path_generator()
	{
		$path = sprintf("%s/", date("Ym/d") );
		return $path;
	}
	
	protected function success($err='', $msg='', $FileName='')
	{
		$FileName = preg_replace("@['\"\(\)\[\]]+@is", '_', $FileName);
		echo json_encode(array('status'=>empty($err)?1:0, 'err'=>$err, 'msg'=>empty($err)?$msg:$err, 'filename'=>$FileName));
		exit;
	}
	
	protected function mkdir($path)
	{
		if (!file_exists($path)){
			$p = dirname($path);
			$this->mkdir($p) or Log::write("无法创建目录{$p}");
			mkdir($path, 0777);
		}
		
		return is_dir($path);
	}
}