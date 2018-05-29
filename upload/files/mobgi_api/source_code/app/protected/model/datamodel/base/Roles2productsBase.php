<?php
Doo::loadModel('AppModel');

class  Roles2productsBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $role_id;

    /**
     * @var int Max length is 11.
     */
    public $product_id;

    /**
     * @var int Max length is 11.
     */
    public $operator;

    /**
     * @var int Max length is 11.
     */
    public $updatetime;

    public $_table = 'roles2products';
    public $_primarykey = 'product_id';
    public $_fields = array('id','role_id','product_id','operator','updatetime');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'role_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'product_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'operator' => array(
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