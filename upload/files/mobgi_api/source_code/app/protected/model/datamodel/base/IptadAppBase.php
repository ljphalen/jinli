<?php
Doo::loadModel('AppModel');

class  IptadAppBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 64.
     */
    public $appkey;

    /**
     * @var varchar Max length is 64.
     */
    public $packname;

    /**
     * @var varchar Max length is 128.
     */
    public $appname;

    /**
     * @var varchar Max length is 11.
     */
    public $platform;

    /**
     * @var tinyint Max length is 2.
     */
    public $state;

    /**
     * @var varchar Max length is 11.
     */
    public $oprater;

    /**
     * @var int Max length is 11.
     */
    public $createtime;

    /**
     * @var int Max length is 11.
     */
    public $updatetime;

    /**
     * @var tinyint Max length is 1.
     */
    public $del;

    public $_table = 'iptad_app';
    public $_primarykey = 'appkey';
    public $_fields = array('id','appkey','packname','appname','platform','state','oprater','createtime','updatetime','del');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'packname' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'appname' => array(
                        array( 'maxlength', 128 ),
                        array( 'optional' ),
                ),

                'platform' => array(
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'state' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'notnull' ),
                ),

                'oprater' => array(
                        array( 'maxlength', 11 ),
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
                ),

                'del' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                )
            );
    }

}