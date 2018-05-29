<?php
Doo::loadModel('AppModel');

class  AdOrderBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 64.
     */
    public $orderid;

    /**
     * @var int Max length is 11.
     */
    public $pid;

    /**
     * @var varchar Max length is 64.
     */
    public $agreementid;

    /**
     * @var int Max length is 11.
     */
    public $startdate;

    /**
     * @var int Max length is 11.
     */
    public $enddate;

    /**
     * @var int Max length is 1.
     */
    public $paymethod;

    /**
     * @var float
     */
    public $price;

    /**
     * @var int Max length is 11.
     */
    public $createtime;

    /**
     * @var int Max length is 11.
     */
    public $updatetime;

    public $_table = 'ad_order';
    public $_primarykey = 'id';
    public $_fields = array('id','orderid','pid','agreementid','startdate','enddate','paymethod','price','createtime','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'orderid' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'pid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'agreementid' => array(
                        array( 'maxlength', 64 ),
                        array( 'optional' ),
                ),

                'startdate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'enddate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'paymethod' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'price' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'createtime' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'updatetime' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}