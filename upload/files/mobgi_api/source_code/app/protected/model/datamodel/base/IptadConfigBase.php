<?php
Doo::loadModel('AppModel');

class  IptadConfigBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $config_name;

    /**
     * @var varchar Max length is 64.
     */
    public $appkey;

    /**
     * @var tinyint Max length is 1.
     */
    public $platform;

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

    public $_table = 'iptad_config';
    public $_primarykey = 'id';
    public $_fields = array('id','config_name','appkey','platform','operator','del','createdate','updatedate');

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

                'appkey' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
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