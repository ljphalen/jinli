<?php
Doo::loadModel('AppModel');

class  PushWeightBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $config_name;

    /**
     * @var int Max length is 11.
     */
    public $start_time;

    /**
     * @var int Max length is 11.
     */
    public $end_time;

    /**
     * @var text
     */
    public $product_combo;

    /**
     * @var tinyint Max length is 4.
     */
    public $state;

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

    public $_table = 'push_weight';
    public $_primarykey = 'id';
    public $_fields = array('id','config_name','start_time','end_time','product_combo','state','operator','del','createdate','updatedate');

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
                        array( 'optional' ),
                ),

                'start_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'end_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'product_combo' => array(
                        array( 'optional' ),
                ),

                'state' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
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