<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   rock
 * @Time          :   2015-5-15 11:36:06
 * $Id: VideoAdsController.php 62100 2015-11-18
 */

Doo::loadController("AppDooController");

class IncentiveVideoAdLimitController extends AppDooController {
    private $actions = array('listUrl'=>'/IncentiveVideoAdLimit/index');
    private $videoLimitModel;
    private $mPlayNetwork = array(0=>'wifi环境下提前缓存',
                                                                    1=>'全网络环境下提前缓存'
                                                                   );
    private $mVideoPlaySet = array(2=>'支持横竖屏',
                                                                 0=>'支持横屏',
                                                                 1=>'支持竖屏' );
    
    private $mIsAlert= array(0=>'播放视频点击关闭不弹窗',
                                                    1=>'播放视频点击关闭弹窗' );

    
    public function __construct() {
        parent::__construct();
        $this->videoLimitModel = Doo::loadModel("datamodel/IncentiveVedioAdLimit", TRUE);
    }
    
    public function index() {
    	# START 检查权限
        if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $params = $this->get;
        $pageParams['platform'] = $params['platform']?$params['platform']:1;
        
        $whereArr = array();
        if (isset($params['app_key']) && $params['app_key']) {
        	$whereArr['app_key'] = array('LIKE',$params['app_key']);
        	$pageParams['app_key'] = $params['app_key'];
        }else if(isset($params['platform']) && $params['platform']){
        	$whereArr['platform'] = $params['platform'];
        	$pageParams['platform'] = $params['platform'];
        }
             
        // 分页
        $total = $this->videoLimitModel->getRecords($whereArr);
        $url   = $this->actions['listUrl'].'?' . http_build_query($pageParams) . '&';
        $pager = $this->page($url, $total); 
        $whereArr['limit'] = $pager->limit;
        $this->data['result'] = $this->videoLimitModel->findAll($whereArr);
        $this->data['params'] = $params;
        

       
        $app=Doo::loadModel("datamodel/AdApp",TRUE);
        //获取应用信息
        foreach ($this->data['result'] as $key => $value){
        	$appInfo = $app->find(array('select' => 'app_name,appkey,platform',
                                    	                            'where' => 'appkey = "'.$value['app_key'].'"', 
                                    	                            'asArray' => TRUE));
        	$appInfo[0]['platformCn'] = $this->getPlatformCn($appInfo['0']['platform']);
        	$this->data['result'][$key]['app'] = $appInfo;
        	$this->data['result'][$key]['conf']= $this->mPlayNetwork[$value['play_network']].'<br />'.$this->mVideoPlaySet[$value['video_play_set']].'<br />'.$this->mIsAlert[$value['is_alert']];
        }
        

        $this->myrender('IncentiveVideoAdLimit/index', $this->data);
    }
    

    public function edit(){
    	# START 检查权限
    	if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	
    	$id = $this->get["id"];
    	$result = $this->videoLimitModel->getById($id);
    	    $content = json_decode($result['content'],  true);
    	    if($result['is_alert'] && $content){
    	        $result['content'] = $content['content'];
    	        $result['title'] = $content['title'];
    	    }
    	
        $this->data["result"] = $result;
       	$this->data['title'] = '新增';   	 
       
       $platform = $this->get['platform']? $this->get['platform']:1;
       $appList = array();
       $appInfo = $this->getAppInfoFromDb($platform);
       foreach ($appInfo as $val){
           $appList[$val['appkey']] = $val;
       }
       $this->data['appList'] = $appList;
       
       if($id){
           $this->data['title'] = '编辑'; 
       }
       //var_dump(    $this->data["result"]);
    	$this->myrender('IncentiveVideoAdLimit/edit', $this->data);
    }
    
    public function autoPlatform(){
        $platform = $this->post['platform']? $this->post['platform']:1;
        $data = $this->getAppInfoFromDb($platform);
        $this->showData($data);      
    }
    
    
    public function getAppInfoFromDb($platform) {     
        $platform = intval($platform);
        $appModel=Doo::loadModel("datamodel/AdApp",TRUE);
        $appInfo = $appModel->find(array('select' => 'app_id,app_name,appkey,platform,dev_id',
            'where' => '1 = 1 and platform = '.$platform,
            'asArray' => TRUE));
        return $appInfo;
    }
    

    public function save(){
    	# START 检查权限
    	if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	$post = $this->post;

    	$this->checkPostParam($post);
        $data =  $this->fillData($post);
        $id=$this->videoLimitModel->upd($post['id'], $data);//更新广告商信息
        if($post['id']){
            $this->videoLimitModel->delCacheInfo($data['app_key']);
        	$this->redirect("/IncentiveVideoAdLimit/index","更新成功！");
        }else{
        	$this->redirect("/IncentiveVideoAdLimit/index","添加成功！");
        }
        
    }
   
   private function fillData($post){
         $data['app_key'] = $post['app_key'];
    	 $data['platform'] = $post['platform'];
    	 $data['play_network'] = $post['play_network'];
    	 $data['video_play_set'] = $post['video_play_set'];
    	 $data['is_alert'] = $post['is_alert'];
    	 $data['rate'] = $post['rate'];
    	 if( $data['is_alert']){
    	     $content['content'] = $post['content'];
    	     $content['title'] = $post['title'];
    	     $data['content'] = json_encode($content);
    	 }else{
    	     $data['content'] = '';
    	 }
    	 return $data;
    }

    
    private function checkPostParam($post)
    {
        if ( empty($post['app_key'])){
    		$this->redirect("javascript:history.go(-1)","请填写必填字段信息");
        }
        if ( !is_numeric($post['rate']) || intval($post['rate']) < 0 ||  intval($post['rate']) > 1 ){
            $this->redirect("javascript:history.go(-1)","概率0-1之间数字");
        }
        if($post['is_alert']){
            if(!$post['title']){
                $this->redirect("javascript:history.go(-1)","标题不能为空");
            }
            if(!$post['content']){
                $this->redirect("javascript:history.go(-1)","内容不能为空");
            }
        }
        
        $params['app_key'] = $post['app_key'];
        if($post['id']){
            $params['id'] = array('<>', $post['id']);
        }
        $result = $this->videoLimitModel->getBy($params);
        if($result){
            $this->redirect("javascript:history.go(-1)","此广告商对此应用已经存在！！！");
        }
    }


    public function delete(){
    	# START 检查权限
    	$get = $this->get;
    	if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_EDIT)) {
    		$this->displayNoPermission();
    	}
    	# END 检查权限
    	$get = $this->get;
        $result = $this->videoLimitModel->delById($get['id']);
    
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

