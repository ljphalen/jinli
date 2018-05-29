<?php
Doo::loadModel('AppModel');

class  AdIncentiveVideoBase extends AppModel{

    public $id;
    public $ad_product_id;
    public $product_name;
    public $video_name;
    public $video_url;
    public $ad_type;
    public $ad_subtype;  
    public $platform;    
    public $ischeck;
    public $checker;
    public $owner;
    public $check_msg;
    public $creattime; 
    public $updatetime;

    public $_table = 'ad_incentive_video';
    public $_primarykey = 'id';
    public $_fields = array('id','ad_product_id','product_name','video_name','video_url','h5_url','ad_type','ad_subtype','platform','ischeck','checker','owner','check_msg','creattime','updatetime');

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

                'video_name' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'video_url' => array(
                        array( 'maxlength', 300 ),
                        array( 'optional' ),
                ),
                'h5_url' => array(
                    array( 'maxlength', 300 ),
                    array( 'optional' ),
                ),

                'ad_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'ad_subtype' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
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
                        array( 'notnull' ),
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

                'creattime' => array(
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