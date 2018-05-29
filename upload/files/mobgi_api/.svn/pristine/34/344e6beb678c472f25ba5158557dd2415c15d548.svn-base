<?php
/**
 * Admin模型
 *
 * @author Intril.Leng
 */
Doo::loadModel('AppModel');
class AdGameConfigs extends AppModel {
        
    private $_adGameConfigsModel;

    public function __construct($properties = null) {
        parent::__construct($properties);
        $this->_adGameConfigsModel = Doo::loadModel("datamodel/AdGameConfig", TRUE);
    }
    
    /**
     * 查询开发者列表
     * @param type $conditions
     */
    public function findAll($conditions = NULL){
        // 默认为没有传条件，取所有数据
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        // 当存在条件时。组合条件
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->_conditions($conditions);
        $result = $this->_adGameConfigsModel->find($whereArr);
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
        $adGameConfigsModel=Doo::loadModel("datamodel/AdGameConfig", TRUE);
        $result = $adGameConfigsModel->getOne($whereArr);
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
        $result = $this->_adGameConfigsModel->count($whereArr);
        return $result;
    }
    
    /**
     * 添加/修改开发者
     * @param type $data
     */
    public function upd($id = NULL, $data){
        $currentUser = Doo::session()->__get('admininfo');
        $this->_adGameConfigsModel->appkey = $data['appkey'];
        $this->_adGameConfigsModel->channel_id = $data['channel_id'];
        $this->_adGameConfigsModel->config_detail = $data['config_detail'];
        $this->_adGameConfigsModel->updated = time();
        $this->_adGameConfigsModel->operator = $currentUser['username'];
        $this->_adGameConfigsModel->config_name = $data['config_name'];
        
        $key=$data['appkey']."_".$data['channel_id']."_GAMECONFIG";
        $this->deleter($key,"CACHE_REDIS_SERVER_2");
        $this->set($key,$data['config_detail'],"CACHE_REDIS_SERVER_2");
        if(empty($id)){
            $this->_adGameConfigsModel->created = time();
            return $this->_adGameConfigsModel->insert();
        }else{
            $this->_adGameConfigsModel->id = $id;
            return $this->_adGameConfigsModel->update();
        }
    }
    public function delapp($post){//删除指定appkey的配置
        $checkis=$this->channel_is_exsit($post);
        if(!empty($checkis) && $checkis!=false){
            return false;
        }
        $sql="delete from ad_game_config where appkey='".$post["appkey"]."' and config_name='".$post["config_name"]."'";
        Doo::db()->query($sql)->execute();
        return true;
    }
    public function channel_is_exsit($post){
        $adgameconfig=Doo::loadModel("datamodel/AdGameConfig", TRUE);
        $result=$adgameconfig->getOne(array("select"=>"*","where"=>"appkey='".$post["appkey"]."' and config_name!='".$post["config_name"]."' and channel_id in('".implode("','",$post["channel_id"])."')","asArray"=>true));
        return $result;
    }


    /**
     * 删除开发者
     * @param int $devId
     * @return boolean
     */
    public function del($devId){
        $this->_developerModel->dev_id = $devId;
        return $this->_developerModel->delete();
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
        if (isset($conditions['id']) && $conditions['id']){
            $where[] = "id = ".$conditions['id'];
        }
        if (isset($conditions['appkey']) && $conditions['appkey']){
            $where[] = "appkey = '".$conditions['appkey']."'";
        }
        if (isset($conditions['channel_id']) && $conditions['channel_id']){
            $where[] = "channel_id = '".$conditions['channel_id']."'";
        }
        if (isset($conditions['config_name']) && $conditions['config_name']){
            $where[] = "config_name = '".$conditions['config_name']."'";
        }
        if(isset($conditions['screated']) && $conditions['screated']){
            $where[] = "created >= '".$conditions['screated']."'";
        }
        if(isset($conditions['ecreated']) && $conditions['ecreated']){
            $where[] = "created < '".$conditions['ecreated']."'";
        }
        if(isset($conditions['supdated']) && $conditions['supdated']){
            $where[] = "updated >= '".$conditions['supdatedate']."'";
        }
        if(isset($conditions['eupdated']) && $conditions['eupdated']){
            $where[] = "updated < '".$conditions['eupdated']."'";
        }
        if(isset($conditions['operator']) && $conditions['operator']){
            $where[] = "operator LIKE '%".$conditions['operator']."%'";
        }
        $whereSQL = implode(" AND ", $where);
        if(isset($conditions['groupby']) && $conditions['groupby']){
            $whereSQL.= " group by ".$conditions['groupby']."";
        }
        return $whereSQL;
    }
    public function getChannelidInfo($channel_id){
        return $this->_getChannelById($channel_id);
    }
}

?>
