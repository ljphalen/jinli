<?php
Doo::loadModel('AppModel');

class  AdApkBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $ad_product_id;

    /**
     * @var varchar Max length is 100.
     */
    public $product_name;

    /**
     * @var varchar Max length is 50.
     */
    public $apk_version;

    /**
     * @var varchar Max length is 50.
     */
    public $package_name;

    /**
     * @var varchar Max length is 30.
     */
    public $channel_id;

    /**
     * @var int Max length is 11.
     */
    public $size;

    /**
     * @var varchar Max length is 256.
     */
    public $apk_url;

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

    public $_table = 'ad_apk';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_product_id','product_name','apk_version','package_name','channel_id','size','apk_url','platform','ischeck','checker','owner','check_msg','createtime','updatetime');

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
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'apk_version' => array(
                        array( 'maxlength', 50 ),
                        array( 'optional' ),
                ),

                'package_name' => array(
                        array( 'maxlength', 50 ),
                        array( 'optional' ),
                ),

                'channel_id' => array(
                        array( 'maxlength', 30 ),
                        array( 'optional' ),
                ),

                'size' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'apk_url' => array(
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
                        array( 'optional' ),
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