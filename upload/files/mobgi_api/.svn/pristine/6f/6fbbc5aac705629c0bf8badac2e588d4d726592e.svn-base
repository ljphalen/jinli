<?php
Doo::loadModel('AppModel');

class  PolymericAdBase extends AppModel{

  
    public $id;
    public $name;
    public $conf_desc;
    public $platform;
    public $app_key;
    public $third_party_appkey;
    public $secret_key;
    public $position_conf;
    public $createtime;
    public $updatetime;
    public $_table = 'polymeric_ads';
    public $_primarykey = 'id';
    public $_fields = array('id','name','conf_desc','platform','app_key','secret_key','third_party_appkey','position_conf','createtime','updatetime');

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
                'secret_key' => array(
                    array( 'maxlength', 100 ),
                    array( 'optional' ),
                ),
                'third_party_appkey' => array(
                    array( 'maxlength', 100 ),
                    array( 'optional' ),
                ),

                'position_conf' => array(
                        array( 'maxlength', 1000 ),
                        array( 'optional' ),
                ),

                'del' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
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