<?php
Doo::loadModel('AppModel');

class  IptadProductBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $pid;

    /**
     * @var varchar Max length is 100.
     */
    public $product_name;

    /**
     * @var int Max length is 2.
     */
    public $platform;

    /**
     * @var int Max length is 5.
     */
    public $publishid;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var varchar Max length is 200.
     */
    public $oprator;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'iptad_product';
    public $_primarykey = 'pid';
    public $_fields = array('pid','product_name','platform','publishid','created','oprator','updated');

    public function getVRules() {
        return array(
                'pid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'product_name' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'publishid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 5 ),
                        array( 'optional' ),
                ),

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'oprator' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'updated' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}