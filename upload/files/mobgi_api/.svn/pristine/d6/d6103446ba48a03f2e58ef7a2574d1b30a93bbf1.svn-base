<?php
Doo::loadModel('AppModel');

class  AdConditionManageBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var text
     */
    public $value;

    /**
     * @var varchar Max length is 64.
     */
    public $type;

    /**
     * @var int Max length is 2.
     */
    public $status;

    /**
     * @var tinyint Max length is 4.
     */
    public $data_type;

    /**
     * @var varchar Max length is 128.
     */
    public $name;

    /**
     * @var varchar Max length is 64.
     */
    public $condition_id;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    /**
     * @var varchar Max length is 20.
     */
    public $operator;

    public $_table = 'ad_condition_manage';
    public $_primarykey = 'id';
    public $_fields = array('id','value','type','status','data_type','name','condition_id','createdate','updatedate','operator');

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

                'type' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'status' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'data_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
                ),

                'name' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'condition_id' => array(
                        array( 'maxlength', 64 ),
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
                        array( 'notnull' ),
                ),

                'operator' => array(
                        array( 'maxlength', 20 ),
                        array( 'notnull' ),
                )
            );
    }

}