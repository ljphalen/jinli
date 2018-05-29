<?php
/**
 *
 * 获取赠送接口
 */

interface Util_Activity_Coin {
	public function getCoin();
}



class Util_Activity_Login implements Util_Activity_Coin{
	private $_config = array();

	public function __construct($config){
		$this->_config = $config;
	}
	 
	public function getCoin(){
		echo "登录";
		var_dump($this->_config);
	}
}

/**
 * 具体策略类 下载
 */
class Util_Activity_DownLoad implements Util_Activity_Coin{
	private $_config = array();

	public function __construct($config){
		$this->_config = $config;
	}
	 
	public function getCoin(){
		echo "下载";
	}
}


class Util_Activity_Context {
	private $_strategy = null;

	public function __construct(Util_Activity_Coin $cion){
		$this->_strategy = $cion;
	}
	 
	public function setStrategy(Util_Activity_Coin $cion){
		$this->_strategy = $cion;
	}

	public function sendTictket(){
		return $this->_strategy->getCoin();
	}
}

//登录
$activity = new Util_Activity_Context(new Util_Activity_Login(array('total'=>10)));
$activity ->sendTictket();

