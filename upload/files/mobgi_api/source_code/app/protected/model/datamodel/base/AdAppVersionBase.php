<?php
Doo::loadModel('AppModel');

class  AdAppVersionBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 128.
     */
    public $app_version;

    /**
     * @var varchar Max length is 128.
     */
    public $appkey;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    /**
     * @var varchar Max length is 128.
     */
    public $app_name;

    public $_table = 'ad_app_version';
    public $_primarykey = 'id';
    public $_fields = array('id','app_version','appkey','createdate','updatedate','app_name');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'app_version' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'createdate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'updatedate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'app_name' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                )
            );
    }

}