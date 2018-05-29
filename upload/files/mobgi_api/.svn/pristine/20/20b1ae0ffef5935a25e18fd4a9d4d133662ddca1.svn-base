<?php
Doo::loadModel('AppModel');

class  IptadPublishBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 256.
     */
    public $compay;

    /**
     * @var varchar Max length is 100.
     */
    public $conact;

    /**
     * @var varchar Max length is 80.
     */
    public $tel;

    /**
     * @var varchar Max length is 256.
     */
    public $address;

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

    public $_table = 'iptad_publish';
    public $_primarykey = 'id';
    public $_fields = array('id','compay','conact','tel','address','del','createtime','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'compay' => array(
                        array( 'maxlength', 256 ),
                        array( 'optional' ),
                ),

                'conact' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'tel' => array(
                        array( 'maxlength', 80 ),
                        array( 'optional' ),
                ),

                'address' => array(
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