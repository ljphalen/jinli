<?php
Doo::loadModel('AppModel');

class  PushDataBase extends AppModel{

    /**
     * @var int Max length is 11.  unsigned.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $config_id;

    /**
     * @var int Max length is 11.
     */
    public $plan_id;

    /**
     * @var int Max length is 11.
     */
    public $imp;

    /**
     * @var int Max length is 11.
     */
    public $click;

    /**
     * @var float
     */
    public $price;

    /**
     * @var date
     */
    public $date;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    public $_table = 'push_data';
    public $_primarykey = 'date';
    public $_fields = array('id','config_id','plan_id','imp','click','price','date','createdate','updatedate');

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

                'plan_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'imp' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'click' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'price' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'date' => array(
                        array( 'date' ),
                        array( 'notnull' ),
                ),

                'createdate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'updatedate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}