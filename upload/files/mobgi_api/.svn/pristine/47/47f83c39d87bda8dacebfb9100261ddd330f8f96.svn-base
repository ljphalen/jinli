<?php
/**
 * Article数据库
 *
 * @author ot.tang
 */
Doo::loadModel('AppModel');
class ConfigManage extends AppModel {

    private $_Config;

    public function __construct($properties = null) {
        parent::__construct($properties);      
        $this->_Config = Doo::loadModel("datamodel/Config",TRUE);
    }
    
    /**
     * 查询应用列表-多条记录
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
        $whereArr['where'] = $this->conditions($conditions,$this->_Config->_fields);
        $result = $this->_Config->find($whereArr);
        return $result;
    }
    
    /**
     * 查询一条记录
     * @param Array $conditions
     */
    public function findOne($conditions){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_Config->_fields);
        $result = $this->_Config->getOne($whereArr);
        return $result;
    }
    
    /**
     * 返回记录数
     * @param type $conditions
     * @return 数据的数量
     */
    public function records($conditions = NULL){
        $whereArr['asArray'] = TRUE;
        $whereArr['select'] = '*'; 
        $whereArr['where'] = $this->conditions($conditions,$this->_Config->_fields);
        $result = $this->_Config->count($whereArr);
        return $result;
    }

    public function upconfig(array $params)
    {
    	$this->_Config->id = 1;
    	if($data = $this->_Config->getOne())
    	{
    	    foreach($this->_Config->_fields as $key)
    		{
    			$result[$key] = $data->{$key};
    		}
    		foreach($params as $key=>$value)
    		{
    			$this->_Config->{$key} = $value;
    			$result[$key] = $value;
    		}
    		Doo::db()->reconnect('cheat');
    		$this->_Config->update();
    	}
    	else
    	{
    		foreach($params as $key=>$value)
    		{
    			$this->_Config->{$key} = $value;
    			$result[$key] = $value;
    		}
    		Doo::db()->reconnect('cheat');
    		$this->_Config->insert();
    	}
    		unset($result['created']);
    		unset($result['updated']);
    		$this->set("CheatConfig",$result,"CACHE_REDIS_CHEAT_CONFIG");
    	
    }
}