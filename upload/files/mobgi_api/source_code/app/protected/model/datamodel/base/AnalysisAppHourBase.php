<?php
Doo::loadModel('AppModel');

class  AnalysisAppHourBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var datetime
     */
    public $date;

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

    public $_table = 'analysis_app_hour';
    public $_primarykey = 'id';
    public $_fields = array('id','date','active','startup','effective','ad_arrived');

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
                        array( 'datetime' ),
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