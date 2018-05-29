<?php
Doo::loadModel('AppModel');

class  AdProductLimitBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $ad_product_id;

    /**
     * @var varchar Max length is 1000.
     */
    public $ad_stat_limit;

    /**
     * @var varchar Max length is 1000.
     */
    public $ad_stat_plan;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'ad_product_limit';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_product_id','ad_stat_limit','ad_stat_plan','created','updated');

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

                'ad_stat_limit' => array(
                        array( 'maxlength', 1000 ),
                        array( 'optional' ),
                ),

                'ad_stat_plan' => array(
                        array( 'maxlength', 1000 ),
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