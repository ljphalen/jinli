<?php
Doo::loadModel('datamodel/base/RtbConfigBase');

class RtbConfig extends RtbConfigBase{
    
    /**
     * 根据关键字，导量状态，平台进行搜索。导量状态需要从rtb_plan表中获取，关键字搜索是多个维度的搜索:配置项id,配置项名称，产品名搜索
     * @param type $search_keyword
     * @param type $state
     * @param type $platform
     * @return string
     */
    public function getListBySearch($search_keyword=null, $state, $platform, $mober){
        $return = array();
        $return['total'] = 0;
        $return['list'] = array();
        
        $where = ' 1 = 1 and rtb_config.del !=0 ';
         
        if(!empty($platform) && in_array($platform, array(1, 2))){
            $where .= ' and rtb_config.platform="'.$platform.'" ';
        }
        
        if(!empty($mober) && in_array($mober, Doo::conf()->RTB_MOBER)){
            $where .= ' and rtb_config.mober="'.$mober.'" ';
        }
        
        //获取指定条件的rtb_config列表
        $configListSql = ' select rtb_config.*, ad_product_info.product_name from rtb_config left join ad_product_info on rtb_config.product_id= ad_product_info.id where'. $where ;
        $configListResult=Doo::db()->query($configListSql)->fetchAll();
        $configIdsArr = array();
        foreach($configListResult as $key=>$configItem ){
            $configIdsArr[] = $configItem['id'];
        }

        if(empty($configIdsArr)){
            return $return;
        }

        //从rtb_plan里面获取rtb_config的状态 state = 2 未导量， state = 1  导量中
        $rtbStateArr = array();
        $rtbStatSql = ' select * from rtb_plan where config_id in ('.  implode(',', $configIdsArr).')';
        $planResult=Doo::db()->query($rtbStatSql)->fetchAll();
        foreach($planResult as  $key=>$planItem){
            //标识导量中状态的rtb配置项
            if($planItem['runstatus'] == 2 && $planItem['state'] == 1){
                $rtbStateArr[$planItem['config_id']] = 1;
            }
        }

        //附上是否导量中状态和平台展示
        foreach($configListResult as $key=>$configItem){
            if(isset($rtbStateArr[$configItem['id']]) && $rtbStateArr[$configItem['id']] == 1){
                $configListResult[$key]['state'] = 1;
            }else{
                $configListResult[$key]['state'] = 2;   
            }
            //新增平台展示
            $platformStr  = $configItem['platform'] == 1?"Android":"IOS";
            $platformStr .= "-" . $configItem['mober'];
            $configListResult[$key]['platformStr'] = $platformStr;
        }

        //列表排序先按“导量状态”排序（导量中>未导量），再按平台排序，先android,后ios，再按“导量产品”升序排序(产品名)，最后按配置ID降序排序（新增的配置排列在上面）
        $sort_state = array();
        $sort_platform = array();
        $sort_productname = array();
        $sort_configid = array();
        $sort_createdate=array();
        foreach($configListResult as $key=>$configItem){
            $sort_state[] = $configItem['state'];
            $sort_platform[] = $configItem['platform'];
            $sort_productname[] = $configItem['product_name'];
            $sort_configid[] = $configItem['id'];
            $sort_createdate[] = $configItem['createdate'];
        }
        array_multisort($sort_state, SORT_ASC, $sort_platform, SORT_ASC,$sort_createdate,SORT_DESC, $sort_productname, SORT_STRING, $sort_configid, SORT_DESC, $configListResult);

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

        $return = array();
        $return['total'] = count($result);
        $return['list'] = $result;
        return $return;
    }
    
    /**
     * 新增RTB导量配置
     * @param type $config_name
     * @param type $product_id
     * @param type $platform
     * @param type $mober
     * @param type $operator
     */
    public function addConfig($config_name, $product_id, $platform, $mober, $operator, $channel){
        $this->config_name = $config_name;
        $this->product_id = $product_id;
        $this->platform = $platform;
        $this->mober = $mober;
        $this->operator = $operator;
        $this->channel_id = $channel;
        $this->createdate = time();
        $this->updatedate = time();
        return $this->insert();
    }
    
    /**
     * 软删除配置项
     * @param type $configid
     * @return type
     */
    public function delConfig($configid){
        $updateSql = "update rtb_config set del=0 where id=".$configid. ' limit 1';
        return Doo::db()->query($updateSql);    
    }
    
    /**
     * 保存编辑的配置项
     * @param type $config_id
     * @param type $config_name
     * @param type $product_id
     * @param type $platform
     * @param type $mober
     * @param type $operator
     * @return type
     */
    public function editConfigSave($config_id, $config_name, $product_id, $platform, $mober, $operator, $channel){
        $updateSql = "update rtb_config set config_name ='".$config_name. "', product_id='".$product_id . "', platform='".$platform. "', mober='".$mober."', operator='".$operator."', channel_id='".$channel."', updatedate='".time()."'  where id='". $config_id."' limit 1;" ;
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
    
}