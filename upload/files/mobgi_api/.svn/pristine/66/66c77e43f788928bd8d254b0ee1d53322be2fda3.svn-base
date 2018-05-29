<?php
Doo::loadModel('AppModel');

class  RtbPlanBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $config_id;

    /**
     * @var int Max length is 11.
     */
    public $start_time;

    /**
     * @var int Max length is 11.
     */
    public $end_time;

    /**
     * @var tinyint Max length is 1.
     */
    public $go_method;

    /**
     * @var tinyint Max length is 1.
     */
    public $ad_type;

    /**
     * @var tinyint Max length is 1.
     */
    public $method;

    /**
     * @var float
     */
    public $price;

    /**
     * @var int Max length is 11.
     */
    public $maxprice;

    /**
     * @var int Max length is 11.
     */
    public $maximp;

    /**
     * @var int Max length is 11.
     */
    public $maxclick;

    /**
     * @var varchar Max length is 200.
     */
    public $maxcondition;

    /**
     * @var tinyint Max length is 1.
     */
    public $runstatus;

    /**
     * @var tinyint Max length is 1.
     */
    public $state;

    /**
     * @var varchar Max length is 100.
     */
    public $oprator;

    /**
     * @var tinyint Max length is 1.
     */
    public $del;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    public $_table = 'rtb_plan';
    public $_primarykey = 'end_time';
    public $_fields = array('id','config_id','start_time','end_time','go_method','ad_type','method','price','maxprice','maximp','maxclick','maxcondition','runstatus','state','oprator','del','createdate','updatedate');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'config_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'start_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'end_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'go_method' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),

                'ad_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'method' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'price' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'maxprice' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'maximp' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'maxclick' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'maxcondition' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'runstatus' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'state' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'oprator' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'del' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'createdate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'updatedate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}