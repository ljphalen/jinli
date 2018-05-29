<?php
Doo::loadModel('AppModel');

class  Roles2permissionBase extends AppModel{

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $id;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $role_id;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $group_id;

    /**
     * @var varchar Max length is 255.
     */
    public $mask;

    public $_table = 'roles2permission';
    public $_primarykey = 'id';
    public $_fields = array('id','role_id','group_id','mask');
    
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('admin');
    }
    
    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'role_id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'group_id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'mask' => array(
                        array( 'maxlength', 255 ),
                        array( 'notnull' ),
                )
            );
    }

}