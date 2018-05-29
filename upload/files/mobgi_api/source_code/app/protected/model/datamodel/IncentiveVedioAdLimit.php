<?php
Doo::loadModel('datamodel/base/IncentiveVedioAdLimitBase');

class IncentiveVedioAdLimit extends IncentiveVedioAdLimitBase{
    
    const CACHE_RESOURCE = 'CACHE_REDIS_SERVER_1';
    const  CACHE_EXPIRE = 7200;
    
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
     * 查询多条记录
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
    public function getById($id=null){
        $where   = array('select'=>'*',
                         'where'=>'id="'.$id.'"',
                         'asArray' => TRUE );
        $result  = $this->getOne($where);
        return $result;
    }

    /**
     * 添加或更新信息
     * @param type $data
     */
    public function upd($id = NULL, $data){
     
    	$this->app_key = $data['app_key'];
    	$this->platform = $data['platform'];
    	$this->play_network = $data['play_network'];
    	$this->video_play_set = $data['video_play_set'];
    	$this->is_alert = $data['is_alert'];
    	$this->rate = $data['rate'];
    	$this->content = $data['content'];
    	$this->updatetime = time();

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
    
    
   public function getInfoFromCacheBy($appKey){
       $key = $this->_getVideoLimitKey($appKey);
       $cacheData =  $this->get($key,  self::CACHE_RESOURCE);
       if($cacheData == false){
           $params['app_key'] = $appKey;
           $dbData = $this->getBy($params);
           $cacheData = $this->formatData($dbData);
           if($cacheData){
               $this->set($key, $cacheData,  self::CACHE_RESOURCE ,  self::CACHE_EXPIRE);
           }
       }
       return $cacheData;
   }

private function formatData($dbData){
        if(!$dbData){  
            return '';
        }
       $cacheData['globalProbability'] = $dbData['rate'];
       $cacheData['screenSupport'] = $dbData['video_play_set'];
       $cacheData['networkSupport'] = $dbData['play_network'];
       $cacheData['closeDialog'] = $dbData['is_alert'];
       $content = $dbData['is_alert']?json_decode($dbData['content'],  true):'';
       $cacheData['title'] = $dbData['is_alert']? $content['title'] :  '';
       $cacheData['message'] = $dbData['is_alert']? $content['content'] : '';
       return json_encode($cacheData);
}

public function delCacheInfo($appKey){
        $key = $this->_getVideoLimitKey($appKey);
        $this->deleter($key, self::CACHE_RESOURCE);
}
           


private function _getVideoLimitKey($appKey){
        $key = 'incentiveVideoLimit_'.$appKey;
        return  $key;
    }

}