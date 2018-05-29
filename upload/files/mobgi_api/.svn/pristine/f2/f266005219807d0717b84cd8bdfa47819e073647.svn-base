<?php
Doo::loadModel('AppModel');

class  AdminsBase extends AppModel{

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $adminid;

    /**
     * @var varchar Max length is 30.
     */
    public $username;

    /**
     * @var varchar Max length is 50.
     */
    public $password;

    /**
     * @var varchar Max length is 30.
     */
    public $realname;

    /**
     * @var varchar Max length is 30.
     */
    public $e_name;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $role_id;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $date_create;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $date_update;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $date_login;
    
      /**
     * @var tinyint Max length is 1.
     */
    public $lock_num;

    public $_table = 'admins';
    public $_primarykey = 'adminid';
    public $_fields = array('adminid','username','password','realname','e_name','role_id','date_create','date_update','date_login', 'lock_num');
    
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect('admin');
    }
    
    public function getVRules() {
        return array(
                'adminid' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'username' => array(
                        array( 'maxlength', 30 ),
                        array( 'notnull' ),
                ),

                'password' => array(
                        array( 'maxlength', 50 ),
                        array( 'notnull' ),
                ),

                'realname' => array(
                        array( 'maxlength', 30 ),
                        array( 'optional' ),
                ),

                'e_name' => array(
                        array( 'maxlength', 30 ),
                        array( 'optional' ),
                ),

                'role_id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'date_create' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'date_update' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'date_login' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),
            
                'lock_num' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),
            );
    }

}