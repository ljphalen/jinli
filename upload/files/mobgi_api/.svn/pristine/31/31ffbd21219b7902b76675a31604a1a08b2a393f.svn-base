<?php
Doo::loadModel('AppModel');

class  AdDeverPosBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 64.
     */
    public $pos_key;

    /**
     * @var varchar Max length is 64.
     */
    public $dever_pos_key;

    /**
     * @var varchar Max length is 100.
     */
    public $dever_pos_name;

    /**
     * @var tinyint Max length is 1.
     */
    public $state;

    /**
     * @var int Max length is 11.
     */
    public $app_id;

    /**
     * @var int Max length is 11.
     */
    public $dev_id;

    /**
     * @var float
     */
    public $rate;

    /**
     * @var int Max length is 2.
     */
    public $acounting_method;

    /**
     * @var float
     */
    public $denominated;

    /**
     * @var tinyint Max length is 1.
     */
    public $del;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatetime;

    public $_table = 'ad_dever_pos';
    public $_primarykey = 'id';
    public $_fields = array('id','pos_key','dever_pos_key','dever_pos_name','state','app_id','dev_id','rate','acounting_method','denominated','del','createdate','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'pos_key' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'dever_pos_key' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'dever_pos_name' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'state' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'app_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'dev_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'rate' => array(
                        array( 'float' ),
                        array( 'notnull' ),
                ),

                'acounting_method' => array(
                        array( 'integer' ),
                        array( 'maxlength', 2 ),
                        array( 'optional' ),
                ),

                'denominated' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'del' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'optional' ),
                ),

                'createdate' => array(
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