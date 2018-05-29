<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   alan.lee
 * @Time          :   2015-5-15 11:36:06
 * $Id: VideoAdsComController.php 62100 2015-11-18
 */

Doo::loadController("AppDooController");

class VideoAdsComController extends AppDooController {
    
    private $appModel = null;
    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->appModel = Doo::loadModel("datamodel/VideoAdsCom", TRUE);
    }
    
    public function index() {
    	# START 检查权限
        if (!$this->checkPermission(VIDEOADSCOM, VIDEOADSCOM_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限

        $param = array();
        $whereParams = array();
        $params = $this->get;
        if (isset($params['name']) && $params['name']) {
        	$whereParams['name'] = $params['name'];
        }
        unset($params['url']);
        
        $total = $this->appModel->records($whereParams);
        $url   = $this->actions['listUrl'].'?' . http_build_query($params) . '&';
        $pager = $this->page($url, $total);
        
        $whereParams['limit'] = $pager->limit;
        $this->data['result'] = $this->appModel->findAll($whereParams);
        $this->data['params'] = $params;
        
        $this->myrender('VideoAdsCom/index', $this->data);
    }
    
    /**
     * 新增和修改广告商信息展示页面
     */
    public function edit(){
    	# START 检查权限
    	if (!$this->checkPermission(VIDEOADSCOM, VIDEOADSCOM_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	
    	$appid = $this->get["id"];
        $this->data["result"] = $this->appModel->view($appid);
        $this->data['title'] = '新增';
        if ($appid) {
        	$this->data['title'] = '编辑';
        }
    	$this->myrender('VideoAdsCom/edit', $this->data);
    }
    
    /**
     * 保存广告商信息
     */
    public function save(){
    	# START 检查权限
    	if (!$this->checkPermission(VIDEOADSCOM, VIDEOADSCOM_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	$post = $this->post;
    	$this->checkPostParam($post);
        
        $id=$this->appModel->upd($post['id'], $post);//更新广告商信息
        if($post['id']){
        	$this->redirect("/VideoAdsCom/index","更新成功！");
        }else{
        	$this->redirect("/VideoAdsCom/index","添加成功！");
        }
        
    }
    
    private function checkPostParam($post)
    {
        if (empty($post['identifier']) || empty($post['name']) || empty($post['settlement_price'])){
            $this->redirect("javascript:history.go(-1)","请填写必填字段信息");
        }
        if(!$post['id']){
            $params = array('identifier'=>trim($post['identifier']));
            $result = $this->appModel->getBy($params);
            if($result){
                $this->redirect("javascript:history.go(-1)","此聚合广告商编号已经存在");
            }
        }
        
    }
    
    /**
     * 软删除广告管理
     */
    public function delete(){
    	# START 检查权限
    	$get = $this->get;
    	if (!$this->checkPermission(VIDEOADSCOM, VIDEOADSCOM_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	$get = $this->get;
        $result = $this->appModel->delById($get['id']);
    	if($result){
    	   $this->redirect("javascript:history.go(-1)","删除成功！");
    	}else{
    	   $this->redirect("javascript:history.go(-1)","删除失败！");
    	}
    	}
    
}

