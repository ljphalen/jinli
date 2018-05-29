<?php
Doo::loadModel('AppModel');

class  AdInstatllRemindBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var text
     */
    public $config;

    /**
     * @var int Max length is 11.
     */
    public $create_date;

    /**
     * @var int Max length is 11.
     */
    public $update_date;

    public $_table = 'ad_instatll_remind';
    public $_primarykey = 'id';
    public $_fields = array('id','config','create_date','update_date');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'config' => array(
                        array( 'notnull' ),
                ),

                'create_date' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'update_date' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}