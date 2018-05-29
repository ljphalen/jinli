<?php
Doo::loadModel('AppModel');

class  AdsInfoBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $ad_product_id;

    /**
     * @var varchar Max length is 200.
     */
    public $ad_name;

    /**
     * @var varchar Max length is 400.
     */
    public $ad_desc;

    /**
     * @var varchar Max length is 2000.
     */
    public $ad_click_type_object;

    /**
     * @var varchar Max length is 300.
     */
    public $ad_target;

    /**
     * @var varchar Max length is 10.
     */
    public $rate;

    /**
     * @var tinyint Max length is 4.
     */
    public $state;

    /**
     * @var varchar Max length is 64.
     */
    public $pos;

    /**
     * @var tinyint Max length is 4.
     */
    public $type;

    /**
     * @var float
     */
    public $show_time;

    /**
     * @var tinyint Max length is 1.
     */
    public $show_detail;
    
    /**
     * @var smallint Max length is 2.
     */
    public $close_wait;

    /**
     * @var int Max length is 2.
     */
    public $r_id;

    /**
     * @var int Max length is 2.
     */
    public $r_type;

    /**
     * @var tinyint Max length is 2.
     */
    public $del;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'ads_info';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_product_id','ad_name','ad_desc','ad_click_type_object','ad_target','rate','state','pos','type','show_time','show_detail','close_wait','r_id','r_type','del','created','updated');

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

                'ad_name' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'ad_desc' => array(
                        array( 'maxlength', 400 ),
                        array( 'optional' ),
                ),

                'ad_click_type_object' => array(
                        array( 'maxlength', 2000 ),
                        array( 'optional' ),
                ),

                'ad_target' => array(
                        array( 'maxlength', 300 ),
                        array( 'optional' ),
                ),

                'rate' => array(
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'state' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'optional' ),
                ),

                'pos' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
                ),

                'show_time' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'show_detail' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),
                
                'close_wait' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'r_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'r_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'del' => array(
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