<?php
Doo::loadModel('AppModel');

class  AdInfoBase extends AppModel{

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
     * @var tinyint Max length is 1.
     */
    public $show_detail;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'ad_info';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_product_id','ad_name','ad_desc','ad_click_type_object','ad_target','state','pos','type','show_detail','created','updated');

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

                'show_detail' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
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