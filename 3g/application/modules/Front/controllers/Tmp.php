<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class TmpController extends Front_BaseController {
	
	 static $num = 1;
    public function feedbackkeyAction() {
        $msg = $this->getInput('msg');
        Gionee_Service_Feedback::filter($msg);
        exit;
    }

    public function baiduAction() {
        $items = array();
        list($hotwords,$keywords) = self::formatBaibuHotWords();
        array_shift($hotwords);//删除第一个热词
        //手动添加的热词内容
        $prev        = $next = array();
        $t_bi        = Util_Cookie::get('3G-SOURCE', true);
        $manualWords = Common::getCache()->get("manual_words");
        if (empty($manualWords['prev']) && empty($manualWords['next'])) {
            $where    = array(
                'column_id'  => self::getColumnID(),
                'status'     => 1,
                'start_time' => array('<=', time()),
                'end_time'   => array('>=', time())
            );
            $dataList = Gionee_Service_Ng::getsBy($where, array('sort' => 'ASC', 'id' => 'DESC'));
            foreach ($dataList as $v) {
                $temp = array('text' => $v['title'], 'url' => Common::clickUrl($v['id'], 'NAV', $v['link'], $t_bi));
                if ($v['sort'] <= self::WIDGET) { //由于按升序排列，故sort值越小越在前面
                    $prev[] = $temp;
                } else {
                    $next[] = $temp;
                }
            }
            $manualWords = array('prev' => $prev, 'next' => $next);
            Common::getCache()->set('manual_words', $manualWords, 60);
        }
        //加入手动词后
        $finalHotWords = array();
        foreach ($hotwords as $v) {
            $bdurl           = Common::clickUrl(-100, 'BAIDU_HOT', self::getSearchUrl(urlencode($v)), $t_bi);
            $finalHotWords[] = array('text' => $v, 'url' => $bdurl);
        }
        $finalHotWords = array_merge(array_merge($manualWords['prev'], $finalHotWords), $manualWords['next']);

        Common::getCache()->set("baidu_keywords", $keywords, Common::T_ONE_DAY);
        Common::getCache()->set("baidu_hotwords", $finalHotWords, Common::T_ONE_DAY);

        $ret = array('items' => $items, 'keywords' => $keywords, 'hotwords' => $finalHotWords);
        var_dump($ret);
        exit;
    }

    public function rsaAction() {
        $msg = 'user_id=65746&user_type=1&recharge_phone_no=13418626960&order_id=201506251555316574626979&subject=&prod_id=1003&prod_type=5&prod_title=100M%E6%B5%81%E9%87%8F&activity_no=&trans_type=1&prod_cnt=1';

        $pubKey = "MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAIcibTqdggcknQkqajywwmTIPJ80V23Ei1hw2VBkzxnm+1qzqEcINgIgeiUzvE0vGCQjbBYvzJmhbNJw6u47N7rW640a4jQHASQ8RzHB+WtxElkAx1pGWvMbCAc+BIQOJ4PqOuW9pmI1OrqL0aIpD2pFTCTizTxBrXh6wonRuDTjAgMBAAECgYArYSPLQzCwdlJq1NDRtrRQge7j9Ht0YzmQJHm+Uv4ghIN/tFh6pOoheKd9NcRmyo1nXG+gF6wITUePrmvcGepjdTvdoaupO0CQ+QX8vIB+FGcx9uenBzUptChr8/NfuUzJ8aJfpTtqSh18skBd/dqVIEC0sOhGDc/rf7xtyXSJcQJBAPrQX5FY7ECH02m0lDdeSjs7Kr9krvTKUZM8Hm4f3XMjH+gQQsJ4/pK6yYkhL32v7/DsdyjQVKFf5wea7j8cN70CQQCJ7br29rArZsrmyPZwL3sArFfezR6kClpm5Dh8kl8k52Pn7XAlacybB/NZm5aWiJhmXFt48qvtO3hKJdx7DBkfAkEAiq1j6e2M0zFGh4+809BiotVYEXMN+XNUH2CCQsmDnAGzxpAXGYfk2iRFAnlC1O/ObxuP1xU5dfCcwOu9B1AG0QJBAIZLzcZWzZOEw7zQIXt8D5TJOcl7CQGJ/xm9zu4kCrXBAvq4qoATMqAkrqIqJwatO043UO1Dw3j6dZYkpTe9wNsCQCsvr/aD9f8J2Evl/F1v9n7bOX0xDEd8hdZPfK5KuFGPmb4i9Z2p/Qg/LRAkIbpiTexA/3YUgkYTapZLws65olg=";
        $pem    = chunk_split($pubKey, 64, "\n");//转换为pem格式的公钥
        $pem    = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
        var_dump($pem);
        $pi_key = openssl_pkey_get_private($pem);
        var_dump($pi_key);
        $publicKey = openssl_pkey_get_public($pem);//获取公钥内容
        var_dump($publicKey);
        $r = openssl_public_encrypt($msg, $encrypted, $publicKey);//用openssl加密
        var_dump($r);
        if ($r) {
            $encrypted = base64_encode($encrypted);//base64编码
        }
        echo $encrypted;
        exit;
    }

    public function stAction() {
        $url = 'http://wx.stcn.com/partner/getLastestNews?userName=jinli&password=pap8*sandier';
        $str = file_get_contents($url);
        $arr = json_decode($str, true);

    }

    public function ccAction() {
        $cc        = '700001834';
        $client_id = 3659;
        $key       = '8*Sgp#';
        $time      = date('YmdHis');
        $sign      = md5($client_id . $key . $time);
        $url       = sprintf('http://218.207.208.30:8080/pae/comic/get?client_id=%d&time=%s&sign=%s', $client_id, $time, $sign);
        //echo $url;

        $respStr = file_get_contents($url);
        $respArr = json_decode($respStr, true);
        foreach ($respArr as $k => $val) {
            $id                      = $val['id'];
            $sign                    = md5($client_id . $key . $id . $time);
            $dataUrl                 = sprintf('http://218.207.208.30:8080/pae/comic/details/get?client_id=%d&id=%s&time=%s&sign=%s', $client_id, $id, $time, $sign);
            $respArr[$k]['data_url'] = $dataUrl;
            $respDataStr             = file_get_contents($dataUrl);
            $respDataArr             = json_decode($respDataStr, true);
            $respArr[$k]['img']      = $respDataArr['cover3'];
            $respArr[$k]['desc']     = $respDataArr['brief'];
            $respArr[$k]['out_time'] = $respDataArr['updated_time'];
            $outLink                 = sprintf('http://wap.dm.10086.cn/m/dm/x?x=%s&cc=%s', $id, $cc);
            $respArr[$k]['out_link'] = $outLink;
        }
        echo Common::jsonEncode($respArr);
        exit;
    }

    public function thumbAction() {
        $url    = $this->getInput('url');
        $path   = '/news/' . date('Ymd');
        $newImg = Common::downImg($url, $path);
        if ($newImg) {
            Common::genThumbImg($newImg, 640, 300, 0);
        }

        echo Common::getImgPath() . $newImg;
        exit;
    }

    public function loginimgAction() {
        $v = Api_Gionee_Account::getImageCode();
        var_dump($v);
        exit;
    }

    public function htAction() {
        $list = Partner_Service_HistoryToday::getDao()->getsBy(array('sort_year' => ''), array('date' => 'asc'));
        foreach ($list as $val) {
            $date = explode('-', $val['date']);
            if (empty($val['year'])) {
                if (count($date) == 4) {
                    $yy = $date[1];
                } else {
                    $yy = $date[0];
                }
                $y = str_pad($yy, 4, 0, STR_PAD_LEFT);
                if ($date[0] < 0) {
                    $sort_year = '1' . $y;
                } else {
                    $sort_year = '2' . $y;
                }
                $updata = array('sort_year' => $sort_year);
                var_dump($val['date'], $updata);
                Partner_Service_HistoryToday::getDao()->update($updata, $val['id']);
            }
        }
        exit;
        var_dump($list);
    }

    public function snAction() {

        Partner_Service_SingerNews::run();
        exit;
    }

    public function uaAction() {
        $pwd = $this->getInput('pwd');
        if ($pwd != 543) {
            exit;
        }
        foreach ($_SERVER as $k => $v) {
            echo "{$k},{$v}<br>";
        }
        exit;
    }

    public function aAction() {
        $content = file_get_contents('http://api2.ofpay.com/querybigcard.do');
        echo "<pre>";
        $content = '';
        //print_r(Util_XML2Array::createArray($content));
        print_r(json_decode(json_encode((array)simplexml_load_string($content)), 1));
        echo "</pre>";
        exit;
    }

    public function serverAction() {
        var_dump($_SERVER);
        exit;
    }

    public function phpAction() {
        $pwd = $this->getInput('pwd');
        if ($pwd != 545) {
            exit;
        }
        phpinfo();
        exit;
    }

    public function cleandataAction() {
        User_Service_TaskBrowserOnline::getDao()->deleteBy(array('cur_date' => date('Ymd')));
        $list = User_Service_TaskBrowserOnline::getDao()->getAll();
        var_dump($list);
        exit;
    }

    
    public  function handupAction(){
    	$phone = '13418626960';
    	$userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($phone);
    	list($leftSec, $upData) = $this->_calcLeftSec($userVoipInfo['m_sys_sec'], $userVoipInfo['exchange_sec'], 1000);
    	
    	if (!empty($upData) && $leftSec >= 0) {
    		Gionee_Service_VoIPUser::set($upData, $userVoipInfo['id']);
    	}
    	 
    	var_dump($leftSec,$upData);exit();
    }
    
    
    private function _calcLeftSec($m_sys_sec, $exchange_sec, $diff) {
    	$upData = array();
    	
    	$total_sec = $m_sys_sec+$exchange_sec;
    	if ($total_sec > $diff) {
    		if ($m_sys_sec>=$diff) {
    			$upData['m_sys_sec'] = $m_sys_sec-$diff;
    		} else {
    			$upData['m_sys_sec'] = 0;
    			$upData['exchange_sec'] = $exchange_sec-($diff-$m_sys_sec);
    		}
    		$leftSec = $total_sec-$diff;
    	} else {
    		$upData['m_sys_sec'] = 0;
    		$upData['exchange_sec'] = 0;
    		$leftSec = 0;
    	}
    	

        return array($leftSec, $upData);
    }
    
    public function telAction() {

        $diff      = 1800;
        $m_sys_sec = 1800;
        $upData    = array();
        $leftSec   = $diff;
        if (!empty($m_sys_sec)) {
            $leftSec = $m_sys_sec - $leftSec;
            if ($leftSec >= 0) {
                $upData['m_sys_sec'] = $leftSec;
            } else {
                $upData['m_sys_sec'] = 0;
            }

            if ($leftSec < 0 && !empty($exchange_sec)) {
                $leftSec = $exchange_sec - abs($leftSec);
                if ($leftSec >= 0) {
                    $upData['exchange_sec'] = $leftSec;
                } else {
                    $upData['exchange_sec'] = 0;
                }
            }
        }

        var_dump($upData, $leftSec);
        exit;

        $caller = $this->getInput('caller');
        $telObj = new Vendor_Tel();
        $info   = $telObj->applyClient(0, 0, $caller, $caller);
        var_dump($info);
        $info = $telObj->getClientInfo($caller);
        var_dump($info);
        exit;
    }

    public function encryptAction() {
        $val = $_GET['v'];
        $t   = $_GET['t'];
        header('Content-Type: text/html; charset=utf-8');
        $e = new Util_Encrypt();
        $a = $e->aesEncrypt($val);
        echo "输入:" . $val;
        echo "<br>";
        echo "加密:" . $a;
        echo "<br>";
        $b = $e->aesDecrypt($a);
        echo "解密:" . $b;
        echo "<hr>";

        if ($t == 1) {
            $e = new Util_Encrypt();
            $b = $e->aesDecrypt($val);
            echo "解密:" . $b;
            echo "<hr>";
        }


        $b = $e->aesEncrypt('');
        var_dump($b);
        $b = $e->aesDecrypt('FD34645D0CF3A18C9FC4E2C49F11C510');
        var_dump($b);
        exit;
    }

    public function voipfixpwdAction() {
        $telObj = new Vendor_Tel();
        $limit  = $this->getInput('limit');
        $limit  = max($limit, 100);

        list($total, $list) = Gionee_Service_VoIPClient::getList(1, intval($limit), array('client_pwd' => 0));
        echo $total . '<br>';
        $n = 1;
        foreach ($list as $val) {
            echo $n . '<br>';
            $clientInfo = $telObj->getClientNumber($val['mobile_number']);

            $key = 'VOIP_MOBILE_NUMBER:' . $val['mobile_number'];
            Common::getCache()->delete($key);

            $info = array(
                'client_pwd'    => $clientInfo['clientPwd'],
                'client_number' => $clientInfo['clientNumber'],
            );
            Gionee_Service_VoIPClient::update($info, $val['id']);
            echo $val['client_number'] . '=>' . $clientInfo['client_pwd'] . "<br>";
            $n++;
        }
        exit;
    }

    public function wxAction() {
        $wx = new Vendor_Weixin();
        $a  = $wx->ipList();

        var_dump($a);
        exit;
    }

    public function uainfoAction() {
        $ua   = Util_Http::ua();
        $area = Vendor_IP::find($ua['ip']);
        $out  = "<pre>%s<br>%s</pre>";
        echo sprintf($out, print_r($ua, 1), print_r($area, 1));
        exit;
    }

    public function veAction() {
       $ids = User_Service_Order::run(5);
       var_dump($ids,'successed!');
       exit();
    }

    public function  flowAction(){
    	User_Service_Order::req_flowOrder_result();
    	echo sprintf('successed!');
    	exit();
    }
    public function apcAction() {
        $authorization = false;
        if ($_SERVER['PHP_AUTH_USER'] == "apcuuser" && $_SERVER['PHP_AUTH_PW'] == "apcuvv") {
            $authorization = true;
        }
        if (!$authorization) {
            header("WWW-Authenticate:Basic realm='Private'");
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }
    }

    public function lnavAction() {
        $id   = $this->getInput('id');
        $data = Gionee_Service_LocalNavList::getData($id, true);
        var_dump($data);
        exit;
    }


    public function syncAction() {
        $msg = date('Y-m-d H:i:s') . ':';
        foreach (Gionee_Service_Log::$types as $v) {
            $sTime = microtime();
            Gionee_Service_Log::sync2DB($v);
            $eTime = microtime();
            $diff  = sprintf('%.3f', ($eTime - $sTime));
            $msg .= "{$v}:({$diff});";
            echo $msg . "<hr>";
        }

        //统计用户数据
        foreach (Gionee_Service_Log::$userTypes as $v) {
            $sTime = microtime();
            Gionee_Service_Log::sync2DB($v);
            $eTime = microtime();
            $diff  = sprintf('%.3f', ($eTime - $sTime));
            $msg .= "{$v}:({$diff});";
            echo $msg . "<hr>";
        }
        Gionee_Service_CronLog::add(array('type' => 'sync_log', 'msg' => $msg));
    }

    public function levelAction() {
        Gionee_Service_Log::writeLevelUserAmountToDb();
        exit();
    }

    public function usersignAction() {
        $userInfo  = Gionee_Service_User::getCurUserInfo();
        $rewardMsg = User_Service_Rewards::getBy(array('uid' => $userInfo['id'], 'group_id' => 1));
        $date      = $this->getInput('date');
        $lastDate  = $date ? strtotime($date) : strtotime("-1 day");
        User_Service_Rewards::edit(array('last_time' => $lastDate), $rewardMsg['id']);
        $res = User_Service_Earn::deletesBy(array('uid' => $userInfo['id']));
        $msg = "连续签到次数:{$rewardMsg['continus_days']}.</br>上次签到时间" . date('Y-m-d H:i:s', $rewardMsg['last_time']);
        exit(iconv('UTF-8', 'GBK', $msg));
    }
   
    public function rechargeAction(){
    	 $userInfo  = Gionee_Service_User::getCurUserInfo();
    	$ret = User_Service_UMoneyOrder::generateOrder($userInfo['id'],0.01,0,2);
    	if(empty($ret)){
    		return 'dddd';
    	} 
    		$model = new Vendor_Money();
    		$data = $model->submit($ret);
    		if(!empty($data) && $data['status'] == '200010000'){
    			$token = $data['token_id'];
    			$config = Common::getConfig('moneyConfig','money_recharge_config');
    			$payUrl = $config['product']['pay_order_url'];
    			$this->redirect("{$payUrl}?token_id={$token}");
    			exit();
    		}
    		exit();
    }
    
   public function execAction(){
   		$start = microtime();
   		$where = array(
   			'score_type'	=>211,
   			'uid'					=>'65743',
   			'fk_earn_id'	=>'253',
   			'date'				=>'20150713',
   		);
   		$ret = User_Service_ScoreLog::getBy($where);
   		$end = microtime();
   		var_dump($end - $start,$ret);
   		exit();
   }
   
   public function recAction(){
   	$postData  = $this->getInput(array('status', 'oid', 'ordersuccesstime', 'err_msg'));
        $orderInfo = User_Service_Order::getBy(array('order_sn' => $postData['oid']));
        if(empty($orderInfo)){
        	error_log("订单不存在:".json_encode($postData)."\n",3,'/tmp/user_order_response_'.date('Ymd',time()).'log');
        	exit('Failed');
        }
        if(in_array($orderInfo['order_status'],array('-1','1'))){
        	exit('OK!');
        }
         $ret = self::_changeOrderStatausAndWriteLogs($postData, $orderInfo);
         if (in_array($postData['status'], array('1', '9'))) { //充值成功或失败时发站内信
            	$status = ($postData['status'] == '1') ? 1 : -1;//订单状态用于前台显示
            	User_Service_Order::changeScoresAndSendMsg($orderInfo, $status);
           }
       	 exit("OK");
   }
   
   private static function _changeOrderStatausAndWriteLogs($postData = array(), $order) {
   	$data = array();
   	switch ($postData['status']) {
   		case 0: {//进行中
   			$data['pay_status']   = 1;
   			$data['order_status'] = $data['shipping_status'] = $data['rec_status'] = 2;
   			break;
   		}
   		case 1: {//充值成功
   			$data['order_status'] = $data['shipping_status'] = $data['pay_status'] = $data['rec_status'] = 1;
   			break;
   		}
   		case 9: {//充值失败
   			$data['order_status'] = $data['pay_status'] = $data['shipping_status'] = $data['rec_code'] = -1;
   			break;
   		}
   		default:
   			break;
   	}
   	$data['desc']           = $postData['err_msg'];
   	$data['rec_order_time'] = strtotime($postData['ordersuccesstime']);
   	$result                 = User_Service_Order::update($data, $order['id']);//更新订单状态
   	//写订单状态更新日志
   	$logData = array(
   			'order_id'        => $order['id'],
   			'action_user'     => 'system',
   			'order_status'    => $data['order_status'],
   			'pay_status'      => $data['pay_status'],
   			'shipping_status' => $data['shipping_status'],
   			'add_time'        => time(),
   			'action_note'     => $data['desc']
   	);
   	$writeLog     = User_Service_Actions::add($logData);
   	//写Api调用返回结果的日志
   	$orderType = $order['order_type'] == 1 ? 'recharge' : 'flow';
   	$apiData   = array(
   			'status'   => $postData['ret_code'],
   			'add_time' => strtotime($postData['ordersuccesstime']),
   			'order_sn' => $postData['sporder_id'],
   			'desc'     => json_encode(array('orderinfo' => $postData)),
   			'api_type' => $orderType
   	);
   	$api       = User_Service_Recharge::add($apiData);
   	return $result && $writeLog;
   }
   
   public function hotBaiduAction(){
   	
   		$apiKeys = Gionee_Service_Baidu::apiKeys();
   		var_dump($apiKeys);
   		$navkeys = $this->updateKeywords();
   		
   		$hot = $this->getNavIndexWrods();
   		var_dump($hot);
 
   }
   
   
    public function updateKeywords() {
   	$items = array();
   	list($hotwords,$keywords) = self::formatBaibuHotWords();
   	array_shift($hotwords);//删除第一个热词
   	//手动添加的热词内容
   	$prev        = $next = array();
   	$t_bi        = Util_Cookie::get('3G-SOURCE', true);
   	$manualWords = Common::getCache()->get("manual_words");
   	if (empty($manualWords['prev']) && empty($manualWords['next'])) {
   		$where    = array(
   				'column_id'  => self::getColumnID(),
   				'status'     => 1,
   				'start_time' => array('<=', time()),
   				'end_time'   => array('>=', time())
   		);
   		$dataList = Gionee_Service_Ng::getsBy($where, array('sort' => 'ASC', 'id' => 'DESC'));
   		foreach ($dataList as $v) {
   			$temp = array('text' => $v['title'], 'url' => Common::clickUrl($v['id'], 'NAV', $v['link'], $t_bi));
   			if ($v['sort'] <= self::WIDGET) { //由于按升序排列，故sort值越小越在前面
   				$prev[] = $temp;
   			} else {
   				$next[] = $temp;
   			}
   		}
   		$manualWords = array('prev' => $prev, 'next' => $next);
   		Common::getCache()->set('manual_words', $manualWords, 60);
   	}
   	//加入手动词后
   	$finalHotWords = array();
   	foreach ($hotwords as $v) {
   		$bdurl           = Common::clickUrl(-100, 'BAIDU_HOT', Gionee_Service_Baidu::getSearchUrl(urlencode($v)), $t_bi);
   		$finalHotWords[] = array('text' => $v, 'url' => $bdurl);
   	}
   	$finalHotWords = array_merge(array_merge($manualWords['prev'], $finalHotWords), $manualWords['next']);
   
   	Common::getCache()->set("baidu_keywords", $keywords, Common::T_ONE_DAY);
   	Common::getCache()->set("baidu_hotwords", $finalHotWords, Common::T_ONE_DAY);
   
   	var_dump($keywords,$finalHotWords);
   	return array('items' => $items, 'keywords' => $keywords, 'hotwords' => $finalHotWords);
   }
   
   
   public function signAction(){
   	 	$postData = $this->getInput(array('username','date'));
   	 	$user = Gionee_Service_User::getUserByName($postData['username']);
   	 	$delete = User_Service_Earn::deletesBy(array('uid'=>$user['id']));
   	 	$time = strtotime($postData['date']." 05:30");
   	 	$earned           = Common_Service_User::getFinalScores($user['id']);
   	 	$ret = Common_Service_User::earnScoresByTask($user['id'],1,0,0,$earned);
   	 	$params = array(
   	 			'uid'      => $user['id'],
   	 			'group_id' => 1,
   	 			'cat_id'   =>0,
   	 			'goods_id' => 0,);
   	 	$data = User_Service_Rewards::getRwardGoodsInfo($params);
   	 	if(empty($data)){
   	 		$params['continus_days'] = 1;
   	 		$params['last_time'] =$time;
   	 		$res =  User_Service_Rewards::add($params);
   	 	}
   	 	if (date('Ymd', $data['last_time']) == date('Ymd', $time- 3600*24)) { //是否是连续操作
   	 		$data['continus_days'] += 1;
   	 	} else {
   	 		$data['continus_days'] = 1;//间断时，重新开始
   	 	}
   	 	$data['last_time'] = $time;
   	 	$res  =  User_Service_Rewards::edit($data, $data['id']);//更新记录
   	 	var_dump(date('Y-m-d H:i:s',$time),$earned,$ret,$res);exit;
   }


	public  function formatBaibuHotWords(){
		$items = Gionee_Service_Baidu::takeKeywords();
		//搜狐新闻页百度关键字
		$keywords = array();
		foreach ($items as $k => $v) {
			if ($k == 1 || mb_strlen($v['word'], 'utf8') < 6) {
				$keywords[] = $v['word'];
			}
		}
		
		//导航页百度热词
		$hotwords = array();
		foreach ($items as $k => $v) {
			$hotwords[] = $v['word'];
		}
		
		$tmpWords = array($keywords[0], $keywords[1], $keywords[2], $keywords[3]);
		$hotwords = array_diff($hotwords, $tmpWords);
		var_dump($items,$tmpWords,$hotwords);
		return array($hotwords,$keywords);
	}
   
	
	 public function getNavIndexWrods() {
		//百度默认关键字
		$hotwords = Common::getCache()->get("baidu_hotwords");
		var_dump($hotwords,'hotwords');
		$max_l    = 18;
		$words    = array();
		$words[]  = array_shift($hotwords);
		foreach ($hotwords as $val) {
			$l = mb_strlen($val['text'], 'utf8');
			if ($l < $max_l) {
				$max_l -= $l;
				$words[] = $val;
			}
			if ($max_l < 0 || count($words) == 2) {
				break;
			}
		}
		if (empty($words)) {
			$words[] = array('text' => $hotwords['text'], 'url' => '');
		}
		return $words;
	}
	
	public function checkExpireAction(){
		
		$ids = Event_Service_Link::checkExpirePrize(); 
		var_dump($ids);
		exit();
	}
	
	public function checkUsernameAction(){
		$postData  = $this->getInput(array('old','new'));
		if(empty($postData)) exit('false!');
		$userInfo = Gionee_Service_User::getUserByName($postData['old']);
		$ret  = Gionee_Service_User::updateUser(array('username'=>$postData['new'],'mobile'=>$postData['new']), $userInfo['id']);
		$viopUser = Gionee_Service_VoIPUser::getBy(array('user_phone'=>$postData['old']));
		$ret2 = Gionee_Service_VoIPUser::update(array("user_phone"=>$postData['new']), $viopUser['id']);
		$newUser = Gionee_Service_User::getUser($userInfo['id']);
		$newVUser = Gionee_Service_VoIPUser::get($viopUser['id']);
		var_dump("old",$userInfo,"\n","new:",$newUser,$viopUser,$newVUser);
		exit();
	}
	
	public function fsAction(){
		/* $orderSn = $this->getInput('order_sn');
		var_dump($orderSn);
		 $ret = User_Service_Order::flowOrderStatus($orderSn); */
		$ret  = User_Service_Order::verifyFlowOrder();
		var_dump($ret);
		exit("success");
	}
	
	public function brAction(){
		 $book = new Vendor_Book();
		$ret  = $book->totalRank(); 
		var_dump($ret);exit;
		
		
		/* $book = new Vendor_Book();
		$result = $book->download();
		var_dump($result);exit; */
		
	}
	
	
	public function downloadAction(){
		$typeList = array('totalrank','weekrank','monthrank');
		$type = $this->getInput('type');
		if(!in_array($type,$typeList)){
			var_dump("类型有错,可选的类型为:<br/>",$typeList);
			exit();
		}
		$book = new Vendor_Book();
		$ret = $book->download($type);
		var_dump($ret);exit();
	}
	
	public function addBooksAction(){
		$num = $this->getInput('num');
		$num = $num?$num:0;
		$book = new Vendor_Book();
		$book->addBooks($num);
	}
	
	public function allBooksAction(){
		$ch = $this->getInput('ch');
		$book = new Vendor_Book();
		$book->downloadAllBooks($ch);
	}
	
	//分机型广告
	public function mAdsAction(){
		$postData = $this->getInput(array('ip','model','version','imei'));
		$result = array();
		$ads    = Gionee_Service_Ng::getAds(); //所有的广告内容
		if (!empty($ads['nor_ad'])) {
			foreach ($ads['nor_ad'] as $s => $t) {
				$result[] = $t;
			}
		}
		Gionee_Service_Label::filterADData('nav', $ads['model_ad'], $result,$postData);
		$this->output(0, '', array('msg' => $result, 'ver' => 0, 'act11' => 0));
	}
	
 static public function filterADData($type, $modelArr, &$data,$modelTypes=array()) {
        $ua      = Util_Http::ua();
        $model   = $modelTypes['model']?$$modelTypes['model']:$ua['model'];
        $version =$modelTypes['version']? $modelTypes['version']:$ua['app_ver'];
        $ip      		= $modelTypes['ip']?$modelTypes['ip']:$ua['ip'];
        $imei    =  $modelTypes['imei']?$modelTypes['imei']:$ua['uuid_real'];
        $debugImei = filter_input(INPUT_GET, 'debugimei');
        if (empty($imei) && !empty($debugImei)) {
            $imei = $debugImei;
        }

        if (!empty($modelArr[1])) {
            $mids = Gionee_Service_ModelContent::curUserModelIds($model, $version, $ip, $type);
            foreach ($modelArr[1] as $m => $n) {
                if (!empty($mids) && in_array($n['model_id'], $mids)) {
                    $data[] = $n;
                }
            }
        }

        if (!empty($modelArr[2]) && !empty($imei)) {
            $imeiData    = Gionee_Service_Label::getCookieImeiData($imei);
            $oldimeiData = $imeiData;
            foreach ($modelArr[2] as $m => $n) {
                if (!empty($imeiData[$type][$n['id']])) {
                    $data[] = $n;
                } else {
                    $flag = Gionee_Service_Label::checkCacheImei($n['model_id'], $imei);
                    if ($flag) {
                        $data[] = $n;
                    }
                    //写Cookie
                    if (!isset($imeiData[$type][$n['id']])) {
                        $imeiData[$type][$n['id']] = $flag ? 1 : 0;
                    }
                }
            }
            if ($oldimeiData != $imeiData) {
                Gionee_Service_Label::setCookieImeiData($imei, $imeiData);
            }

        }
    }
    
    
   	public function maddAction(){
   		$input = $this->getInput('i');
   		$step = max(1,$input);
   		$start = microtime();	
    	$array = array();
    	$prizeList = array('1','2','3','4','5','6');
    	for($i=($step-1) * 50000;$i<$step*50000;$i++){
    		$key = array_rand($prizeList,1);
    		$array[] = array(
    			'id'	=>'',
    			'uid'=>16000+$i,
    			'prize_id'		=>$prizeList[$key],
    			'prize_status'	=>0,
    			'user_ip'		=>Util_Http::getClientIp(),
    			'add_time'=>time(),
    			'get_time'	=>0,
    			'expire_time'=>0,
    			'add_date'=>date("Ymd",time()),
    			'pop_status'=>0,
    		);
    	}
    	$ret = Event_Service_Activity::getResultDao()->mutiInsert($array);
    	$end = microtime();
    	var_dump(abs($end-$start));exit;
    }
    
   public function gqcAction(){
   		$ids = Event_Service_Activity::updateExpiredPrizeStatus();
   		var_dump($ids);
   		exit('成功');
   }
   
   public function  dailyDataAction(){
   		$date = $this->getInput('date');
   		if(empty($date)){
   			$date = date("Ymd",time());
   		}
   		$res = Common::getCache();
   		
   		$data          = array();
   		$p['add_time'] = array(array('>=', strtotime($date)), array('<=', strtotime($date."235959")));
   		$signin        = User_Service_Earn::getPerDayUserAmount($p); //当天签到数据
   		foreach ($signin as $k => $v) {
   			if ($v['group_id'] == '1') {
   				$data['user_signin_num']    = $v['user_number'];
   				$data['user_signin_scores'] = $v['earn_scores'];
   			} elseif ($v['group_id'] == '2') {
   				$data['user_earn_num']    = $v['user_number'];
   				$data['user_earn_amount'] = $v['earn_scores'];
   			}
   		}
   		//任务完成数
   		$params                    = array_merge($p, array('cat_id' => array('>', 0)));
   		$tasks                     = User_Service_Earn::getDoneTasksData($params);
   		$data['user_tasks_num']    = $tasks['user_number'];
   		$data['user_tasks_amount'] = $tasks['times'];
   		//用户兑换信息
   		$exchanges                            = User_Service_Order::getUserExchangeMsg($p);
   		$data['user_exchange_total']          = $exchanges['total_orders'];
   		$data['user_exchange_num']            = $exchanges['user_amount'];
   		$data['user_exchange_scuessed_times'] = $exchanges['successed_orders'];
   		$data['user_exchange_costs']          = $exchanges['total_costs'];
   		//金币流通情况
   		$scoreParams = array();
   		$scoreParams['created_time'] = array("<=",date("Y-m-d",strtotime($date)));
   		$userMsg                      = User_Service_Gather::getSumScoresInfo($scoreParams);
   		$data['user_total_number']    = $userMsg['total_users'];
   		$yestParams =array(
   				'date' => date('Ymd', strtotime("$date -1 day")),
   				'type' => 'user',
   				'key'  => 'user_total_number'
   		);
   		$yestdayUserAmount            = Gionee_Service_Log::getBy($yestParams);
   		$lastTotal                    = $yestdayUserAmount ? $yestdayUserAmount['val'] : 0;
   		$data['user_day_incre']       = $data['user_total_number'] - $lastTotal; //每天新增用户数
   		$data['user_currency_scores'] = $userMsg['total_remained_scores'];
   		foreach ($data as $k => $v) {
   			$params = array(
   					'type' => 'user',
   					'key'  => $k,
   					'val'  => $v,
   					'date' =>$date,
   			);
   			$info   = Gionee_Service_Log::getBy(array(
   					'type' => $params['type'],
   					'key'  => $params['key'],
   					'date' => $params['date']
   			));
   			if ($info) {
   				Gionee_Service_Log::set($params, $info['id']);
   			} else {
   				Gionee_Service_Log::add($params);
   			}
   		}
   		var_dump($data);
   		exit('成功');
   }
 
   public function addBlackUserAction(){
   		set_time_limit(0);
   		$page = 3;
   		do{
	   	 	list($totalIds,$updatedIds,$hasNext ) = User_Service_Earn::getAllUserIdsFromSameIP(3,$page,1);
	   	 	echo "page:{$page}!<br/>";
	   	 	$page++;
	   	 	sleep(2);
	   	 	echo "begin</br>";
   		}while($hasNext);
   		echo "<br/>finished!<pre>";
   		var_dump($totalIds);
   	 	exit('successed');
   }
   
   public function invalidIpAction(){
   	$date = $this->getInput('date');
   !$date&&	$date = date("Ymd",strtotime('-1 day'));
   	$num = $this->getInput('num');
   	$num = max(3,$num);
   	$dataList  = User_Service_Earn::getDubiousIpData($date, $num);
   	$i = 0;
   	foreach($dataList as $k=>$v){
   		$data = User_Service_DubiousIp::getBy(array('user_ip'=>$v['user_ip']));
   		if(empty($data)){
   			$params  = array();
   			$params['user_ip'] = $v['user_ip'];
   			$params['add_time'] = time();
   			$ret = User_Service_DubiousIp::add($params);
   			++$i;
   		}
   	}
   	var_dump("{$i} successed");
   	exit();
   }
   
   public function invalidIdsAction(){
   		set_time_limit(0);
   		$page = $this->getInput('page');
   		$page = max($page,1);
   		$date = $this->getInput('date');
   		!$date && $date = date("Ymd",strtotime("-1 day"));
   		list($total,$ipList) = User_Service_DubiousIp::getList($page, 10, array('status'=>0), array('id'=>'ASC'));
   		$pageNum = (int)ceil($total/5);
   		$num = 0;
   		foreach ($ipList as $k=>$v){
   			$where = array();
   			$where['add_date'] = $date;
   			$where['user_ip'] = $v['user_ip'];
   			$dataList = User_Service_Earn::getsBy($where);
   			foreach ( $dataList as $m=>$n){
   				$ipUser = User_Service_DubiousIpUser::getBy(array("uid"=>$n['uid']));
   				if(empty($ipUser)){
   					$params = array();
   					$params['pid'] = $v['id'];
   					$params['uid'] = $n['uid'];
   					$params['add_time'] = time();
   					$params['status'] = 0;
   					$params['add_date'] = date("Ymd",time());
   					User_Service_DubiousIpUser::add($params);
   					++$num;
   				}
   			}
   			User_Service_DubiousIp::edit(array("status"=>1), $v['id']);
   		}

   		
   		 if($page>= $pageNum){
   			self::$num = 1;
   		} 
   		print_r("{$num} successed<br/>   current page: {$page}");
   		exit();
   }

   public function changeUserAction(){
   			User_Service_DubiousIpUser::changeUserStatus();
   			exit();
   			$page = $this->getInput('page');
   			$page = max(1,$page);
   			$pageSize = 20;
   			$where = array();
   			$where['satus'] = 0;
   			list($total,$dataList ) = User_Service_DubiousIpUser::getList($page, $pageSize, array('status'=>0), array('id'=>'ASC'));
   			foreach($dataList as $k=>$v){
   				$user = Gionee_Service_User::getUser($v['uid']);
   				if(!$user['is_black_user']){
   					Gionee_Service_User::updateUser(array("is_black_user"), $v['uid']);
   				}
   				User_Service_DubiousIpUser::edit(array("status"=>1), $v['id']);
   			}
   			exit('successed');
   }
   
   public function userdataAction(){
   	 $data = file_get_contents('/home/panxb/桌面/2014-12.txt');
   	// var_dump($data);exit;
   	 $date  = $this->getInput('date');
   	list($year ,$month) = explode('-', $date);
   	$lastDay = Common::getMonthLastDay($year,$month);
   	$stime = strtotime($date);
   	$etime = strtotime("$date-$lastDay 23:59:59");
   //	var_dump($stime,$etime,date("Y-m-d H:i:s",$stime),date("Y-m-d H:i:s",$etime));exit;
   	 $data = explode(',', $data);
   	 $where = array();
   	 //$where['group_id'] = 2;
   	 $where['score'] = array("<=",20);
   	 $where['add_time']  = array(array(">=",$stime),array("<=",$etime));
   	 $where['uid'] = array("IN",$data);
   	 $ret = User_Service_Earn::getActiviteUsersNumber($where);
   	 var_dump($ret);exit; 
   }
   
   
   public function  sepAction(){
   			$sql = "SELECT g.goods_id,g.goods_name ,count(g.goods_id) as total,o.uid FROM user_order_goods as g,user_order_info as o 
							WHERE o.order_status = 1  and  o.id = g.order_id and g.created_time>='1441036800' and g.created_time<='1443628799' 
							GROUP BY  g.goods_id,o.uid ORDER BY total DESC";
   			$data = Db_Adapter_Pdo::fetchAll($sql);
   			$list =array();
   			$goods = array();
   			foreach ($data as $k=>$v){
   				$list[$v['goods_id']][$v['total']][] = $v['uid'];
   				$goods[$v['goods_id']] = $v['goods_name'];
   			}
   			$ret = array();
   			foreach ($goods as $m=>$n){
   				$tmp = $list[$m];
   				foreach ( $tmp as $s=>$t){
   					$ret[$m][$s] = count($t);
   					
   				}
   				$ret[$m]['name'] = $n;
   			}

   			print_r($ret);exit;
   }
}

