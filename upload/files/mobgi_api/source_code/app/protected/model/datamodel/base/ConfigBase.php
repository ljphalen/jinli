<?php
Doo::loadModel('AppModel');

class  ConfigBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var float
     */
    public $click_speed;

    /**
     * @var float
     */
    public $click_rate;

    /**
     * @var float
     */
    public $ims_speed;

    /**
     * @var float
     */
    public $ims_rate;

    /**
     * @var float
     */
    public $install_speed;

    /**
     * @var float
     */
    public $install_rate;

    /**
     * @var int Max length is 11.
     */
    public $blackusertime;

    /**
     * @var int Max length is 11.
     */
    public $holdexpire;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'config';
    public $_primarykey = 'id';
    public $_fields = array('id','click_speed','click_rate','ims_speed','ims_rate','install_speed','install_rate','blackusertime','holdexpire','created','updated');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'click_speed' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'click_rate' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'ims_speed' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'ims_rate' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'install_speed' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'install_rate' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'blackusertime' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'holdexpire' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
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