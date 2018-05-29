<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Doo::loadController("AppDooController");

class IncentiveVideoAdConfController extends AppDooController{
    
    const  AD_CONFIG_TYPE_VIDEO = 'video';
    
    public function index(){//配置项列表
        # START 检查权限
        if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $keyword=$this->get["keyword"];
        $platform=$this->get["platform"];
        $url = "/IncentiveVideoAdConf/index?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        if (in_array($platform, array(0,1,2))){
            $url .= "platform=".$platform.'&';
        }
        $config=Doo::loadModel("AdConfigs",TRUE);
        $result = '';
        //默认列表        关键字搜索:根据配置名,应用名,渠道名,产品名搜索.
        if(empty($keyword)){
            $total = $config->getCount($keyword, $platform, self::AD_CONFIG_TYPE_VIDEO);
            $page = $this->page($url, $total);
            $limit = $page->limit;
            $result=$config->getList($keyword, $limit, $platform, self::AD_CONFIG_TYPE_VIDEO);
        } else{
            $searchResult=$config->getListBySearch($keyword,  $limit,  $platform,  self::AD_CONFIG_TYPE_VIDEO);
            $total = $searchResult['total'];
            $page = $this->page($url, $total);
            $limit = $page->limit;
            list($offset, $length) = explode(',',  $limit);
            $result = array_slice($searchResult['list'], $offset, $length);
        }
        
        
        $this->data["result"]=$result;
        $this->data["keyword"]=$keyword;
        $this->data["platform"]=$platform;
        $this->myrender("IncentiveVideoAdConf/configList",$this->data);
    }
    

    
    public function showAdConfigListPop(){
        $config=Doo::loadModel("AdConfigs",TRUE);
        $result=$config->getList();

        $this->data["result"]=$result;
        //print_r($this->data);
        $this->myRenderWithoutTemplate("adconfig/pop",$this->data);
    }
    
    public function del(){
        # START 检查权限
        if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $adid=$this->get["adid"];
        $detailid=$this->get["addetailid"];
        $type=$this->get["configtype"];
        $adconfigtag=Doo::loadModel("AdConfigTag",TRUE);
        $adconfigtag->delConfiginfoByConfigidForRedis($adid);
        $adconfigtag->delByConfigid($adid);


        $configdetails=Doo::loadModel("datamodel/AdConfigDetails",TRUE);
        $configdetails->id=$detailid;
        $configdetails->delete();

        $ad=Doo::loadModel("datamodel/AdConfig",TRUE);
        $ad->id=$adid;
        $ad->delete();
        if($type=="push"){
            $adconfig=Doo::loadModel("AdConfigs",TRUE);
            $adconfig->CreatePushConfigJson();
            $adconfig->delpushFile($adid);
        }
        $this->userLogs(array('msg' => json_encode($this->get), 'title' => '配制项列表', 'action' => 'delete'));
        $this->redirect("/IncentiveVideoAdConf/index","删除成功");
    }
    
    public function edit(){
        # START 检查权限
        if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $configid=$this->get["configid"];
        $config=Doo::loadModel("AdConfigs",TRUE);

        $result=$config->getView($configid);
        if(!empty($result["product_comb"]))
        {
            $this->get_simple_platform($result["product_comb"]);
        }
        if(!empty($result)){
            $configtag=Doo::loadModel("AdConfigTag",TRUE);
            $configtaginfo=$configtag->getValueByConfigid($configid);
            $result["configtag"]=$configtaginfo;
        }
        $conditionModel = Doo::loadModel('ConditionManages', TRUE);
        $this->data['condition_conf'] = json_encode($conditionModel->toSelect());
        $this->data["inc"]="adconfig/configDetail_inc";
        $this->data["product_pop"]=Doo::conf()->BASEURL."product/showProductlitsPop";
        $this->data["adconfig_pop"]=Doo::conf()->BASEURL."adconfig/showAdConfigListPop";
        $this->data["result"]=$result;

            $this->myrender("IncentiveVideoAdConf/configDetail",$this->data);
        
    }
    
    public function upd(){
        # START 检查权限
        if (!$this->checkPermission(ADCONFIG, ADCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        //先插adc_config_detail表得到detail_id后插入ad_config表,然后更新ad_configtag表
        if(empty($this->post) || empty($this->post["config_name"]) || empty($this->post["appkey"])){
            $this->redirect("javascript:history.go(-1)","非法操作,必要参数为空");
        }
        $config_id=empty($this->post["config_id"])?null:$this->post["config_id"];
        $config_name=$this->post["config_name"];
        $config_desc=$this->post["config_desc"];
        $appkey=$this->post["appkey"];
        $channel_id=$this->post["channel_id"];
        $config_type=$this->post["config_type"];
        $platform=$this->post["platform"];
        $product_comb=stripslashes($this->post["products"]);
        $config_level=empty($this->post["config_lelvel"])?null:$this->post["config_lelvel"];
        $config_deital=empty($this->post["config_value"])?'':stripslashes($this->post["config_value"]);
        $config_detail_id=empty($this->post["config_detail_id"])?null:$this->post["config_detail_id"];
        

        
        $configdetails=Doo::loadModel("datamodel/AdConfigDetails",TRUE);
        $configdetails->type=$config_level;
        $configdetails->type_value=$config_deital;
        $configdetails->updated=time();
        //更新ad_config_detail表
        if(empty($config_detail_id) || $config_detail_id==-1){ //如果为空则视为新增
            $configdetails->created=time();
             if(!empty($config_deital) && $config_level!=1){//如果是初级配置不增加
                 $config_detail_id=$configdetails->insert();
             }
        }else{
                $configdetails->id=$config_detail_id;
                $configdetails->update();
        }
        //更新adconfig表
        $config=Doo::loadModel("AdConfigs",TRUE);
        $configinfo=$config->upd($config_name,$config_desc,$product_comb,$config_detail_id,$config_level,$config_type,$platform,$config_id);
        if(!$configinfo){
            $this->redirect("javascript:history.go(-1)","操作失败,ad_config表错误");
        }
        if(empty($config_id)){
            $config_id=$configinfo;
        }
        //更新adconfigtag
        $configtag=Doo::loadModel("AdConfigTag",TRUE);
        $configtag->delConfigByIdFromRedis($config_id);
        if(!empty($appkey)){
            $configtag->add($config_id,$appkey,1);
        }
        if(!empty($channel_id)){
            $configtag->add($config_id,$channel_id,2);
        }
        $title = '配制项列表';
        $configtag->delConfiginfoByAppkeyChannelIdForRedis($appkey,$channel_id);
  
        //记录后台管理员操作日志
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer), $config_id);
        $url='/IncentiveVideoAdConf/index';
        $this->redirect($url,"操作成功");
    }
    
   

    
    
    public function showExchangePid(){
        # START 检查权限
        if (!$this->checkPermission(INCENTIVEVIDEOADLIMIT, INCENTIVEVIDEOADLIMIT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $config=Doo::loadModel("AdConfigs",TRUE);
        $exchangeResult=$config->getList(null, null, null, self::AD_CONFIG_TYPE_VIDEO);
        $this->data['exchange_result'] = $exchangeResult;
        $productModel = Doo::loadModel("AdProducts", TRUE);
        $product = $productModel->productlist();
        $this->data['product_list'] = $product;
        $this->myRenderWithoutTemplate("adconfig/exchangepid",$this->data);
    }
    
   
    
    //为数组新增键为platform_product_name的值.即产品名称前新增平台的简称如:
    public function get_simple_platform(&$arr)
    {
        if(empty($arr))
        {
            return;
        }
        $platform_config_arr = array(0=>'(T)', 1=>"(A)", 2=>"(I)");
        foreach($arr as $key=>$item)
        {
            $arr[$key]['simple_platform']= $platform_config_arr[$item['platform']];
        }
        return;
    }
    
    /**
     * 返回平台标识
     * @param type $platform
     * @return string
     */
    public function get_platformCn($platform){
        if(!isset($platform)){
            return false;
        }
        $platform_config_arr = array(0=>'(T)', 1=>"(A)", 2=>"(I)");
        return $platform_config_arr[$platform];
    }
    
}
?>
