<?php
Doo::loadModel('AppModel');

class  PushLogBase extends AppModel{

    /**
     * @var int Max length is 10.
     */
    public $id;

    /**
     * @var tinyint Max length is 1.
     */
    public $type;

    /**
     * @var text
     */
    public $log;

    /**
     * @var varchar Max length is 50.
     */
    public $operator;

    /**
     * @var varchar Max length is 200.
     */
    public $response;

    /**
     * @var int Max length is 10.
     */
    public $createtime;

    public $_table = 'push_log';
    public $_primarykey = 'id';
    public $_fields = array('id','type','log','operator','response','createtime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'log' => array(
                        array( 'optional' ),
                ),

                'operator' => array(
                        array( 'maxlength', 50 ),
                        array( 'optional' ),
                ),

                'response' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'createtime' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                )
            );
    }

}