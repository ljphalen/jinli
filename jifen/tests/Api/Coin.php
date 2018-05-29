<?php 
require_once (dirname(dirname(__FILE__)) . '/Base/TestCase.php');

class Api_Test_Coin extends Base_TestCase{
	public $coin = null;
	public $webroot = null;
	
	public function __construct() {
		$this->webroot = Yaf_Application::app()->getConfig()->webroot;
	}	
    public function test_add() {
		$in = '/api/coin/add';
		$params = array('coin_type'=>2, 'coin'=>100.02, 'msg'=>"Test add");
		$ret = $this->getRequest($in, $params);
		$this->assertEquals(0, $ret['code']);
	}

	public function test_use() {
		$in = '/api/coin/use';
		$params = array('coin_type'=>2, 'coin'=>100, 'msg'=>"Test use");
		$ret = $this->getRequest($in, $params);
		$this->assertEquals(0, $ret['code']);
	}
	
	
   public function test_freeze(){
		$in = '/api/coin/freeze';
		$mark = Common::getTime();
		$cache = Common::getCache();
		$cache->set('mark', $mark);
		$params = array('coin_type'=>2, 'coin'=>1, 'mark'=>$mark, 'msg'=>"Test freeze");
		$ret = $this->getRequest($in, $params);
		$this->assertEquals(0, $ret['code']);
	}
	
	public function test_notify() {
// 		$url = 'http://dev.client.gionee.com/notify/order';
		$url = 'http://3gtest.gionee.com:84/notify/order';
		$http = new Util_Http_Curl($url);
		$ret = $http->post(array('order_no'=>'509215730cf26e032fecaca8'));
		$ret = json_decode($ret, true);
		$this->assertEquals(true, $ret['success']);
	}
	
	public function test_unfreeze(){
		$in = '/api/coin/unfreeze';
		$cache = Common::getCache();
		$mark = $cache->get('mark');
		$params = array('mark'=>$mark, 'unfreeze_type'=>2);
		$ret = $this->getRequest($in, $params, false);
		$this->assertEquals(0, $ret['code']);
	} 
	
	public function test_getuser() {
		$in = '/api/user/get';
		$ret = $this->getRequest($in, array());
		$this->assertEquals(0, $ret['code']);
	} 
	
 	public function test_coinlog() {
		$in = '/api/user/coinlog';
		$ret = $this->getRequest($in, array('coin_type'=>1, 'page_no'=>1, 'limit'=>2));
		$this->assertEquals(0, $ret['code']);
	} 
	
	private function getRequest($in, $params, $out_uid=true) {
		$url = $this->webroot.$in;
		if($out_uid) $params['out_uid'] = "8BFA8F255A4A4B12ADF67CB7EF898BCC";
		$params = array_merge($params,
			array('appid'=>304984)		
		);
		$http = new Util_Http_Curl($url);
		$ret = $http->post($params);
		$ret = json_decode($ret, true);
		print_r($ret);
		return $ret;
	}
}
