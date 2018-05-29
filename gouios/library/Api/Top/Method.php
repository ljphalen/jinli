<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 
 * @author rainkid
 *
 */
class Api_Top_Method {
	private $methodName = '';
	private $apiParas = array ();
	
	public function __construct($methodName, $apiParas = array()) {
		$this->methodName = $methodName;
		$this->apiParas = $apiParas;
	}
	
	public function getApiMethodName() {
		return $this->methodName;
	}
	
	public function getApiParas() {
		return $this->apiParas;
	}
}