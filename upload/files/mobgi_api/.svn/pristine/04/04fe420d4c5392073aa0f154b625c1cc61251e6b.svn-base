<?php
Doo::loadController("AppDooController");

class ApkManageController extends AppDooController{

	private $_ImgManageModel;
	public function __construct()
	{
		$this->data["rootUrl"]=Doo::conf()->MISC_BASEURL;
        $this->data['siteUrl'] = Doo::conf()->BASEURL;
        $this->data["project_title"]=Doo::conf()->PROJECT_TITLE;
        $this->data["project_version"]=Doo::conf()->PROJECT_VERSION;
        $this->data["project_offer"]=Doo::conf()->PROJECT_OFFER;
        $this->data["project_developer"]=Doo::conf()->PROJECT_DEVELOPER;
        $this->data["project_copyright"]=Doo::conf()->PROJECT_COPYRIGHT;
        $this->data['session'] = Doo::session()->__get("admininfo");
        $this->data["inc"]="inc";//默认css,js文件内容
		//parent::__construct();
		$this->_ImgManageModel = Doo::loadModel('ImgManages',TRUE);
	}
	/*
	 * 使用之前要先将数据库切换到dev
	 */
	public function beforeRun($resource, $action)
	{
		
		parent::beforeRun($resource, $action);
		Doo::db()->reconnect('admin');
	}
	
	public function afterRun($routeResult)
	{
		
	}

	public function index()
	{
		# START 检查权限

		$this->data['category'] = $category;
		$this->data["content"] = "apkmanage/index";
		$this->view()->render("ledou/index", $this->data);
	}
	
	public function savefile()
	{
		$result = array("success"=>false,"msg"=>"");
		Doo::loadCore('helper/DooFile');
		$dooUpload = new DooFile();
		if (!$dooUpload->checkFileExtension('Filedata',array('apk')))
		{
				$result = array("success"=>false,"msg"=>"只支持APK文件");;
		}
		else 
		{
			$_filename = time().rand(2,100);
			$post['url'] = $dooUpload->upload(UPLOAD_PATH."apkfile/",'Filedata', $_filename);
			$result = array("success"=>true,"msg"=>$post['url']);
		}
		echo json_encode($result);
	}
}