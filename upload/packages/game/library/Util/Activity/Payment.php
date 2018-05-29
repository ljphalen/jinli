<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/** 
 * 具体策略类 登录 
 */  
class Util_Activity_Payment implements Util_Activity_Coin{  
  private $_config = array(); 

   public function __construct($config){  
        $this->_config = $config;  
   }  
   
   public function getCoin(){
		
	}
}   
  
