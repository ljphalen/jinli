<?php
Doo::loadModel('AppModel');

class  UserLogsBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 255.
     */
    public $title;

    /**
     * @var varchar Max length is 128.
     */
    public $action;

    /**
     * @var varchar Max length is 255.
     */
    public $msg;
    
    /**
     * @var varchar Max length is 255.
     */
    public $type;

    /**
     * @var varchar Max length is 255.
     */
    public $snapshot_url;
    
     /**
     * @var varchar Max length is 255.
     */
    public $update_url;

    /**
     * @var datetime
     */
    public $date;

    /**
     * @var varchar Max length is 128.
     */
    public $username;

    public $_table = 'user_logs';
    public $_primarykey = 'id';
    public $_fields = array('id','title','action','msg','type', 'snapshot_url','update_url','date','username');

    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('admin');
    }
    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'uri' => array(
                        array( 'maxlength', 255 ),
                        array( 'notnull' ),
                ),

                'action' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'msg' => array(
                        array( 'maxlength', 255 ),
                        array( 'notnull' ),
                ),
            
                'type' => array(
                            array( 'maxlength', 255 ),
                            array( 'notnull' ),
                    ),

                'snapshot_url' => array(
                            array( 'maxlength', 255 ),
                            array( 'notnull' ),
                    ),
            
                'update_url' => array(
                            array( 'maxlength', 255 ),
                            array( 'notnull' ),
                    ),

                'date' => array(
                        array( 'datetime' ),
                        array( 'optional' ),
                ),

                'username' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                )
            );
    }

}