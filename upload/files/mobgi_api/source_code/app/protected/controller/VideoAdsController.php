<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   alan.lee
 * @Time          :   2015-5-15 11:36:06
 * $Id: VideoAdsController.php 62100 2015-11-18
 */

Doo::loadController("AppDooController");

class VideoAdsController extends AppDooController {
    
    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
    	# START 检查权限
        if (!$this->checkPermission(VIDEOADS, VIDEOADS_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        // 检测如果有查询时。拼条件。<查询所有开发者时，传过来是0>
        $whereArr = array();
        $url = "/VideoAds/index?";
        if (isset($params['name']) && $params['name']) {
        	$whereArr['name'] = $params['name'];
        	$url .= "name=".$params['name']."&";
        }else if(isset($params['platform']) && $params['platform']){
        	$whereArr['platform'] = $params['platform'];
        	$url .= "platform=".$params['platform']."&";
        }else{
        	$params['name'] = '';
        }
        
        $this->appModel = Doo::loadModel("datamodel/VideoAds", TRUE);
        
        // 分页
        $total = $this->appModel->records($whereArr);
        
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->appModel->findAll($whereArr);
        $this->data['params'] = $params;
        $app=Doo::loadModel("datamodel/AdApp",TRUE);
        //获取广告商列表
//         $videoAdsCom = $this->getVideoAdsCom();
        //获取应用信息
        foreach ($this->data['result'] as $key => $value){
        	$appInfo = $app->find(array('select' => 'app_name,appkey,platform', 'where' => 'appkey = "'.$value['app_key'].'"', 'asArray' => TRUE));
        	$appInfo[0]['platformCn'] = $this->getPlatformCn($appInfo['0']['platform']);
        	$this->data['result'][$key]['app'] = $appInfo;
	        //将广告上比例配置json格式转换为数组
	        $this->data['result'][$key]['video_ads_com_conf_arr'] = json_decode($this->data['result'][$key]['video_ads_com_conf'],true);
// 	        foreach ($this->data['result'][$key]['video_ads_com_conf_arr'] as $vack => $vacv){
// 	        	$this->data['result'][$key]['video_ads_com_conf_arr'][$videoAdsCom[$vack]] = $vacv;
// 	        	unset($this->data['result'][$key]['video_ads_com_conf_arr'][$vack]);
// 	        }
        }
//         echo "<pre>";
//         print_r($this->data);
//         die;
        $this->myrender('VideoAds/index', $this->data);
    }
    
    /**
     * 新增和修改广告商信息展示页面
     */
    public function edit(){
    	# START 检查权限
    	if (!$this->checkPermission(VIDEOADS, VIDEOADS_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	
    	$appid = $this->get["id"];
    	$this->appModel = Doo::loadModel("datamodel/VideoAds", TRUE);
        $this->data["result"] = $this->appModel->view($appid);
       	$this->data['title'] = '新增';
       	//获取广告商列表
       	$videoAdsCom = $this->getVideoAdsCom();
        if ($appid) {
        	$this->data['title'] = '编辑';
	        $app=Doo::loadModel("datamodel/AdApp",TRUE);
	        $appInfo = $app->find(array('select' => 'app_name,appkey,platform', 'where' => 'appkey = "'.$this->data['result']['app_key'].'"', 'asArray' => TRUE));
	        $this->data['result']['app_name'] = $appInfo['0']['app_name'];
        }
        //将广告上比例配置json格式转换为数组
        $this->data['result']['video_ads_com_conf_arr'] = json_decode($this->data['result']['video_ads_com_conf'],true);
		foreach ($videoAdsCom as $key => $value){
			$this->data['videoAdsCom'][$key]['name'] = $value;
			$this->data['videoAdsCom'][$key]['percent']=$this->data['result']['video_ads_com_conf_arr'][$value];
		}
    	$this->myrender('VideoAds/edit', $this->data);
    }
    
    /**
     * 保存广告商信息
     */
    public function save(){
//     	echo "<pre>";
    	# START 检查权限
    	if (!$this->checkPermission(VIDEOADS, VIDEOADS_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	$post = $this->post;
//     	print_r($post);
//     	die;
    	
    	if (empty($post['name']) || empty($post['platform']) || empty($post['appkey'])){
    		$this->redirect("javascript:history.go(-1)","请填写必填字段信息");
        }
        if (count($post['appkey']) > 1) {
        	$this->redirect("javascript:history.go(-1)","目前只支持选择单个应用");
        }
        $post['app_key'] = $post['appkey'][0];
        unset($post['appkey']);
    	
        $this->appModel = Doo::loadModel("datamodel/VideoAds", TRUE);
    	
        $id=$this->appModel->upd($post['id'], $post);//更新广告商信息
        
        if($post['id']){
        	$this->redirect("/VideoAds/index","更新成功！");
        }else{
        	$this->redirect("/VideoAds/index","添加成功！");
        }
        
    }
    
    /**
     * 软删除广告管理
     */
    public function delete(){
    	# START 检查权限
    	$get = $this->get;
    	if (!$this->checkPermission(VIDEOADS, VIDEOADS_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	$get = $this->get;
    	//逻辑校验
    	$this->appModel = Doo::loadModel("datamodel/VideoAds", TRUE);
        $result = $this->appModel->softDelApp($get['id']);
    
    	if($result){
    		$this->redirect("javascript:history.go(-1)","删除成功！");
    	}else{
    		$this->redirect("javascript:history.go(-1)","删除失败！");
    	}
    }
    
    
    /**
     * 获取应用所属平台
     * @param unknown $platform
     * @return string
     */
    public function getPlatformCn($platform)
    {
    	if($platform=='' || $platform=='0'){
    		return "(T)";
    	}else if($platform == "1"){
    		return '(A)';
    	}else if($platform == '2'){
    		return '(I)';
    	}
    }
    
    /**
     * 获取广告商列表
     */
    public function getVideoAdsCom()
    {
    	$videoAdsCom = Doo::loadModel("datamodel/VideoAdsCom", TRUE);
    	$comList = $videoAdsCom->find(array('select' => 'name,identifier', 'asArray' => TRUE));
    	$ret = array();
    	foreach ($comList as $value){
    		$ret[$value['identifier']] = $value['name'];
    	}
    	return $ret;
    }
    
}

