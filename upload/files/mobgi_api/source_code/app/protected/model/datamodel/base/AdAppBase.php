<?php
Doo::loadModel('AppModel');

class  AdAppBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $app_id;

    /**
     * @var varchar Max length is 128.
     */
    public $app_name;

    /**
     * @var varchar Max length is 32.
     */
    public $appkey;

    /**
     * @var varchar Max length is 256.
     */
    public $packagename;

    /**
     * @var int Max length is 10.
     */
    public $platform;

    /**
     * @var varchar Max length is 256.
     */
    public $app_desc;

    /**
     * @var int Max length is 10.
     */
    public $appcate_id;

    /**
     * @var tinyint Max length is 2.
     */
    public $state;

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

    /**
     * @var varchar Max length is 20.
     */
    public $operator;

    /**
     * @var int Max length is 2.
     */
    public $acounting_method;

    /**
     * @var float
     */
    public $income_rate;

    /**
     * @var int Max length is 255.
     */
    public $denominated;

    /**
     * @var tinyint Max length is 1.
     */
    public $ischeck;

    /**
     * @var tinyint Max length is 1.
     */
    public $from;

    /**
     * @var varchar Max length is 200.
     */
    public $icon;

    /**
     * @var varchar Max length is 100.
     */
    public $keyword;

    /**
     * @var varchar Max length is 200.
     */
    public $check_msg;

    /**
     * @var varchar Max length is 200.
     */
    public $apk_url;

    public $_table = 'ad_app';
    public $_primarykey = 'app_id';
    public $_fields = array('app_id','app_name','appkey','packagename','platform','app_desc','appcate_id','state','dev_id','createdate','updatedate','operator','acounting_method','income_rate','denominated','ischeck','from','icon','keyword','check_msg','apk_url');

    public function getVRules() {
        return array(
                'app_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'app_name' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 32 ),
                        array( 'notnull' ),
                ),

                'packagename' => array(
                        array( 'maxlength', 256 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'app_desc' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'appcate_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'state' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
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
                        array( 'notnull' ),
                ),

                'operator' => array(
                        array( 'maxlength', 20 ),
                        array( 'notnull' ),
                ),

                'acounting_method' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'income_rate' => array(
                        array( 'float' ),
                        array( 'notnull' ),
                ),

                'denominated' => array(
                        array( 'integer' ),
                        array( 'maxlength', 255 ),
                        array( 'notnull' ),
                ),

                'ischeck' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),

                'from' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),

                'icon' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'keyword' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'check_msg' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'apk_url' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                )
            );
    }

}