<?php
Doo::loadModel('AppModel');

class  AppStatBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $app_id;

    /**
     * @var int Max length is 11.
     */
    public $click;

    /**
     * @var int Max length is 11.
     */
    public $impressions;

    /**
     * @var int Max length is 11.
     */
    public $download;

    /**
     * @var int Max length is 11.
     */
    public $install;

    /**
     * @var int Max length is 11.
     */
    public $income;

    /**
     * @var int Max length is 11.
     */
    public $income_after;

    /**
     * @var int Max length is 11.
     */
    public $dev_id;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    public $_table = 'app_stat';
    public $_primarykey = 'app_id';
    public $_fields = array('app_id','click','impressions','download','install','income','income_after','dev_id','createdate','updatedate');

    public function getVRules() {
        return array(
                'app_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'click' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'impressions' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'download' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'install' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'income' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'income_after' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'dev_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'createdate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'updatedate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}