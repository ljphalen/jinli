<?php
Doo::loadModel('AppModel');

class  RtbBlacklistBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 11.
     */
    public $type;

    /**
     * @var text
     */
    public $value;

    /**
     * @var varchar Max length is 30.
     */
    public $mober;

    /**
     * @var tinyint Max length is 1.
     */
    public $platform;

    /**
     * @var varchar Max length is 100.
     */
    public $oprator;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    public $_table = 'rtb_blacklist';
    public $_primarykey = 'type';
    public $_fields = array('id','type','value','mober','platform','oprator','createdate','updatedate');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'type' => array(
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'value' => array(
                        array( 'optional' ),
                ),

                'mober' => array(
                        array( 'maxlength', 30 ),
                        array( 'optional' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'oprator' => array(
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