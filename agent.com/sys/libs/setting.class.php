<?php
 /**
 * 配置设置接口
 * @package application
 * @since 1.0.0 (2013-03-22)
 * @version 1.0.0 (2013-03-22)
 * @author jun <huanghaijun@mykj.com>
 */
 
 class setting{
 	public function __construct(){
 	}
 	
 	/**
 	 * 取我方接入支付方式配置
 	 * @return multitype:number
 	 */
 	public function get_our_paytype(){
 		if(!empty($GLOBALS['config']['paydetailenddate']) && !empty($_REQUEST['dateEnd']) && strtotime($_REQUEST['dateEnd'])<strtotime($GLOBALS['config']['paydetailenddate'])){
 			return get_configs('payours_all_config');
 		}elseif(!empty($GLOBALS['config']['paydetailenddate'])&&strtotime($GLOBALS['config']['paydetailenddate'])<=time()){
 			return get_configs('payours_config');
 		}else{
 			return get_configs('payours_all_config');
 		}
 	}
 	
 	/**
 	 * 取渠道自有支付方式配置
 	 * @return multitype:number
 	 */
 	public function get_channel_paytype(){
 		return get_configs('paytheirs_config');
 	}
 	
 	/**
 	 * 取支付方式配置
 	 */
 	public function get_payways(){
 		return get_configs('payways_config');
 	}
 	
 	/**
 	 * 保存支付方式配置
 	 */
 	public function set_payways($data){
	 	$config_file = SROOT.'/data/config/payways_config.php';
	 	$data = '<?php'."\nreturn ".var_export($data,true).';';
		return $this->write($config_file, $data);
 	}
 	
 	/**
 	 * 保存渠道自有支付方式配置
 	 */
 	public function set_channel_paytype($data){
 		$config_file = SROOT.'/data/config/paytheirs_config.php';
 		$data = '<?php'."\nreturn ".var_export($data,true).';';
 		return $this->write($config_file, $data);
 	}
 	
 	/**
 	 * 保存我方接入支付方式配置
 	 */
 	public function set_our_paytype($data){
 		$config_file = SROOT.'/data/config/payours_config.php';
 		$data = '<?php'."\nreturn ".var_export($data,true).';';
 		return $this->write($config_file, $data);
 	}
 	
 	
 	/**
 	 * 取爱贝支付方式分成比率
 	 */
 	public function get_ipay_rate(){
 		return get_configs('ipay_rate_config');
 	}
 	
 	/**
 	 * 保存爱贝支付方式分成比率
 	 */
 	public function set_ipay_rate($data){
 		$config_file = SROOT.'/data/config/ipay_rate_config.php';
 		$data = '<?php'."\nreturn ".var_export($data,true).';';
 		return $this->write($config_file, $data);
 	}
 	
 	/**
 	 * 取移动平台渠道分成比率
 	 */
 	public function get_yd_rate(){
 		return get_configs('yd_rate_config');
 	}
 	
 	/**
 	 * 保存移动平台渠道分成比率
 	 */
 	public function set_yd_rate($data){
 		$config_file = SROOT.'/data/config/yd_rate_config.php';
 		$data = '<?php'."\nreturn ".var_export($data,true).';';
 		return $this->write($config_file, $data);
 	}
 	
 	/**
 	 * 取联通分成比率
 	 */
 	public function get_lt_rate(){
 		return get_configs('lt_rate_config');
 	}
 	
 	/**
 	 * 保存联通分成比率
 	 */
 	public function set_lt_rate($data){
 		$config_file = SROOT.'/data/config/lt_rate_config.php';
 		$data = '<?php'."\nreturn ".var_export($data,true).';';
 		return $this->write($config_file, $data);
 	}
 	
 	/**
 	 * 取移动自有渠道及费率
 	 */
 	public function get_yd_channel(){
 		return get_configs('yd_channel_config');
 	}
 	
 	/**
 	 * 保存移动自有渠道及费率
 	 */
 	public function set_yd_channel($data){
 		$config_file = SROOT.'/data/config/yd_channel_config.php';
 		$data = '<?php'."\nreturn ".var_export($data,true).';';
 		return $this->write($config_file, $data);
 	}
 	
 	
 	/**
 	 * 取基本配置
 	 */
 	public function get_base_rate(){
 		return get_configs('base_rate_config');
 	}
 	
 	/**
 	 * 保存基本配置
 	 */
 	public function set_base_rate($data){
 		$config_file = SROOT.'/data/config/base_rate_config.php';
 		$data = '<?php'."\nreturn ".var_export($data,true).';';
 		return $this->write($config_file, $data);
 	}
 
 	
 	private function write($filename, $data) {
 		if($fp = fopen($filename, 'w+')){
 			fwrite($fp, $data);
 			fclose($fp);
 			return true;
 		}
 		return false;
 	}
 
 }
?>