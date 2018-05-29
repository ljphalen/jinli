<?php
Doo::loadModel('AppModel');

class  AdTextBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $ad_product_id;

    /**
     * @var varbinary Max length is 100.
     */
    public $product_name;

    /**
     * @var tinyint Max length is 2.
     */
    public $subtype;

    /**
     * @var varchar Max length is 10.
     */
    public $type;

    /**
     * @var varchar Max length is 100.
     */
    public $ad_name;

    /**
     * @var varchar Max length is 256.
     */
    public $content;

    /**
     * @var varchar Max length is 256.
     */
    public $style;

    /**
     * @var int Max length is 2.
     */
    public $platform;

    /**
     * @var tinyint Max length is 2.
     */
    public $ischeck;

    /**
     * @var varchar Max length is 100.
     */
    public $checker;

    /**
     * @var varchar Max length is 100.
     */
    public $owner;

    /**
     * @var varchar Max length is 100.
     */
    public $check_msg;

    /**
     * @var int Max length is 11.
     */
    public $createtime;

    /**
     * @var int Max length is 11.
     */
    public $updatetime;

    public $_table = 'ad_text';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_product_id','product_name','subtype','type','ad_name','content','style','platform','ischeck','checker','owner','check_msg','createtime','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'ad_product_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'product_name' => array(
                        array( 'notnull' ),
                ),

                'subtype' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'type' => array(
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'ad_name' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'content' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'style' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'ischeck' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'checker' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'owner' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'check_msg' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'createtime' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'updatetime' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}