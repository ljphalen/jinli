<?php
Doo::loadModel('AppModel');

class  VideoAdsComBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 256.
     */
    public $identifier;

    /**
     * @var varchar Max length is 256.
     */
    public $name;

    /**
     * @var varchar Max length is 256.
     */
    public $settlement_method;

    /**
     * @var decimal Max length is 16. ,2).
     */
    public $settlement_price;

    /**
     * @var tinyint Max length is 4.
     */
    public $del;

    /**
     * @var int Max length is 11.
     */
    public $createtime;
    
    /**
     * @var int Max length is 11.
     */
    public $updatetime;

    public $_table = 'video_ads_com';
    public $_primarykey = 'id';
    public $_fields = array('id','identifier','name','settlement_method','settlement_price','del','createtime','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'identifier' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'name' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'settlement_method' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'settlement_price' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'del' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
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