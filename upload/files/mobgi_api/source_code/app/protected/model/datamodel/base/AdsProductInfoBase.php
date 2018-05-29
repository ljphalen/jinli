<?php
Doo::loadModel('AppModel');

class  AdsProductInfoBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $product_name;

    /**
     * @var varchar Max length is 32.
     */
    public $appkey;

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
     * @var tinyint Max length is 1.
     */
    public $star;

    /**
     * @var int Max length is 11.
     */
    public $playering;
    
    /**
     * @var tinyint Max length is 1.
     */
    
    public $promote_type;
    
    /**
     * @var float 
     */
    
    public $profit_margin;

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
    public $created;
    /**
     * @var int Max length is 11.
     */
    public $updated;

   public $ios_identify;
   public $json_conf;

    public $_table = 'ads_product_info';
    public $_primarykey = 'appkey';
    public $_fields = array('id','product_name','appkey','product_icon','product_desc','product_url','click_type_object','product_version','product_package','platform','star','playering','promote_type','profit_margin','ischeck','checker','owner','check_msg','created','updated','ios_identify','json_conf');

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

                'appkey' => array(
                        array( 'maxlength', 32 ),
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
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),

                'playering' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),
                
                'promote_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),
                
                'profit_margin' => array(
                        array( 'float' ),
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

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
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