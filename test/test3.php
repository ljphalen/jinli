<?php  
/** 
* 策略模式 
* 
*/   
  
/** 
* 赠送
* 
*/  
interface SendStrategy{  
    public function send();  
}   
  
  
/** 
 * 具体策略类 登录 
 */  
class LoginStrategy implements SendStrategy{  
  private $_config = array(); 

  public function __construct($config){  
        $this->_config = $config;  
    }  
 	 public function send(){
   
        print_r($this->_config);
        echo "登录  <br />";
	}
}   
  
  
/** 
 * 具体策略类
 */  
class ConsumeStrategy implements SendStrategy{  
    

     public function send(){
         
     }
}   
  

/** 
 * 具体策略类
 */  
class PaymentStrategy implements SendStrategy{  
     public function send(){
         
     }
}   

/** 
 * 具体策略类
 */  
class DownloadStrategy implements SendStrategy{ 
    private $_config = array(); 
    
    public function __construct($config){  
        $this->_config = $config;  
     }  
     public function send(){
     
        print_r($this->_config);
         echo "下载  <br />";
     }
}  
  
  
  
/** 
 *  
 * 活动类(Context): 
 */  
class ActivityContext{  
    private $_strategy = null;  
  
    public function __construct(SendStrategy $send){  
        $this->_strategy = $send;  
    }  
   

    public function setTravelStrategy(SendStrategy $send){  
        $this->_strategy = $send;  
    }  
 
    public function sendTictket(){  
        return $this->_strategy ->send();  
    }  
}   
  
//登录 
$activity = new Util_Activity_Context(new Util_Activity_Login(array('total'=>10)));
$activity ->sendTictket();
  
   
?>   
