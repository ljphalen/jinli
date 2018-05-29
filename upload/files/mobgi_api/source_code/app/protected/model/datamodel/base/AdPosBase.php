<?php
Doo::loadModel('AppModel');

class  AdPosBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $parent_id;

    /**
     * @var varchar Max length is 64.
     */
    public $pos_key;

    /**
     * @var varchar Max length is 64.
     */
    public $pos_name;

    /**
     * @var int Max length is 10.
     */
    public $ad_type;

    /**
     * @var int Max length is 10.
     */
    public $ad_subtype;

    /**
     * @var int Max length is 11.
     */
    public $created;

    /**
     * @var int Max length is 11.
     */
    public $updated;

    public $_table = 'ad_pos';
    public $_primarykey = 'id';
    public $_fields = array('id','parent_id','pos_key','pos_name','ad_type','ad_subtype','created','updated');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'parent_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'pos_key' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'pos_name' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'ad_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'ad_subtype' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'created' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'updated' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                )
            );
    }

}