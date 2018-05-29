<?php
Doo::loadModel('AppModel');

class  RolesBase extends AppModel{

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 255.
     */
    public $title;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $createdate;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $lastupdate;

    public $_table = 'roles';
    public $_primarykey = 'id';
    public $_fields = array('id','title','createdate','lastupdate');

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

                'title' => array(
                        array( 'maxlength', 255 ),
                        array( 'notnull' ),
                ),

                'createdate' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'lastupdate' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                )
            );
    }

}