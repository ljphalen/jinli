<?php
/**
 * 配置类控制器
 * @author Stephen.Feng
 */
Doo::loadController("AppDooController");
class OthercfController extends AppDooController {
    public function __construct(){
        parent::__construct();
    }
    public function index() {
//        # START 检查权限 
        if (!$this->checkPermission(OTHERCONFIG, OTHERCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
//        # END 检查权限
        $params = $this->get;
        $where = array("select"=>"*","where"=>"type='".$params["type"]."'","groupby"=>"config_name","asArray"=>true);
        $ConfigModel = Doo::loadModel('datamodel/AdOtherConfig', TRUE);
        
        $this->data['list'] = $ConfigModel->find($where);
        $appkey=array();
        $channel=array();
        if ($params["type"] == 'channellink' || $params["type"] == 'directpush' ) { // 取产品
            $product=Doo::loadModel("datamodel/AdProductInfo",TRUE);
            $productArr = $product->find(array('select' => '*', 'asArray' => TRUE));
            $this->get_platform_product_name($productArr);
            $productList = listArray($productArr, 'id', 'platform_product_name');
            $left = $productList;
            $right = array();
        }
        if(!empty($this->data['list']) && !empty($params["config_name"])){
            $left = array();
            $where = array("select"=>"*","where"=>"type='".$params["type"]."' and config_name='".$params['config_name']."'","asArray"=>true);
            $result = $ConfigModel->find($where);
            if(!empty($result)){
                foreach($result as $value){
                    $this->data['id']=$value["id"];
                    $this->data['config_name']=$value["config_name"];
                    $this->data['platform']=$value["platform"];
                    $this->data['config_detail']=  json_decode($value["config_detail"],true);
                    if(!in_array($value["appkey"], $appkey)){
                        array_push($appkey, $value["appkey"]);
                    }
                    if(!in_array($value["channel_id"],$channel)){
                        array_push($channel,$value["channel_id"]);
                    }
                    if ($params["type"] == 'channellink' || $params["type"] == 'directpush'){
                        $right[$value['appkey']] = $productList[$value['appkey']];
                    }
                }
                if ($params["type"] == 'channellink' || $params["type"] == 'directpush'){
                    $this->data['appkey']= implode(",", $appkey);
                }else{
                    $this->data['appkey']= $ConfigModel->_getAppkeyBykey($appkey);
                }
                $this->data['channel']=$ConfigModel->_getChannelById($channel);
            }
        }
        if ($params["type"] == 'channellink'|| $params["type"] == 'directpush' ) {
            $this->data['left'] = json_encode(array_diff($productList, $right));
            $this->data['right'] = json_encode($right);
            $click_type_object = json_decode($this->data['config_detail']['click_type_object'], true);
            $this->data['inner_install_manage'] = $click_type_object['inner_install_manage'];
        }
        $this->myrender('otherconfig/'.$params["type"], $this->data);
    }

    /**
     * rtb报价配置展示
     */
    public function rtb(){
        if(!$this->checkPermission(OTHERCONFIG, OTHERCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        
        $param = $this->get;
        //默认选项:芒果AMAX
        if(empty($param['ader'])){
            $param['ader'] = 'AMAX';
        }
        if(empty($param['ad_type'])){
            $param['ad_type'] = '0';
        }
        if(empty($param['acounting_method'])){
            $param['acounting_method'] = 'CPM';
        }
        $ConfigModel = Doo::loadModel('datamodel/AdOtherConfig', TRUE);
        $rtbMoneyInfo = $ConfigModel->getRtbByAder($param['ader'], $param['ad_type'], $param['acounting_method']);
        //当前消耗金额读取 key 广告商date("Ymd",time())MONEY 的值
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_RTB_MONEY_KEY');
        $key = $param['ader'].date("Ymd",time())."MONEY";
        $current_sale = $redis->get($key);
        $rtbMoneyInfo['current_sale'] =  number_format($current_sale/10000000, 4);
        $this->data['rtbinfo'] = $rtbMoneyInfo;
        $this->myrender('otherconfig/rtb', $this->data);
    }
    
    /**
    * 获取ＲＴＢ记录信息，输出ＪＳＯＮ格式
    */
    public function getRtbMoney(){
        if(!$this->checkPermission(OTHERCONFIG, OTHERCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        
        $param = $this->get;
        //默认选项:芒果AMAX
        if(empty($param['ader'])){
            $param['ader'] = 'AMAX';
        }
        if(empty($param['ad_type'])){
            $param['ad_type'] = '0';
        }
        if(empty($param['acounting_method'])){
            $param['acounting_method'] = 'CPM';
        }
        $ConfigModel = Doo::loadModel('datamodel/AdOtherConfig', TRUE);
        $rtbMoneyInfo = $ConfigModel->getRtbByAder($param['ader'], $param['ad_type'], $param['acounting_method']);
        $retCode = '-1';
        $errMsg = '';
        if(empty($rtbMoneyInfo)){
            $retCode = '-1';
            $errMsg = '记录不存在';
            $rtbMoneyInfo = $param;
        }else{
            $retCode = '0';
        }
        //当前消耗金额读取 key 广告商date("Ymd",time())MONEY 的值
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_RTB_MONEY_KEY');
        $key = $param['ader'].date("Ymd",time())."MONEY";
        $current_sale = $redis->get($key);
        $rtbMoneyInfo['current_sale'] =  number_format($current_sale/10000000, 4);
        echo json_encode(array('errMsg' => $errMsg, 'retCode' => $retCode,'data'=>$rtbMoneyInfo));
    }
    
    /**
     * rtb报价配置保存
     */
    public function rtbsave(){
        if(!$this->checkPermission(OTHERCONFIG, OTHERCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        
        $param = $this->post;
        if(empty($param['ader'])){
            $this->redirect("javascript:history.go(-1)",'ADX平台不能为空');
        }
        
        if(!in_array($param['ad_type'], array(0, 1))){
            $this->redirect("javascript:history.go(-1)",'广告类型参数错误');
        }
        
        if(!in_array($param['acounting_method'], array('CPM', 'CPC'))){
            $this->redirect("javascript:history.go(-1)",'结算方式参数错误');
        }
        
        if(empty($param['price']) || empty($param['day_sale'])){
            $this->redirect("javascript:history.go(-1)",'报价与单日消耗值都不能为空');
        }
        $ConfigModel = Doo::loadModel('datamodel/AdOtherConfig', TRUE);
        $result = $ConfigModel->rtbSave($param);
        if($result){
            //每次修改清除7004 key RTBMONEYCONFIG广告商 的值
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_RTB_MONEY_KEY');
            $key = "RTBMONEYCONFIG".$param['ader'].$param['ad_type'];
            $redis->del($key);
            $this->redirect('javascript:history.go(-1)','保存成功');
        }else{
            $this->redirect('javascript:history.go(-1)','保存失败');
        }
        $this->myrender('otherconfig/rtb', $this->data);
    }
    
    public function configsave(){
        if (!$this->checkPermission(OTHERCONFIG, OTHERCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        $post = $this->post;
        $ConfigModel = Doo::loadModel('datamodel/AdOtherConfig', TRUE);
        if (empty($post['channel_id'])){
            $this->redirect("javascript:history.go(-1)",'请选择渠道');
        }
        if (empty($post['appkey'])){
            $this->redirect("javascript:history.go(-1)",'请选择应用');
        }
        Doo::db()->query("delete from ad_other_config where type='".$post["types"]."' and config_name='".$post["config_name"]."'");
        if(!empty($result)){
            $this->redirect('javascript:history.go(-1)','该配置项已经存在');
        }
        if ($post["types"] == 'channellink') {
            $post['appkey'] = explode(',', $post['appkey']);
            $click_type_object = $post['data']['click_type_object'];
            $post['data'] = array(
                    'download_link' => $post['data']['download_link'],
                    'click_type_object' => json_encode($click_type_object),
            );
        }
        if ($post["types"] == 'directpush') {
            $post['appkey'] = explode(',', $post['appkey']);
        }
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_DEFAULT');
        foreach($post['appkey'] as $appkey){
                foreach($post['channel_id'] as $channel){
                    $ConfigModel->upd(array('appkey' => $appkey, 'config_detail' => json_encode($post['data']), 'channel_id' =>$channel,'config_name'=>$post["config_name"],'type'=>$post['types'],"platform"=>$post["platform"]));
                    //$redis->set($channel."_".$appkey."_channellink", json_encode($post['data']));
                }
        }
        if($post["types"]=="pushcdnset"){//如果是pushcdn的配置则出发生成pushconfig.json
            $OtherConfigModel = Doo::loadModel('AdConfigs', TRUE);
            $OtherConfigModel->CreatePushConfigJson();
        }
        
        if ($post["types"] == 'directpush') {
            //每次修改清除6500 key DIRECTIONAL_CONFIG 的值
            Doo::loadClass("Fredis/FRedis");
            $redis = FRedis::getSingletonPort('CACHE_REDIS_DIRECTIONAL_CONFIG_KEY');
            $key = "DIRECTIONAL_CONFIG";
            $redis->del($key);
        }
        $this->redirect('javascript:history.go(-1)','保存成功');
    }
    public function del(){
        if (!$this->checkPermission(OTHERCONFIG, OTHERCONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        $get = $this->get;
        Doo::db()->query("delete from ad_other_config where type='".$get["type"]."' and config_name='".$get["config_name"]."'");
        $this->redirect('javascript:history.go(-1)','删除成功');
    }
    
    //为数组新增键为platform_product_name的值.即产品名称前新增平台的简称如:
    public function get_platform_product_name(&$arr)
    {
        if(empty($arr))
        {
            return;
        }
        $platform_config_arr = array(0=>'(T)', 1=>"(A)", 2=>"(I)");
        foreach($arr as $key=>$item)
        {
            $arr[$key]['platform_product_name']= $platform_config_arr[$item['platform']].$arr[$key]['product_name'];
        }
        return;
    }
}

?>
