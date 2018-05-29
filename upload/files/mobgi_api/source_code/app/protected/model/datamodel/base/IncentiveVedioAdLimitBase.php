<?php
Doo::loadModel('AppModel');

class  IncentiveVedioAdLimitBase extends AppModel{

  
    public $id;
    public $app_key;
    public $platform;
    public $play_network;
    public $video_play_set;
    public $is_alert;
    public $rate;
    public $content;
    public $createtime;
    public $updatetime;
    
    public $_table = 'ad_incentive_video_limit';
    public $_primarykey = 'id';
    public $_fields = array('id','platform','app_key','play_network','video_play_set','is_alert','rate','content','createtime','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),
                'app_key' => array(
                    array( 'maxlength', 256 ),
                    array( 'optional' ),
                ),

                'platform' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),
            'play_network' => array(
                array( 'integer' ),
                array( 'maxlength', 2 ),
                array( 'optional' ),
            ),
            'video_play_set' => array(
                array( 'integer' ),
                array( 'maxlength', 2 ),
                array( 'optional' ),
            ),
            'is_alert' => array(
                array( 'integer' ),
                array( 'maxlength', 2 ),
                array( 'optional' ),
            ),
            'is_alert' => array(
                array( 'float' ),
                array( 'optional' ),
            ),
                'content' => array(
                    array( 'maxlength', 100 ),
                    array( 'optional' ),
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