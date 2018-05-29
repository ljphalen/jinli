<?php
Doo::loadModel('AppModel');

class  AdStatGameDayStatiscsBase extends AppModel{

    /**
     * @var int Max length is 10.  unsigned.
     */
    public $id;

    /**
     * @var varchar Max length is 200.
     */
    public $aid;

    /**
     * @var int Max length is 11.
     */
    public $gid;

    /**
     * @var varchar Max length is 400.
     */
    public $gpkg;

    /**
     * @var varchar Max length is 128.
     */
    public $app_version;

    /**
     * @var varchar Max length is 64.
     */
    public $net_type;

    /**
     * @var int Max length is 20.
     */
    public $show_type;

    /**
     * @var varchar Max length is 200.
     */
    public $pid;

    /**
     * @var int Max length is 11.
     */
    public $domain;

    /**
     * @var datetime
     */
    public $stat_date;

    /**
     * @var int Max length is 11.
     */
    public $requests_success;

    /**
     * @var int Max length is 11.
     */
    public $requests;

    /**
     * @var int Max length is 11.
     */
    public $impressions;

    /**
     * @var int Max length is 11.
     */
    public $cancel_impressions;

    /**
     * @var int Max length is 11.
     */
    public $clicks;

    /**
     * @var int Max length is 11.
     */
    public $start_downloads;

    /**
     * @var int Max length is 11.
     */
    public $continue_downloads;

    /**
     * @var int Max length is 11.
     */
    public $cancel_downloads;

    /**
     * @var int Max length is 11.
     */
    public $finish_downloads;

    /**
     * @var int Max length is 11.
     */
    public $goto_urls;

    /**
     * @var int Max length is 11.
     */
    public $installed;

    /**
     * @var int Max length is 11.
     */
    public $first_startups;

    /**
     * @var int Max length is 11.
     */
    public $not_first_startups;

    /**
     * @var int Max length is 11.
     */
    public $first_registers;

    /**
     * @var int Max length is 11.
     */
    public $not_first_registers;

    /**
     * @var int Max length is 11.
     */
    public $int1;

    /**
     * @var int Max length is 11.
     */
    public $int2;

    /**
     * @var int Max length is 11.
     */
    public $int3;

    /**
     * @var int Max length is 11.
     */
    public $int4;

    /**
     * @var int Max length is 11.
     */
    public $int5;

    /**
     * @var int Max length is 11.
     */
    public $int6;

    /**
     * @var int Max length is 11.
     */
    public $int7;

    /**
     * @var int Max length is 11.
     */
    public $int8;

    /**
     * @var varchar Max length is 200.
     */
    public $string1;

    /**
     * @var varchar Max length is 200.
     */
    public $string2;

    /**
     * @var varchar Max length is 200.
     */
    public $string3;

    /**
     * @var varchar Max length is 200.
     */
    public $string4;

    /**
     * @var varchar Max length is 200.
     */
    public $string5;

    /**
     * @var varchar Max length is 200.
     */
    public $string6;

    /**
     * @var datetime
     */
    public $created;

    /**
     * @var datetime
     */
    public $update;

    public $_table = 'Ad_stat_GameDayStatiscs';
    public $_primarykey = 'stat_date';
    public $_fields = array('id','aid','gid','gpkg','app_version','net_type','show_type','pid','domain','stat_date','requests_success','requests','impressions','cancel_impressions','clicks','start_downloads','continue_downloads','cancel_downloads','finish_downloads','goto_urls','installed','first_startups','not_first_startups','first_registers','not_first_registers','int1','int2','int3','int4','int5','int6','int7','int8','string1','string2','string3','string4','string5','string6','created','update');
    
    public function __construct($properties = null) {
        parent::__construct($properties);
        Doo::db()->reconnect("statis");
    }
    
    public function getVRules() {
        return array(
                'id' => array(
                        array( 'integer' ),
                        array( 'min', 0 ),
                        array( 'maxlength', 10 ),
                        array( 'optional' ),
                ),

                'aid' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'gid' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'gpkg' => array(
                        array( 'maxlength', 400 ),
                        array( 'notnull' ),
                ),

                'app_version' => array(
                        array( 'maxlength', 128 ),
                        array( 'notnull' ),
                ),

                'net_type' => array(
                        array( 'maxlength', 64 ),
                        array( 'notnull' ),
                ),

                'show_type' => array(
                        array( 'integer' ),
                        array( 'maxlength', 20 ),
                        array( 'notnull' ),
                ),

                'pid' => array(
                        array( 'maxlength', 200 ),
                        array( 'notnull' ),
                ),

                'domain' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'notnull' ),
                ),

                'stat_date' => array(
                        array( 'datetime' ),
                        array( 'notnull' ),
                ),

                'requests_success' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'requests' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'impressions' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'cancel_impressions' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'clicks' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'start_downloads' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'continue_downloads' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'cancel_downloads' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'finish_downloads' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'goto_urls' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'installed' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'first_startups' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'not_first_startups' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'first_registers' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'not_first_registers' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int1' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int2' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int3' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int4' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int5' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int6' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int7' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'int8' => array(
                        array( 'integer' ),
                        array( 'maxlength', 11 ),
                        array( 'optional' ),
                ),

                'string1' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'string2' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'string3' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'string4' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'string5' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'string6' => array(
                        array( 'maxlength', 200 ),
                        array( 'optional' ),
                ),

                'created' => array(
                        array( 'datetime' ),
                        array( 'notnull' ),
                ),

                'update' => array(
                        array( 'datetime' ),
                        array( 'notnull' ),
                )
            );
    }

}