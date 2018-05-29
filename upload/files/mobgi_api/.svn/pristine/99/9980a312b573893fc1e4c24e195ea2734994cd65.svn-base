<?php
Doo::loadModel('AppModel');

class  RtbLimitBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $config_id;

    /**
     * @var text
     */
    public $cat;

    /**
     * @var text
     */
    public $make;

    /**
     * @var varchar Max length is 100.
     */
    public $carrier;

    /**
     * @var varchar Max length is 100.
     */
    public $net_type;

    /**
     * @var varchar Max length is 20.
     */
    public $screen_type;

    /**
     * @var text
     */
    public $loc;

    /**
     * @var varchar Max length is 100.
     */
    public $opertor;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    public $_table = 'rtb_limit';
    public $_primarykey = 'config_id';
    public $_fields = array('id','config_id','cat','make','carrier','net_type','screen_type','loc','opertor','createdate','updatedate');

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

                'cat' => array(
                        array( 'optional' ),
                ),

                'make' => array(
                        array( 'optional' ),
                ),

                'carrier' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'net_type' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'screen_type' => array(
                        array( 'maxlength', 20 ),
                        array( 'optional' ),
                ),

                'loc' => array(
                        array( 'optional' ),
                ),

                'opertor' => array(
                        array( 'maxlength', 100 ),
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