<?php
Doo::loadModel('AppModel');

class  AdConfigBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $name;

    /**
     * @var varchar Max length is 200.
     */
    public $config_desc;

    /**
     * @var text
     */
    public $product_comb;

    /**
     * @var int Max length is 11.
     */
    public $config_detail_id;

    /**
     * @var int Max length is 11.
     */
    public $config_detail_type;

    /**
     * @var varchar Max length is 20.
     */
    public $type;

    /**
     * @var int Max length is 2.
     */
    public $platform;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'ad_config';
    public $_primarykey = 'id';
    public $_fields = array('id','name','config_desc','product_comb','config_detail_id','config_detail_type','type','platform','created','updated');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'name' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'config_desc' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'product_comb' => array(
                        array( 'notnull' ),
                ),

                'config_detail_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'config_detail_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'type' => array(
                        array( 'maxlength', 20 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
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
                )
            );
    }

}