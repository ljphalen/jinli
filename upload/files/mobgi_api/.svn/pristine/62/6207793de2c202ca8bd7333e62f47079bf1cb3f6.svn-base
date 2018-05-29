<?php
Doo::loadModel('AppModel');

class  AdDauBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var date
     */
    public $date;

    /**
     * @var int Max length is 11.
     */
    public $num;

    /**
     * @var varchar Max length is 128.
     */
    public $app_version;

    /**
     * @var varchar Max length is 128.
     */
    public $net_type;

    /**
     * @var varchar Max length is 128.
     */
    public $channel;

    /**
     * @var varchar Max length is 32.
     */
    public $consumerkey;

    /**
     * @var int Max length is 11.
     */
    public $aid;

    /**
     * @var int Max length is 11.
     */
    public $pid;

    /**
     * @var int Max length is 11.
     */
    public $domain;

    /**
     * @var int Max length is 11.
     */
    public $show_type;

    public $_table = 'ad_dau';
    public $_primarykey = 'id';
    public $_fields = array('id','date','num','app_version','net_type','channel','consumerkey','aid','pid','domain','show_type');

    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("statis");
    }

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'date' => array(
                        array( 'date' ),
                        array( 'notnull' ),
                ),

                'num' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'app_version' => array(
                        array( 'maxlength', 128 ),
                        array( 'optional' ),
                ),

                'net_type' => array(
                        array( 'maxlength', 128 ),
                        array( 'optional' ),
                ),

                'channel' => array(
                        array( 'maxlength', 128 ),
                        array( 'optional' ),
                ),

                'consumerkey' => array(
                        array( 'maxlength', 32 ),
                        array( 'optional' ),
                ),

                'aid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'pid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'domain' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'show_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}