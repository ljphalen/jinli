<?php
Doo::loadModel('AppModel');

class  AnalysisAppBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var date
     */
    public $date;

    /**
     * @var varchar Max length is 128.
     */
    public $appkey;

    /**
     * @var int Max length is 11.
     */
    public $active;

    /**
     * @var int Max length is 11.
     */
    public $startup;

    /**
     * @var int Max length is 11.
     */
    public $effective;

    /**
     * @var int Max length is 11.
     */
    public $ad_arrived;

    public $_table = 'analysis_app';
    public $_primarykey = 'id';
    public $_fields = array('id','date','appkey','active','startup','effective','ad_arrived');

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

                'appkey' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'active' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'startup' => array(
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
                )
            );
    }

}