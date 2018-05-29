<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Doo::loadController("AppDooController");

class AdConfigController extends AppDooController{
    public function lists(){//配置项列表
        # START 检查权限
        if (!$this->checkPermission(ADCONFIG, ADCONFIG_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $keyword=$this->get["keyword"];
        $platform=$this->get["platform"];
        $url = "/adconfig/lists?";
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        if (in_array($platform, array(0,1,2))){
            $url .= "platform=".$platform.'&';
        }
        $config=Doo::loadModel("AdConfigs",TRUE);
        $result = '';
        //默认列表
        if(empty($keyword)){
            $total = $config->getCount($keyword, $platform);
            $page = $this->page($url, $total);
            $limit = $page->limit;
            $result=$config->getList($keyword, $limit, $platform);
        }
        //关键字搜索:根据配置名,应用名,渠道名,产品名搜索.
        else{
            $searchResult=$config->getListBySearch($keyword, $limit, $platform);
            $total = $searchResult['total'];
            $page = $this->page($url, $total);
            $limit = $page->limit;
            list($offset, $length) = explode(',', $limit);
            $result = array_slice($searchResult['list'], $offset, $length);
        }
        
        
        $this->data["result"]=$result;
        $this->data["keyword"]=$keyword;
        $this->data["platform"]=$platform;
        $this->myrender("adconfig/configList",$this->data);
    }
    public function listconfig(){//列表页广告
        # START 检查权限
        if (!$this->checkPermission(ADDCONFIG, ADDCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["inc"]="adconfig/configDetail_inc";
        $this->data["product_pop"]=Doo::conf()->BASEURL."product/showProductlitsPop";
        $this->data["adconfig_pop"]=Doo::conf()->BASEURL."adconfig/showAdConfigListPop";
        $conditionModel = Doo::loadModel('ConditionManages', TRUE);
        $this->data['condition_conf'] = json_encode($conditionModel->toSelect());
        $this->myrender("adconfig/configDetailListAdd",$this->data);
    }
    public function pushconfig(){//PUSH广告
        # START 检查权限
        if (!$this->checkPermission(ADDCONFIG, ADDCONFIG_PUSH)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["inc"]="adconfig/configDetail_inc";
        $this->data["product_pop"]=Doo::conf()->BASEURL."product/showProductlitsPop";
        $this->data["adconfig_pop"]=Doo::conf()->BASEURL."adconfig/showAdConfigListPop";
        $conditionModel = Doo::loadModel('ConditionManages', TRUE);
        $this->data['condition_conf'] = json_encode($conditionModel->toSelect());
        $this->myrender("adconfig/configDetailPush",$this->data);
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
        if (!$this->checkPermission(ADCONFIG, ADCONFIG_DEL)) {
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
        $this->redirect("/adconfig/lists","删除成功");
    }
    public function edit(){
        # START 检查权限
        if (!$this->checkPermission(ADCONFIG, ADCONFIG_EDIT)) {
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
//var_dump($this->get["configlevel"]);die;
        if($this->get["configlevel"]==-1){
            $this->myrender("adconfig/configDetailListEdit",$this->data);
        }elseif($this->get["configtype"]=="push"){
            $this->myrender("adconfig/configDetailPush",$this->data);
        }else{
            $this->myrender("adconfig/configDetail",$this->data);
        }
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
        
        if($this->post['config_type'] == "list" || $this->post['config_type'] == "push"){
            $products = json_decode($product_comb, true);
            //新增/编辑列表配置项时，需校验所选的产品是否都有配置ICON和（至少一个）文案素材
            $productModel=Doo::loadModel("datamodel/AdProductInfo",TRUE);
            foreach($products['products'] as $product){
                $productid = $product['productid'];
                //配置列表及PUSH配置:产品必须存在ICON素材
                $existIcon=$productModel->getOne(array("select"=>"*","where"=>" id=$productid ","asArray"=>true));
                if(!$existIcon){
                    $prodcutmodel = Doo::loadModel("AdProducts", TRUE);
                    $productinfo = $prodcutmodel->view($productid);
                    $this->redirect("javascript:history.go(-1)",$this->get_platformCn($productinfo['platform']).$productinfo['product_name']." 产品缺少ICON素材，请先配置素材！");
                }
                //列表配置:产品必须存在文案素材
                if($this->post['config_type'] == "list"){
                    $adsInfoModel = Doo::loadModel("datamodel/AdInfo", TRUE);
                    $existText = $adsInfoModel->getAdTextByProductid($productid);
                    //更新hot,new属性
                    $sql="update mobgi_ads.ads_product_info set promote_type='".$this->post["promote_type_".$productid]."' where id='".$productid."'";
                    Doo::db()->query($sql)->execute();
                    $sql="update mobgi_api.ad_product_info set promote_type='".$this->post["promote_type_".$productid]."' where id='".$productid."'";
                    Doo::db()->query($sql)->execute();
                    if(!$existText){
                        $prodcutmodel = Doo::loadModel("AdProducts", TRUE);
                        $productinfo = $prodcutmodel->view($productid);
                        $this->redirect("javascript:history.go(-1)",$this->get_platformCn($productinfo['platform']).$productinfo['product_name']." 产品缺少文案素材，请先配置素材！");
                    }
                }
                //PUSH配置:产品必须存在文案素材
                if($this->post['config_type'] == "push"){
                    $ad = Doo::loadModel("datamodel/AdInfo", TRUE);
                    $existPushAds=$ad->getPushAdsInfoByProductid($productid);
                    if(!$existPushAds){
                        $prodcutmodel = Doo::loadModel("AdProducts", TRUE);
                        $productinfo = $prodcutmodel->view($productid);
                        $this->redirect("javascript:history.go(-1)",$this->get_platformCn($productinfo['platform']).$productinfo['product_name']." 产品缺少PUSH文案素材，请先配置素材！");
                    }
                }
                
            }
        }
        
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
        if($config_type=="push"){
            $title = '新增PUSH广告配制项';
            $config->CreatePushConfigJson();
        }
        if ($config_type == "list"){
            $title = '新增列表页广告配置项';
        }
        //记录后台管理员操作日志
        $referer = $_SERVER['HTTP_REFERER'];
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer), $config_id);
        $url=empty($_SERVER[HTTP_REFERER])?"/adconfig/lists":$_SERVER[HTTP_REFERER];
        if($config_type == 'ad'){
            $url = "/adconfig/lists";
        }
        $this->redirect($url,"操作成功");
    }
    //更新到其他的配置项
    public function import_config(){
        $product_json=stripslashes($this->get["prodcutjson"]);
        $config_id=$this->get["config_id"];
        $config_level=$this->get["config_level"];
        $config_type=$this->get["config_type"];
        if(empty($config_id)){
            $this->showMsg("更新失败,没有选择任何产品");
        }
        $config_id=  explode(",",$config_id);
        $config_level=  explode(",",$config_level);
        $config_type=  explode(",",$config_type);
        global $config;
        foreach($config_id as $key=>$id){
            $adconfig=Doo::loadModel("datamodel/AdConfig",TRUE);
            $adconfig->id=$id;
            $adconfig->product_comb=$product_json;
            $adconfig->update();
            //快照,由于是更新到其它应用,所以需要拼出地址.
            $updateUrl = $config['APP_URL'].'adconfig/edit?configid='.$id.'&configlevel='.$config_level[$key].'&configtype='.$config_type[$key];
            $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
            $type = $file_pre;
            $snapsot_url = save_referer_page($updateUrl, $file_pre);
            $this->userLogs(array('msg' => $product_json, 'title' => '配制项列表-广告产品-更新到其它配置','type'=>  $type, 'snapshot_url'=> $snapsot_url,'update_url'=>$updateUrl), $config_id);
        }
        
        $this->showMsg("更新成功",0);
    }
    public function updForpush(){
        $config=Doo::loadModel("AdConfigs",TRUE);
        $appkey=$this->post["appkey"];
        $config_level=empty($this->post["config_lelvel"])?null:$this->post["config_lelvel"];
        if($config_level!=1){
            $config_deital=empty($this->post["config_value"])?'':stripslashes($this->post["config_value"]);
        }else{
            $config_deital="";
        }

        $channel_id=$this->post["channel_id"];
        if(empty($appkey)||empty($channel_id)){
            $this->redirect("javascript:history.go(-1)","APPKEY或渠道ID不能为空");
        }
        if(!$config->CreateAppkeyChannelConfigContents($appkey,$channel_id,$config_deital)){
            $this->redirect("javascript:history.go(-1)","生成数据失败!");
        }
        $configtag=Doo::loadModel("AdConfigTag",TRUE);
        if(isset($this->post["config_id"]) && !empty($this->post["config_id"])){//如果是更新的话
            $appkeyExsit=$configtag->getAppkeyByConfigid($this->post["config_id"]);
            if($appkeyExsit){
                foreach($appkey as $key){
                        if(!in_array($key,$appkeyExsit)){//如果新增的key的话
                            $exsitkey=$configtag->checkPushKeyUsed($key);
                            if($exsitkey){
                                $this->redirect("javascript:history.go(-1)","KEY($exsitkey)已经被别的PUSH配置项引用,请选择别的KEY代替");
                            }
                        }
                    }
                }
                $diffkey=array_diff($appkeyExsit,$appkey);
                $dir=Doo::conf()->PUSH_JSON_PATH;
                foreach($diffkey as $dkey){
                    if(is_dir($dir.$dkey)){
                        system("rm -rf ".$dir.$dkey);
                    }
                }

        }else{
            $exsitkey=$configtag->checkPushKeyUsed($appkey);
            if($exsitkey){
                $this->redirect("javascript:history.go(-1)","KEY($exsitkey)已经被别的PUSH配置项引用,请选择别的KEY代替");
            }
        }
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => '新增PUSH广告配置项'), $appkey);
        $this->upd();
    }
    public function getConfigDetail(){
        $configid=$this->get["configid"];
        if(empty($configid)){
            $this->showMsg("配置文件ID不能为空");
        }
        $config=Doo::loadModel("datamodel/AdConfig",TRUE);
        $result=$config->getOne(array("select"=>"config_detail_id","where"=>"id='$configid' and type!='list'","asArray"=>true));
        if(empty($result)){
            $this->showMsg("导入失败,该配置不存在");
        }

        $configDetail=Doo::loadModel("datamodel/AdConfigDetails",TRUE);
        $resultDetail=$configDetail->getOne(array("select"=>"type,type_value","where"=>"id='".$result["config_detail_id"]."'","asArray"=>true));
        if(empty($resultDetail) || empty($resultDetail["type_value"])){
            $this->showMsg("该配置没有配置过滤条件信息");
        }
        $this->showMsg(array("configLevel"=>$resultDetail["type"],"configDetail"=>$resultDetail["type_value"]),0);
    }
    public function getChannelinfo(){
        $channel_id=$this->get["channel_id"];
        if(empty($channel_id)){
            $this->showMsg("channel_id is empty");
        }
        $channel=Doo::loadModel("AdConfigs",TRUE);
        $channel_info=$channel->getChannelInfoByChannelid($channel_id);
        $this->showMsg($channel_info,0);
    }
    
    public function showExchangePid(){
        # START 检查权限
        if (!$this->checkPermission(ADCONFIG, ADCONFIG_LIST)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $config=Doo::loadModel("AdConfigs",TRUE);
        $exchangeResult=$config->getList();
        $this->data['exchange_result'] = $exchangeResult;
        $productModel = Doo::loadModel("AdProducts", TRUE);
        $product = $productModel->productlist();
        $this->data['product_list'] = $product;
        $this->myRenderWithoutTemplate("adconfig/exchangepid",$this->data);
    }
    
    public function exchange(){
        $post = $_POST;
        if (empty($post['after']) || empty($post['before']) || empty($post['configId'])) {
            echo json_encode(array('errMsg' => '请选择产品', 'retCode' => -1));
        }
        $configId = trim($post['configId'], ",");
        if (empty($configId)) {
            echo json_encode(array('errMsg' => '请选择配置项', 'retCode' => -1));
        }
        $config=Doo::loadModel("AdConfigs",TRUE);
        $configInfo = $config->findAll($configId);
        if (empty($configInfo)) {
            echo json_encode(array('errMsg' => '没有配置项', 'retCode' => -1));
        }
        foreach ($configInfo as $value) {
            $product_comb = str_replace('"productid":"'.$post['before'].'"', '"productid":"'.$post['after'].'"', $value['product_comb']);
            $config->exchangepid($product_comb, $value['id']);
        }
        echo json_encode(array('errMsg' => '替换成功', 'retCode' => 1));
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
