<?php

/**
 * RTB管理
 */
Doo::loadController("AppDooController");

class RtbController extends AppDooController {

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
     * 新增黑名单表单
     */
    public function blacklist(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_BLACKLIST_VIEW)) {
            $this->displayNoPermission();
        }

        $this->myrender("rtb/blacklist", $this->data);        
    }
    
    /**
     * 获取rtb黑名单列表，输出json数据。
     */
    public function getRtbBlacklist(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_BLACKLIST_VIEW)) {
            $this->displayNoPermission();
        }
        
        $return = array();
        $return['package'] = array();
        $return['ip'] = array();
        
        $RtbBlacklistModel = Doo::loadModel("datamodel/rtb/RtbBlacklist", true);
        $black_list = $RtbBlacklistModel->getRtbBlacklist();

        if(!empty($black_list)){
            foreach($black_list as $black_list_item){
                if($black_list_item['type'] == 'ip'){
                    $return['ip'] = explode("\r\n", $black_list_item['value']);
                }
                elseif($black_list_item['type'] == 'package'){
                    $return['package'] = explode("\r\n", $black_list_item['value']);
                }
            }
        }
        echo json_encode($return, true);
    }
    
    /**
     * 保存黑名单
     */
    public function save_blacklist(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_BLACKLIST_EDIT)) {
            $this->displayNoPermission();
        }
        
        $post = $_POST;
        $package_blacklist = $this->removeAllXss($post['package_blacklist']);
        if($package_blacklist != $post['package_blacklist']){
            $this->redirect("javascript:history.go(-1)","包名黑名单参数错误！");
        }
        
        $ip_blacklist = $this->removeAllXss($post['ip_blacklist']);
        if($ip_blacklist != $post['ip_blacklist']){
            $this->redirect("javascript:history.go(-1)","ip黑名单参数错误！");
        }
        
        
        $session=Doo::session()->__get("admininfo");
        $operator=$session["username"];
        
        //删除redis里面缓存的RTB黑名单配置
        $this->del_redis_rtb_blacklist();
        
        //增,更package黑名单列表(value值以\r\n为分隔符合并成字符串)
        $RtbBlacklistModel = Doo::loadModel("datamodel/rtb/RtbBlacklist", true);
        $package_blacklist_arr = explode("\r\n", $package_blacklist);
        $package_black_db = $RtbBlacklistModel->getRtbBlackByType("package");
        if(empty($package_black_db)){
            $type = "package";
            //去空值　
            $this->remove_empty_item($package_blacklist_arr);
            $value = implode("\r\n", $package_blacklist_arr);
            $mober =$post['package_mober'];
            $RtbBlacklistModel->addBlacklist($type, $value, $mober, $operator);
        }else{
            $package_black_db_arr_new = array_unique( $package_blacklist_arr);
            //去空值　
            $this->remove_empty_item($package_black_db_arr_new);
            $id = $package_black_db['id'];
            $type = 'package';
            $value = implode("\r\n", $package_black_db_arr_new);
            $RtbBlacklistModel->updateBlacklist($id, $type, $value, $operator);
        }
        
        //增,更ip黑名单列表(value值以\r\n为分隔符合并成字符串)
        $RtbBlacklistModel = Doo::loadModel("datamodel/rtb/RtbBlacklist", true);
        $ip_blacklist_arr = explode("\r\n", $ip_blacklist);
        $ip_black_db = $RtbBlacklistModel->getRtbBlackByType("ip");
        if(empty($ip_black_db)){
            $type = "ip";
            //去空值　
            $this->remove_empty_item($ip_blacklist_arr);
            $value = implode("\r\n", $ip_blacklist_arr);
            $mober =$post['ip_mober'];
            $RtbBlacklistModel->addBlacklist($type, $value, $mober, $operator);
        }else{
            $ip_black_db_arr_new = array_unique( $ip_blacklist_arr);
            //去空值　
            $this->remove_empty_item($ip_black_db_arr_new);
            $id = $ip_black_db['id'];
            $type = 'ip';
            $value = implode("\r\n", $ip_black_db_arr_new);
            $RtbBlacklistModel->updateBlacklist($id, $type, $value, $operator);
        }
        
        //记录后台管理员操作日志
        $referer = $_SERVER['HTTP_REFERER'];
        $title = 'RTB保存黑名单';
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer, 'action'=>'update'));
        
        $this->redirect('javascript:history.go(-1)','保存成功');
    }
    
    /**
     * rtv导量配置列表展示页
     */
    public function config(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $keyword=$this->get["keyword"];
        $state=$this->get["state"];
        $platform=$this->get["platform"];
        $mober = $this->get['mober'];
        $url = "/rtb/config?";
        
        if ($keyword){
            $url .= "keyword=".$keyword.'&';
        }
        if (in_array($state, array(1,2))){
            $url .= "state=".$state.'&';
        }
        if (in_array($platform, array(1,2))){
            $url .= "platform=".$platform.'&';
        }
        if(!empty($mober)){
            $url .= "mober=".$mober.'&';
        }
        
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $searchResult=$RtbConfigModel->getListBySearch($keyword, $state, $platform, $mober);
        $total = $searchResult['total'];
        $page = $this->page($url, $total);
        $limit = $page->limit;
        list($offset, $length) = explode(',', $limit);
        $result = array_slice($searchResult['list'], $offset, $length);
        
        $this->data["result"]=$result;
        $this->data["keyword"]=$keyword;
        $this->data["state"]=$state;
        $this->data["platform"]=$platform;
        $this->data["mober"]=$mober;
        $this->myrender("rtb/configlist", $this->data);       
    }
    
    
    /**
     * 实时获取rtb的展示量，点击数
     */
    public function getRtbPlanRefreshData()
    {
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_VIEW)) {
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
        
        $RtbPlanModel = Doo::loadModel("datamodel/rtb/RtbData", true);
        $configInfo = $RtbPlanModel->getRtbPlanRefreshData($config_ids_arr);
        echo json_encode($configInfo, true);
    }
    
    /**
     * 新增rtb导量配置展示页
     */
    public function addconfig(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $this->data["rtb_mober"]= Doo::conf()->RTB_MOBER;
//        $this->data["android_amax_cat_app"]= Doo::conf()->ANDROID_AMAX_CAT_APP;
        $this->data["mobile_make"]= array_unique(Doo::conf()->MOBILE_MAKE);
//        $this->data["carrieroperator"]= Doo::conf()->CARRIEROPERATOR;
//        $this->data["network_environment"]= Doo::conf()->NETWORK_ENVIRONMENT;
//        $this->data["screen_orientation"]= Doo::conf()->SCREEN_ORIENTATION;
        $this->data["geographical_position"]= Doo::conf()->GEOGRAPHICAL_POSITION;
        $this->data['standard'] = Doo::conf()->STANDARD;
        $this->data['rtbchannel'] = Doo::conf()->RTBCHANNEL;
        
        $this->myrender("rtb/addconfig", $this->data);      
    }
    
    public function changeRtbStandard(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $standard =  $this->removeAllXss($_POST['standard']);
        $result =array();
        if(!empty($standard)){
            $result = Doo::conf()->$standard;
        }
        
        $config_id =  $this->removeAllXss($_POST['config_id']);
       
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $configInfo = $RtbConfigModel->getOne(array("select"=>"*","where"=>"id='".$config_id."'", "asArray"=>TRUE));
        $RtbLimitModel = Doo::loadModel("datamodel/rtb/RtbLimit", true);
        $limitInfo = $RtbLimitModel->getOne(array("select"=>"*","where"=>"config_id='".$config_id."'", "asArray"=>TRUE));
        
        
        //把数据里面存的json数据转成数组.
        if(!empty($limitInfo['cat'])){
            $limitInfo['cat'] = json_decode($limitInfo['cat']);
        }
        if(!empty($limitInfo['carrier'])){
            $limitInfo['carrier'] = json_decode($limitInfo['carrier']);
        }
        if(!empty($limitInfo['net_type'])){
            $limitInfo['net_type'] = json_decode($limitInfo['net_type']);
        }
        if(!empty($limitInfo['screen_type'])){
            $limitInfo['screen_type'] = json_decode($limitInfo['screen_type']);
        }
        
        if(!empty($limitInfo['make'])){
            $limitInfo['make'] = json_decode($limitInfo['make']);
        }
        
        //应用种类
        $cat_app_options = '';
        if(!empty($result['CAT_APP'])){
            foreach($result['CAT_APP'] as $key=>$cat_app_item){
                //一级应用
                if(!is_array($cat_app_item)){
                    $checked = "";
                    //有指定config_id的需要根据这个config_id的配置项是否选中来设置选中状态。
                    if($config_id){
                        if(!empty($limitInfo['cat'])&&in_array($key, $limitInfo['cat'])){
                            $checked = 'checked="checked"';
                        }
                    }
                    //默认选中状态
                    else{
                        $checked = 'checked="checked"';
                    }
                    $cat_app_options .= '<label class="addconfiglabel"><input type="checkbox" name="app_cat[]" value="'.$key.'" '.$checked.' />'.$cat_app_item.'</label>';
                }
                //多级应用
                else{
                    $cat_checked = "";
                    $cat_app_options .= '<div class="app_cat_1"><label class="addconfiglabel"><input class="cat_checkbox app_cat_1_input" type="checkbox" name="app_cat_'.$key.'" cat_level="'.$key.'" value="'.$key.'" '.$cat_checked.' />'.$key.'</label>';
                    $cat_app_options_2 = '';
                    $cat_app_options_3 = '';
                    foreach($cat_app_item as $key2=>$cat_app_item2){
                        $cat_app_options_3_inner = '';
                        $cat_app_options_3_display = "style='display:none;'";
                        //二级应用
                        if(!is_array($cat_app_item2)){
                            $checked = "";
                            //有指定config_id的需要根据这个config_id的配置项是否选中来设置选中状态。
                            if($config_id){
                                if(!empty($limitInfo['cat'])&&in_array($key2, $limitInfo['cat'])){
                                    $checked = 'checked="checked"';
                                }
                            }
                            //默认选中状态
                            else{
                                $checked = 'checked="checked"';
                            }
                            $cat_app_options_2 .= '<label class="app_cat_2_label addconfiglabel"><input class="cat_checkbox app_cat_2_input" type="checkbox" name="app_cat[]" cat_level="'.$key."_".$key2.'" value="'.$key2.'" '.$checked.' />'.$cat_app_item2.'</label>';
                        }
                        //三级应用
                        else{
                            foreach($cat_app_item2 as $key3=>$cat_app_item3){
                                $checked = "";
                                //有指定config_id的需要根据这个config_id的配置项是否选中来设置选中状态。
                                if($config_id){
                                    if(!empty($limitInfo['cat'])&&in_array($key3, $limitInfo['cat'])){
                                        $checked = 'checked="checked"';
                                        $cat_app_options_3_display = "style='display:block;'"; 
                                    }
                                }
                                //默认选中状态
                                else{
                                    $checked = 'checked="checked"';
                                    $cat_app_options_3_display = "style='display:block;'"; 
                                }
                                $cat_app_options_3_inner .= '<label class="addconfiglabel"><input class="cat_checkbox" type="checkbox" name="app_cat[]" cat_level="'.$key."_".$key2."_".$key3.'" value="'.$key3.'" '.$checked.' />'.$cat_app_item3.'</label>';
                            }
                            $cat_app_options_2 .= '<label class="app_cat_2_label addconfiglabel"><input class="cat_checkbox app_cat_2_input" type="checkbox" name="app_cat_'.$key2.'" cat_level="'.$key."_".$key2.'" value="'.$key2.'" '.$cat_checked.' />'.$key2.'</label>';
                            $cat_app_options_3  .= "<span ".$cat_app_options_3_display." cat_level='".$key."_".$key2."'>".$cat_app_options_3_inner.'</span>';
                        }
                        
                    }
                    //二级应用
                    if(empty($cat_app_options_3)){
                        $cat_app_options_2 = '<div class="app_cat_2" style="font-weight:normal;">'.$cat_app_options_2.'</div>';
                        $cat_app_options .=$cat_app_options_2;
                    }
                    //三级应用
                    else{
                        $cat_app_options_2 = '<div class="app_cat_2">'.$cat_app_options_2.'</div>';
                        $cat_app_options .=$cat_app_options_2 ."<div class='app_cat_3'>". $cat_app_options_3 ."</div>";
                    }
                    $cat_app_options .= '</div>';
                }
                
            }
        }
        
        //运营商
        $carrieroperator_options = '';
        if(!empty($result['CARRIEROPERATOR'])){
            foreach($result['CARRIEROPERATOR'] as $key=>$carrieroperator){
                $checked = "";
                if($config_id){
                    if(!empty($limitInfo['carrier'])&&array_intersect(explode(",", $key), $limitInfo['carrier'])){
                        $checked = 'checked="checked"';
                    }
                }else{
                    if($carrieroperator == '其它'){
                        $checked = '';
                    }else{
                        $checked = 'checked="checked"';
                    }
                }
                $carrieroperator_options .= '<label class="addconfiglabel"><input type="checkbox" name="carrieroperator[]" value="'.$key.'" '.$checked.'/>'.$carrieroperator.'</label>';
            }
        }
        
        //网络环境。
        $net_environment_options = '';
        if(!empty($result['NETWORK_ENVIRONMENT'])){
            foreach($result['NETWORK_ENVIRONMENT'] as $key=>$environment){
                $checked = "";
                if($config_id){
                    if(!empty($limitInfo['net_type'])&&array_intersect(explode(",", $key), $limitInfo['net_type'])){
                        $checked = 'checked="checked"';
                    }
                }else{
                    if($environment == '未知'){
                        $checked = '';
                    }else{
                        $checked = 'checked="checked"';
                    }
                }
                $net_environment_options .= '<label class="addconfiglabel"><input type="checkbox" name="network_environment[]" value="'.$key.'" '.$checked.'/>'.$environment.'</label>';
            }
        }
        
        //屏幕方向
        $screen_orientation_options = '';
        if(!empty($result['SCREEN_ORIENTATION'])){
            foreach($result['SCREEN_ORIENTATION'] as $key=>$screen_orientation){
                $checked = "";
                if($config_id){
                    if(!empty($limitInfo['screen_type'])&&in_array($key, $limitInfo['screen_type'])){
                        $checked = 'checked="checked"';
                    }
                }
                else{
                    $checked = 'checked="checked"';
                }
                $screen_orientation_options .= '<label class="addconfiglabel"><input type="checkbox" name="screen_orientation[]" value="'.$key.'" '.$checked.'/>'.$screen_orientation.'</label>';
            }
        }
        
        //手机品牌(需要区分ＩＯＳ和ＡＮＤＲＯＩＤ)
        $android_mobile_type_options = '';
        $ios_mobile_type_options = '';
        $mobile_type = Doo::conf()->MOBILE_MAKE;
        
        if(!empty($mobile_type)){
            foreach($mobile_type as $key=>$mobile_type_item){
                $checked = "";
                if($config_id){
                    if(!empty($limitInfo['make'])&&array_intersect(explode(",", $key), $limitInfo['make'])){
                        $checked = 'checked="checked"';
                    }
                }
                else{
                    $checked = 'checked="checked"';
                }
                if($key == 'apple' ){
                    $ios_mobile_type_options .= '<label class="addconfiglabel"><input type="checkbox" name="mobile_type[]" value="'.$key.'" '.$checked.'/>'.$mobile_type_item.'</label>';
                }else if($key=='其它'){
                    $ios_mobile_type_options .= '<label class="addconfiglabel"><input type="checkbox" name="mobile_type[]" value="'.$key.'" '.$checked.'/>'.$mobile_type_item.'</label>';
                    $android_mobile_type_options .= '<label class="addconfiglabel"><input type="checkbox" name="mobile_type[]" value="'.$key.'" '.$checked.'/>'.$mobile_type_item.'</label>';
                }else{
                    $android_mobile_type_options .= '<label class="addconfiglabel"><input type="checkbox" name="mobile_type[]" value="'.$key.'" '.$checked.'/>'.$mobile_type_item.'</label>';
                }
            }
        }
        
        $return = array();
        $return['cat_app_options'] = $cat_app_options;
        $return['carrieroperator_options'] = $carrieroperator_options;
        $return['net_environment_options'] = $net_environment_options;
        $return['screen_orientation_options'] = $screen_orientation_options;
        $return['ios_mobile_type_options'] = $ios_mobile_type_options;
        $return['android_mobile_type_options'] = $android_mobile_type_options;
        echo json_encode($return);
    }
    
    /**
     * 编辑配置项
     */
    public function editconfig()
    {
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
         # END 检查权限
        
        $config_id = intval($_GET['configid']);
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $configInfo = $RtbConfigModel->getOne(array("select"=>"*","where"=>"id='".$config_id."'", "asArray"=>TRUE));
        $RtbLimitModel = Doo::loadModel("datamodel/rtb/RtbLimit", true);
        $limitInfo = $RtbLimitModel->getOne(array("select"=>"*","where"=>"config_id='".$config_id."'", "asArray"=>TRUE));

        //把数据里面存的json数据转成数组.
        if(!empty($limitInfo['cat'])){
            $limitInfo['cat'] = json_decode($limitInfo['cat']);
        }
        if(!empty($limitInfo['make'])){
            $limitInfo['make'] = json_decode($limitInfo['make']);
        }
        if(!empty($limitInfo['carrier'])){
            $limitInfo['carrier'] = json_decode($limitInfo['carrier']);
        }
        if(!empty($limitInfo['net_type'])){
            $limitInfo['net_type'] = json_decode($limitInfo['net_type']);
        }
        if(!empty($limitInfo['screen_type'])){
            $limitInfo['screen_type'] = json_decode($limitInfo['screen_type']);
        }
        if(!empty($limitInfo['loc'])){
            $limitInfo['loc'] = json_decode($limitInfo['loc']);
        }
        
        //多个英文键值的配置处理。
        $this->process_mobile_make_multikey($limitInfo['make']);
        $this->process_net_enviroment_multikey($limitInfo['net_type']);
        
        $this->data["rtb_mober"]= Doo::conf()->RTB_MOBER;
//        $this->data["android_amax_cat_app"]= Doo::conf()->ANDROID_AMAX_CAT_APP;
        $this->data["mobile_make"]= array_unique(Doo::conf()->MOBILE_MAKE);
//        $this->data["carrieroperator"]= Doo::conf()->CARRIEROPERATOR;
//        $this->data["network_environment"]= Doo::conf()->NETWORK_ENVIRONMENT;
//        $this->data["screen_orientation"]= Doo::conf()->SCREEN_ORIENTATION;
        $this->data["geographical_position"]= Doo::conf()->GEOGRAPHICAL_POSITION;
        $this->data["configInfo"] = $configInfo;
        $this->data["limitInfo"] = $limitInfo;
        $this->data['standard'] = Doo::conf()->STANDARD;
        $this->data['rtbchannel'] = Doo::conf()->RTBCHANNEL;
        
        $this->myrender("rtb/editconfig", $this->data);      
    }
    
    /**
     * 保存编辑的配置项
     */
    public function editconfigsave(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $post = $_POST;
        $config_name = $this->removeAllXss($post['config_name']);
        if($config_name != $post['config_name']){
            $this->redirect("javascript:history.go(-1)","导量配置名称参数错误！");
        }
        
        $config_id = intval($post['config_id']);
        $product_id = intval($post['product_id']);
        $platform = $post['platform'];
        $mober = $post['mober'];
        $session=Doo::session()->__get("admininfo");
        $operator=$session["username"];
        $channel = $post['channel'];
        
        //保存信息到mobgi_api.rtb_config
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $exist =  $RtbConfigModel->getOne(array("select"=>"*","where"=>"id='".$config_id."' ","asArray"=>TRUE));
        if($exist){
            $resultConfig = $RtbConfigModel->editConfigSave($config_id, $config_name, $product_id, $platform, $mober, $operator, $channel);
        }

        //保存信息到mobgi_api.rtb_limit
        if($post['platform']==1 && $post['mober']=="AMAX" && $post['app_cat'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择应用类型");
        }
        if($post['mobile_type'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择手机品牌");
        }
        if($post['carrieroperator'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择运营商");
        }
        if($post['network_environment'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择网络环境");
        }
        if($post['screen_orientation'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择屏幕方向");
        }
        if($post['geographical_position'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择地理位置");
        }
        
        //手机类型参数处理（把值为逗号隔开的手机类型分离出来）
        if(!empty($post['mobile_type'])){
            $tmp_mobile_type = array();
            foreach($post['mobile_type'] as $mobile_type_item){
                $tmp_mobile_type = array_merge($tmp_mobile_type, explode(',', $mobile_type_item));
            }
            $post['mobile_type'] = $tmp_mobile_type;
        }
        
        //运营商参数处理（把值为逗号隔开的运营商分离出来）
        if(!empty($post['carrieroperator'])){
            $tmp_carrieroperator = array();
            foreach($post['carrieroperator'] as $carrieroperator_item){
                $tmp_carrieroperator = array_merge($tmp_carrieroperator, explode(',', $carrieroperator_item));
            }
            $post['carrieroperator'] = $tmp_carrieroperator;
        }
        
        //网络环境参数处理（把值为逗号隔开的网络环境分离出来）
        if(!empty($post['network_environment'])){
            $tmp_network_environment = array();
            foreach($post['network_environment'] as $network_environment_item){
                $tmp_network_environment = array_merge($tmp_network_environment, explode(',', $network_environment_item));
            }
            $post['network_environment'] = $tmp_network_environment;
        }
        
        $RtbLimitModel = Doo::loadModel("datamodel/rtb/RtbLimit", true);
        $cat = json_encode($post['app_cat'], true);
        $make = json_encode($post['mobile_type'], true);
        $carrier = json_encode($post['carrieroperator'], true);
        $net_type = json_encode($post['network_environment'], true);
        $screen_type = json_encode($post['screen_orientation'], true);
        $loc = json_encode($post['geographical_position'], true);
        
        $existLimit =  $RtbLimitModel->getOne(array("select"=>"*","where"=>"config_id='".$config_id."' ","asArray"=>TRUE));
        if($existLimit){
            $resultlimit = $RtbLimitModel->updateLimit($config_id, $cat, $make, $carrier, $net_type, $screen_type, $loc, $operator);
        }else{
            $resultlimit = $RtbLimitModel->addLimit($config_id, $cat, $make, $carrier, $net_type, $screen_type, $loc, $operator);
        }
        
        //删除redis里面缓存的RTB配置
        $this->del_redis_rtb_config();
        
        //记录后台管理员操作日志
        $referer = $_SERVER['HTTP_REFERER'];
        $title = 'RTB编辑保存导量配置';
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        $snapsot_url = save_referer_page($referer, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer, 'action'=>'update'));
        
        if($resultConfig && $resultlimit){
            $this->redirect("/rtb/config","保存成功！");
        }else{
            $this->redirect("/rtb/config","保存失败！");
        }
        
    }
    
    /**
     * 复制配置项
     */
    public function copyconfig()
    {
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
         # END 检查权限
        
        $config_id = intval($_GET['configid']);
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $configInfo = $RtbConfigModel->getOne(array("select"=>"*","where"=>"id='".$config_id."'", "asArray"=>TRUE));
        $RtbLimitModel = Doo::loadModel("datamodel/rtb/RtbLimit", true);
        $limitInfo = $RtbLimitModel->getOne(array("select"=>"*","where"=>"config_id='".$config_id."'", "asArray"=>TRUE));
        
        //把数据里面存的json数据转成数组.
        if(!empty($limitInfo['cat'])){
            $limitInfo['cat'] = json_decode($limitInfo['cat']);
        }
        if(!empty($limitInfo['make'])){
            $limitInfo['make'] = json_decode($limitInfo['make']);
        }
        if(!empty($limitInfo['carrier'])){
            $limitInfo['carrier'] = json_decode($limitInfo['carrier']);
        }
        if(!empty($limitInfo['net_type'])){
            $limitInfo['net_type'] = json_decode($limitInfo['net_type']);
        }
        if(!empty($limitInfo['screen_type'])){
            $limitInfo['screen_type'] = json_decode($limitInfo['screen_type']);
        }
        if(!empty($limitInfo['loc'])){
            $limitInfo['loc'] = json_decode($limitInfo['loc']);
        }
        
        //多个英文键值的配置处理。
        $this->process_mobile_make_multikey($limitInfo['make']);
        $this->process_net_enviroment_multikey($limitInfo['net_type']);
        
        $this->data["rtb_mober"]= Doo::conf()->RTB_MOBER;
//        $this->data["android_amax_cat_app"]= Doo::conf()->ANDROID_AMAX_CAT_APP;
        $this->data["mobile_make"]= array_unique(Doo::conf()->MOBILE_MAKE);
//        $this->data["carrieroperator"]= Doo::conf()->CARRIEROPERATOR;
//        $this->data["network_environment"]= Doo::conf()->NETWORK_ENVIRONMENT;
//        $this->data["screen_orientation"]= Doo::conf()->SCREEN_ORIENTATION;
        $this->data["geographical_position"]= Doo::conf()->GEOGRAPHICAL_POSITION;
        $this->data["configInfo"] = $configInfo;
        $this->data["limitInfo"] = $limitInfo;
        $this->data['standard'] = Doo::conf()->STANDARD;
        $this->data['rtbchannel'] = Doo::conf()->RTBCHANNEL;
        
        $this->myrender("rtb/copyconfig", $this->data);      
    }


    /**
     * 保存配置项
     */
    public function saveconfig(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_EDIT)) {
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
        
        //保存信息到mobgi_api.rtb_config
        $platform = $post['platform'];
        $mober = $post['mober'];
        $session=Doo::session()->__get("admininfo");
        $operator=$session["username"];
        $channel = $post['channel'];
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $exist =  $RtbConfigModel->getOne(array("select"=>"*","where"=>"config_name='".$post['config_name']."' and product_id=$product_id and platform = $platform and mober='".$mober."' ","asArray"=>TRUE));

        if($exist){
            $this->redirect("javascript:history.go(-1)","已经存在该配置项,请不要重复添加！");
        }
        
        $config_id = $RtbConfigModel->addConfig($config_name, $product_id, $platform, $mober, $operator,$channel);

        //保存信息到mobgi_api.rtb_limit
        if($post['app_cat'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择应用类型");
        }
        if($post['mobile_type'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择手机品牌");
        }
        if($post['carrieroperator'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择运营商");
        }
        if($post['network_environment'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择网络环境");
        }
        if($post['screen_orientation'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择屏幕方向");
        }
        if($post['geographical_position'] ===NULL){
            $this->redirect("javascript:history.go(-1)","请选择地理位置");
        }
        
        //手机类型参数处理（把值为逗号隔开的手机类型分离出来）
        if(!empty($post['mobile_type'])){
            $tmp_mobile_type = array();
            foreach($post['mobile_type'] as $mobile_type_item){
                $tmp_mobile_type = array_merge($tmp_mobile_type, explode(',', $mobile_type_item));
            }
            $post['mobile_type'] = $tmp_mobile_type;
        }
        
        //运营商参数处理（把值为逗号隔开的运营商分离出来）把值["46000,46002,46007","46001,46006","46003,46005"]转成["46000","46002","46007","46001","46006","46003","46005"]
        if(!empty($post['carrieroperator'])){
            $tmp_carrieroperator = array();
            foreach($post['carrieroperator'] as $carrieroperator_item){
                $tmp_carrieroperator = array_merge($tmp_carrieroperator, explode(',', $carrieroperator_item));
            }
            $post['carrieroperator'] = $tmp_carrieroperator;
        }

        //网络环境参数处理（把值为逗号隔开的网络环境分离出来）
        if(!empty($post['network_environment'])){
            $tmp_network_environment = array();
            foreach($post['network_environment'] as $network_environment_item){
                $tmp_network_environment = array_merge($tmp_network_environment, explode(',', $network_environment_item));
            }
            $post['network_environment'] = $tmp_network_environment;
        }

        $RtbLimitModel = Doo::loadModel("datamodel/rtb/RtbLimit", true);
        $cat = json_encode($post['app_cat'], true);
        $make = json_encode($post['mobile_type'], true);
        $carrier = json_encode($post['carrieroperator'], true);
        $net_type = json_encode($post['network_environment'], true);
        $screen_type = json_encode($post['screen_orientation'], true);
        $loc = json_encode($post['geographical_position'], true);

        $limit_id = $RtbLimitModel->addLimit($config_id, $cat, $make, $carrier, $net_type, $screen_type, $loc, $operator);
        
        //删除redis里面缓存的RTB配置
        $this->del_redis_rtb_config();
        
        //记录后台管理员操作日志
        $referer = $_SERVER['HTTP_REFERER'];
        $title = 'RTB新增导量配置';
        $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
        $type = $file_pre;
        global $config;
        //保存后的ＵＲＬ地址。
        $save_url = $config['APP_URL'].'rtb/editconfig?configid='. $config_id;
        $snapsot_url = save_referer_page($save_url, $file_pre);
        $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer, 'action'=>'insert'));
        
        if($config_id && $limit_id){
            $this->redirect("/rtb/config","保存成功！");
        }else{
            $this->redirect("/rtb/config","保存失败！");
        }
    }
    
    /**
     * 软删除配置项
     */
    public function delconfig(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_CONFIG_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        
        $config_id = intval($_GET['configid']);
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $result = $RtbConfigModel->delConfig($config_id);
        
        //删除redis里面缓存的RTB配置
        $this->del_redis_rtb_config();
        
        //记录后台管理员操作日志
        $this->userLogs(array('msg' => json_encode(array('configid' => $config_id)), 'title' => 'RTB软删除导量配置', 'action' => 'delete'));
        
        if($result){
            $this->redirect("javascript:history.go(-1)","删除成功！");
        }else{
            $this->redirect("javascript:history.go(-1)","删除失败！");
        }
    }
    //*****************************************导量计划开始******************************************************//
    public function planview(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_PLAN_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $config_id=$this->get["config_id"];
        $config_name = $this->get['config_name'];
        if(empty($config_id)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        $whereArr=array("del"=>1);
        $url = "/rtb/planview?config_id=".$config_id."&";
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
        $plan=Doo::loadModel("datamodel/rtb/RtbPlan",True);
        $total = $plan->records($whereArr);
        $pager = $this->page($url, $total);
        $whereArr['limit'] = $pager->limit;
//        $whereArr['no_apk_url'] =1;//只显示上传了应用的
        $result=$plan->findAll($whereArr);
        $data=Doo::loadModel("datamodel/rtb/RtbData",True);

        //根据广告商的不同获取相应的倍数。
        $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
        $configInfo = $RtbConfigModel->getCongfigByid($result["config_id"]);
        $priceTime = 10000;
        if(!empty($configInfo)){
            $priceTime = $this->get_rtb_price_double_by($configInfo['mober']);
        }
        
        foreach($result as $k=>$v){
            foreach(array("maxprice","maximp","maxclick") as $k1=>$v1){
                $con=  strstr($v["maxcondition"],$v1);
                if(empty($con)){
                    unset($result[$k][$v1]);
                }
            }
            $dresult=$data->get_config_plan_data(array("config_id"=>$v["config_id"],"plan_id"=>$v["id"]));
            if(empty($dresult)){continue;}
            $result[$k]=array_merge($result[$k],$dresult);
            
            //导量价格，导量金额上限根据广告商的不同除以相应的倍数。
            $result[$k]['price'] = round($result[$k]['price']/$priceTime,4);
            $result[$k]['maxprice'] = round($result[$k]['maxprice']/$priceTime,4);
            $result[$k]['statprice'] = round($result[$k]['statprice']/$priceTime,4);
            
            //echo $result[$k]['imppirce'] ;
            if($result[$k]['method']=="CPM"){
                $result[$k]['statprice'] = round($result[$k]['statprice']/1000,6);
                $result[$k]['imppirce'] = round($result[$k]['imppirce']*1000/$priceTime,4);
            }else{
                $result[$k]['imppirce'] = round($result[$k]['imppirce']/$priceTime,4);
            }
            $result[$k]['clickpirce'] = round($result[$k]['clickpirce']/$priceTime,4);
        }
        $this->data["result"]=$result;
        foreach ($this->get as $k=>$v){
            $this->data[$k]=$v;
        }
        $this->myrender("rtb/planlist",$this->data);
    }
    /*
     * 导量计划状态变更
     */
    public function plan_set_state(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $planid=$this->get["plan_id"];
        if(empty($planid)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        try{
            $plan=Doo::loadModel("datamodel/rtb/RtbPlan",True);
            $whereArr["id"]=$planid;
            $state=$this->get["state"];
//            if($state==1){
//                $state=$plan->get_plan_state($planid);
//                if($state===0){$this->showMsg("该记录已经不存在");}
//                //if($state===4){$this->showMsg("此计划已经导量完毕,不能再继续操作！");}
//            }
            $whereArr["state"]=$state;
            $whereArr["oprator"]=$this->data['session']["username"];
            $result=$plan->upd($whereArr);
            
            //删除redis里面缓存的RTB配置
            $this->del_redis_rtb_config();
            
            //记录后台管理员操作日志
            $referer = $_SERVER['HTTP_REFERER'];
            $title = 'RTB导量计划状态变更';
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
        if (!$this->checkPermission(RTB, RTB_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $config_id=$this->get["config_id"];
        if(empty($config_id)){
            $this->redirect("javascript:history.go(-1)","id为空！");
        }
        try{
            $plan=Doo::loadModel("datamodel/rtb/RtbPlan",True);
            $result=$plan->findAll(array("config_id"=>$config_id));
            $data=Doo::loadModel("datamodel/rtb/RtbData",True);
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
    public function planedit_view(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_PLAN_VIEW)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        $plan_id=isset($this->get["plan_id"])?$this->get["plan_id"]:0;
        $whereArr=array();
        $whereArr["id"]=$plan_id;
        $plan=Doo::loadModel("datamodel/rtb/RtbPlan",True);
        $result=$plan->findOne($whereArr);
        if(!empty($result)){
            //导量价格，导量金额上限根据广告商的不同除以相应的倍数。
            $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
            $configInfo = $RtbConfigModel->getCongfigByid($result["config_id"]);
            $priceTime = 10000;
            if(!empty($configInfo)){
                $priceTime = $this->get_rtb_price_double_by($configInfo['mober']);
            }
            $result['price'] = round($result['price']/$priceTime,4);
            $result['maxprice'] = round($result['maxprice']/$priceTime,4);
            
            //maximp||;maxclick&&;maxprice
            $maxcondition=explode(";",$result["maxcondition"]);
            foreach ($maxcondition as $k=>$v){
                $or = strstr($v, "||");
                $and = strstr($v, "&&");
                $flag=empty($or)?$and:$or;
                $c=  str_replace($flag,"",$v);
                if($k<1){
                    $result["maxcondition"]=array("c"=>$c,"v"=>$result[$c]);
                }else{
                    $result["maxcondition1"][$k]=array("f"=>$preflag,"c"=>$c,"v"=>$result[$c]);
                }
                $preflag=$flag;
            }
        }
        $this->data["plan"]=$result;
        $this->data["copy"]=$this->get["copy"];
        $this->data["config_name"]=$this->get["config_name"];
        $this->data["stime"]=date("Y-m-d 00:00:00",time());
        $this->data["etime"]=date("Y-m-d 23:59:59",time());
        $this->data["config_id"]=$this->get["config_id"];
        $this->data["method"]=Doo::conf()->RTB_ACOUNTING_METHOD;
        $this->data["ad_type"]=Doo::conf()->RTB_AD_TYPE;
        $this->myrender("rtb/planadd",$this->data);
    }
    /*
     * 保存导量计划
     */
    public function plan_save(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        if(empty($this->post["start_time"]) || empty($this->post["end_time"]) || !isset($this->post["price"])){
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
            $plan=Doo::loadModel("datamodel/rtb/RtbPlan",True);
            if(isset($this->post["copy"])&& !empty($this->post["copy"])){$this->post["id"]="";}#是否复制
            if(isset($this->post["id"]) && !empty($this->post["id"])){
                $planinfo=$plan->findOne(array("id"=>$this->post["id"],"del"=>1));
                if(empty($planinfo)){$this->redirect('javascript:history.go(-1)','该记录已经被删除');}
                if($planinfo["runstatus"]==3){#导量完毕的不允许修改导量计划时间
                    $this->redirect('javascript:history.go(-1)','猴哥别闹');
                }
            }else{
                $planinfo=Doo::db()->query("SELECT * FROM rtb_plan  WHERE config_id ='".$this->post["config_id"]."' AND start_time= UNIX_TIMESTAMP('".$this->post["start_time"]."') AND end_time= UNIX_TIMESTAMP('".$this->post["end_time"]."') and del=1    LIMIT 1")->fetch();
                if(!empty($planinfo)){$this->redirect('javascript:history.go(-1)','该配置已经存在相同时间段,相同有效次数的导量计划,请不要重复添加');}
            }
            if(isset($this->post["limit_type"])){
                foreach ($this->post["limit_type"] as $k=>$v){
                    $this->post[$v]=$this->post["maxvalue"][$k];
                    $this->post["maxcondition"].=$v.$this->post["limitcondition"][$k].";";
                }
                $this->post["maxcondition"]=trim($this->post["maxcondition"],";");
            }
            $this->post["oprator"]=$this->data['session']["username"];
            
            //导量价格，导量金额上限根据广告商的不同乘上相应的倍数。
            $RtbConfigModel = Doo::loadModel("datamodel/rtb/RtbConfig", true);
            $configInfo = $RtbConfigModel->getCongfigByid($this->post["config_id"]);
            $priceTime = 10000;
            if(!empty($configInfo)){
                $priceTime = $this->get_rtb_price_double_by($configInfo['mober']);
            }
            if(isset($this->post['price'])){
                $this->post['price'] = $this->post['price'] * $priceTime;
                $this->post['maxprice'] = $this->post['maxprice'] * $priceTime;
            }
            
            $result=$plan->upd($this->post);

            //删除redis里面缓存的RTB配置
            $this->del_redis_rtb_config();
            $config_id = empty($this->post["config_id"])?$planinfo["config_id"]:$this->post["config_id"];
            
            //记录后台管理员操作日志
            $referer = $_SERVER['HTTP_REFERER'];
            $title = 'RTB保存导量计划';
            $file_pre = str_replace("::", "_", __METHOD__);//过滤掉::特殊字符(否则不能创建文件）\
            $type = $file_pre;
            $snapsot_url = save_referer_page($referer, $file_pre);
            $this->userLogs(array('msg' => json_encode($this->post), 'title' => $title, 'type'=>$type,'snapshot_url'=>$snapsot_url,'update_url'=>$referer),$this->post['id']);
            
            $this->redirect('/rtb/planview?config_id='.$config_id."&config_name=".$this->post["config_name"],'保存成功');
        } catch (Exception $exc) {
            $this->redirect('javascript:history.go(-1)','保存失败');
        } 
    }
    /*
     * 删除导量计划
     */
    public function plandel(){
        # START 检查权限
        if (!$this->checkPermission(RTB, RTB_PLAN_EDIT)) {
            $this->displayNoPermission();
        }
        # END 检查权限
        if(empty($this->get["id"])){
            $this->redirect('javascript:history.go(-1)','参数不正确');
        }
        try{
            $plan=Doo::loadModel("datamodel/rtb/RtbPlan",True);
            $whereArr["id"]=$this->get["id"];
            $whereArr["del"]=0;
            $whereArr["oprator"]=$this->data['session']["username"];
            $result=$plan->upd($whereArr);
            
            //删除redis里面缓存的RTB配置
            $this->del_redis_rtb_config();
            
            //记录后台管理员操作日志
            $this->userLogs(array('msg' => json_encode(array('id' => $this->get["id"])), 'title' => 'RTB删除导量计划', 'action' => 'delete'));
            
            $this->redirect('javascript:history.go(-1)','删除成功');
        } catch (Exception $exc) {
            $this->redirect('javascript:history.go(-1)','删除失败');
        }
    }
    //*****************************************导量计划结束******************************************************//
    
    
    /**
     * 删除redis里面缓存的RTB配置
     * @return boolean
     */
    public function del_redis_rtb_config(){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_5');
        // 删除Redis
        foreach (Doo::conf()->RTB_MOBER as $k=>$v){
            $redis->delete("RTB_CONFIG_".$v."_1");
            $redis->delete("RTB_CONFIG_".$v."_2");
        }
        return true;
    }
    
    /**
     * 删除redis里面缓存的RTB黑名单配置
     * @return boolean
     */
    public function del_redis_rtb_blacklist(){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('CACHE_REDIS_SERVER_5');
        // 删除Redis
        $redis->delete("RTB_BLACKLIST");
        return true;
    }
    
    /**
     * 把手机品牌含有多个英文KEY的手机品牌合并
     * @param type $mobile_make_arr
     * @return string
     */
    public function process_mobile_make_multikey(&$mobile_make_arr){
        if(in_array('bbk', $mobile_make_arr) || in_array('vivo', $mobile_make_arr)){
            $mobile_make_arr[] = 'bbk,vivo';
        }
        if(in_array('coolpad', $mobile_make_arr) || in_array('yulong', $mobile_make_arr)){
            $mobile_make_arr[] = 'coolpad,yulong';
        }
        if(in_array('spread', $mobile_make_arr) || in_array('sprd', $mobile_make_arr)){
            $mobile_make_arr[] = 'spread,sprd';
        }
        return $mobile_make_arr;
    }
    
    /**
     * 把网络类型含有多个英文KEY的网络类型合并
     * @param string $net_type
     * @return string
     */
    public function process_net_enviroment_multikey(&$net_type){
        if(in_array(0, $net_type) || in_array(2, $net_type)){
            $net_type[] = '0,2';
        }
        return $net_type;
    }
    
    /**
     * 获取指定广告商的价格倍数。
     * @param type $mober　AMAX ADVIEW
     * @return type
     */
    public function get_rtb_price_double_by($mober){
        return Doo::conf()->RTB_PRICE_DOUBLE[$mober]; 
    }
    
    /**
     * 去空值
     * @param type $arr
     * @return type
     */
    public function remove_empty_item(&$arr){
        foreach($arr as $key=>$item){
            if(empty($item)){
                unset($arr[$key]); 
            }
        }
        return $arr;
    }
    
    
}
?>
