<?php
Doo::loadModel('AppModel');

class  AnalysisProductCityBase extends AppModel{

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
    public $pid;

    /**
     * @var varchar Max length is 128.
     */
    public $appkey;

    /**
     * @var varchar Max length is 32.
     */
    public $city;

    /**
     * @var varchar Max length is 32.
     */
    public $province;

    /**
     * @var int Max length is 11.
     */
    public $impressions;

    /**
     * @var int Max length is 11.
     */
    public $clicks;

    /**
     * @var int Max length is 11.
     */
    public $start_downloads;

    /**
     * @var int Max length is 11.
     */
    public $finish_downloads;

    /**
     * @var int Max length is 11.
     */
    public $installs;

    /**
     * @var int Max length is 11.
     */
    public $startup;

    public $_table = 'analysis_product_city';
    public $_primarykey = 'id';
    public $_fields = array('id','date','pid','appkey','city','province','impressions','clicks','start_downloads','finish_downloads','installs','startup');

    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("statis");
    }

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'date' => array(
                        array( 'date' ),
                        array( 'notnull' ),
                ),

                'pid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'city' => array(
                        array( 'maxlength', 32 ),
                        array( 'notnull' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'province' => array(
                        array( 'maxlength', 32 ),
                        array( 'notnull' ),
                ),

                'impressions' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'clicks' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'start_downloads' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'finish_downloads' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'installs' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'startup' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                )
            );
    }

}