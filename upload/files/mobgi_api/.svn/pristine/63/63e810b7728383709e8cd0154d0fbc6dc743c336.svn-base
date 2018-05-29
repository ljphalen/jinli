<?php
Doo::loadModel('AppModel');

class  CategoryBase extends AppModel{

    /**
     * @var int Max length is 10.
     */
    public $id;

    /**
     * @var int Max length is 10.
     */
    public $parentId;

    /**
     * @var varchar Max length is 50.
     */
    public $name;

    /**
     * @var tinyint Max length is 4.
     */
    public $type;

    /**
     * @var text
     */
    public $channelData;

    public $_table = 'category';
    public $_primarykey = 'type';
    public $_fields = array('id','parentId','name','type','channelData');
    
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('admin');
    }

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'parentId' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'name' => array(
                        array( 'maxlength', 50 ),
                        array( 'notnull' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
                ),

                'channelData' => array(
                        array( 'optional' ),
                )
            );
    }

}