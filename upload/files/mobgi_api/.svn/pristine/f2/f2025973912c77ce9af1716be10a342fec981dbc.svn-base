<?php
Doo::loadModel('AppModel');

class  OrderTableBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $company_id;

    /**
     * @var int Max length is 11.
     */
    public $pid;

    /**
     * @var varchar Max length is 32.
     */
    public $order_num;

    /**
     * @var varchar Max length is 100.
     */
    public $contract_num;

    /**
     * @var date
     */
    public $start_date;

    /**
     * @var date
     */
    public $end_date;

    /**
     * @var int Max length is 11.
     */
    public $type;

    /**
     * @var decimal Max length is 14. ,2).
     */
    public $price;

    public $_table = 'order_table';
    public $_primarykey = 'id';
    public $_fields = array('id','company_id','pid','order_num','contract_num','start_date','end_date','type','price');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'company_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'pid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'order_num' => array(
                        array( 'maxlength', 32 ),
                        array( 'notnull' ),
                ),

                'contract_num' => array(
                        array( 'maxlength', 100 ),
                        array( 'notnull' ),
                ),

                'start_date' => array(
                        array( 'date' ),
                        array( 'notnull' ),
                ),

                'end_date' => array(
                        array( 'date' ),
                        array( 'notnull' ),
                ),

                'type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'price' => array(
                        array( 'float' ),
                        array( 'notnull' ),
                )
            );
    }

}