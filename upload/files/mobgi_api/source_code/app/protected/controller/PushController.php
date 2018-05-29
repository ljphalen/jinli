<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2014-12-30 15:14:31
 * $Id: PushController.php 62100 2014-12-30 15:14:31Z hunter.fang $
 */

Doo::loadController("AppDooController");

class PushController extends AppDooController {
    
    /**
     * Role模型对象
     * @var Object
     */
    private $_roleModel;

    /**
     * 构造方法，初始化模型和
     */
    public function __construct() {
        parent::__construct();
        $this->_roleModel = Doo::loadModel('Role', TRUE);
    }
    
    /**
     * push导量配置列表展示页
     */
    public function config(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $keyword=$this->get["keyword"];
        $state=$this->get["state"];
        $platform=$this->get["platform"];
        $url = "/push/config?";
        
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        if (in_array($state, array(1,2))){
            $url .= "state=".$state.'&';
        }
        if (in_array($platform, array(1,2))){
            $url .= "platform=".$platform.'&';
        }
        
        $pushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
        $searchResult=$pushConfigModel->getListBySearch($keyword, $state, $platform);
        
        $total = $searchResult['total'];
        $page = $this->page($url, $total);
        $limit = $page->limit;
        list($offset, $length) = explode(',', $limit);
        $result = array_slice($searchResult['list'], $offset, $length);
        
        $this->data["result"]=$result;
        $this->data["keyword"]=$keyword;
        $this->data["state"]=$state;
        $this->data["platform"]=$platform;
        $this->myrender("push/configlist", $this->data);       
    }
    
    
    /**
     * 实时获取push的展示量，点击数
     */
    public function getPushPlanRefreshData()
    {
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $post = $_REQUEST;
        $config_ids = $post['config_ids'];
        
        //28,26,25,23,22,21,20,5,17,6,
        $config_ids_arr = explode(',', $config_ids);
        //去掉空值。
        foreach($config_ids_arr as $key=>$value){
            if(empty($value)){
                unset($config_ids_arr[$key]);
            }
        }
        
        $PushDataModel = Doo::loadModel("datamodel/push/PushData", true);
        $configInfo = $PushDataModel->getPushPlanRefreshData($config_ids_arr);
        
        echo json_encode($configInfo, true);
    }
    
    /**
     * 新增push导量配置展示页
     */
    public function addconfig(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
//        $this->data['condition_conf'] = json_encode(Doo::conf()->PUSH_OTHER_CONDITION);
        $conditionModel = Doo::loadModel('ConditionManages', TRUE);
        $this->data['condition_conf'] = json_encode($conditionModel->toSelect());
        
        $this->myrender("push/addconfig", $this->data);      
    }
    
    /**
     * 保存配置项
     */
    public function saveconfig(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $post = $_POST;
        //参数校验        
        $config_name = $this->removeAllXss($post['config_name']);
        if($config_name != $post['config_name']){
            $this->redirect("javascript:history.go(-1)","导量配置名称参数错误！");
        }
        
        if(empty($config_name)){
            $this->redirect("javascript:history.go(-1)","导量配置名称不能为空！");
        }
        
        $product_id = intval($post['product_id']);
        if(empty($product_id)){
            $this->redirect("javascript:history.go(-1)","导量产品不能为空！");
        }
        
        //保存信息到mobgi_api.push_config
        $platform = $post['platform'];
        $session=Doo::session()->__get("admininfo");
        $operator=$session["username"];
        $con_value = $post['config_value'];
        $PushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
        $exist =  $PushConfigModel->getOne(array("select"=>"*","where"=>"config_name='".$post['config_name']."' and product_id=$product_id and platform = $platform ","asArray"=>TRUE));

        if($exist){
            $this->redirect("javascript:history.go(-1)","已经存在该产品该平台配置名为 ".$config_name." 的配置,请不要重复添加！");
        }
        
        //产品需要先配置push广告
        if(!$PushConfigModel->getConfigProduceDetail($product_id)){
            $this->redirect("javascript:history.go(-1)","请先配置该产品的push广告！");
        }
        
        $config_id = $PushConfigModel->addConfig($config_name, $product_id, $platform,  $operator,$con_value);

        //保存信息到mobgi_api.push_limit
        
        //屏蔽的应用，存储成json格式
        $limit_appkey_arr = array();
        if(!empty($post['appkey'])){
            $limit_appkey_arr = $post['appkey'];
        }
        $limit_appkey = json_encode($limit_appkey_arr);
        
        //根据屏蔽的应用查找对应的包名
        $limit_pkg_arr = array();
        $adappModel=Doo::loadModel('datamodel/AdApp', TRUE);
        $limit_pkg_arr = $adappModel->getPkgByAppkey($limit_appkey_arr);
        $limit_pkg = json_encode($limit_pkg_arr);
        
        //屏蔽的应用，存储成json格式
        $limit_channel_id_arr = array();
        if(!empty($post['channel_id'])){
            $limit_channel_id_arr = $post['channel_id'];
        }
        if(count($limit_channel_id_arr) > 30){
            $this->redirect("javascript:history.go(-1)","渠道最多只能选择30个！");
        }
        $limit_channel_id = json_encode($limit_channel_id_arr);
        
        $PushLimitModel = Doo::loadModel("datamodel/push/PushLimit", true);
        $limit_id = $PushLimitModel->addLimit($config_id, $limit_appkey, $limit_pkg, $limit_channel_id, $operator);
        
        //不需要推送该导量配置下所有的导量计划配置（因为新增的导量配置肯定没有导量计划）
        
        //删除redis里面缓存的PUSH配置
        $this->del_redis_push_config();
        
        //记录后台管理员操作日志
        $referer = $_SERVER['HTTP_REFERER'];
        $title = 'PUSH新增导量配置';
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        global $config;
        //保存后的ＵＲＬ地址。
        $save_url = $config['APP_URL'].'push/editconfig?configid='. $config_id;
        $snapsot_url = save_referer_page($save_url, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer, 'action'=>'insert'));
        
        if($config_id && $limit_id){
            $this->redirect("/push/config","保存成功！");
        }else{
            $this->redirect("/push/config","保存失败！");
        }
    }
    
    /**
     * 编辑配置项
     */
    public function editconfig()
    {
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
         # END 检查权限
        
        $config_id = intval($_GET['configid']);
        $PushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
        $configInfo = $PushConfigModel->getOne(array("select"=>"*","where"=>"id='".$config_id."'", "asArray"=>TRUE));
        $PushLimitModel = Doo::loadModel("datamodel/push/PushLimit", true);
        $limitInfo = $PushLimitModel->getOne(array("select"=>"*","where"=>"config_id='".$config_id."'", "asArray"=>TRUE));
        
        $configInfo['config_value']["json2value"] = $PushConfigModel->getJsonConfigValue($configInfo['con_value']);
        
        $limitInfo['appkey'] = array();
        $limitInfo['channel'] = array();
        
        //获取应用的ＩＤ和名称
        if(!empty($limitInfo['app_id'])){
            $limitInfo['app_id'] = json_decode($limitInfo['app_id']);
            if(!empty($limitInfo['app_id'])){
                $limitInfo['appkey'] = $PushLimitModel->getAppkey($limitInfo['app_id']);
            }
        }
        
        //获取渠道的ＩＤ和名称
        if(!empty($limitInfo['channel_id'])){
            $limitInfo['channel_id'] = json_decode($limitInfo['channel_id']);
            if(!empty($limitInfo['channel_id'])){
                $limitInfo['channel'] = $PushLimitModel->getChannelById($limitInfo['channel_id']);
            }
        }
        
        //生效时间开始时间前5min到生效时间结束的时间段内不可编辑
        $plan=Doo::loadModel("datamodel/push/PushPlan",True);
        $canEdit = $plan->canEditConfig($config_id);
        $intime = intval(!$canEdit);
        $this->data["intime"] = $intime;
        
        $this->data["configInfo"] = $configInfo;
        $this->data["limitInfo"] = $limitInfo;
//        $this->data['condition_conf'] = json_encode(Doo::conf()->PUSH_OTHER_CONDITION);
        $conditionModel = Doo::loadModel('ConditionManages', TRUE);
        $this->data['condition_conf'] = json_encode($conditionModel->toSelect());
        
        $this->myrender("push/editconfig", $this->data);      
    }
    
    
    public function editconfigsave(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        //不允许保存导量配置
        $this->redirect("javascript:history.go(-1)","不允许编辑保存导量配置！");
        
        $post = $_POST;
        
        $config_name = $this->removeAllXss($post['config_name']);
        if($config_name != $post['config_name']){
            $this->redirect("javascript:history.go(-1)","导量配置名称参数错误！");
        }
        
        $config_id = intval($post['config_id']);
        $product_id = intval($post['product_id']);
        $platform = $post['platform'];
        $session=Doo::session()->__get("admininfo");
        $operator=$session["username"];
        $con_value = $post['config_value'];

        //保存信息到mobgi_api.push_config
        $PushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
        
        $existConfig =  $PushConfigModel->getOne(array("select"=>"*","where"=>"config_name='".$post['config_name']."' and product_id=$product_id and platform = $platform and id!= $config_id ","asArray"=>TRUE));
        if($existConfig){
            $this->redirect("javascript:history.go(-1)","已经存在该产品该平台配置名为 ".$config_name." 的配置,请不要重复添加！");
        }
        
        //产品需要先配置push广告
        if(!$PushConfigModel->getConfigProduceDetail($product_id)){
            $this->redirect("javascript:history.go(-1)","请先配置该产品的push广告！");
        }
        
        $plan=Doo::loadModel("datamodel/push/PushPlan",True);
        $canEdit = $plan->canEditConfig($config_id);
        if(!$canEdit){
            $this->redirect('javascript:history.go(-1)','生效时间的开始时间前5min到生效时间结束的时间段内不可编辑、删除');
        }
        
        $exist =  $PushConfigModel->getOne(array("select"=>"*","where"=>"id='".$config_id."' ","asArray"=>TRUE));
        if($exist){
            $resultConfig = $PushConfigModel->editConfigSave($config_id, $config_name, $product_id, $platform,  $operator, $con_value);
        }
        
        //保存信息到mobgi_api.push_limit
        
        //屏蔽的应用，存储成json格式
        $limit_appkey_arr = array();
        if(!empty($post['appkey'])){
            $limit_appkey_arr = $post['appkey'];
        }
        $limit_appkey = json_encode($limit_appkey_arr);
        
        //根据屏蔽的应用查找对应的包名
        $limit_pkg_arr = array();
        $adappModel=Doo::loadModel('datamodel/AdApp', TRUE);
        $limit_pkg_arr = $adappModel->getPkgByAppkey($limit_appkey_arr);
        $limit_pkg = json_encode($limit_pkg_arr);
        
        //屏蔽的应用，存储成json格式
        $limit_channel_id_arr = array();
        if(!empty($post['channel_id'])){
            $limit_channel_id_arr = $post['channel_id'];
        }
        if(count($limit_channel_id_arr) > 30){
            $this->redirect("javascript:history.go(-1)","渠道最多只能选择30个！");
        }
        
        $limit_channel_id = json_encode($limit_channel_id_arr);
        
        $PushLimitModel = Doo::loadModel("datamodel/push/PushLimit", true);
        
        $existLimit =  $PushLimitModel->getOne(array("select"=>"*","where"=>"config_id='".$config_id."' ","asArray"=>TRUE));
        if($existLimit){
            $resultlimit = $PushLimitModel->updateLimit($config_id,$limit_appkey, $limit_pkg, $limit_channel_id , $operator);
        }else{
            $resultlimit = $PushLimitModel->addLimit($config_id, $limit_appkey, $limit_pkg, $limit_channel_id, $operator);
        }
        
        //推送该导量配置下所有的导量计划配置
        $pushPlanModel = Doo::loadModel("datamodel/push/PushPlan", true);
        $plans = $pushPlanModel->getPlan($config_id);
        if(!empty($plans)){
            foreach($plans as $planItem){
                $planId = $planItem['id'];
                //推送更改的导量计划设置到服务器
                if(!($this->pushConfig($config_id, $planId))){
                    $this->redirect("javascript:history.go(-1)","推送导量计划配置失败！");
                }
            }
        }

        //删除redis里面缓存的PUSH配置
        $this->del_redis_push_config();
        
        //记录后台管理员操作日志
        $referer = $_SERVER['HTTP_REFERER'];
        $title = 'PUSH编辑保存导量配置';
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer, 'action'=>'update'));
        
        if($resultConfig && $resultlimit){
            $this->redirect("/push/config","保存成功！");
        }else{
            $this->redirect("/push/config","保存失败！");
        }
        
    }
    
    /**
     * 复制配置
     */
    public function copyconfig()
    {
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
         # END 检查权限
        
        $config_id = intval($_GET['configid']);
        $PushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
        $configInfo = $PushConfigModel->getOne(array("select"=>"*","where"=>"id='".$config_id."'", "asArray"=>TRUE));
        $PushLimitModel = Doo::loadModel("datamodel/push/PushLimit", true);
        $limitInfo = $PushLimitModel->getOne(array("select"=>"*","where"=>"config_id='".$config_id."'", "asArray"=>TRUE));
        
        $configInfo['config_value']["json2value"] = $PushConfigModel->getJsonConfigValue($configInfo['con_value']);
        
        $limitInfo['appkey'] = array();
        $limitInfo['channel'] = array();
        
        //获取应用的ＩＤ和名称
        if(!empty($limitInfo['app_id'])){
            $limitInfo['app_id'] = json_decode($limitInfo['app_id']);
            if(!empty($limitInfo['app_id'])){
                $limitInfo['appkey'] = $PushLimitModel->getAppkey($limitInfo['app_id']);
            }
        }
        
        //获取渠道的ＩＤ和名称
        if(!empty($limitInfo['channel_id'])){
            $limitInfo['channel_id'] = json_decode($limitInfo['channel_id']);
            if(!empty($limitInfo['channel_id'])){
                $limitInfo['channel'] = $PushLimitModel->getChannelById($limitInfo['channel_id']);
            }
        }
        
        $this->data["configInfo"] = $configInfo;
        $this->data["limitInfo"] = $limitInfo;
//        $this->data['condition_conf'] = json_encode(Doo::conf()->PUSH_OTHER_CONDITION);
        $conditionModel = Doo::loadModel('ConditionManages', TRUE);
        $this->data['condition_conf'] = json_encode($conditionModel->toSelect());
        
        $this->myrender("push/copyconfig", $this->data);      
    }
    
    /**
     * 软删除配置项
     */
    public function delconfig(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $config_id = intval($_GET['configid']);
        $PushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
        $result = $PushConfigModel->delConfig($config_id);
        
        //软删除导量配置下面的所有导量计划
        $pushPlanModel = Doo::loadModel("datamodel/push/PushPlan", true);
        $operator = $this->data['session']["username"];
        $resultPlan = $pushPlanModel->delPlanByConfigid($config_id, $operator);
        
//        //推送该导量配置下所有的导量计划配置(删除)
//        $pushPlanModel = Doo::loadModel("datamodel/push/PushPlan", true);
//        $plans = $pushPlanModel->getPlan($config_id);
//        if(!empty($plans)){
//            foreach($plans as $planItem){
//                $planId = $planItem['id'];
//                //推送撤销导量计划配置
//                if(!($this->pushConfig($config_id, $planId, true))){
//                    $this->redirect("/push/config","推送导量计划配置失败！");
//                }
//            }
//        }
        
        //删除redis里面缓存的PUSH配置
        $this->del_redis_push_config();
        
        //记录后台管理员操作日志
        $this->userLogs(array('msg' => json_encode(array('configid' => $config_id)), 'title' => 'PUSH软删除导量配置', 'action' => 'delete'));
        
        if($result&& $resultPlan){
            $this->redirect("javascript:history.go(-1)","删除成功！");
        }else{
            $this->redirect("javascript:history.go(-1)","删除失败！");
        }
    }
    
    /**
     * 导量计划列表
     */
    public function planview(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_PLAN_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $config_id=$this->get["config_id"];
        $config_name = $this->get['config_name'];
        if(empty($config_id)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        $whereArr=array("del"=>1);
        $url = "/push/planview?config_id=".$config_id."&";
        //搜索时间　
        if (isset($this->get['screatedate']) && $this->get['screatedate']) {
            $whereArr['ustart_time'] = $this->get['screatedate'];
            $url .= "screatedate=".$this->get['screatedate']."&";
        }
        if (isset($this->get['ecreatedate']) && $this->get['ecreatedate']) {
            $whereArr['uend_time'] = $this->get['ecreatedate']." 23:59:59";
            $url .= "ecreatedate=".$this->get['ecreatedate']."&";
        }
        if(isset($this->get["go_method"])){
            $go_method=$this->get["go_method"];
            $whereArr["go_method"]=$go_method;
            $url .= "go_method=".$this->get['go_method']."&";
        }
        if(isset($this->get["state"])){
            $state=$this->get["state"];
            $whereArr["runstate"]=$state;
            $url .= "state=".$this->get['state']."&";
        }
        if(isset($this->get["config_id"])){
            $whereArr["config_id"]=$config_id;
            $url .= "config_id=".$config_id."&";
        }
        if(isset($this->get["config_name"])){
            $url .= "config_name=".$config_name."&";
        }
        $plan=Doo::loadModel("datamodel/push/PushPlan",True);
        $total = $plan->records($whereArr);
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $result=$plan->findAll($whereArr);
        
        $data=Doo::loadModel("datamodel/push/PushData",True);
        
        foreach($result as $k=>$v){
            $dresult=$data->get_config_plan_data(array("config_id"=>$v["config_id"],"plan_id"=>$v["id"]));
            if(empty($dresult)){continue;}
            $result[$k]=array_merge($result[$k],$dresult);
        }

        $this->data["result"]=$result;
        foreach ($this->get as $k=>$v){
            $this->data[$k]=$v;
        }
        $this->myrender("push/planlist",$this->data);
    }
    
    /*
     * 导量计划状态变更
     */
    public function plan_set_state(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $planid=$this->get["plan_id"];
        if(empty($planid)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        try{
            $plan=Doo::loadModel("datamodel/push/PushPlan",True);
            $state=$this->get["state"];
            $oprerator=$this->data['session']["username"];
            $result=$plan->set_state($planid, $state, $oprerator);

            //删除redis里面缓存的PUSH配置
            $this->del_redis_push_config();
            
            $cancel = false;
            //state: 1运行,2停止
            if($state == 2){
                $cancel = true;
            }
            $planModel=Doo::loadModel("datamodel/push/PushPlan",True);
            $planInfo = $planModel->findOne(array('id'=>$planid));
            //推送撤销导量计划配置
            if(!($this->pushConfig($planInfo['config_id'], $planid, $cancel))){
                $this->showMsg("推送导量计划配置失败");
            }
            
            //记录后台管理员操作日志
            $referer = $_SERVER['HTTP_REFERER'];
            $title = 'PUSH导量计划状态变更';
            $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
            $type = $file_pre;
            $snapsot_url = save_referer_page($referer, $file_pre);
            $this->userLogs(array('msg' => json_encode(array('plan_id'=>$planid)), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer, 'action'=>'update'));
            
            $this->showSucess("操作成功");
        }  catch (Exception $e){
           $this->showMsg("操作失败");
        }
    }
    
    
    /*
     * 实时获取导量计划信息
     */
    public function plan_get_state(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $config_id=$this->get["config_id"];
        if(empty($config_id)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        try{
            $plan=Doo::loadModel("datamodel/push/PushPlan",True);
            $result=$plan->findAll(array("config_id"=>$config_id));
            $data=Doo::loadModel("datamodel/push/PushData",True);
            foreach($result as $k=>$v){
                $dresult=$data->get_config_plan_data(array("config_id"=>$v["config_id"],"plan_id"=>$v["id"]));
                if(empty($dresult)){continue;}
                $result[$k]=array_merge($result[$k],$dresult);
            }
            $this->showMsg($result,0);
        }  catch (Exception $e){
            $this->showMsg("拉取数据失败");
        }
    }
    
    /**
     * 编辑导量计划
     */
    public function planedit_view(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_PLAN_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $plan_id=isset($this->get["plan_id"])?$this->get["plan_id"]:0;
        $whereArr=array();
        $whereArr["id"]=$plan_id;
        $plan=Doo::loadModel("datamodel/push/PushPlan",True);
        $result=$plan->findOne($whereArr);
        $this->data["plan"]=$result;
        $this->data["copy"]=$this->get["copy"];
        $this->data["config_name"]=$this->get["config_name"];
        $this->data["stime"]=date("Y-m-d 00:00:00",time());
        $this->data["etime"]=date("Y-m-d 23:59:59",time());
        $this->data["config_id"]=$this->get["config_id"];
        $this->data["push_plan_type"]=Doo::conf()->PUSH_PLAN_TYPE;
        $this->myrender("push/planadd",$this->data);
    }
    
    /*
     * 保存导量计划
     */
    public function plan_save(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        if(empty($this->post["start_time"]) || empty($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','参数不正确');
        }
        if(strtotime($this->post["start_time"])>=strtotime($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','开始时间不能大于结束时间');
        }
        if(strtotime($this->post["start_time"])==strtotime($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','开始时间不能等于结束时间');
        }
        if(strtotime($this->post["start_time"])+300>strtotime($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','亲,至少整个大于五分钟的计划吧');
        }
        try {            
            $plan=Doo::loadModel("datamodel/push/PushPlan",True);
            if(isset($this->post["copy"])&& !empty($this->post["copy"])){$this->post["id"]="";}#是否复制
            if(isset($this->post["id"]) && !empty($this->post["id"])){
                $planinfo=$plan->findOne(array("id"=>$this->post["id"],"del"=>1));
                if(empty($planinfo)){$this->redirect('javascript:history.go(-1)','该记录已经被删除');}
                if($planinfo["runstatus"]==3){#导量完毕的不允许修改导量计划时间
                    $this->redirect('javascript:history.go(-1)','猴哥别闹');
                }
                if(!$plan->judgeCanEditPlan($planinfo)){
                    $this->redirect('javascript:history.go(-1)','生效时间的开始时间前5min到生效时间结束的时间段内不可编辑、删除');
                }
            }else{
                $planinfo=Doo::db()->query("SELECT * FROM push_plan  WHERE config_id ='".$this->post["config_id"]."' AND start_time= UNIX_TIMESTAMP('".$this->post["start_time"]."') AND end_time= UNIX_TIMESTAMP('".$this->post["end_time"]."') and del=1    LIMIT 1")->fetch();
                if(!empty($planinfo)){$this->redirect('javascript:history.go(-1)','该配置已经存在相同时间段,相同有效次数的导量计划,请不要重复添加');}
            }
            $this->post["oprator"]=$this->data['session']["username"];
            
            $result=$plan->upd($this->post);
            
            if(!empty($this->post['id'])){
                $configId = $planinfo['config_id'];
                $planId = $this->post['id'];
            }else{
                $configId = $this->post['config_id'];
                $planId = $result;
            }
            //推送更改的导量计划设置到服务器
            if(!($this->pushConfig($configId, $planId))){
                $this->redirect("javascript:history.go(-1)","推送导量计划配置失败！");
            }
            
            //删除redis里面缓存的PUSH配置
            $this->del_redis_push_config();
            $config_id = empty($this->post["config_id"])?$planinfo["config_id"]:$this->post["config_id"];
            
            //记录后台管理员操作日志
            $referer = $_SERVER['HTTP_REFERER'];
            $title = 'PUSH保存导量计划';
            $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
            $type = $file_pre;
            $snapsot_url = save_referer_page($referer, $file_pre);
            $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer),$this->post['id']);
            
            $this->redirect('/push/planview?config_id='.$config_id."&config_name=".$this->post["config_name"],'保存成功');
        } catch (Exception $exc) {
            $this->redirect('javascript:history.go(-1)','保存失败');
        } 
    }
    
    /*
     * 删除导量计划
     */
    public function plandel(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        if(empty($this->get["id"])){
            $this->redirect('javascript:history.go(-1)','参数不正确');
        }
        try{
            $planid = intval($this->get["id"]);
            $operator = $this->data['session']["username"];
            $planModel=Doo::loadModel("datamodel/push/PushPlan",True);
            $planInfo = $planModel->findOne(array('id'=>$planid));
            if(empty($planInfo)){
                $this->redirect('javascript:history.go(-1)','该导量计划不存在');
            }
            
            if(!$planModel->judgeCanEditPlan($planinfo)){
                $this->redirect('javascript:history.go(-1)','生效时间的开始时间前5min到生效时间结束的时间段内不可编辑、删除');
            }
            
            //如果已经存在已软删除了的导量计划，则直接硬删除该导量计划,否则软删除该导量计划
            if($planModel->findOne(array('config_id'=>$planInfo['config_id'],'r_start_time'=>$planInfo['start_time'],'r_end_time'=>$planInfo['end_time'],'del'=>0))){
                $result = $planModel->deletePlan($planid);
            }else{
                $result = $planModel->delPlan($planid, $operator);
            }
            
            //推送撤销导量计划配置
            if(!($this->pushConfig($planInfo['config_id'], $planid, true))){
                $this->redirect("javascript:history.go(-1)","推送导量计划配置失败！");
            }

            //删除redis里面缓存的PUSH配置
            $this->del_redis_push_config();
            
            //记录后台管理员操作日志
            $this->userLogs(array('msg' => json_encode(array('id' => $this->get["id"])), 'title' => 'PUSH删除导量计划', 'action' => 'delete'));
            
            $this->redirect('javascript:history.go(-1)','删除成功');
        } catch (Exception $exc) {
            $this->redirect('javascript:history.go(-1)','删除失败');
        }
    }
    
    /**
     * 防骚挠设置
     */
    public function harass(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_HARASS_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $harassModel=Doo::loadModel("datamodel/push/PushHarass",True);
        $harassInfo = $harassModel->getHarass();
        $this->data['harass'] = $harassInfo;
        
        $this->myrender("push/harass",$this->data);
    }
    
    /**
     * 保存防骚挠设置
     */
    public function saveharass(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_HARASS_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $harassid = $this->post['id'];
        $this->post['one_day_max_msg'] = intval($this->post['one_day_max_msg']);
        $this->post['one_product_msg_limit'] = intval($this->post['one_product_msg_limit']);
        $this->post['lastest_msg_interval'] = intval($this->post['lastest_msg_interval']);

        $harassModel=Doo::loadModel("datamodel/push/PushHarass",True);
        $operator = $this->data['session']["username"];
        $valueArr = array();
        $valueArr['one_day_max_msg'] = $this->post['one_day_max_msg'];
        $valueArr['one_product_msg_limit'] = $this->post['one_product_msg_limit'];
        $valueArr['lastest_msg_interval'] = $this->post['lastest_msg_interval'];
        $value = json_encode($valueArr);
        if(empty($harassid)){
            $result = $harassModel->add($value, $operator);
        }else{
            $result = $harassModel->upd($harassid, $value, $operator);
        }
        
        //推送更改的防骚挠设置到服务器
        if(!($this->pushHarass())){
            $this->redirect("/push/harass","推送全局规则配置失败！");
        }
        
        if($result){
            $this->redirect("/push/harass","保存成功！");
        }else{
            $this->redirect("/push/harass","保存失败！");
        }
        
    }
    
    /**
     * push权重列表展示页
     */
    public function weight(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_WEIGHT_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $keyword = $this->get['keyword'];
        $whereArr=array("del"=>1);
        $url = "/push/weight?";
        
        //没有时间限制，则默认搜索当天开始30天内的计划
        if(!isset($this->get['screatedate'])&&!isset($this->get['ecreatedate'])){
            $this->get['screatedate'] = $_GET['screatedate'] = date("Y-m-d");
            $this->get['ecreatedate'] = $_GET['ecreatedate'] = date("Y-m-d", strtotime(date('Y-m-d'))+86400*30);
        }
        
        //搜索时间　
        if (isset($this->get['screatedate']) && $this->get['screatedate']) {
            $whereArr['ustart_time'] = $this->get['screatedate'];
            $url .= "screatedate=".$this->get['screatedate']."&";
        }
        if (isset($this->get['ecreatedate']) && $this->get['ecreatedate']) {
            $whereArr['uend_time'] = $this->get['ecreatedate']." 23:59:59";
            $url .= "ecreatedate=".$this->get['ecreatedate']."&";
        }
        if(isset($this->get["state"])){
            $state=$this->get["state"];
            $whereArr["state"]=$state;
            $url .= "state=".$this->get['state']."&";
        }
        if(isset($this->get["keyword"])){
            $whereArr["keyword"]=$keyword;
            $url .= "keyword=".$keyword."&";
        }
        $weightModel=Doo::loadModel("datamodel/push/PushWeight",True);
        $total = $weightModel->records($whereArr);
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
        $result=$weightModel->findAll($whereArr);
        
        //查找产品是否有可用的导量计划
        if(!empty($result)){
            $product_plan_time_arr = array();
            $plan=Doo::loadModel("datamodel/push/PushPlan",True);
            foreach($result as $key=>$item){
                foreach($item['product_combo_arr'] as $key2=>$combo_item){
                    $product_id = $combo_item['pid'];
                    $result[$key]['product_combo_arr'][$key2]['has_plan'] = false;
                    if(isset($product_plan_time_arr[$product_id])){
                        if(!empty($product_plan_time_arr[$product_id])){
                            foreach($product_plan_time_arr[$product_id] as $planTimeItem){
                                if($this->isMixTime($planTimeItem['start_time'], $planTimeItem['end_time'], $item['start_time'], $item['end_time'])){
                                    $result[$key]['product_combo_arr'][$key2]['has_plan'] = true;
                                }
                            }
                        }
                    }else{
                        $planResult = $plan->getPlanByProductid($product_id);
                        $product_plan_time_arr[$product_id] = $planResult;
                        if(!empty($planResult)){
                            foreach($planResult as $planTimeItem){
                                if($this->isMixTime($planTimeItem['start_time'], $planTimeItem['end_time'], $item['start_time'], $item['end_time'])){
                                    $result[$key]['product_combo_arr'][$key2]['has_plan'] = true;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $this->data["result"]=$result;
        foreach ($this->get as $k=>$v){
            $this->data[$k]=$v;
        }
        $this->myrender("push/weightlist",$this->data);
    }
    
    /**
     * 编辑产品权重
     */
    public function weightedit_view(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_WEIGHT_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $weight_id=isset($this->get["weight_id"])?$this->get["weight_id"]:0;
        $whereArr=array();
        $whereArr["id"]=$weight_id;
        $weightModel=Doo::loadModel("datamodel/push/PushWeight",True);
        $result=$weightModel->findOne($whereArr);
        $this->data["weight"]=$result;
        $this->data["copy"]=$this->get["copy"];
        $this->data["stime"]=date("Y-m-d 00:00:00",time());
        $this->data["etime"]=date("Y-m-d 23:59:59",time());
        $this->data["product_pop"]=Doo::conf()->BASEURL."push/showProductlitsPop";
        $this->data["weight_pop"]=Doo::conf()->BASEURL."push/showWeightlistPop";
        $this->myrender("push/weightadd",$this->data);
    }
    
    /**
     * 保存权重设置
     */
    public function weight_save(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_WEIGHT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        if(empty($this->post["start_time"]) || empty($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','参数不正确');
        }
        if(strtotime($this->post["start_time"])>=strtotime($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','开始时间不能大于结束时间');
        }
        if(strtotime($this->post["start_time"])==strtotime($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','开始时间不能等于结束时间');
        }
        if(strtotime($this->post["start_time"])+300>strtotime($this->post["end_time"])){
            $this->redirect('javascript:history.go(-1)','亲,至少整个大于五分钟的计划吧');
        }
        try {            
            $weightModel=Doo::loadModel("datamodel/push/PushWeight",True);
            
            
            if(isset($this->post["copy"])&& !empty($this->post["copy"])){$this->post["id"]="";}#是否复制
            if(isset($this->post["id"]) && !empty($this->post["id"])){
                $weightInfo=$weightModel->findOne(array("id"=>$this->post["id"],"del"=>1));
                if(empty($weightInfo)){$this->redirect('javascript:history.go(-1)','该记录已经被删除');}
                if((time() > $weightInfo['start_time'] - 300) && (time() < $weightInfo['end_time'])){
                    $this->redirect('javascript:history.go(-1)','生效时间的开始时间前5min到生效时间结束的时间段内不可编辑、删除');
                }
            }
            
            //复制和新增权重操作需要把生效时间与以前的导量权重配置时间进行对比。（把更新保存权重排除在外）
            if( !$this->post["id"] && !($weightModel->isTimeAvailable(strtotime($this->post["start_time"]), strtotime($this->post["end_time"])))){
                $this->redirect('javascript:history.go(-1)','生效时间与以前的导量权重配置冲突');
            }
            
            $this->post["operator"]=$this->data['session']["username"];
            //生效中：１，未生效：２，已过期：３
            if(time()<strtotime($this->post['start_time'])){
                $this->post['state'] = 2;
            }else if(time()>=strtotime($this->post['start_time']) || time()<strtotime($this->post['end_time'])){
                $this->post['state'] = 1;
            }else{
                $this->post['state'] = 3;
            }
            //存JSON数据去掉斜杠
            if(get_magic_quotes_gpc()){
                $this->post['product_combo'] = stripcslashes($this->post['product_combo']);
            }
            
            $result=$weightModel->upd($this->post);
            
            if(!empty($this->post['id'])){
                $weightId = $this->post['id'];
            }else{
                $weightId = $result;
            }
            //推送更新后的权重配置
            if(!($this->pushWeight($weightId))){
                $this->redirect("/push/weight","推送导量权重配置失败！");
            }
            
            //删除redis里面缓存的PUSH配置
            $this->del_redis_push_config();
            
            //记录后台管理员操作日志
            $referer = $_SERVER['HTTP_REFERER'];
            $title = 'PUSH保存导量权重';
            $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
            $type = $file_pre;
            $snapsot_url = save_referer_page($referer, $file_pre);
            $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer),$this->post['id']);
            
            if($result){
                $this->redirect("/push/weight","保存成功！");
            }else{
                $this->redirect("/push/weight","保存失败！");
            }
            
        } catch (Exception $exc) {
            $this->redirect('javascript:history.go(-1)','保存失败');
        } 
        
        
    }
    
    /*
     * 删除导量权重
     */
    public function weightdel(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_WEIGHT_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        if(empty($this->get["id"])){
            $this->redirect('javascript:history.go(-1)','参数不正确');
        }
        try{
            $weightId = intval($this->get["id"]);
            $operator = $this->data['session']["username"];
            $weightModel=Doo::loadModel("datamodel/push/PushWeight",True);
            
            if(!($weightModel->canEditWeight($weightId))){
                $this->redirect('javascript:history.go(-1)','生效时间的开始时间前5min到生效时间结束的时间段内不可编辑、删除');
            }
            
            $result = $weightModel->delWeight($weightId, $operator);
            
            //推送更新后的权重配置
            if(!($this->pushWeight($weightId, true))){
                $this->redirect("/push/weight","推送导量权重配置失败！");
            }

            //删除redis里面缓存的PUSH配置
            $this->del_redis_push_config();
            
            //记录后台管理员操作日志
            $this->userLogs(array('msg' => json_encode(array('id' => $this->get["id"])), 'title' => 'PUSH删除导量权重', 'action' => 'delete'));
            
            $this->redirect('javascript:history.go(-1)','删除成功');
        } catch (Exception $exc) {
            $this->redirect('javascript:history.go(-1)','删除失败');
        }
    }
    
    //弹出产品列表窗口
    public function showProductlitsPop(){
        $weightModel=Doo::loadModel("datamodel/push/PushWeight",True);
        $this->data['products'] = $weightModel->getProductPop();
        $this->get_simple_platform($this->data['products']);
        $this->myRenderWithoutTemplate("push/product_pop",$this->data);
    }
    
    /**
     * 其它权重配置弹出框
     */
    public function showWeightlistPop(){
        $weightModel=Doo::loadModel("datamodel/push/PushWeight",True);
        $whereArr=array("del"=>1);
        $result=$weightModel->findAll($whereArr);
        $this->data["result"]=$result;
        //print_r($this->data);
        $this->myRenderWithoutTemplate("push/weight_pop",$this->data);
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
     * 删除redis里面缓存的PUSH配置
     * @return boolean
     */
    public function del_redis_push_config(){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_5');
        // 删除Redis
        $redis->delete("PUSH_CONFIG");
        return true;
    }
    
    /**
     * 推送防骚挠配置
     */
    public function pushHarass(){
        $harassModel=Doo::loadModel("datamodel/push/PushHarass",True);
        $pushConfig = $harassModel->getPushHarass();
        $pushUrl = Doo::conf()->PUSH_URL;
        $returnStr = $harassModel->curl()->post($pushUrl, $pushConfig, false);
        
        //记录push日志
        $this->recordPushLog(3, $pushConfig, $returnStr);
        
        $returnArr = json_decode($returnStr, true);
        if($returnArr['error_code'] == 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 推送push权重配置
     */
    public function pushWeight($weightId, $cancel = false){
        $weightModel=Doo::loadModel("datamodel/push/PushWeight",True);
        $pushConfig=$weightModel->getPushWeight($weightId, $cancel);
        $pushUrl = Doo::conf()->PUSH_URL;
        $returnStr = $weightModel->curl()->post($pushUrl, $pushConfig, false);
        
        //记录push日志
        $this->recordPushLog(2, $pushConfig, $returnStr);
        
        $returnArr = json_decode($returnStr, true);
        if($returnArr['error_code'] == 0){
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 推送导量配置（判断是一次性导量还是周期性导量，一次性导量配置，直接推送，周期性导量配置先判断是否可推送，再决定推送与否）
     * @param type $configId
     * @param type $planId
     * @param type $cancel
     * @return boolean
     */
    public function pushConfig($configId, $planId, $cancel = false){
        //组织导量计划plan
        $planModel=Doo::loadModel("datamodel/push/PushPlan",True);
        $planInfo = $planModel->findOne(array('config_id'=>$configId, 'id'=>$planId));
        if(empty($planInfo)){
            return false;
        }
        //一次性导量计划直接推送
        if($planInfo['go_method']==1){
            //停止运行的，已软删除的但是不是推送取消push的一次性导量计划直接略过不推送
            if($planInfo['state']!=1 || ($planInfo['del']!=1 && $cancel!=true )){
                return true;
            }
            return $this->puchConfigNotCycle($configId, $planId, $cancel);
        }
        
        //以下是周期性导量计划逻辑（先判断是否需要推送）
        $s_time = strtotime(date("Y-m-d"));
        $e_time = strtotime("tomorrow");
        //停止运行的，已删除的，不在今天有效期内的导量计划直接略过不推送
        if($planInfo['state']!=1 || $planInfo['del']!=1 || ! ( ( $planInfo['start_time'] <=$s_time&&$planInfo['end_time']>=$s_time) || ($planInfo['start_time']>=$s_time&&$planInfo['start_time']<=$e_time) ) ){
            return true;
        }
        
        //获取防骚挠设置的产品限制（产品限制：N个自然日内，同一用户最多收到 1 条相同产品的PUSH）
        $harassModel=Doo::loadModel("datamodel/push/PushHarass",True);
        $harassInfo = $harassModel->getHarass();
        $one_product_msg_limit = $harassInfo['value']['one_product_msg_limit'];
        $time_apart = 0;
        if($one_product_msg_limit > 0){
            $time_apart = $one_product_msg_limit * 3600 * 24;
        }
        
        //push日志
        $pushplanlogModel=Doo::loadModel("datamodel/push/PushPlanLog",True);

        //把周期性导量计划分拆成今天可用的导量计划
        $pushplanlog = $pushplanlogModel->getLatestPushplanlogByPlanid($planId);
        if(empty($pushplanlog)){
            //首次推送
            return $this->pushConfigCycle($configId, $planId, $cancel);
        }else{
            //如果今天推送过了，就不需要重复推送
            $s_time = strtotime(date("Y-m-d"));
            $e_time = strtotime("tomorrow");
            if($pushplanlog['start_time'] >= $s_time && $pushplanlog['end_time'] <= $e_time){
                //本日已经推送过，不需要重复推送
                return true;
            }else{
                //相隔N个自然日才推送本产品的PUSH
                if($pushplanlog['send_time']+$time_apart <=time()){
                    return $this->pushConfigCycle($configId, $planId, $cancel);
                }
            }
        }
        return true;
    }
    
    /**
     * 推送导量配置(针对一次性导量计划)
     * @param type $configId
     * @param type $planId
     * @param type $cancel
     * @return boolean
     */
    public function puchConfigNotCycle($configId, $planId, $cancel = false){
        $configModel=Doo::loadModel("datamodel/push/PushConfig",True);
        $pushConfig=$configModel->getPushConfigPlan($configId, $planId, $cancel);
        $pushUrl = Doo::conf()->PUSH_URL;
        $returnStr = $configModel->curl()->post($pushUrl, $pushConfig, false);
        
        //记录push日志
        $this->recordPushLog(1, $pushConfig, $returnStr);
        
//        var_dump($returnStr);
        $returnArr = json_decode($returnStr, true);
        if($returnArr['error_code'] == 0){
            return true;
        }else{
            return false;
        }
    }
    
     /**
     * 推送导量配置（针对周期性导量计划）
     * @param type $configId
     * @param type $planId
     */
    public function pushConfigCycle($configId, $planId, $cancel = false){
        
        $configModel=Doo::loadModel("datamodel/push/PushConfig",True);
        $pushConfig=$configModel->getPushConfigCyclePlan($configId, $planId, $cancel);
        $pushUrl = Doo::conf()->PUSH_URL;
        $returnStr = $configModel->curl()->post($pushUrl, $pushConfig, false);
        
        //记录push日志
        $this->recordPushLog(1, $pushConfig, $returnStr);
        
        $returnArr = json_decode($returnStr, true);
        if($returnArr['error_code'] == 0){
            //记录发送周期性导量计划日志
            parse_str($pushConfig, $pushConfigArr);
            //时间毫秒转秒
            $start_time = $pushConfigArr['start_time']/1000;
            $end_time = $pushConfigArr['end_time']/1000;
            $this->recordPushPlanLog($planId, $start_time, $end_time);
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 记录周期性导量计划日志
     * @param type $plan_id
     * @param type $start_time
     * @param type $end_time
     */
    public function recordPushPlanLog($plan_id, $start_time, $end_time){
        $pushplanlogModel=Doo::loadModel("datamodel/push/PushPlanLog",True);
        $send_time = time();
        $pushplanlogModel->add($plan_id, $start_time, $end_time, $send_time);
    }
    
    /**
     * 记录推送日志
     * @param type $type
     * @param type $log
     * @param type $returnStr
     */
    public function recordPushLog($type, $log, $returnStr){
        $pushlogModel=Doo::loadModel("datamodel/push/PushLog",True);
        $session=Doo::session()->__get("admininfo");
        $operator=$session["username"];
        $pushlogModel->add($type, $log, $operator, $returnStr);
    }
    
    /**
     * 查看该产品是否有ＰＵＳＨ广告
     */
    public function getProductPushAd(){
        $productId = intval($_GET['product_id']);
        $return = array();
        if(empty($productId)){
            $return['exist'] = 0;
        }else{
            $pushConfigModel = Doo::loadModel("datamodel/push/PushConfig", true);
            if($data = $pushConfigModel->getConfigProduceDetail($productId)){
                $return['exist'] = 1;
            }else{
                $return['exist'] = 0;
            }
        }
        echo json_encode($return);
    }
    
    /**
     * push日志列表
     */
    public function log(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_LOG_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $params = $this->get;
        $url = "/push/log?";
        $pushlogModel=Doo::loadModel("datamodel/push/PushLog",True);
        
        $where = '1=1';
        $total = $pushlogModel->count(array('select'=>'*', 'asArray' => true, 'where' => $where));
        $pager = $this->page($url, $total);
        $this->data['result'] = $pushlogModel->find(array('select'=>'*', 'asArray' => true, 'where' => $where,'limit' => $pager->limit, 'desc' => 'createtime'));
        
        $logTypeDesc = array('1'=>"导量计划", "2"=>"导量权重", "3"=>"全局规则配置");
        foreach($this->data['result'] as $key=>$item){
            $this->data['result'][$key]['createtime_desc'] = date("Y-m-d H:i:s", $item['createtime']);
            $this->data['result'][$key]['type_desc'] = $logTypeDesc[$item['type']];
        }
        
        $this->myrender('push/log', $this->data);
    }
    
    public function loginfo(){
        # START 检查权限
        if (!$this->checkPermission(PUSH, PUSH_LOG_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $logId = intval($this->get['id']);
        if(empty($logId)){
            $this->redirect('javascript:history.go(-1)','id不能为空');
        }
        $pushlogModel=Doo::loadModel("datamodel/push/PushLog",True);
        $logInfo = $pushlogModel->getOne(array('select'=>'*', 'asArray' => true, 'where' => 'id='.$logId));
        if(!empty($logInfo)){
            $pushlog = $logInfo['log'];
            $pushlogArr = $this->convertUrlQuery($pushlog);
            $pushlogArr['extra'] = urldecode($pushlogArr['extra']);
        }
        $this->data['pushLogArr'] = $pushlogArr;
        $this->myrender('push/log_info', $this->data);
    }
    
    /**
 	 * Returns the url query as associative array
 	 *
 	 * @param    string    query
 	 * @return    array    params
 	 */
    private function convertUrlQuery($query)
 	{
	    $queryParts = explode('&', $query);
 	     
 	    $params = array();
 	    foreach ($queryParts as $param)
 	    {
 	        $item = explode('=', $param);
 	        $params[$item[0]] = $item[1];
 	    }
 	    return $params;
 	}
    
    
    /**
     * 比较时间段一与时间段二是否有交集
     * @param type $begintime1
     * @param type $endtime1
     * @param type $begintime2
     * @param type $endtime2
     * @return boolean
     */
   private function isMixTime($begintime1, $endtime1, $begintime2, $endtime2) {
        $status = $begintime2 - $begintime1;
        if ($status > 0) {
            $status2 = $begintime2 - $endtime1;
            if ($status2 > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            $status2 = $begintime1 - $endtime2;
            if ($status2 > 0) {
                return false;
            } else {
                return true;
            }
        }
    }

}
