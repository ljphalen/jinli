<?php
Doo::loadModel('AppModel');

class  LogBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 64.
     */
    public $uuid;

    /**
     * @var varchar Max length is 64.
     */
    public $udid;

    /**
     * @var varchar Max length is 32.
     */
    public $appkey;

    /**
     * @var int Max length is 10.
     */
    public $count;

    /**
     * @var int Max length is 11.
     */
    public $created;

    public $_table = 'log';
    public $_primarykey = 'id';
    public $_fields = array('id','uuid','udid','appkey','count','created');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'uuid' => array(
                        array( 'maxlength', 64 ),
                        array( 'optional' ),
                ),

                'udid' => array(
                        array( 'maxlength', 64 ),
                        array( 'optional' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 32 ),
                        array( 'optional' ),
                ),

                'count' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}