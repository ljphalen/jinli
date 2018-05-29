<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Game_Service_H5RecommendDelete {
    
    public static function deleteAllRowByRecommendId($table, $id) {
        $data = self::checkData(array('table_name' => $table,'delete_id' => $id, 'table_main_name' => 'recommend_id', 'table_ismain' => 1));
        self::deleteInit($data);
    }
    
    public static function deleteAllRowByRecommendIdList($table, $idList) {
        $data = self::checkData(array('table_name' => $table,'delete_id' => serialize($idList), 'table_main_name' => 'recommend_id', 'table_ismain' => 1));
        self::deleteInit($data);
    }
    
    public static function deleteAllRowByDayId($table, $id) {
        $data = self::checkData(array('table_name' => $table,'delete_id' => $id, 'table_main_name' => 'day_id', 'table_ismain' => 1));
        self::deleteInit($data);
    }
    
    public static function deleteAllByParams($table, $params) {
        $data = self::checkData(array('table_name' => $table,'delete_id' => serialize(self::paramsHandle($params)), 'table_main_name' => '*', 'table_ismain' => 1));
        self::deleteInit($data);
    }
    
    public static function deleteOneRowById($table, $id) {
        $data = self::checkData(array('table_name' => $table,'delete_id' => $id));
        self::deleteInit($data);
    }
    
    public static function deleteAllRowByIdList($table, $idList) {
        $data = self::checkData(array('table_name' => $table,'delete_id' => serialize($idList)));
        self::deleteInit($data);
    }
    
    public static function getsBy($params) {
        if (!is_array($params)) return false;
        return self::getDao()->getsBy($params);
    }
    
    public static function deleteBy($params) {
        if (!is_array($params)) return false;
        return self::getDao()->deleteBy($params);
    }
    
    private static function paramsHandle($array) {
        foreach($array as $key => $value) {
            $params['where'][$key] = $value;
        }
        return $params;
    }
    
    private static function deleteInit($dbData) {
        self::getDao()->insert($dbData);
    }
    
    private static function checkData($data) {
        $data['tmp_id'] = Game_Service_H5HomeAutoHandle::getID();
        if(!isset($data['table_main_name'])) $data['table_main_name'] = 'id';
        if(!isset($data['table_ismain'])) $data['table_ismain'] = '0';
        return $data;
    }
    
	private static function getDao() {
		return Common::getDao("Game_Dao_H5RecommendDelete");
	}
	
}
