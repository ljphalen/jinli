<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class WifiController extends Api_BaseController {  

    public function hbAction() {
        $params = $this->getInput(array('device_mac', 'ht', 'wl_ssid','ip', 'upload_speed', 'download_speed', 'totalram', 'freeram', 'shareram', 'bufferram', 'cached', 'totalswap', 'freeswap', 'totalfreeram', 'online_num', 'online_list', 'wanstatus'));
        if (!$params['ht'] && !$params['device_mac']) $this->output(0, "faild");
        $sk = Common::getConfig('siteConfig', 'secretKey');
        if ($params['ht'] != crc32(sprintf("%s:%s", $params['device_mac'], $sk))) $this->output(-1, 'fail');
        
        Common::log($_POST, "hb.log");
          
        $params['online_list'] = implode("|", $params['online_list']);
        
        $device = Wifi_Service_Device::getBy(array('ht'=>$params['ht']));
        if (!$device) {
        	$params['hb_time'] = $params['create_time'] = Common::getTime();
            $params['hs_enable'] = 1;
        	Wifi_Service_Device::add($params);
        } else {
        	$params['hb_time'] = Common::getTime();
        	Wifi_Service_Device::update($params, $device['id']);
        }
        $this->output(0,"success");
    } 

    public function cfgAction() {
        $ht = $this->getInput("ht");
    	$config = array(
    			"hb_url"=>"http://www.hotwifi.cc/api/wifi/hb",
    			"hb_interval"=>"40",
    			"reboot"=>0,
    			"reconnect"=>0,
                "upgrade"=>0,
                "hs_enable"=>1,
                "hs_authurl"=>"http://www.hotwifi.cc/auth",
                "hs_kickout"=>0,
                "hs_timeout"=>600,
    	);
    	
    	$this->check(array('reboot', 'reconnect', 'upgrade'), $config, $ht);
    	
    	$device = Wifi_Service_Device::getBy(array('ht'=>$ht));
    	if ($device) {
    		$config['hs_enable'] = $device['hs_enable'];
    		$config['hs_kickout'] = $device['hs_timeout'];
    	}
    	
    	$str = "";
    	foreach ($config as $key=>$value) {
    		$str .= sprintf("%s:%s\n", $key, $value);
    	}
        print_r(sprintf("version:%d\n%s", crc32($str), $str));
        exit;
    }

    public function check($list, &$config, $ht) {
        $cache = Common::getCache();
        foreach ($list as $key=>$v) {
            $rkey = $ht."_".$v;
            if ($cache->get($rkey)) {
                $config[$v] = 1;
                $cache->delete($rkey);
            }
        }
    }

    public function statAction() {
        $cache = Common::getCache();
        $ht = $this->getInput('ht');
        if (strlen($ht) < 5) self::_img();

        $pv = $cache->get('ht_pv');
        if (!$pv[$ht]) $pv[$ht] = 0;
        $pv[$ht] += 1;

        $cache->set('ht_pv', $pv);

        if (self::_getToday()) {
            $uv = $cache->get('ht_uv');
            if (!$uv[$ht]) $uv[$ht] = 0;
            $uv[$ht] += 1;
            $cache->set('ht_uv', $uv);
        }
        self::_img();
    }

    public static function _img() {
        header('Content-Type:image/jpeg');
        header('Content-Length:0');
        exit;
    }

    /**
     *
     * @return boolean
     */
    public static function _getToday() {
        $hwu = Util_Cookie::get('hwu', true);
        if ($hwu) return false;
        return Util_Cookie::set('hwu', 1, true, strtotime(date('Y-m-d 23:59:59')), '/');
    }

    public function upgradeAction() {
        $path = Common::getConfig('siteConfig', 'attachPath');
        $file = 'trx/fireware.trx';
        $output = array(
            "version"=> "0.0.2", 
            "file"=> $this->attachroot . $file,
            "md5sum"=>md5_file($path . $file)
        );

        $str = "";
        foreach ($output as $key=>$value) {
            $str .= sprintf("%s:%s\n", $key, $value);
        }
        print_r($str);
        exit;
    }
}
