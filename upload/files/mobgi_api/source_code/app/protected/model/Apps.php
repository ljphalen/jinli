<?php
/**
 * 应用中心模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class Apps extends AppModel {

    private $_appsModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_appsModel = Doo::loadModel("datamodel/AdApp", TRUE);
    }

    /**
     * 查询应用列表-多条记录
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*, case when apk_url is null then 0 else 1 end apk_url_exist';
        $whereArr['desc'] = 'apk_url_exist desc, createdate ';
        // 当存在条件时。组合条件
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_appsModel->find($whereArr);
        return $result;
    }

    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $appsmodel=Doo::loadModel("datamodel/AdApp", TRUE);
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $appsmodel->getOne($whereArr);
        return $result;
    }
    
    /**
     * 返回记录数
     * @param type $conditions
     * @return type
     */
    public function records($conditions = NULL){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_appsModel->count($whereArr);
        return $result;
    }

    /**
     * 返回开发者的应用
     */
    public function retDevelopsApp($opids){
        if (!empty($opids)){
            $appInfo = $this->_appsModel->find(array('select'=>'*', 'asArray'=>TRUE, 'where' => "dev_id in (".implode(',', $opids).") AND state = 1 AND ischeck=1"));
        }
        if (empty($appInfo)){
            return array();
        }
       $result = array();
        foreach($appInfo as $key => $value){
            $value['platform_app_name'] = $this->getPlatformCn($value['platform']) . $value['app_name'];
            $result[$key]['dev_id'] = $value;
        }
        return $result;
    }
    /**
     * 获取AppInfo
     * @param string $devid
     */
    public function getAppinfoBydevid($devid){
        $app = $this->_appsModel->find(array('asArray' => true, 'select' => '*',"where"=>"dev_id=".$devid));
        $devloper=Doo::loadModel("datamodel/AdDeveloper", TRUE);
        $devloperinfo=$devloper->getOne(array('asArray' => true, 'select' => '*',"where"=>"dev_id=".$devid));
        foreach($app as $k=>$a){
            $pos=$this->getPosinfoCountState($devid,$a["app_id"]);
            $app[$k]["pos"]=$this->getPosCountStr($pos);
            $app[$k]["devloper"]=$devloperinfo;
        }
        return $app;
    }
    /**
     * 获取广告位开启关闭汇总信息
     * @param array $param
     * **/
    public function getPosCountStr($param){
        $str="";
        $str.="<font color='blue'><b>".$param["open"]."</b></font>"."个广告位<u>开启</u><br>";
        $str.="<font color='blue'><b>".$param["close"]."</b></font>"."个广告位<u>关闭</u>";
        return $str;
    }
    /*
     * 根据开发者id,或者应用id获取广告位开启关闭信息
     */
    public function getPosinfoCountState($dev_id=NULL,$app_id=NULL){
        $pos=Doo::loadModel("datamodel/AdDeverPos", TRUE);
        $where="1=1";
        if(!empty($dev_id)){
            $where.=" and dev_id=".$dev_id;
        }
        if(!empty($app_id)){
            $where.=" and app_id=".$app_id;
        }
        $close=$pos->getOne(array('asArray' => true, 'select' => 'count(*) as count',"where"=>$where." and state=0"));
        $open=$pos->getOne(array('asArray' => true, 'select' => 'count(*)  as count',"where"=>$where." and state=1"));
        
        return array("close"=>$close["count"],"open"=>$open["count"]);
    }
    /*
     * 根据开发者id,或者应用id获取广告位信息
     */
    public function getPosinfo($dev_id,$app_id=NULL){
        $pos=Doo::loadModel("datamodel/AdDeverPos", TRUE);
        //del=-1的广告位是已经被删除的广告位(软删除)
        $where="dev_id=".$dev_id . " and del != -1" ;
        if(!empty($app_id)){
            $where.=" and app_id=".$app_id;
        }     
        $result=$pos->find(array('asArray' => true, 'select' => '*',"where"=>$where));
        return $result;
    }
    /**
     * 获取AppInfo
     * @param string $appkey
     */
    public function getAppInfo($params){
        $app = $this->_appsModel->find(array('asArray' => true, 'select' => '*'));
        $appList = listArray($app, 'appkey', 'app_name');
        $versionInfo = array();
        foreach ($app as $val) {
            $versionInfo[$val['appkey']]['app_name'] = $val['app_name'];
        }
        // 取版本&应用
        $AppVersionModel = Doo::loadModel("datamodel/AdAppVersion", TRUE);
        $appVersion = $AppVersionModel->find(array('asArray' => true));

        foreach ($appVersion as $appInfo) {
            $versionInfo[$appInfo['appkey']]['app_version'][] = $appInfo['app_version'];
        }
        // 取产品信息
        $ProductModel = Doo::loadModel("datamodel/AdProductInfo",TRUE);
        $productInfo = listArray($ProductModel->find(array('asArray' => true)), 'id', 'product_name');
        $AdConfigModel = Doo::loadModel("datamodel/AdConfig",TRUE);
        $adConfigInfo = $AdConfigModel->find(array('asArray' => true));
        $adConfigIdArr = array();
        foreach ($adConfigInfo as $adConfig) {
            $product_comb = json_decode($adConfig['product_comb'], true);
            if (empty($product_comb['products'])) {
                continue;
            }
            foreach ($product_comb['products'] as $product) {
                $adConfigIdArr[$adConfig['id']][$product['productid']] = $productInfo[$product['productid']];
            }
        }
        // 取渠道 和 应用 产品 关系
        $AdConfigTagsModel = Doo::loadModel("datamodel/AdConfigTags",TRUE);
        $adConfigId = array_keys($adConfigIdArr);
        $whereArr = array('asArray' => true, 'where' => "ad_config_id in (".implode(',', $adConfigId).")");
        $adConfigInfo = $AdConfigTagsModel->find($whereArr);
        $adappkey =  array();
        foreach ($adConfigInfo as $adConfig){
            if ($adConfig['type'] == 1) {
                $adappkey[$adConfig['type_value']][] = $adConfig['ad_config_id'];
            }
        }
        // 组装最终数据
        $result = array();
        foreach ($adappkey as $appkey => $value) {
            $result[$appkey]['app_name'] = $versionInfo[$appkey]['app_name'];
            if (in_array('app_version', $params)) {
                $result[$appkey]['app_version'] = $versionInfo[$appkey]['app_version'];
            }
            if (in_array('product', $params)) {
                $result[$appkey]['product'] = array();
            }
            if (in_array('channel', $params)) {
                $result[$appkey]['channel'] = array();
            }
            foreach ($value as $id) {
                if (in_array('product', $params)) {
                    $result[$appkey]['product'] += $adConfigIdArr[$id];
                }
                if (in_array('channel', $params)) {
                    $result[$appkey]['channel'] = Doo::conf()->report_channel_conf;
                }
            }
        }
        return $result;
    }
    /**
     * 删除指定广告位
     **/
    public function del_app_pos($pos_key){
        if(empty($pos_key)){
            return false;
        }
        $sql="update ad_dever_pos set del = -1 where dever_pos_key='".$pos_key."'";
        Doo::db()->query($sql)->execute();;
        return true;
    }
    /**
     * 添加应用
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->_appsModel->app_name = $data['app_name'];
        $this->_appsModel->platform = $data['platform'];
        $this->_appsModel->appcate_id = $data['appcate_id'];
        $this->_appsModel->state = $data['state'];
        $this->_appsModel->dev_id = $data['dev_id'];
        $this->_appsModel->app_desc = $data['app_desc'];
        $this->_appsModel->appkey = $data['appkey'];
        $this->_appsModel->packagename = $data['packagename'];
        $this->_appsModel->income_rate = $data['income_rate'];
        $this->_appsModel->updatedate = time();
        $this->_appsModel->operator = $currentUser['username'];
        if(empty($id)){
            $this->_appsModel->createdate = time();
            return $this->_appsModel->insert();
        }else{
            $this->_appsModel->app_id = $id;
            $this->_appsModel->update();
            return $id;
        }
    }
    
    /**
     * 更新应用状态
     * @param type $data
     */
    public function updateAppState($appid, $state){
        $currentUser = Doo::session()->__get('admininfo');
        $this->_appsModel->state = $state;
        $this->_appsModel->updatedate = time();
        $this->_appsModel->operator = $currentUser['username'];
        $this->_appsModel->app_id = $appid;
        $this->_appsModel->update();
        return $appid;
    }

    public function ischeck($data){
        $this->_appsModel->ischeck=$data["ispass"];
        $this->_appsModel->income_rate=$data["income_rate"];
        $this->_appsModel->check_msg=$data["msg"];
        $this->_appsModel->app_id = $data['app_id'];
        $this->_appsModel->state = $data['state'];
        if(is_array($data["pos_key"]) && !empty($data["pos_key"])){
            foreach($data["pos_key"] as $k=>$v){
                $pos=Doo::loadModel("datamodel/AdDeverPos",TRUE);
                $pos->state=$data["pos_state"][$k];
                $pos->acounting_method=$data["acounting_method"][$k];
                $pos->denominated=$data["denominated"][$k];
                $pos->dever_pos_key=$v;
                $pos->id=$data["pos_id"][$k];
                $pos->updatetime=time();
                $pos->update();
            }
        }
        return $this->_appsModel->update();
    }
    
    /**
     * 删除应用
     * @param int $appId
     * @return boolean
     */
    public function del($appId){
        $this->_appsModel->app_id = $appId;
        $this->del_posbyapp($appId);
        return $this->_appsModel->delete();
    }
    /**
     * 根据应用删除广告位
     * **/
    public function del_posbyapp($appid){
//        $sql="delete from ad_dever_pos where app_id='".$appid."'";
        //广告位软删除
        $sql="update ad_dever_pos set del=-1 where app_id='".$appid."'";
        return Doo::db()->query($sql)->execute();
    }
    /*
     * 更新广告位
     */
    public function upd_pos($id=NULL,$data){
        $pos=Doo::loadModel("datamodel/AdDeverPos",TRUE);
        $pos->pos_key=$data["pos_key_type"];
        $pos->dever_pos_name=$data["pos_name"];
        $pos->acounting_method=$data["acounting_method"];
        $pos->denominated=$data["denominated"];
        $pos->rate=$data["rate"];
        $pos->state=$data["state"];
        $pos->app_id=$data["app_id"];
        $pos->dev_id=$data["dev_id"];
        $pos->dever_pos_key=$data["pos_key"];
        $this->deleter($data["pos_key"],"CACHE_REDIS_POS_KEY");
        if(empty($id)){
            $pos->createdate=time();
            return $pos->insert();
        }else{
            $pos->id=$id;
            $pos->updatetime=time();
            return $pos->update();
        }
    }
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return "1=1";
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        if (isset($conditions['app_id']) && $conditions['app_id']){
            $where[] = "app_id = ".$conditions['app_id'];
        }
        if (isset($conditions['app_name']) && $conditions['app_name']){
            $where[] = "app_name LIKE '%".$conditions['app_name']."%'";
        }
        if (isset($conditions['platform']) && $conditions['platform']){
            $where[] = "platform = ".$conditions['platform'];
        }
        if (isset($conditions['appcate_id']) && $conditions['appcate_id']){
            $where[] = "appcate_id = ".$conditions['appcate_id'];
        }
        if(isset($conditions['state'])){
            $where[] = "state = ".$conditions['state'];
        }
        if(isset($conditions['dev_id']) && $conditions['dev_id']){
            $where[] = "dev_id = ".$conditions['dev_id'];
        }
        if(isset($conditions['screatedate']) && $conditions['screatedate']){
            $where[] = "createdate >= '".$conditions['screatedate']."'";
        }
        if(isset($conditions['appdate']) && $conditions['appdate']){
            $where[] = "createdate >= UNIX_TIMESTAMP('".$conditions['appdate']."')";
        }
        if(isset($conditions['ecreatedate']) && $conditions['ecreatedate']){
            $where[] = "createdate < '".$conditions['ecreatedate']."'";
        }
        if(isset($conditions['supdatedate']) && $conditions['supdatedate']){
            $where[] = "updatedate >= '".$conditions['supdatedate']."'";
        }
        if(isset($conditions['eupdatedate']) && $conditions['eupdatedate']){
            $where[] = "updatedate < '".$conditions['eupdatedate']."'";
        }
        if(isset($conditions['operator']) && $conditions['operator']){
            $where[] = "operator LIKE '%".$conditions['operator']."%'";
        }
        if(isset($conditions['ischeck']) && $conditions['ischeck']){
            $where[] = "ischeck = ".$conditions['ischeck'];
        }
        if(isset($conditions['no_apk_url']) && $conditions['no_apk_url']){
            $where[] = "apk_url !=''";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    public function getPlatformCn($platform){
        if($platform=='' || $platform=='0'){
            return "(T)";
        }else if($platform == "1"){
            return '(A)';
        }else if($platform == '2'){
            return '(I)';
        }
    }
    
}