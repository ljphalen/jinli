<?php
Doo::loadModel('AppModel');

class  RtbConfigBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $config_name;

    /**
     * @var mediumint Max length is 11.
     */
    public $product_id;

    /**
     * @var tinyint Max length is 1.
     */
    public $platform;

    /**
     * @var varchar Max length is 100.
     */
    public $mober;

    /**
     * @var varchar Max length is 256.
     */
    public $channel_id;

    /**
     * @var varchar Max length is 100.
     */
    public $operator;

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

    public $_table = 'rtb_config';
    public $_primarykey = 'mober';
    public $_fields = array('id','config_name','product_id','platform','mober','channel_id','operator','del','createdate','updatedate');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'config_name' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'product_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),

                'mober' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'channel_id' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'operator' => array(
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