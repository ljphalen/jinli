<?php
Doo::loadModel('AppModel');

class  ImgManageBase extends AppModel{

    /**
     * @var int Max length is 10.
     */
    public $id;

    /**
     * @var varchar Max length is 120.
     */
    public $name;

    /**
     * @var varchar Max length is 255.
     */
    public $content;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $createdate;

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $updatedate;

    /**
     * @var varchar Max length is 255.
     */
    public $url;

    /**
     * @var varchar Max length is 120.
     */
    public $category;

    public $bre_url;
    
    public $_table = 'img_manage';
    public $_primarykey = 'id';
    public $_fields = array('id','name','content','createdate','updatedate','url','bre_url','category');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'name' => array(
                        array( 'maxlength', 120 ),
                        array( 'notnull' ),
                ),

                'content' => array(
                        array( 'maxlength', 255 ),
                        array( 'optional' ),
                ),

                'createdate' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'updatedate' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'notnull' ),
                ),

                'url' => array(
                        array( 'maxlength', 255 ),
                        array( 'notnull' ),
                ),

                'category' => array(
                        array( 'maxlength', 120 ),
                        array( 'notnull' ),
                )
            );
    }

}