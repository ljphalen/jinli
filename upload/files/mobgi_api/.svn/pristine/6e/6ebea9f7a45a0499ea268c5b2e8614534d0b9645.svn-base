<?php
Doo::loadModel('AppModel');

class  AdPidInShowBase extends AppModel{

    /**
     * @var int Max length is 10.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $product_package;

    /**
     * @var float
     */
    public $denominated;

    /**
     * @var varchar Max length is 10.
     */
    public $acounting_method;

    /**
     * @var float
     */
    public $profit_margin;

    public $_table = 'ad_pid_in_show';
    public $_primarykey = 'id';
    public $_fields = array('id','product_package','denominated','acounting_method','profit_margin');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'product_package' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'denominated' => array(
                        array( 'float' ),
                        array( 'optional' ),
                ),

                'acounting_method' => array(
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'profit_margin' => array(
                        array( 'float' ),
                        array( 'optional' ),
                )
            );
    }

}