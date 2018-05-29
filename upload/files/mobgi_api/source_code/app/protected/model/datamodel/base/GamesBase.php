<?php
Doo::loadModel('AppModel');

class  GamesBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $developer_id;

    /**
     * @var varchar Max length is 100.
     */
    public $developer_name;

    /**
     * @var varchar Max length is 100.
     */
    public $name;

    /**
     * @var varchar Max length is 100.
     */
    public $ename;

    /**
     * @var varchar Max length is 16.
     */
    public $abbreviated_name;

    /**
     * @var varchar Max length is 45.
     */
    public $product_key;

    /**
     * @var varchar Max length is 45.
     */
    public $product_secret;

    /**
     * @var tinyint Max length is 4.
     */
    public $status;

    /**
     * @var int Max length is 11.
     */
    public $platform;

    /**
     * @var int Max length is 11.
     */
    public $achievements;

    /**
     * @var int Max length is 11.
     */
    public $leaderboards;

    /**
     * @var int Max length is 11.
     */
    public $downloads;

    /**
     * @var int Max length is 11.
     */
    public $player_count;

    /**
     * @var varchar Max length is 100.
     */
    public $formula;

    /**
     * @var int Max length is 1.
     */
    public $game_attribute;

    /**
     * @var int Max length is 11.
     */
    public $game_type;

    /**
     * @var int Max length is 11.
     */
    public $pf;

    /**
     * @var int Max length is 11.
     */
    public $debug;

    /**
     * @var int Max length is 11.
     */
    public $sdcard;

    /**
     * @var int Max length is 11.
     */
    public $checksum;

    /**
     * @var varchar Max length is 5000.
     */
    public $checksum_data;

    /**
     * @var int Max length is 11.
     */
    public $game_sort;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'games';
    public $_primarykey = '';
    public $_fields = array('id','developer_id','developer_name','name','ename','abbreviated_name','product_key','product_secret','status','platform','achievements','leaderboards','downloads','player_count','formula','game_attribute','game_type','pf','debug','sdcard','checksum','checksum_data','game_sort','created','updated');

    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("user_retention");
    }

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'developer_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'developer_name' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'name' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'ename' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'abbreviated_name' => array(
                        array( 'maxlength', 16 ),
                        array( 'notnull' ),
                ),

                'product_key' => array(
                        array( 'maxlength', 45 ),
                        array( 'notnull' ),
                ),

                'product_secret' => array(
                        array( 'maxlength', 45 ),
                        array( 'notnull' ),
                ),

                'status' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'achievements' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'leaderboards' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'downloads' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'player_count' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'formula' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'game_attribute' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),

                'game_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'pf' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'debug' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'sdcard' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'checksum' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'checksum_data' => array(
                        array( 'maxlength', 5000 ),
                        array( 'optional' ),
                ),

                'game_sort' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'updated' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}