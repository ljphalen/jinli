<?php
Doo::loadModel('AppModel');

class  PushHarassBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var text
     */
    public $value;

    /**
     * @var varchar Max length is 256.
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

    public $_table = 'push_harass';
    public $_primarykey = 'id';
    public $_fields = array('id','value','oprator','createdate','updatedate');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'value' => array(
                        array( 'optional' ),
                ),

                'oprator' => array(
                        array( 'maxlength', 256 ),
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