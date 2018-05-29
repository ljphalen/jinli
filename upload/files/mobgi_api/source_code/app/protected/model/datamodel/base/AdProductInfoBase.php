<?php
Doo::loadModel('AppModel');

class  AdProductInfoBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $product_name;

    /**
     * @var varchar Max length is 300.
     */
    public $product_icon;

    /**
     * @var varchar Max length is 500.
     */
    public $product_desc;

    /**
     * @var varchar Max length is 300.
     */
    public $product_url;

    /**
     * @var tinyint Max length is 4.
     */
    public $net_type;

    /**
     * @var varchar Max length is 2000.
     */
    public $click_type_object;

    /**
     * @var varchar Max length is 30.
     */
    public $product_version;

    /**
     * @var varchar Max length is 100.
     */
    public $product_package;

    /**
     * @var int Max length is 2.
     */
    public $platform;

    /**
     * @var float
     */
    public $star;

    /**
     * @var int Max length is 11.
     */
    public $playering;

    /**
     * @var varchar Max length is 32.
     */
    public $appkey;

    /**
     * @var tinyint Max length is 1.
     */
    public $promote_type;

    /**
     * @var float
     */
    public $profit_margin;

    /**
     * @var int Max length is 5.
     */
    public $publishid;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var varchar Max length is 200.
     */
    public $oprator;

    /**
     * @var int Max length is 11.
     */
    public $updated;

   public $ios_identify;
   public $json_conf;
   
    public $_table = 'ad_product_info';
    public $_primarykey = 'id';
    public $_fields = array('id','product_name','product_icon','product_desc','product_url','net_type','click_type_object','product_version','product_package','platform','star','playering','appkey','promote_type','profit_margin','publishid','created','oprator','updated','ios_identify','json_conf');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'product_name' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'product_icon' => array(
                        array( 'maxlength', 300 ),
                        array( 'optional' ),
                ),

                'product_desc' => array(
                        array( 'maxlength', 500 ),
                        array( 'optional' ),
                ),

                'product_url' => array(
                        array( 'maxlength', 300 ),
                        array( 'optional' ),
                ),

                'net_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'optional' ),
                ),

                'click_type_object' => array(
                        array( 'maxlength', 2000 ),
                        array( 'notnull' ),
                ),

                'product_version' => array(
                        array( 'maxlength', 30 ),
                        array( 'notnull' ),
                ),

                'product_package' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'star' => array(
                        array( 'float' ),
                        array( 'notnull' ),
                ),

                'playering' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 32 ),
                        array( 'notnull' ),
                ),

                'promote_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'profit_margin' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'publishid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 5 ),
                        array( 'optional' ),
                ),

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'oprator' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'updated' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),
               'ios_identify' => array(
                array( 'maxlength', 50 ),
                array( 'optional' ),
              ),
             'json_conf' => array(
                array( 'maxlength', 1000 ),
                array( 'optional' ),
            )
            );
    }

}