<?php
Doo::loadModel('AppModel');

class  AdCustomizedInfoBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var tinyint Max length is 1.
     */
    public $type;

    /**
     * @var int Max length is 11.
     */
    public $ad_info_id;

    /**
     * @var varchar Max length is 400.
     */
    public $ad_pic_url;

    /**
     * @var varchar Max length is 400.
     */
    public $ad_desc;

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
     * @var varchar Max length is 20.
     */
    public $resolution;

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

    public $_table = 'ad_customized_info';
    public $_primarykey = 'id';
    public $_fields = array('id','type','ad_info_id','ad_pic_url','ad_desc','screen_ratio','show_time','screen_type','resolution','rate','created','updated');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'ad_info_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'ad_pic_url' => array(
                        array( 'maxlength', 400 ),
                        array( 'notnull' ),
                ),

                'ad_desc' => array(
                        array( 'maxlength', 400 ),
                        array( 'optional' ),
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

                'resolution' => array(
                        array( 'maxlength', 20 ),
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