<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TestController extends Api_BaseController {
    
    public function phpinfoAction() {
        phpinfo();
        exit;
    }
    
    public function testAction() {
    	var_dump($this->loginDate(3));
    	 
    }
    
    /**
     * 算出用户的登录日期显示
     */
    static public function loginDate($days = 1 ,$cycle = 7 ){
    	$tmp = array();
    	$z = 0;
    	for ($i=1; $i <$days+1; $i++){
    		$tmp[$i] = date('Y-m-d',strtotime($z.' day'));
    		$z--;
    	}
    	$j = 0;
    	for($i = $days+1; $i <= $cycle; $i++ ){
    		$j++;
    		$tmp[$i] = date('Y-m-d',strtotime('+'.$j.' day'));
    
    	}
    	sort($tmp);
    	return $tmp ;
    }
    
    function input_csv($handle) {
    	$out = array ();
    	$n = 0;
    	while ($data = fgetcsv($handle, 10000)) {
    		$num = count($data);
    		for ($i = 0; $i < $num; $i++) {
    			$out[$n][$i] = $data[$i];
    		}
    		$n++;
    	}
    	return $out;
    }
    
    public function getClientIDAction() {
    
    	$file = fopen("/home/ljp/www/test.csv","r");
    	$data = $this->input_csv($file);
    	fclose($file);
    
    	header("Content-type:text/html;charset=utf-8");
    
    	foreach ($data as $key=>$val){
    		$uname = trim($val[0]);
    		$imei  = trim($val[1]);
    		$uuid  = trim($val[2]);
    		$clientVersion = '1.6.0.a';
    			
    		$keyParam = array(
    				'apiName' => strtoupper('grab'),
    				'imei' => $imei,
    				'uname' => $uname,
    		);
    		$ivParam = $uuid;
    		$serverIdParam = array(
    				'clientVersion' => $clientVersion,
    				'imei' => $imei,
    				'uname' => $uname,
    		);
    			
    		$imeiDecrypt = Util_Imei::decryptImei(trim($val[1]));
    		$clientID = Common::encryptClientData(trim($val[2]), trim($val[0]));
    		$serverID = strtoupper($this->decryptServerId($keyParam, $ivParam, $clientVersion));
    		$sp = "E6mini_1.6.0.a_4.2.2_Android4.2.1_720*1280_I01000_wifi_".$imei;
    		echo  'uname='.$uname.',puuid='.$uuid.',imei='.$imei.',clientID='.strtoupper(md5($clientID)).',serverID='.$serverID.',sp='.$sp."<br />";
    	}
    
    
    }
    
    
    private function decryptServerId($keyParam, $ivParam, $clientVersion) {
    	$apiName = strtoupper($keyParam['apiName']);
    	$imei = $keyParam['imei'];
    	$uname = $keyParam['uname'];
    
    	$key = md5($apiName . $imei . $uname);
    	$key = substr($key, 0, 16);
    
    	$iv = md5($ivParam);
    	$iv = substr($iv, 0, 16);
    
    	$serverId = $clientVersion . '_' . $imei . '_' . $uname;
    
    	$cryptAES = new Util_CryptAES();
    	$cryptAES->setIv($iv);
    	$cryptAES->setKey($key);
    	return $cryptAES->encrypt($serverId);
    }
}