<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   alan.lee
 * @Time          :   2015-5-15 11:36:06
 * $Id: VideoAdsController.php 62100 2015-11-18
 */

Doo::loadController("AppDooController");

class PolymericAdController extends AppDooController {
    private $actions = array('listUrl'=>'/PolymericAd/index');
    private $appModel;
    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->appModel = Doo::loadModel("datamodel/PolymericAd", TRUE);
    }
    
    public function index() {
    	# START 检查权限
        if (!$this->checkPermission(POLYMERICAD, POLYMERICAD_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        $pageParams['platform'] = $params['platform']?$params['platform']:1;
        
        $whereArr = array();
        if (isset($params['name']) && $params['name']) {
        	$whereArr['name'] = array('LIKE',$params['name']);
        	$pageParams['name'] = $params['name'];
        }else if(isset($params['platform']) && $params['platform']){
        	$whereArr['platform'] = $params['platform'];
        	$pageParams['platform'] = $params['platform'];
        }
             
        // 分页
        $total = $this->appModel->getRecords($whereArr);
        $url   = $this->actions['listUrl'].'?' . http_build_query($pageParams) . '&';
        $pager = $this->page($url, $total); 
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->appModel->findAll($whereArr);
        $this->data['params'] = $params;
        
        
       
        $app=Doo::loadModel("datamodel/AdApp",TRUE);
        $adsConfig = Doo::loadModel("datamodel/VideoAdsCom", TRUE);
        //获取应用信息
        foreach ($this->data['result'] as $key => $value){
        	$appInfo = $app->find(array('select' => 'app_name,appkey,platform',
        	                            'where' => 'appkey = "'.$value['app_key'].'"', 
        	                            'asArray' => TRUE));
        	$appInfo[0]['platformCn'] = $this->getPlatformCn($appInfo['0']['platform']);
        	$this->data['result'][$key]['app'] = $appInfo;
        	$adsInfo = $adsConfig->getBy(array('identifier'=>$value['name']));
        	$this->data['result'][$key]['name'] = $adsInfo['identifier'].'('.$adsInfo['name'].')';
        	
        }

        $this->myrender('PolymericAd/index', $this->data);
    }
    
    /**
     * 新增和修改广告商信息展示页面
     */
    public function edit(){
    	# START 检查权限
    	if (!$this->checkPermission(POLYMERICAD, POLYMERICAD_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	
    	$appid = $this->get["id"];
        $this->data["result"] = $this->appModel->getById($appid);
       	$this->data['title'] = '新增';
       	
       //获取广告商列表
       $adsConfig = Doo::loadModel("datamodel/VideoAdsCom", TRUE);
       $result =  $adsConfig->findAll();
       $adsList = array();
       foreach ($result as $val){
           $adsList[$val['identifier']] = $val['name'];
       }
       $this->data['adsList'] = $adsList;
       
       $appModel=Doo::loadModel("datamodel/AdApp",TRUE);
       $appInfo = $appModel->find(array('select' => 'app_id,app_name,appkey,platform,dev_id', 
                                        'where' => '1 = 1', 
                                        'asArray' => TRUE));
       $appList = array();
       foreach ($appInfo as $val){
           $appList[$val['appkey']] = $val;
       }
       $this->data['appList'] = $appList;
       
       $posModel = Doo::loadModel('Apps', TRUE);
       $this->data['result']["pos"]=$posModel->getPosinfo($appInfo[0]['dev_id'], $appInfo[0]['app_id']);
       
       if($appid){
           $this->data['title'] = '编辑';
           $positionConf = json_decode($this->data["result"]['position_conf'], true);
           foreach ($positionConf['pos_id'] as $key=>$val){
               $pos[$key] = array('id'=>$val,
                                  'dever_pos_key'=>$positionConf['pos_key'][$key],
                                  'state'=>$positionConf['status'][$key],
                                  'dever_pos_name'=>$positionConf['pos_name'][$key],
                                  'pos_key'=>$positionConf['pos_key_type'][$key],
                                  'rate'=>$positionConf['rate'][$key],
                                  'other_block_id'=>$positionConf['other_block_id'][$key],
               );
           } 
           $this->data['result']["pos"]=$pos;  
       }
    	$this->myrender('PolymericAd/edit', $this->data);
    }
    
    /**
     * 保存广告商信息
     */
    public function save(){
    	# START 检查权限
    	if (!$this->checkPermission(POLYMERICAD, POLYMERICAD_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	$post = $this->post;

    	$this->checkPostParam($post);
        $data =  $this->fillData($post);
        $id=$this->appModel->upd($post['id'], $data);//更新广告商信息
        if($post['id']){
        	$this->redirect("/PolymericAd/index","更新成功！");
        }else{
        	$this->redirect("/PolymericAd/index","添加成功！");
        }
        
    }
   
   private function fillData($post){
         $data['conf_desc'] = $post['conf_desc'];
    	 $data['name'] = $post['name'];
    	 $data['platform'] = $post['platform'];
    	 $data['app_key'] = $post['app_key'];
    	 $data['secret_key'] = $post['secret_key'];
    	 $data['third_party_appkey'] = $post['third_party_appkey'];
    	 $data['position_conf']='';
    	 foreach ($post['pos_id'] as $key =>$val){
    	     $status[$key] = $post['pos_state_'.$val][0];
    	 }
    	 $position_conf['status'] = $status;
    	 $position_conf['rate']   = $post['rate'];
    	 $position_conf['pos_name']   = $post['pos_name'];
    	 $position_conf['pos_key_type']   = $post['pos_key_type'];
    	 $position_conf['pos_key']   = $post['pos_key'];
    	 $position_conf['pos_id']   = $post['pos_id'];
    	 $position_conf['other_block_id']   = $post['other_block_id'];
    	 
    	 if(count($position_conf)){
    	     $data['position_conf'] = json_encode($position_conf);
    	 }
    	 return $data;
    }

    
    private function checkPostParam($post)
    {
        if (empty($post['name']) || empty($post['platform']) || empty($post['app_key']) || empty($post['third_party_appkey'])){
    		$this->redirect("javascript:history.go(-1)","请填写必填字段信息");
        }
        
        $params['name'] = $post['name'];
        $params['app_key'] = $post['app_key'];
        if($post['id']){
            $params['id'] = array('<>', $post['id']);
        }
        $result = $this->appModel->getBy($params);
        if($result){
            $this->redirect("javascript:history.go(-1)","此广告商对此应用已经存在！！！");
        }
    }

    
    /**
     * 软删除广告管理
     */
    public function delete(){
    	# START 检查权限
    	$get = $this->get;
    	if (!$this->checkPermission(POLYMERICAD, POLYMERICAD_EDIT)) {
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
    
    
    public function  getAdPosInfo(){
        $appkey = $this->get['appkey'];
        $appModel=Doo::loadModel("datamodel/AdApp",TRUE);
        $appInfo = $appModel->find(array('select' => 'app_id,app_name,appkey,platform,dev_id',
                                         'where' => "appkey='".$appkey."'",
                                         'asArray' => TRUE)); 
        $posModel = Doo::loadModel('Apps', TRUE);
        $adPosList = $posModel->getPosinfo($appInfo[0]['dev_id'], $appInfo[0]['app_id']);
        echo  json_encode($adPosList);   
    }
    

    
}

