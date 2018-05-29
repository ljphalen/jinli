<?php
Doo::loadModel('AppModel');

class  PushPlanLogBase extends AppModel{

    /**
     * @var int Max length is 11.
     */
    public $id;

    /**
     * @var int Max length is 11.
     */
    public $plan_id;

    /**
     * @var int Max length is 11.
     */
    public $start_time;

    /**
     * @var int Max length is 11.
     */
    public $end_time;

    /**
     * @var int Max length is 11.
     */
    public $send_time;

    public $_table = 'push_plan_log';
    public $_primarykey = 'id';
    public $_fields = array('id','plan_id','start_time','end_time','send_time');

    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'plan_id' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'start_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'end_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'send_time' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                )
            );
    }

}