<?php
Doo::loadModel('datamodel/base/PolymericAdBase');

class PolymericAd extends PolymericAdBase{
    /**
     * 构造函数重连db
     * @param type $properties
     */
    public function __construct($properties = null) {
        
        parent::__construct($properties);
        
    }
    
    /**
     * 返回记录数
     * @param type $conditions
     * @return type
     */
    public function getRecords($conditions = NULL){
        if(!is_array($conditions)){
            return false;
        } 
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';      
        $whereArr['where'] = $this->sqlWhere($conditions);
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
        $whereArr['desc'] = 'updateTime';
        // 当存在条件时。组合条件
        if (is_array($conditions) && isset($conditions['limit'])){
            $whereArr['limit'] = $conditions['limit'];
            unset($conditions['limit']);
        }
        $whereArr['where'] = $this->sqlWhere($conditions);
      
        $result = $this->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function getBy($params){
        if(!is_array($params)){
            return false;
        }
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['where'] = $this->sqlWhere($params);
        $result = $this->getOne($whereArr);
        return $result;
    }
    
    
    /**
     * 查询多条记录
     * @param Array $conditions
     */
    public function getsBy($params){
        if(!is_array($params)){
            return false;
        }
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*';
        $whereArr['desc'] = 'updateTime';
        $whereArr['where'] = $this->sqlWhere($params);
        $result = $this->find($whereArr);
        return $result;
    }
    /**
     * 返回某条信息
     * @param type $appid
     * @return type
     */
    public function getById($appid=null){
        $where   = array('select'=>'*',
                         'where'=>'id="'.$appid.'"',
                         'asArray' => TRUE );
        $result  = $this->getOne($where);
        return $result;
    }

    /**
     * 添加或更新信息
     * @param type $data
     */
    public function upd($id = NULL, $data){
    	$this->conf_desc = $data['conf_desc'];
    	$this->name = $data['name'];
    	$this->platform = $data['platform'];
    	$this->app_key = $data['app_key'];
    	$this->position_conf = $data['position_conf'];
    	$this->updatetime = time();
    	$this->secret_key = $data['secret_key'];
    	$this->third_party_appkey    = $data['third_party_appkey'];
    	
    	if(empty($id)){
    		$this->createtime = time();
    		return $this->insert();
    	}else{
    		$this->id = $id;
    		$this->update();
    		return $id;
    	}
    }
    
    public function delById($id){
        $this->id = $id;
        $this->delete();
        return true;
    }
    
}