<?php
/**
 * 通用上传组件
 * 
 * 1,上传文件域名字为：filedata
 * 2,返回结构如下：{"err":"","msg":"http://4wei.cn/xx/200906030521128703.gif","filename":"我上传的文件名.gif"}
 * 
 * @author yang
 *
 */
class UploadBaseAction extends Action
{
	//登录检查
	protected function _initialize()
	{
		$this->_authority();
	}
	
	//权限检查
	protected function _authority()
	{
		return false;
	}
	
	//缩略图参数配置
	protected function thumb(&$upload){}
	
	public function save()
	{
		if(!IS_POST)
			$this->success('post null');

		if(!$this->_authority())
			$this->success('您还没有登录或者您没有权限操作');;
		
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
		$save_path = Helper("Apk")->get_path("doc");
		
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
		
		$separator = (substr(cdn('ATTACH'), -1) != '/' && substr($upload_path, 1, 1) != '/') ? '/' : '';
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
		$path = sprintf("%s\//%0.4s/", date("Ym/d"), md5(uniqid(date("His"))));
		$path = preg_replace('@[/\\\\]+@is', '/', $path);
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