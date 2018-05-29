<?php
Doo::loadModel('AppModel');

class  IptadSourceBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $pid;

    /**
     * @var varchar Max length is 64.
     */
    public $blockkey;

    /**
     * @var varchar Max length is 256.
     */
    public $image_url;

    /**
     * @var text
     */
    public $text;

    /**
     * @var tinyint Max length is 1.
     */
    public $status;

    /**
     * @var varchar Max length is 256.
     */
    public $target_url;

    /**
     * @var tinyint Max length is 1.
     */
    public $del;

    /**
     * @var int Max length is 11.
     */
    public $createtime;

    /**
     * @var int Max length is 11.
     */
    public $updatetime;

    public $_table = 'iptad_source';
    public $_primarykey = 'id';
    public $_fields = array('id','pid','blockkey','image_url','text','status','target_url','del','createtime','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'pid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'blockkey' => array(
                        array( 'maxlength', 64 ),
                        array( 'optional' ),
                ),

                'image_url' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'text' => array(
                        array( 'optional' ),
                ),

                'status' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'target_url' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'del' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
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