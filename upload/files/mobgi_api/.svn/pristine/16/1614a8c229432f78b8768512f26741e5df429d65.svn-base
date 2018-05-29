<?php
Doo::loadController("AppDooController");

class ImgManageController extends AppDooController{

	private $_ImgManageModel;
	public function __construct()
	{
		parent::__construct();
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

	public function index()
	{
		# START 检查权限
		if (!$this->checkPermission(DEVELOP, DEVELOP_LIST)) {
			$this->displayNoPermission();
		}
		# END 检查权限

		// 选择模板
		$get = $_GET;
		if (isset($get['id']) && $get['id'] && is_numeric($get['id'])) 
        {
            $whereArr = array('id' => $get['id']);
            $this->data['result'] = $this->_ImgManageModel->findOne($whereArr);
        }else{
            $this->data['result'] = array(
               'id' => '', 'name' => '', 'category' =>'', 'url' => '', 'content' => ''
            );
        }
		$category = array("imgmanage_a"=>"横屏","imgmanage_b"=>"竖屏","imgmanage_c"=>"横图","imgmanage_d"=>"icon");
		$this->data['category'] = $category;
		$this->data["content"] = "imgmanage/index";
		$this->view()->render("ledou/index", $this->data);
	}

	public function lists()
	{
		# START 检查权限
		if (!$this->checkPermission(DEVELOP, DEVELOP_LIST)) {
			$this->displayNoPermission();
		}
		# END 检查权限

		// 选择模板
		$this->data['params']['check_name'] = "";
		$this->data['params']['category'] = "0";
		$this->data['i'] = 0;
		$category = array("imgmanage_a"=>"横屏","imgmanage_b"=>"竖屏","imgmanage_c"=>"横图","imgmanage_d"=>"icon");
		$sqlBlock = " 1=1";
		// 分页
		$url = "/imgManage/lists?";
		$get = $this->get;
		if(isset($get['category']) && ($get['category'] != '0') && $get['category'] != '')
		{
			$this->data['params']['category'] = $get['category'];
			$sqlBlock .= ' and category='."'".$get['category']."'";
		}
		if(isset($get['check_name']) && $get['check_name'] != '')
		{
			$this->data['params']['check_name'] = $get['check_name'];
			$sqlBlock .= ' and name like'."'".$get['check_name']."%'";
		}
		$sqlBlock .= " order by id desc";
        $total = $this->_ImgManageModel->records($sqlBlock);
        $pager = $this->page($url, $total,40);
        $sqlBlock .= " LIMIT ".$pager->limit;
        $this->data['img'] = $this->_ImgManageModel->findAll($sqlBlock);
		$this->data['category'] = $category;
		$this->data["content"] = "imgmanage/list";
		$this->view()->render("ledou/index", $this->data);
	}

	public function slists()
	{
		$this->data['params']['check_name'] = "";
		$this->data['params']['category'] = "0";
		// 选择模板
		$this->data['i'] = 0;
		$category = array("imgmanage_a"=>"横屏","imgmanage_b"=>"竖屏","imgmanage_c"=>"横图","imgmanage_d"=>"icon");
		$sqlBlock = " 1=1";
		// 分页
		$get = $this->get;
		$url = "/imgManage/slists?";
		if(isset($get['category']) && ($get['category'] != '0') && $get['category'] != '')
		{
			$this->data['params']['category'] = $get['category'];
			$sqlBlock .= ' and category='."'".$get['category']."'";
		}
		if(isset($get['check_name']) && $get['check_name'] != '')
		{
			$this->data['params']['check_name'] = $get['check_name'];
			$sqlBlock .= ' and name like'."'".$get['check_name']."%'";
		}
		$sqlBlock .= " order by id desc ";
        $total = $this->_ImgManageModel->records($sqlBlock);
        $pager = $this->page($url, $total,16);
        $sqlBlock .= " LIMIT ".$pager->limit;
        $this->data['img'] = $this->_ImgManageModel->findAll($sqlBlock);
		$this->data['category'] = $category;
		$this->data["content"] = "imgmanage/list";
		$this->data['inc'] = "../inc";
		$this->data['input_id'] = $_GET['name'];
		$this->view()->render("ledou/imgmanage/slist", $this->data);
		exit;
	}
	
	public function confirm()
	{
		# START 检查权限
		if (!$this->checkPermission(DEVELOP, DEVELOP_LIST)) {
			$this->displayNoPermission();
		}
		# END 检查权限
		$post = $_POST;
		Doo::loadCore('helper/DooFile');
		if (!$post['id'] || $post['id'] ==''){

			$dooUpload = new DooFile();
			if (!$dooUpload->checkFileExtension('url',array('png', 'gif', 'jpeg','jpg'))){
				$this->redirect("javascript:history.go(-1)","上传文件类型必须为jpeg,gif,png,jpg图片中一种");
			}
			$_filename = time().rand(2,100);
			$post['url'] = $dooUpload->upload(UPLOAD_PATH.$post['category']."/",'url', $_filename);
			
			$_sourcepath = UPLOAD_PATH.$post['category']."/".$post['url'];
			$_distdir = UPLOAD_PATH."../img_bre/";
			$this->bre_img($_sourcepath, $_distdir, 150, 80);
			$post['bre_url'] = Doo::conf()->MISC_BASEURL."/img_bre/".$post['url'];
			if(!$post['url']){
				$this->redirect("javascript:history.go(-1)","请上传图片");
			}
			$post['url'] = $this->viewUploadFile($post['category']."/".$post['url']);
			$insertId = $this->_ImgManageModel->upd($post);
		}else{
			if($_FILES['url']['size'] > 0)
			{
				$dooUpload = new DooFile();
				if (!$dooUpload->checkFileExtension('url',array('png', 'gif', 'jpeg','jpg'))){
					$this->redirect("javascript:history.go(-1)","编辑文件类型必须为jpeg,gif,png,jpg图片中一种");
				}
				$_filename = time().rand(2,100);
				$post['url'] = $dooUpload->upload(UPLOAD_PATH.$post['category']."/",'url', $_filename);
				$_sourcepath = UPLOAD_PATH.$post['category']."/".$post['url'];
				$_distdir = UPLOAD_PATH."../img_bre/";
				$this->bre_img($_sourcepath, $_distdir, 150, 80);		
				$post['bre_url'] = Doo::conf()->MISC_BASEURL."/img_bre/".$post['url'];
				if(!$post['url']){
					$this->redirect("javascript:history.go(-1)","上传图片失败");
				}
			}
			$post['url'] = $this->viewUploadFile($post['category']."/".$post['url']);
			$insertId = $this->_ImgManageModel->upd($post, $post['id']);
		}
		
		$this->redirect('../imgManage/lists?category=0&check_name='.$post['name']);
	}
	
	public function progress()
	{
		if(isset($_GET['progress_key'])) {
			$name = 'upload_'.$_GET['progress_key'];
			$status = apc_fetch($name);
			if($status['total'] == 0)
			{
				$temp = 0;
			}
			else
			{
				$temp = $status['current']/$status['total']*100;
			}
			echo ceil($temp);
		}
	}
}