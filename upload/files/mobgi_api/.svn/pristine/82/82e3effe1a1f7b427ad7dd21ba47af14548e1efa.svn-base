<?php
Doo::loadModel('AppModel');

class  AdFinancialBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $f_id;

    /**
     * @var varchar Max length is 32.
     */
    public $cred_name;

    /**
     * @var varchar Max length is 50.
     */
    public $bank;

    /**
     * @var varchar Max length is 50.
     */
    public $sub_branch;

    /**
     * @var varchar Max length is 100.
     */
    public $bank_account;

    /**
     * @var int Max length is 10.
     */
    public $cred_type;

    /**
     * @var varchar Max length is 64.
     */
    public $cred_num;

    /**
     * @var varchar Max length is 100.
     */
    public $cred_pic;

    /**
     * @var int Max length is 10.
     */
    public $ftype;

    /**
     * @var int Max length is 10.
     */
    public $dev_id;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    /**
     * @var varchar Max length is 11.
     */
    public $operator;

    public $_table = 'ad_financial';
    public $_primarykey = 'f_id';
    public $_fields = array('f_id','cred_name','bank','sub_branch','bank_account','cred_type','cred_num','cred_pic','ftype','dev_id','createdate','updatedate','operator');

    public function getVRules() {
        return array(
                'f_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'cred_name' => array(
                        array( 'maxlength', 32 ),
                        array( 'notnull' ),
                ),

                'bank' => array(
                        array( 'maxlength', 50 ),
                        array( 'notnull' ),
                ),

                'sub_branch' => array(
                        array( 'maxlength', 50 ),
                        array( 'notnull' ),
                ),

                'bank_account' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'cred_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'cred_num' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'cred_pic' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'ftype' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'dev_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
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
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                )
            );
    }

}