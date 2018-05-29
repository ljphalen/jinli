<?php
Doo::loadModel('datamodel/base/VideoAdsBase');

class VideoAds extends VideoAdsBase{
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
        $whereArr['desc'] = 'updated';
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
     * 返回某条信息
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
        if(!is_array($conditions)){
            return $conditions;
        }
        $where = array();
        $where[] = 'del = 1';
        if (isset($conditions['name']) && $conditions['name']){
            $where[] = "name LIKE '%".$conditions['name']."%'";
        }
        if (isset($conditions['platform']) && $conditions['platform']){
            $where[] = "platform = {$conditions['platform']}";
        }
        $whereSQL = implode(" AND ", $where);
        return $whereSQL;
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
    	$this->video_ads_com_conf = json_encode($data['video_ads_com_conf']);
    	$this->updated = time();
    
    	if(empty($id)){
    		$this->created = time();
    		return $this->insert();
    	}else{
    		$this->id = $data['id'];
    		$this->update();
    		return $id;
    	}
    }
    
    /**
     * 软删除广
     * @param type $publishid
     */
    public function softDelApp($id){
    	$this->updated=time();
    	$this->id=$id;
    	$this->del=0;
    	return $this->update();
    }
    
}