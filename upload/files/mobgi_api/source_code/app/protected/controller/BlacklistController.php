<?php
/**
 *黑名单 操作
 *
 * @author ot.tang
 */
Doo::loadController('AppDooController');
class BlacklistController extends AppDooController{
	private $_BlackAppModel;
	private $_BlackUserModel;
	private $_LogModel;
	private $_ConfigModel;
	
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
        $this->_ConfigModel = Doo::loadModel("ConfigManage",TRUE);
        $this->_LogModel = Doo::loadModel("LogManage",TRUE);   
        $this->_BlackAppModel = Doo::loadModel("BlackappManage",TRUE);
        $this->_BlackUserModel = Doo::loadModel("BlackuserManage",TRUE);     
	}
	/*
	 * 使用之前要先将数据库切换到cheat
	 */
	public function beforeRun($resource, $action)
	{
   	 	parent::beforeRun($resource, $action);
   	 	Doo::db()->reconnect('cheat');      	
    }
    /*
     * 
     */
    public function config()
    {
    	if (!$this->checkPermission(CHEAT, CHEAT_CONFIG))		/////////////////////////////////////////////////////////////////////////////////////			
        {
            $this->displayNoPermission();
        }
        
    	$where['id'] = 1;
    	$result = $this->_ConfigModel->findOne($where);
    	if($result)
    		$this->data["result"] = $result;
        $this->data["content"]="blacklist/config";
        $this->view()->render("ledou/index", $this->data);
    }
    /*
     * log显示
     */
	public function showlog()
	{
		
		# START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_LOG))		/////////////////////////////////////////////////////////////////////////////////////			
        {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/blacklist/log?";
        $sqlBlock = " 1=1";
        if (isset($params['check']) && $params['check']) {
            $sqlBlock .= " AND (uuid = '".$params['check']."' OR udid = '".$params['check']."' OR appkey = '".$params['check']."')";
            $url .= "check=".$params['check']."&";
        }else{
            $params['check'] = '';
        }
        // 分页
        $total = $this->_LogModel->records($sqlBlock);
        $pager = $this->page($url, $total);
        $sqlBlock .= " LIMIT ".$pager->limit;
        $this->data['result'] = $this->_LogModel->findAll($sqlBlock);        
        $this->data['params'] = $params;
        // 选择模板
        $this->data["content"]="blacklist/log";
        $this->view()->render("ledou/index", $this->data);
	}
	/*
	 * 显示App黑名单
	 */
	public function blackapp()
	{
		
		# START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_APP))		/////////////////////////////////////////////////////////////////////////////////////			
        {
            $this->displayNoPermission();
        }
        # END 检查权限
       
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/blacklist/blackapp?";
        $sqlBlock = " 1=1";
        if (isset($params['check']) && $params['check']) {
            $sqlBlock .= " AND appkey like '". $params['check']."%'";
            $url .= "check=".$params['check']."&";
        }else{
            $params['check'] = '';
        }
		if (isset($params['data_from']) && $params['data_from']) {
            $url .= "data_from=".$params['data_from']."&";
        }else{
            $params['data_from'] = 'db';
        }
        // 分页
		if($params['data_from'] == "db")
		{        
        	$total = $this->_BlackAppModel->records($sqlBlock);
        	$pager = $this->page($url, $total);
        	$sqlBlock .= " LIMIT ".$pager->limit;
        	$this->data['result'] = $this->_BlackAppModel->findAll($sqlBlock); 
		}
		else 
		{
			$total = 1;
        	$pager = $this->page($url, $total);        	
			if (isset($params['check']) && $params['check']) {
        		$check = $params['check'];
        		$this->data['result'] = $this->_BlackAppModel->find_inredis($check);
        	}
        	else
        	{
        		$this->data['result'] = $this->_BlackAppModel->find_inredis();
        	}
		}       
        $this->data['params'] = $params;
        // 选择模板
        $this->data["content"]="blacklist/app";
        $this->view()->render("ledou/index", $this->data);
	}
	/*
	 * 显示用户黑名单
	 */
	public function blackuser()
	{
		
		# START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_USER))		///////////////////////////////////////////////////			
        {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/blacklist/blackuser?";
        $sqlBlock = " 1=1";
        if (isset($params['check']) && $params['check']) {
            $sqlBlock .= " AND (uuid like '".$params['check']."%' OR udid like '".$params['check']."%')";
            $url .= "check=".$params['check']."&";
        }else{
            $params['check'] = '';
        }
		if (isset($params['data_from']) && $params['data_from']) {
            $url .= "data_from=".$params['data_from']."&";
        }else{
            $params['data_from'] = 'db';
        }
        // 分页
        if($params['data_from'] == "db")
		{  
	        $total = $this->_BlackUserModel->records($sqlBlock);
	        $pager = $this->page($url, $total);
	        $sqlBlock .= " LIMIT ".$pager->limit;
	        $this->data['result'] = $this->_BlackUserModel->findAll($sqlBlock);
		}    
		else 
		{
			$total = 1;
        	$pager = $this->page($url, $total);
        	if (isset($params['check']) && $params['check']) {
        		$check = $params['check'];
        		$this->data['result'] = $this->_BlackUserModel->find_inredis($check);
        	}
        	else 
        	{
        		$this->data['result'] = $this->_BlackUserModel->find_inredis();
        	}
		}        
        $this->data['params'] = $params;
        // 选择模板
        $this->data["content"]="blacklist/user";
        $this->view()->render("ledou/index", $this->data);
	}
    /*
     * 删除黑名单app
     */
	public function deleteapp() {
        # START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_APP)) {   
            $this->displayNoPermission();
        }
        # END 检查权限
        $APPKEY=$this->get['APPKEY'];
        if(!isset($_GET['data_from']))
        {
        	$_GET['data_from'] = "db";
        }
		if($_GET['data_from'] == "db")
		{
	        $id = $this->get['id'];
	        if ($id)
	        {
	        	$where = array('id'=>$id);
	            $exist = $this->_BlackAppModel->findOne($where);
	            if($exist)
	            {
	            $this->_BlackAppModel->del($where,$exist);
	            $this->_BlackAppModel->del_fromredis($APPKEY);
	            }else {
	            $this->redirect("javascript:history.go(-1)","不存在此记录");
	            }
	        }
		}
		else 
		{
			$this->_BlackAppModel->del_fromredis($APPKEY);
		}
        $this->redirect("../blacklist/blackapp?data_from={$_GET['data_from']}","删除成功");
    }
    /*
     * 清空app  redis缓存
     */
    public function flushapp()
    {
    	# START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_APP)) {   
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->_BlackAppModel->delall_fromredis();
        $this->redirect("../blacklist/blackapp","清空成功");
    }
    
    /*
     * 删除黑名单用户
     */
	public function deleteuser() {
        # START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_USER)) {   ///////////////////////////////////////////////
            $this->displayNoPermission();
        }
        # END 检查权限
		$UUID = $this->get['uuid'];
		if(!isset($_GET['data_from']))
        {
        	$_GET['data_from'] = "db";
        }
        if($_GET['data_from'] == "db")
        {
	        $id = $this->get['id'];
	        if ($id)
	        {
	        	$where = array('id'=>$id);
	            $exist = $this->_BlackUserModel->findOne($where);
	            if($exist)
	            {
	            $this->_BlackUserModel->del($where,$exist);
	            $this->_BlackUserModel->del_fromredis($UUID);
	            }else {
	            $this->redirect("javascript:history.go(-1)","不存在此记录");
	            }
	        }
        }
        else 
        {
        	$this->_BlackUserModel->del_fromredis($UUID);
        }
        $this->redirect("../blacklist/blackuser?data_from={$_GET['data_from']}","删除成功");
    }
    /*
     * 清空user  redis缓存
     */
    public function flushuser()
    {
    	# START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_APP)) {   
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->_BlackUserModel->delall_fromredis();
        $this->redirect("../blacklist/blackuser","清空成功");
    }
    /*
     * 保存配置项
     */
    public function saveconfig()
    {
    	# START 检查权限 
        if (!$this->checkPermission(CHEAT,CHEAT_CONFIG)) {   ///////////////////////////////////////////////
            $this->displayNoPermission();
        }
        # END 检查权限
    	$params = $this->post;

    	$this->_ConfigModel->upconfig($params);
    	$this->redirect('../blacklist/config',"修改成功");
    	
    }
    
    /*
     * 增加app黑名单
     */
    public function addapp()
    {
    	# START 检查权限 
    	if (!$this->checkPermission(CHEAT,CHEAT_APP)) {   ///////////////////////////////////////////////
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->post;
        if(isset($params['appkey']) && $params['appkey'])
        {
        	$this->_BlackAppModel->add($params['appkey']);
        	$this->redirect('../blacklist/blackapp',"添加成功");
        }
        else
        {
        	$this->redirect("javascript:history.go(-1)","没有appkey");
        }
    }
    /*
     * 增加用户黑名单
     */
    public function adduser()
    {
    	# START 检查权限 
    	if (!$this->checkPermission(CHEAT,CHEAT_USER)) {   ///////////////////////////////////////////////
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->post;
        if(isset($params['add_name']) && isset($params['add_value']))
        {
        	if($params['add_name'] == 'uuid' || $params['add_name'] == 'udid')
        	{
        		$this->_BlackUserModel->add($params['add_name'],$params['add_value']);
        		$this->redirect('../blacklist/blackuser',"添加成功");
        	}
        	else 
        	{
        		$this->redirect("javascript:history.go(-1)","不合法选项");
        	}
        }
        else
        {
        	$this->redirect("javascript:history.go(-1)","缺乏参数");
        }
    }
}