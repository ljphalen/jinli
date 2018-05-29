<?php
Doo::loadModel('AppModel');

class  AdsNotEmbeddedInfoBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $ad_info_id;

    /**
     * @var tinyint Max length is 4.
     */
    public $type;

    /**
     * @var varchar Max length is 400.
     */
    public $ad_pic_url;

    /**
     * @var varchar Max length is 100.
     */
    public $screen_ratio;

    /**
     * @var int Max length is 11.
     */
    public $show_time;

    /**
     * @var tinyint Max length is 4.
     */
    public $screen_type;

    /**
     * @var varchar Max length is 10.
     */
    public $rate;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'ads_not_embedded_info';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_info_id','type','ad_pic_url','screen_ratio','show_time','screen_type','rate','created','updated');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'ad_info_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'notnull' ),
                ),

                'ad_pic_url' => array(
                        array( 'maxlength', 400 ),
                        array( 'notnull' ),
                ),

                'screen_ratio' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'show_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'screen_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 4 ),
                        array( 'optional' ),
                ),

                'rate' => array(
                        array( 'maxlength', 10 ),
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