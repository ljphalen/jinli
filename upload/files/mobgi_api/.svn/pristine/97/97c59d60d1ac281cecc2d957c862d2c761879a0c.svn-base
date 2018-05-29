<?php

Doo::loadModel('datamodel/base/AdOtherConfigBase');

class AdOtherConfig extends AdOtherConfigBase {
    public function upd($data) {
        $currentUser = Doo::session()->__get('admininfo');
        $this->appkey = $data['appkey'];
        $this->channel_id = $data['channel_id'];
        $this->config_detail = $data['config_detail'];
        $this->platform = $data['platform'];
        $this->type = $data['type'];
        $this->updated = time();
        $this->operator = $currentUser['username'];
        $this->config_name = $data['config_name'];
        $key = $data['appkey'] . "_" . $data['channel_id']."_otherconfig_".$data['type'];
        $this->deleter($key, "CACHE_REDIS_SERVER_3");
        $this->set($key, $data['config_detail'], "CACHE_REDIS_SERVER_3");
        $this->created = time();
        return $this->insert();
    }
    
    /**
     * 根据平台，广告类型，结算方式获取报价信息．
     * @param type $ader
     * @param type $ad_type
     * @param type $acounting_method
     * @return boolean
     */
    public function getRtbByAder($ader, $ad_type, $acounting_method){
        if(empty($ader) || empty($acounting_method)){
            return false;
        }
        Doo::db()->reconnect('rtb');
        $whereSql = " 1=1 and ader = '".$ader ."' and ad_type='". $ad_type. "' and acounting_method = '". $acounting_method. "' ";
        $sql="select * from mobgi_rtb.money where ".$whereSql;
        $rtbMoneyInfo=Doo::db()->query($sql)->fetch();
        Doo::db()->reconnect('prod');
        return $rtbMoneyInfo;
    }
    
    /**
     * rtb数据更新
     * @param type $data
     * @return boolean
     */
    public function rtbSave($data){
        if(empty($data['price']) || empty($data['day_sale'])){
            return false;
        }Doo::db()->reconnect('rtb');
        $searchSql = "select * from mobgi_rtb.money where ader='".$data['ader']."' and ad_type='". $data['ad_type']. "' and acounting_method='". $data['acounting_method']. "'";
        $searchSqlResult=Doo::db()->query($searchSql)->fetch();
        if(empty($searchSqlResult)){
            $insertSql = "insert into mobgi_rtb.money( ader,ad_type,acounting_method,price,day_sale, created) values('".$data['ader']. "', '". $data['ad_type']."', '". $data['acounting_method']. "', '". $data['price']."', '".$data['day_sale']. "',".time().")";
            $result=Doo::db()->query($insertSql);
        }else{
            $updateSql = "update mobgi_rtb.money set price ='".$data['price']. "', day_sale='".$data['day_sale'] . "', updated=".time(). " where ader='". $data['ader']."' and ad_type='".$data['ad_type']."' and acounting_method='".$data['acounting_method']."'";
            $result=Doo::db()->query($updateSql);        
        }
        Doo::db()->reconnect('prod');
        return $result;
    }

}