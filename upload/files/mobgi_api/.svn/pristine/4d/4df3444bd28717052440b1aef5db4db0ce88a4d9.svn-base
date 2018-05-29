<?php
Doo::loadModel('AppModel');

class  AdDeveloperBase extends AppModel{

    /**
     * @var int Max length is 10.
     */
    public $dev_id;

    /**
     * @var varchar Max length is 32.
     */
    public $user_name;

    /**
     * @var varchar Max length is 64.
     */
    public $password;

    /**
     * @var varchar Max length is 100.
     */
    public $email;

    /**
     * @var varchar Max length is 128.
     */
    public $mobile;

    /**
     * @var varchar Max length is 128.
     */
    public $qq;

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

    /**
     * @var tinyint Max length is 2.
     */
    public $from;

    /**
     * @var varchar Max length is 20.
     */
    public $tel;

    /**
     * @var varchar Max length is 200.
     */
    public $address;

    /**
     * @var tinyint Max length is 1.
     */
    public $ischeck;

    /**
     * @var tinyint Max length is 4.
     */
    public $isactive;

    /**
     * @var varchar Max length is 256.
     */
    public $check_msg;

    public $_table = 'ad_developer';
    public $_primarykey = 'dev_id';
    public $_fields = array('dev_id','user_name','password','email','mobile','qq','createdate','updatedate','operator','from','tel','address','ischeck','isactive','check_msg');

    public function getVRules() {
        return array(
                'dev_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'user_name' => array(
                        array( 'maxlength', 32 ),
                        array( 'notnull' ),
                ),

                'password' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'email' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'mobile' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'qq' => array(
                        array( 'maxlength', 128 ),
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
                ),

                'from' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'tel' => array(
                        array( 'maxlength', 20 ),
                        array( 'notnull' ),
                ),

                'address' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'ischeck' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'isactive' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
                ),

                'check_msg' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                )
            );
    }

}