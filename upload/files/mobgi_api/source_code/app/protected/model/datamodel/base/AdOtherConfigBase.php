<?php
Doo::loadModel('AppModel');

class  AdOtherConfigBase extends AppModel{

    /**
     * @var int Max length is 10.
     */
    public $id;

    /**
     * @var varchar Max length is 200.
     */
    public $config_name;

    /**
     * @var varchar Max length is 20.
     */
    public $appkey;

    /**
     * @var varchar Max length is 20.
     */
    public $channel_id;

    /**
     * @var text
     */
    public $config_detail;

    /**
     * @var varchar Max length is 11.
     */
    public $type;

    /**
     * @var int Max length is 2.
     */
    public $platform;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    /**
     * @var varchar Max length is 64.
     */
    public $oprator;

    public $_table = 'ad_other_config';
    public $_primarykey = 'id';
    public $_fields = array('id','config_name','appkey','channel_id','config_detail','type','platform','created','updated','oprator');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'config_name' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 20 ),
                        array( 'notnull' ),
                ),

                'channel_id' => array(
                        array( 'maxlength', 20 ),
                        array( 'optional' ),
                ),

                'config_detail' => array(
                        array( 'notnull' ),
                ),

                'type' => array(
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'updated' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'oprator' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                )
            );
    }

}