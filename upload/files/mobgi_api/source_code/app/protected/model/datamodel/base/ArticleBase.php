<?php
Doo::loadModel('AppModel');

class  ArticleBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var varchar Max length is 100.
     */
    public $title;

    /**
     * @var text
     */
    public $content;

    /**
     * @var varchar Max length is 100.
     */
    public $w_from;

    /**
     * @var varchar Max length is 255.
     */
    public $icon;

    /**
     * @var tinyint Max length is 1.
     */
    public $type;

    /**
     * @var varchar Max length is 100.
     */
    public $operator;
    /**
     * @var int Max length is 11.
     */
    public $click;

    /**
     * @var int Max length is 11.
     */
    public $pubdate;

    /**
     * @var int Max length is 11.
     */
    public $createdate;

    /**
     * @var int Max length is 11.
     */
    public $updatedate;

    public $_table = 'article';
    public $_primarykey = 'id';
    public $_fields = array('id','title','content','w_from','icon','type','operator','click','pubdate','createdate','updatedate');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'title' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'content' => array(
                        array( 'notnull' ),
                ),

                'w_from' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),

                'icon' => array(
                        array( 'maxlength', 255 ),
                        array( 'optional' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 1 ),
                        array( 'notnull' ),
                ),

                'operator' => array(
                        array( 'maxlength', 100 ),
                        array( 'optional' ),
                ),
            
                'click' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                ),

                'pubdate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'createdate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'updatedate' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}