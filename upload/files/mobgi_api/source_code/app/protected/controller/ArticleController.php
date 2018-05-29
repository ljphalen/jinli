<?php
/**
 * Article 操作
 *
 * @author ot.tang
 */
Doo::loadController('AppDooController');
class ArticleController extends AppDooController{
	private $_ArticleModel;
	
	public function __construct()
	{
		parent::__construct();
		$this->data["rootUrl"]=Doo::conf()->MISC_BASEURL;
        $this->data['siteUrl'] = Doo::conf()->BASEURL;
        $this->data["project_title"]=Doo::conf()->PROJECT_TITLE;
        $this->data["project_version"]=Doo::conf()->PROJECT_VERSION;
        $this->data["project_offer"]=Doo::conf()->PROJECT_OFFER;
        $this->data["project_developer"]=Doo::conf()->PROJECT_DEVELOPER;
        $this->data["project_copyright"]=Doo::conf()->PROJECT_COPYRIGHT;
        $this->data['session'] = Doo::session()->__get("admininfo");	
        $this->data["inc"]="inc";
		$this->_ArticleModel = Doo::loadModel('ArticleManage',TRUE);
	}
	/*
	 * 使用之前要先将数据库切换到www
	 */
	public function beforeRun($resource, $action)
	{
   	 	parent::beforeRun($resource, $action);
    	Doo::db()->reconnect('www');        
    }
	/*
	 * 显示资讯列表
	 */
	public function index()
	{
		
		# START 检查权限 
        if (!$this->checkPermission(ARTICLE, ARTICLE_LIST))					
        {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/article/index?";
        $sqlBlock = " 1=1";
        if (isset($params['article_check']) && $params['article_check']) {
            $sqlBlock .= " AND (id = ".$params['article_check']." or title like '%".$params['article_check']."%')";
            $url .= "article_check=".$params['article_check']."&";
        }else{
            $params['article_check'] = '';
        }
        $sqlBlock .= " order by id desc";
        // 分页
        $total = $this->_ArticleModel->records($sqlBlock);
        $pager = $this->page($url, $total);
        $sqlBlock .= " LIMIT ".$pager->limit;
        $this->data['result'] = $this->_ArticleModel->findAll($sqlBlock);        
        $this->data['params'] = $params;
        // 选择模板
        $this->data["content"]="article/index";
        $this->view()->render("ledou/index", $this->data);
	}
	
	/**
     * 修改，添加资讯
     */
    public function edit() {
        # START 检查权限 
        if (!$this->checkPermission(ARTICLE, ARTICLE_EDIT)) 						
        {
            $this->displayNoPermission();
        }
        # END 检查权限
        $get = $this->get;
        $whereArr = array();
        if (isset($get['id']) && $get['id'] && is_numeric($get['id'])) 
        {
            $whereArr = array('id' => $get['id']);
            $this->data['result'] = $this->_ArticleModel->findOne($whereArr);
            $_date = $this->data['result']['pubdate'];
            $this->data['result']['pubdate'] = array(); //清空pubdate，主要清空他的int格式。
            $this->data['result']['pubdate']['date'] = date("Y-m-d",$_date);
            $this->data['result']['pubdate']['hour'] = date("H",$_date);
            $this->data['result']['pubdate']['min'] = date("i",$_date);          
            $this->data['title'] = '修改';
        }else{
            $this->data['result'] = array(
               'id' => '', 'title' => '', 'pubdate' =>array('date'=>'','hour'=>'','min'=>''), 'w_from' => '', 'content' => '','icon'=>'nopic.jpg'
            );
            $this->data['title'] = '添加';
        }
        
        // 选择模板
        $this->data["content"]="article/detail";
        $this->data['upload_path'] = $this->data['result']['icon'];
        $this->myrender("article/detail", $this->data);
    }
    
    /*
     * 保存资讯
     */
	public function save () {
        # START 检查权限 
        if (!$this->checkPermission(ARTICLE, ARTICLE_EDIT)) {   
            $this->displayNoPermission();
        }
        # END 检查权限
        $post = $this->post;
        $post["content"]=$post["editorValue"];
        if (!$post['title']){
            $this->redirect("javascript:history.go(-1)","请填写标题");
        }
	if (!$post['content']){
            $this->redirect("javascript:history.go(-1)","请填写内容");
        }
	 	
        Doo::loadCore('helper/DooFile');
        
        if(!$post['pubdate']['date'])
        {
        	$post['pubdate'] = '';
        }else {
        	$_date = $post['pubdate']['date'];
        	if(!$post['pubdate']['hour'])
        	{
        		$_hour = 0;
        	}else{
        		$_hour = $post['pubdate']['hour'];
        	}
        	if(!$post['pubdate']['min'])
        	{
        		$_min = 0;
        	}else{
        		$_min = $post['pubdate']['min'];
        	}
        	$post['pubdate'] = strtotime("$_date $_hour:$_min:00");


        }
        
        if (!$post['id']){//插入新的记录.一定要上传图片
        	 
        	$dooUpload = new DooFile();
        	if (!$dooUpload->checkFileExtension('icon',array('png', 'gif', 'jpeg','jpg'))){
        		$this->redirect("javascript:history.go(-1)","上传文件类型必须为jpeg,gif,png,jpg图片中一种");
        	}
        	$_filename = time().rand(2,100);
        	if(empty($_FILES['icon']))
        	{
        		$this->redirect("javascript:history.go(-1)","请上传图片");
        	}
        	set_time_limit(40);
        	$post['icon'] = doo::conf()->MISC_BASEURL."/upload/".$dooUpload->upload(UPLOAD_PATH,'icon', $_filename);
        	
        	$insertId = $this->_ArticleModel->upd($post);
        }else{
        	if($_FILES['icon']['size'] > 0)
        	{
        		$dooUpload = new DooFile();
        		if (!$dooUpload->checkFileExtension('icon',array('png', 'gif', 'jpeg','jpg'))){
        			$this->redirect("javascript:history.go(-1)","编辑文件类型必须为jpeg,gif,png,jpg图片中一种");
        		}
        		$_filename = time().rand(2,100);
        		if(empty($_FILES['icon']))
        		{
        			$this->redirect("javascript:history.go(-1)","请上传图片");
        		}
        		$post['icon'] = doo::conf()->MISC_BASEURL."/upload/".$dooUpload->upload(UPLOAD_PATH,'icon', $_filename);
        	}
        	$insertId = $this->_ArticleModel->upd($post, $post['id']);
        }
        $this->redirect('../article/edit?id='.$insertId);
        
        
    }
    
    /*
     * 删除资讯
     */
	public function delete() {
        # START 检查权限 
        if (!$this->checkPermission(ARTICLE, ARTICLE_DEL)) {   
            $this->displayNoPermission();
        }
        # END 检查权限
        $id = $this->get['id'];
        if ($id)
        {
        	$where = array('id'=>$id);
            $exist = $this->_ArticleModel->findOne($where);
            if($exist)
            {
            $this->_ArticleModel->del($where,$exist);
            }else {
            $this->redirect("javascript:history.go(-1)","不存在此记录");
            }
        }
        $this->redirect('../article/index');
    }
	
 
}