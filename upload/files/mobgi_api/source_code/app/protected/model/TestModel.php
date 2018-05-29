<?php
/**
 * 扩展Doophp模型
 *
 * @author: Intril
 * Date: 13-4-15
 */

Doo::loadModel('AppModel');
class TestModel extends AppModel { 
    public $id;
    public $game_id;
    public $player_id;
    public $achievement_id;
    public $percentage;
    public $created;
    public $updated;
    public $_primarykey = 'id';
    public $_table = '';
    public $_fields = array('id', 'game_id', 'player_id', 'achievement_id', 'percentage', 'created', 'updated');
    private $_dbPrefix = 'player_achievements';

    /**
     * 构造函数
     * Enter description here ...
     * @param unknown_type $properties
     */
    public function __construct($shardId = null){
        $properties = null;
        parent::__construct($properties);
        // 初始化分表分库
        $this->_table = $this->mysqlShard($shardId, $this->_dbPrefix);
    }

    public function select(){
        $data = $this->find(array('select' => 'id, game_id, player_id','asArray' => TRUE));
        return $data;
    }
}