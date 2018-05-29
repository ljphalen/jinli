<?php
Doo::loadModel('datamodel/base/IptadAppBase');

class IptadApp extends IptadAppBase{
    
    /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('implantable');
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
        $result = $this->count($whereArr);
        return $result;
    }
    
    /**
     * 查询应用列表-多条记录
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['desc'] = 'updatetime';
        // 当存在条件时。组合条件
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->getOne($whereArr);
        return $result;
    }
    
    /**
     * 返回某条应用信息
     * @param type $appid
     * @return type
     */
    public function view($appid=null){
        $where=array('select'=>'*','where'=>'id="'.$appid.'"','asArray' => TRUE);
        $lists=$this->getOne($where);
        return $lists;
    }
    
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if (empty($conditions)){
            return "del=1";
        }
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        $where[] = 'del = 1';
        if (isset($conditions['appname']) && $conditions['appname']){
            $where[] = "appname LIKE '%".$conditions['appname']."%'";
        }
        if (isset($conditions['full_appname']) && $conditions['full_appname']){
            $where[] = "appname = '".$conditions['full_appname']."'";
        }
        if (isset($conditions['id']) && $conditions['id']){
            $where[] = "id = '".$conditions['id']."'";
        }
        if (isset($conditions['appkey']) && $conditions['appkey']){
            $where[] = "appkey = '".$conditions['appkey']."'";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
    
    /**
     * 更新应用状态
     * @param type $data
     */
    public function updateAppState($appkey, $state){
        $currentUser = Doo::session()->__get('admininfo');
        $this->state = $state;
        $this->updatetime = time();
        $this->oprater = $currentUser['username'];
        $this->appkey = $appkey;
        $this->update();
        return $appkey;
    }
    
    /**
     * 添加应用
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->appkey = $data['appkey'];
        $this->packname = $data['packname'];
        $this->appname = $data['appname'];
        $this->platform = $data['platform'];
        $this->oprater = $currentUser['username'];
        $this->updatetime = time();
        
        if(empty($id)){
            $this->createtime = time();
            return $this->insert();
        }else{
            $this->appkey = $data['appkey'];
            $this->update();
            return $id;
        }
    }
    
    /**
     * 软删除客户名
     * @param type $publishid
     */
    public function softDelApp($appkey){
        $this->updatetime=time();
        $this->appkey=$appkey;
        $this->del=0;
        return $this->update();
    }
    /**
     * 更新,删除应用时删除对应应用APPKEY的key redis中的值
     * @return boolean
     */
    public function del_appcache_by_appkey($appkey){
        Doo::loadClass("Fredis/FRedis");
        $redis = FRedis::getSingletonPort('IMPLANTABLE_REDIS_CACHE_DEFAULT');
        // 删除Redis
        $redis->delete($appkey);
        return true;
    }
    
    //关闭应用时，关掉该应用下的所有广告类型，同时关闭把所有广告类型下的所有素材全部关闭
    public function setBlockOffAndSourceOffByAppkey($appkey){
        //关掉该应用下的所有广告类型
        Doo::db()->query("update iptad_block set status=0 where appkey='".$appkey."'");
        //同时关闭把所有广告类型下的所有素材全部关闭
        $blockModel = Doo::loadModel("datamodel/implantable/IptadBlock", TRUE);
        $sourceModel = Doo::loadModel("datamodel/implantable/IptadSource", TRUE);
            
        $tmpBlockData = $blockModel->getBlocksByAppkey($appkey);
        if(!empty($tmpBlockData)){
            foreach($tmpBlockData as $blockinfo){
                $sourceModel->setSourceOffByBlockkey($blockinfo['blockkey']);
            }
        }
        //广告类型:新增,修改,删除时删除对应应用的block key  "BLOCK_" .$appkey;
        $blockModel->del_blockcache($appkey);
        //素材:新增,修改,删除操作删除对应的redis key  "SOURCE_".  implode("_",$id); "SOURCE_".$id;
        $sourceModel->del_sourcecache();
        return true;
    }
    
}