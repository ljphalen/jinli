<?php
Doo::loadModel('AppModel');

class  PushLimitBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $config_id;

    /**
     * @var varchar Max length is 256.
     */
    public $app_id;

    /**
     * @var varchar Max length is 256.
     */
    public $packagename;

    /**
     * @var varchar Max length is 256.
     */
    public $channel_id;

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

    public $_table = 'push_limit';
    public $_primarykey = 'config_id';
    public $_fields = array('id','config_id','app_id','packagename','channel_id','opertor','createdate','updatedate');

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

                'app_id' => array(
                        array( 'maxlength', 256 ),
                        array( 'notnull' ),
                ),

                'packagename' => array(
                        array( 'maxlength', 256 ),
                        array( 'notnull' ),
                ),

                'channel_id' => array(
                        array( 'maxlength', 256 ),
                        array( 'notnull' ),
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