<?php
Doo::loadModel('AppModel');

class  AdConfigDetailsBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var tinyint Max length is 4.
     */
    public $type;

    /**
     * @var varchar Max length is 2000.
     */
    public $type_value;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'ad_config_details';
    public $_primarykey = 'id';
    public $_fields = array('id','type','type_value','created','updated');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
                ),

                'type_value' => array(
                        array( 'maxlength', 2000 ),
                        array( 'notnull' ),
                ),

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'updated' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}