<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
$config =  array(
        'test'    => array(
            'url' => 'http://debug.mobads.baidu.com/api_5',
        	'app_id'=>'f09880d0',
        	'api_version'=>array('major'=>4,
        			             'minor'=>0
        			          ),
        	'os_version'=>array('major'=>4,
        			            'minor'=>2
        			          ),
        	
     
   
            
        ),
        'product' => array(
            'url' => 'http://debug.mobads.baidu.com/api_5',
        	'app_id'=>'f09880d0',
        	'api_version'=>array('major'=>4,
        						 'minor'=>0
        		),
        	'os_version'=>array('major'=>4,
        						'minor'=>2
        		),
          
        ),
    		
    	'develop' => array(
    		'url' => 'http://debug.mobads.baidu.com/api_5',
    		'app_id'=>'f09880d0',
    		'api_version'=>array('major'=>4,
    							 'minor'=>0	 
    		),
    		'os_version'=>array('major'=>4,
    							'minor'=>2
    			),
    	
    	),

		
		
);
return defined('ENV') ? $config[ENV] : $config['product'];