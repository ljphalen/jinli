<?php
Doo::loadModel('AppModel');

class  AnalysisWauBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $date;

    /**
     * @var int Max length is 11.
     */
    public $active;

    /**
     * @var int Max length is 11.
     */
    public $effective;

    /**
     * @var int Max length is 11.
     */
    public $ad_arrived;

    /**
     * @var varchar Max length is 128.
     */
    public $appkey;

    /**
     * @var varchar Max length is 128.
     */
    public $channel;

    /**
     * @var varchar Max length is 128.
     */
    public $app_version;

    public $_table = 'analysis_wau';
    public $_primarykey = 'id';
    public $_fields = array('id','date','active','effective','ad_arrived','appkey','channel','app_version');

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
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'active' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'effective' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'ad_arrived' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'channel' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'app_version' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                )
            );
    }

}