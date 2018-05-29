<?php
Doo::loadModel('AppModel');

class  AdIncentiveVideoInfoBase extends AppModel{

    public $id;
    public $ad_info_id;
    public $type;
    public $video_url;
    public $h5_url;
    public $rate;
    public $created;
    public $updated;
    public $_table = 'ad_incentive_video_info';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_info_id','type','video_url','h5_url','rate','created','updated');

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

                'video_url' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'h5_url' => array(
                        array( 'maxlength', 100 ),
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