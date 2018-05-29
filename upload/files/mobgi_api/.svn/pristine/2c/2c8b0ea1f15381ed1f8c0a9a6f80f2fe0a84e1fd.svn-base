<?php
Doo::loadModel('AppModel');

class  MonitorConfigBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $eventttype;

    /**
     * @var varchar Max length is 128.
     */
    public $name;

    /**
     * @var int Max length is 11.
     */
    public $max;

    /**
     * @var int Max length is 11.
     */
    public $min;

    /**
     * @var varchar Max length is 128.
     */
    public $time;

    /**
     * @var int Max length is 11.
     */
    public $isopen;

    public $_table = 'monitor_config';
    public $_primarykey = 'id';
    public $_fields = array('id','eventtype','name','max','min','time','isopen','email');

    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("statis");
    }

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'eventtype' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'name' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'max' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'min' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'time' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'isopen' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),
                
                'email' => array(
                        array( 'maxlength', 255 ),
                        array( 'notnull' ),
                ),
            );
    }

}