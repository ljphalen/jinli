<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2014-12-30 15:58:37
 * $Id: PushConfig.php 62100 2014-12-30 15:58:37Z hunter.fang $
 */

Doo::loadModel('datamodel/base/PushConfigBase');

class PushConfig extends PushConfigBase{
    
    /**
     * 获取导量配置下面的导量计划配置
     * @return array
     */
    public function getPushConfigPlan($configId, $planId, $cancel){
        $configId = intval($configId);
        if(empty($configId)){
            return false;
        }
        
        $configInfo = $this->getConfigInfo($configId);
        $config = array();
        if(!empty($configInfo)){
            $config['config_name'] = $configInfo['config_name'];
            $config['platform'] = $configInfo['platform'];
            $config['del'] = $configInfo['del'];
            $config['cancel'] = $cancel;
            
            //组织条件限制limit
            $con_value_arr = json_decode($configInfo['con_value'], true);
            $tmp_limit = array();
            //推送取消导量计划的操作时不需要limit的内容
            if(!$cancel){
                foreach($con_value_arr['config_value'] as $key2=>$configValueItem){
                    $tmp_limit_item = array();
                    $tmp_limit_item['type'] = $configValueItem['type'];
                    $tmp_limit_item['params'] = $configValueItem['params'];
                    $tmp_limit_item['operate'] = $configValueItem['operate'];//此处为了SDK代码和前一个版本保持一致
                    $tmp_limit_item['expected_value'] = $configValueItem['expected_value'];
                    $tmp_limit[] = $tmp_limit_item;
                }

                //添加指定的渠道限制到limit子项下
                $channel = '';
                $channelArr = json_decode($configInfo['channel_id']);
                if(!empty($channelArr)){
                    $channel = implode(',', $channelArr);
                    $tmp_limit_item = array();
                    $tmp_limit_item['type'] = 'default';
                    $tmp_limit_item['params'] = 'channel_id';
                    $tmp_limit_item['operate'] = 'in';
                    $tmp_limit_item['expected_value'] = $channel;
                    $tmp_limit[] = $tmp_limit_item;
                }
            }
            
            $config['limit'] = $tmp_limit;
            
            //组织导量计划plan
            $planModel=Doo::loadModel("datamodel/push/PushPlan",True);
            $planResult = $planModel->findOne(array('config_id'=>$configId, 'id'=>$planId));
            $tmp_plan = array();
            $tmp_plan['id']=$planResult['id'];
            $tmp_plan['start_time']=$planResult['start_time'];
            $tmp_plan['end_time']=$planResult['end_time'];
            $tmp_plan['cycle']=($planResult['go_method']==2?true:false);//go_method1为一次性导量，2为周期性导量=>cycle为true是周期性导量，false为一次性导量
            $product = $this->getConfigProduceDetail($configInfo['product_id']);
            //推送取消导量计划的操作时不需要product的内容
            if(empty($product) || $cancel){
                $tmp_plan['product'] = new stdClass();
            }else{
                $ad_info_detail = array();
                $ad_info_detail['ad_info_id'] = $product['ad_info_id'];
                $ad_info_detail['type'] = $planResult['type'];
                $ad_info_detail['ad_name'] = $product['ad_name'];
                $ad_info_detail['ad_pic_url'] = $product['ad_pic_url'];
                $ad_info_detail['ad_desc'] = $product['ad_desc'];
                $ad_info_detail['screen_ratio'] = $product['screen_ratio'];
                $ad_info_detail['show_detail'] = $product['show_detail'];
                $ad_info_detail['ad_click_type_object'] = $product['ad_click_type_object'];
                $ad_info_detail['ad_target'] = $product['ad_target'];
                $product['ad_info_detail'] = $ad_info_detail;
                unset($product['ad_info_id']);
                unset($product['type']);
                unset($product['ad_name']);
                unset($product['ad_pic_url']);
                unset($product['ad_desc']);
                unset($product['screen_ratio']);
                unset($product['show_detail']);
                unset($product['ad_click_type_object']);
                unset($product['ad_target']);
                $tmp_plan['product'] = $product;
            }
            $config['plan'] = $tmp_plan;

        }
        
        $pkgs = '';
        $pkgs_arr = $this->getAllAppPackagenames();
        //去除屏蔽的包名
        if(!empty($configInfo['packagename'])){
            $pkgArr = json_decode($configInfo['packagename']);
            $pkgs_arr = array_diff($pkgs_arr, $pkgArr);
        }
        //去重
        $pkgs_arr = array_unique($pkgs_arr);
        //去空
        $packagesArr = array();
        if($pkgs_arr){
            foreach($pkgs_arr as $key=>$item){
                if($item){
                    $packagesArr[] = $item;
                }
            }
        }
        $pkgs = implode(',', $packagesArr);
        
        //配置类型  type:0/1/2  分别代表广告推送消息、全局配置、全局产品权重配置
        $extra = array();
        $extra['type'] = 0;
        $extra['config'] = $config;
        
        //push格式
        $pushConfig = array();
        $pushConfig['pkg'] = $pkgs;
        $pushConfig['key'] = 'dfv4tdfq34gd';
        $pushConfig['type'] = 1;
        $pushConfig['start_time'] = $tmp_plan['start_time']*1000;
        $pushConfig['end_time'] = $tmp_plan['end_time']*1000;
        $pushConfig['important'] = 1;
        $pushConfig['extra'] = json_encode($extra);
        //接收端是java,所以需要加http_build_query
//        print_r($pushConfig);
        $pushConfig = http_build_query($pushConfig);
        return $pushConfig;
    }
    
    /**
     * 把周期性导量计划拆成当天的导量计划并推送
     * @param type $configId
     * @param type $planId
     * @param type $cancel
     * @return boolean
     */
    public function getPushConfigCyclePlan($configId, $planId, $cancel){
        $configId = intval($configId);
        if(empty($configId)){
            return false;
        }
        
        $configInfo = $this->getConfigInfo($configId);
        
        $config = array();
        if(!empty($configInfo)){
            $config['config_name'] = $configInfo['config_name'];
            $config['platform'] = $configInfo['platform'];
            $config['del'] = $configInfo['del'];
            $config['cancel'] = $cancel;
            
            //组织条件限制limit
            $con_value_arr = json_decode($configInfo['con_value'], true);
            $tmp_limit = array();
            foreach($con_value_arr['config_value'] as $key2=>$configValueItem){
                $tmp_limit_item = array();
                $tmp_limit_item['type'] = $configValueItem['type'];
                $tmp_limit_item['params'] = $configValueItem['params'];
                $tmp_limit_item['operate'] = $configValueItem['operate'];//此处为了SDK代码和前一个版本保持一致
                $tmp_limit_item['expected_value'] = $configValueItem['expected_value'];
                $tmp_limit[] = $tmp_limit_item;
            }
            
            //添加屏蔽渠道限制到limit子项下
            $channel = '';
            $channelArr = json_decode($configInfo['channel_id']);
            if(!empty($channelArr)){
                $channel = implode(',', $channelArr);
                $tmp_limit_item = array();
                $tmp_limit_item['type'] = 'default';
                $tmp_limit_item['params'] = 'channel_id';
                $tmp_limit_item['operate'] = 'not in';
                $tmp_limit_item['expected_value'] = $channel;
                $tmp_limit[] = $tmp_limit_item;
            }
            
            $config['limit'] = $tmp_limit;
            
            //组织导量计划plan
            $planModel=Doo::loadModel("datamodel/push/PushPlan",True);
            $planResult = $planModel->findOne(array('config_id'=>$configId, 'id'=>$planId));

            //只处理周期性导量计划
            if($planResult['go_method']!=2){
                return false;
            }

            $tmp_plan = array();
            $tmp_plan['id']=$planResult['id'];
            //把周期性导量计划的时间分拆成今天的时间。
            $start_h = date("H",$planResult["start_time"]);
            $start_m = date("i",$planResult["start_time"]);
            $start_s = date("s",$planResult["start_time"]);
            $end_h = date("H", $planResult["end_time"]);
            $end_m = date("i", $planResult["end_time"]);
            $end_s = date("s", $planResult["end_time"]);
            $r_start_time = strtotime(date("Y-m-d")." ". $start_h.":". $start_m. ":". $start_s);
            $r_end_time = strtotime(date("Y-m-d")." ". $end_h.":". $end_m. ":". $end_s);
            $tmp_plan['start_time']=$r_start_time;
            $tmp_plan['end_time']=$r_end_time;
            
            $tmp_plan['cycle']=($planResult['go_method']==2?true:false);//go_method1为一次性导量，2为周期性导量=>cycle为true是周期性导量，false为一次性导量
            $product = $this->getConfigProduceDetail($configInfo['product_id']);
            if(empty($product)){
                $tmp_plan['product'] = array();
            }else{
                $ad_info_detail = array();
                $ad_info_detail['ad_info_id'] = $product['ad_info_id'];
                $ad_info_detail['type'] = $planResult['type'];
                $ad_info_detail['ad_name'] = $product['ad_name'];
                $ad_info_detail['ad_pic_url'] = $product['ad_pic_url'];
                $ad_info_detail['ad_desc'] = $product['ad_desc'];
                $ad_info_detail['screen_ratio'] = $product['screen_ratio'];
                $ad_info_detail['show_detail'] = $product['show_detail'];
                $ad_info_detail['ad_click_type_object'] = $product['ad_click_type_object'];
                $ad_info_detail['ad_target'] = $product['ad_target'];
                $product['ad_info_detail'] = $ad_info_detail;
                unset($product['ad_info_id']);
                unset($product['type']);
                unset($product['ad_name']);
                unset($product['ad_pic_url']);
                unset($product['ad_desc']);
                unset($product['screen_ratio']);
                unset($product['show_detail']);
                unset($product['ad_click_type_object']);
                unset($product['ad_target']);
                $tmp_plan['product'] = $product;
            }
            $config['plan'] = $tmp_plan;

        }
        
        $pkgs = '';
        $pkgs_arr = $this->getAllAppPackagenames();
        //去除屏蔽的包名
        if(!empty($configInfo['packagename'])){
            $pkgArr = json_decode($configInfo['packagename']);
            $pkgs_arr = array_diff($pkgs_arr, $pkgArr);
        }
        //去重
        $pkgs_arr = array_unique($pkgs_arr);
        //去空
        $packagesArr = array();
        if($pkgs_arr){
            foreach($pkgs_arr as $key=>$item){
                if($item){
                    $packagesArr[] = $item;
                }
            }
        }
        $pkgs = implode(',', $packagesArr);
        
        //配置类型  type:0/1/2  分别代表广告推送消息、全局配置、全局产品权重配置
        $extra = array();
        $extra['type'] = 0;
        $extra['config'] = $config;
        
        //push格式
        $pushConfig = array();
        $pushConfig['pkg'] = $pkgs;
        $pushConfig['key'] = 'dfv4tdfq34gd';
        $pushConfig['type'] = 1;
        $pushConfig['start_time'] = $tmp_plan['start_time']*1000;
        $pushConfig['end_time'] = $tmp_plan['end_time']*1000;
        $pushConfig['important'] = 1;
        $pushConfig['extra'] = json_encode($extra);
        //接收端是java,所以需要加http_build_query
//        print_r($pushConfig);
        $pushConfig = http_build_query($pushConfig);
        return $pushConfig;
    }

    /**
     * 获取该产品的push广告及产品信息
     * @param type $productId
     */
    public function getConfigProduceDetail($productId)
    {
        $sql = 'SELECT a.id,a.product_name,a.product_icon, a.product_url,a.product_version,a.product_package,a.click_type_object,'
                . 'b.show_detail, b.ad_click_type_object, b.ad_target,'
                . 'c.ad_info_id, c.type, c.ad_name, c.ad_pic_url, c.ad_desc, c.screen_ratio,c.rate'
                . '  FROM `ad_product_info` a LEFT JOIN ad_info b ON a.`id`=b.`ad_product_id` LEFT JOIN ad_embedded_info c ON b.`id`=c.`ad_info_id` WHERE b.`type`=2 and a.id='.$productId . " and b.`state` = 1 and c.rate !=0";
        $result = Doo::db()->query($sql)->fetchAll();
        //根据分配比率随机出一个PUSH文案
        $rand_arr = array();
        foreach($result as $key=>$item){
            $rand_arr[$key] = $item['rate'] *1000;
            unset($result[$key]['rate']);
        }
        $rand_key = $this->get_rand($rand_arr);
        //从嵌入式表中获取800*480格式的图片地址
        $sql_ad_pic = 'SELECT c.ad_pic_url '
                . '  FROM `ad_product_info` a LEFT JOIN ad_info b ON a.`id`=b.`ad_product_id` LEFT JOIN ad_not_embedded_info c ON b.`id`=c.`ad_info_id` WHERE b.`type`=0 and a.id='.$productId . " and b.`state` = 1 and c.resolution='800*480' order by c.updated desc limit 1";
        $ad_pic_result =Doo::db()->query($sql_ad_pic)->fetch();
        $result[$rand_key]['ad_pic_url'] = $ad_pic_result['ad_pic_url'];
        return $result[$rand_key];
    }
    
    /**
     * 联表获取配置信息。
     * @param type $configId
     * @return type
     */
    public function getConfigInfo($configId){
        $sql = 'select a.id, a.config_name, a.product_id, a.platform, a.con_value,a.del, b.config_id, b.app_id,b.packagename, b.channel_id from push_config a left join push_limit b on a.id=b.config_id where a.id= '. $configId;
        return Doo::db()->query($sql)->fetch();
    }
    
    
    /**
     * 根据关键字，导量状态，平台进行搜索。导量状态需要从push_plan表中获取，关键字搜索是多个维度的搜索:配置项id,配置项名称，产品名搜索
     * @param type $search_keyword
     * @param type $state
     * @param type $platform
     * @return string
     */
    public function getListBySearch($search_keyword=null, $state, $platform){
        $return = array();
        $return['total'] = 0;
        $return['list'] = array();
        
        $where = ' 1 = 1 and push_config.del !=0 ';
         
        if(!empty($platform) && in_array($platform, array(1, 2))){
            $where .= ' and push_config.platform="'.$platform.'" ';
        }
        
        //获取指定条件的push_config列表
        $configListSql = ' select push_config.*, ad_product_info.product_name from push_config left join ad_product_info on push_config.product_id= ad_product_info.id where'. $where ;
        $configListResult=Doo::db()->query($configListSql)->fetchAll();
        $configIdsArr = array();
        foreach($configListResult as $key=>$configItem ){
            $configIdsArr[] = $configItem['id'];
        }

        if(empty($configIdsArr)){
            return $return;
        }

        //从push_plan里面获取push_config的状态 state = 2 未导量， state = 1  导量中
        $pushStateArr = array();
        $pushStatSql = ' select * from push_plan where config_id in ('.  implode(',', $configIdsArr).') and del = 1';
        $planResult=Doo::db()->query($pushStatSql)->fetchAll();
        foreach($planResult as  $key=>$planItem){
            //标识导量中状态的push配置项
            if($planItem['runstatus'] == 2 && $planItem['state'] == 1){
                $pushStateArr[$planItem['config_id']] = 1;
            }
        }

        //附上是否导量中状态和平台展示
        foreach($configListResult as $key=>$configItem){
            if(isset($pushStateArr[$configItem['id']]) && $pushStateArr[$configItem['id']] == 1){
                $configListResult[$key]['state'] = 1;
            }else{
                $configListResult[$key]['state'] = 2;   
            }
            //新增平台展示
            $platformStr  = $configItem['platform'] == 1?"Android":"IOS";
            $configListResult[$key]['platformStr'] = $platformStr;
        }

        //列表排序先按“导量状态”排序（导量中>未导量），再按平台排序，先android,后ios，再按“导量产品”升序排序(产品名)，最后按配置ID降序排序（新增的配置排列在上面）
        $sort_state = array();
        $sort_platform = array();
        $sort_productname = array();
        $sort_configid = array();
        foreach($configListResult as $key=>$configItem){
            $sort_state[] = $configItem['state'];
            $sort_platform[] = $configItem['platform'];
            $sort_productname[] = $configItem['product_name'];
            $sort_configid[] = $configItem['id'];
        }
        array_multisort($sort_state, SORT_ASC, $sort_platform, SORT_ASC, $sort_productname, SORT_STRING, $sort_configid, SORT_DESC, $configListResult);

        //结果集
        $result = array();
        foreach($configListResult as $key=>$configItem){
            //若有导量状态的条件限制，则根据导量中、未导量的值进行筛选
            if(!empty($state)){
                if($state == $configItem['state']){
                    $result[] =  $configItem;
                }
            }
            //没有导量状态的条件限制
            else{
               $result[] =  $configItem;
            } 
        }
        
        //若有关键字，则根据配置项id,配置项名称，产品名搜索
        if(!empty($search_keyword)){
            foreach($result as $key=>$result_item){
                if((strpos($result_item['id'],$search_keyword) === false) && (strpos($result_item['config_name'],$search_keyword) === false) && (strpos($result_item['product_name'],$search_keyword) === false)){
                    unset($result[$key]);
                }
            }
        }
        
        //新增该导量配置下面的导量计划是否在在生效中状态
        if(!empty($result)){
            $plan=Doo::loadModel("datamodel/push/PushPlan",True);
            foreach($result as $key=>$item){
                $canEdit = $plan->canEditConfig($item['id']);
                $intime = intval(!$canEdit);
                $result[$key]['intime'] = $intime;
            }
        }
        
        $return = array();
        $return['total'] = count($result);
        $return['list'] = $result;
        return $return;
    }
    
    /**
     * 新增PUSH导量配置
     * @param type $config_name
     * @param type $product_id
     * @param type $platform
     * @param type $operator
     * @param type $con_value
     * @return type
     */
    public function addConfig($config_name, $product_id, $platform, $operator, $con_value){
        $this->config_name = $config_name;
        $this->product_id = $product_id;
        $this->platform = $platform;
        $this->operator = $operator;
        $this->con_value = $con_value;
        $this->createdate = time();
        $this->updatedate = time();
        return $this->insert();
    }
    
    public function getJsonConfigValue($con_value){
        if(empty($con_value)){
            return false;
        }
        $result = array();
        $formula_desc = array();
        $formula_desc["=="]="等于";
        $formula_desc["!="]="不等于";
        $formula_desc[">"]="大于";
        $formula_desc[">="]="大于等于";
        $formula_desc["<"]="小于";
        $formula_desc["<="]="小于等于";
        $formula_desc["in"]="包含";
        $formula_desc["not in"]="不包含";
        
        $config_value = json2array($con_value);
        if(is_array($config_value)){
            $result =$config_value["config_value"];
            foreach($config_value["config_value"] as $key=>$item){
                $config_value["config_value"][$key]['operate_desc'] = $formula_desc[$item['operate']];
            }
        }
        return $config_value["config_value"];
    }
    
    /**
     * 保存push配置
     * @param type $config_id
     * @param type $config_name
     * @param type $product_id
     * @param type $platform
     * @param type $operator
     * @param type $con_value
     * @return type
     */
    public function editConfigSave($config_id, $config_name, $product_id, $platform, $operator, $con_value){
        $updateSql = "update push_config set config_name ='".$config_name. "', product_id='".$product_id . "', platform='".$platform."', operator='".$operator."', con_value='".$con_value."', updatedate='".time()."'  where id='". $config_id."' limit 1;" ;
        return Doo::db()->query($updateSql);     
    }
    
    /**
     * 软删除配置项
     * @param type $configid
     * @return type
     */
    public function delConfig($configid){
        $updateSql = "update push_config set del=0 where id=".$configid. ' limit 1';
        return Doo::db()->query($updateSql);    
    }
    
    /**
     * 获取指定ＩＤ的配置项
     * @param type $config_id
     * @return type
     */
    public function getCongfigByid($config_id){
        $configInfo = $this->getOne(array("select"=>"*","where"=>"id='".$config_id."'", "asArray"=>TRUE));
        return $configInfo;
    }
    
    /**
     * 获取pus导量的产品和配置id
     * @return type
     */
    public function getProductids(){
        $sql = ' select id as config_id, product_id from push_config where del=1 ' ;
        return Doo::db()->query($sql)->fetchAll();
    }
    
    /*
     * 根据概率返回
     * @param:array("1001"=>333,"1002"=>333,"1003"=>334),返回值 KEY,value必须大于1
     */
    protected  function get_rand($proArr) { 
        $result = ''; 
        //概率数组的总概率精度 
        $proSum = array_sum($proArr); 
        //概率数组循环 
        foreach ($proArr as $key => $proCur) { 
            $randNum = mt_rand(1, $proSum); 
            if ($randNum <= $proCur) { 
                $result = $key; 
                break; 
            } else { 
                $proSum -= $proCur; 
            } 
        } 
        unset ($proArr); 
        return $result; 
    }
    
    /**
     * 获取所有包
     * @return type
     */
    public function getAllAppPackagenames(){
        $developerModel = Doo::loadModel('Developer', TRUE);
        $where = array();
        $where["ischeck"]=1;
        $developer = $developerModel->findAll($where);
        if (empty($developer)){
            return array();
        }
        $dev_ids = array();
        foreach($developer as $key => $value){
            $dev_ids[] = $value['dev_id'];
        }
        
        $this->_appsModel = Doo::loadModel("datamodel/AdApp", TRUE);
        $appInfo = $this->_appsModel->find(array('select'=>'packagename', 'asArray'=>TRUE, 'where' => "dev_id in (".implode(',', $dev_ids).") AND state = 1 AND ischeck=1"));
        $appkey_arr = array();
        if(!empty($appInfo)){
            foreach($appInfo as $appItem){
                $appkey_arr[] = $appItem['packagename'];
            }
        }
        return $appkey_arr;
    }
    
}

