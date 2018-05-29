<?php
Doo::loadModel('AppModel');

class  UserRetentionRateReportBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var date
     */
    public $report_date;

    /**
     * @var int Max length is 11.
     */
    public $game_id;

    /**
     * @var varchar Max length is 16.
     */
    public $channel_id;

    /**
     * @var int Max length is 11.
     */
    public $new_user;

    /**
     * @var int Max length is 11.
     */
    public $first_day_retention;

    /**
     * @var int Max length is 11.
     */
    public $third_day_retention;

    /**
     * @var int Max length is 11.
     */
    public $seventh_day_retention;

    /**
     * @var int Max length is 11.
     */
    public $fifteenth_day_retention;

    /**
     * @var int Max length is 11.
     */
    public $thirtieth_day_retention;

    /**
     * @var int Max length is 11.
     */
    public $sixtieth_day_retention;

    public $_table = 'user_retention_rate_report';
    public $_primarykey = 'id';
    public $_fields = array('id','report_date','game_id','channel_id','new_user','first_day_retention','third_day_retention','seventh_day_retention', 'fifteenth_day_retention', 'thirtieth_day_retention', 'sixtieth_day_retention');

    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("user_retention");
    }

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'report_date' => array(
                        array( 'date' ),
                        array( 'notnull' ),
                ),

                'game_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'channel_id' => array(
                        array( 'maxlength', 16 ),
                        array( 'notnull' ),
                ),

                'new_user' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'first_day_retention' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'third_day_retention' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'seventh_day_retention' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'fifteenth_day_retention' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'thirtieth_day_retention' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'sixtieth_day_retention' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),
            );
    }

}