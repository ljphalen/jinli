<?php
Doo::loadModel('AppModel');

class  IptadBlockBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 20.
     */
    public $blockkey;

    /**
     * @var varchar Max length is 20.
     */
    public $blockname;

    /**
     * @var varchar Max length is 64.
     */
    public $appkey;

    /**
     * @var tinyint Max length is 1.
     */
    public $inapp;

    /**
     * @var tinyint Max length is 1.
     */
    public $source_type;

    /**
     * @var tinyint Max length is 1.
     */
    public $status;

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

    public $_table = 'iptad_block';
    public $_primarykey = 'blockkey';
    public $_fields = array('id','blockkey','blockname','appkey','inapp','source_type','status','oprater','createtime','updatetime','del');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'blockkey' => array(
                        array( 'maxlength', 20 ),
                        array( 'notnull' ),
                ),

                'blockname' => array(
                        array( 'maxlength', 20 ),
                        array( 'optional' ),
                ),

                'appkey' => array(
                        array( 'maxlength', 64 ),
                        array( 'optional' ),
                ),

                'inapp' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'source_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'status' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
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