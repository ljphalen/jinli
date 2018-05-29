<?php
Doo::loadModel('AppModel');

class  VideoAdsBase extends AppModel{

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
    public $desc;

    /**
     * @var int Max length is 2.
     */
    public $platform;

    /**
     * @var varchar Max length is 256.
     */
    public $app_key;

    /**
     * @var varchar Max length is 256.
     */
    public $video_ads_com_conf;

    /**
     * @var int Max length is 11.
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

    public $_table = 'video_ads';
    public $_primarykey = 'id';
    public $_fields = array('id','name','desc','platform','app_key','video_ads_com_conf','del','created','updated');

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

                'conf_desc' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'app_key' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'video_ads_com_conf' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'del' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
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