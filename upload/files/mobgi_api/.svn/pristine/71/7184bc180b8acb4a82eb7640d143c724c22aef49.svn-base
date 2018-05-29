<?php
Doo::loadModel('datamodel/base/VideoAdsComBase');

class VideoAdsCom extends VideoAdsComBase{
    
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
        $where=array('select'=>'*',
                     'where'=>'id="'.$appid.'"',
                     'asArray' => TRUE);
        $lists=$this->getOne($where);
        return $lists;
    }
    
    /**
     * 根据任意条件返回一条记录
     * @param unknown $params
     */
    public function getBy($params){
        if(!is_array($params)){
            return false;
        }
        $where = $this->sqlWhere($params);  
        $options=array('select'=>'*',
                       'where'=>$where,
                       'asArray' => TRUE);
        $result=$this->getOne($options);;
        return $result;
    }
    
    
    /**
     * 条件构造
     * @param Array/String $conditions
     * @return SQLblock where conditions
     */
    private function _conditions($conditions = NULL){
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        $where[] = 'del = 1';
        if (isset($conditions['name']) && $conditions['name']){
            $where[] = "name LIKE '%".$conditions['name']."%'";
        }
        if (isset($conditions['identifier']) && $conditions['identifier']){
            $where[] = "identifier =".$conditions['identifier']."";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
    }
   
    
    /**
     * 添加或更新广告商信息
     * @param type $data
     */
    public function upd($id = NULL, $data){
    	$this->identifier = $data['identifier'];
    	$this->name = $data['name'];
    	$this->settlement_method = $data['settlement_method'];
    	$this->settlement_price = $data['settlement_price'];
    	$this->type = $data['type'];
    	$this->updatetime = time();
    	if(empty($id)){
    		$this->createtime = time();
    		return $this->insert();
    	}else{
    		$this->id = $data['id'];
    		$this->update();
    		return $id;
    	}
    }
    
    /**
     * 软删除广告商
     * @param type $publishid
     */
    public function softDelApp($id){
    	$this->updatetime=time();
    	$this->id=$id;
    	$this->del=0;
    	return $this->update();
    }
    
    public function delById($id){   
        $this->id=$id;
        $this->delete();
        return true; 
    }
    
}