<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 云之讯回调接口
 * @author huwei
 */
class TelController extends Api_BaseController {
    public function indexAction() {
        $data     = $data1 = array();
        $postData = file_get_contents("php://input");

        if (!empty($postData)) {
            $data = (array)simplexml_load_string($postData);
        }

        $data['called'] = str_ireplace('0086', '', $data['called']);
        $data['caller'] = str_ireplace('0086', '', $data['caller']);

        if (!empty($data['event'])) {
            $outArr = array();
            switch ($data['event']) {
                case 'callreq':
                    $outArr = $this->callreq($data);
                    break;
                case 'callestablish':
                    $outArr = $this->callestablish($data);
                    break;
                case 'callhangup':
                    $outArr = $this->callhangup($data);
                    break;
            }
            $xml = new Util_XmlParser();
            $out = $xml->parseToXml($outArr);

            header("Content-Type:text/xml;charset=utf-8");
            header("Content-Length:" . strlen($out));
            echo $out;
        }
        exit;
    }

    public function billListAction() {
        $telObj = new Vendor_Tel();
        $date   = $this->getInput('date');
        $caller = $this->getInput('caller');
        if (empty($date)) {
            $date = 'day';
        }
        $info = $telObj->getClientInfo($caller);
        $out  = $telObj->getClientBillList($info['clientNumber'], $date);
        $url  = "{$out['downUrl']}?token={$out['token']}";
        echo $url;
        exit;

    }

    public function callreq($data) {
        $now    = time();
        $caller = Gionee_Service_VoIPClient::getMobileNumber($data['caller']);

        if (empty($caller)) {
            $outArr = array(
                'response' => array(
                    'retcode' => '1',
                    'reason'  => '失败',
                ),
            );
            Gionee_Service_ErrLog::add(array('type' => 'voip_ucpaas', 'msg' => $data['caller'] . '-' . $caller));
            return $outArr;
        }

        $area   = Gionee_Service_VoIP::getAreaCode($data['called']);
        $params = array(
            'caller_phone' => $caller,
            'called_phone' => $data['called'],
            'called_time'  => $now,
            'area'         => $area,
            'date'         => date('Ymd', $now),
            'identifier'   => $data['callid'],
        );

        Gionee_Service_VoIPLog::add($params);
        $userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($caller);
        $diff         = $userVoipInfo['m_sys_sec'] + $userVoipInfo['exchange_sec'];
        $t            = floor($diff / 60) * 60;//值必须为60的倍数
        $outArr       = array(
            'response' => array(
                'retcode'         => '0',
                'reason'          => '成功',
                'displaynumber'   => $caller,
                'allowedcalltime' => strval($t),
            ),
        );
        //Gionee_Service_VoIP::log(array(__METHOD__, $outArr));
        return $outArr;
    }

    public function callestablish($data) {
        $param = array('connected_time' => time());
        $where = array('identifier' => $data['callid']);
        Gionee_Service_VoIPLog::updateBy($param, $where);
        //Gionee_Service_VoIP::log(array(__METHOD__, $param, $where));
        $outArr = array(
            'response' => array(
                'retcode' => '0',
                'reason'  => '成功',
            ),
        );

        return $outArr;
    }

    public function callhangup($data) {
        $where = array('identifier' => $data['callid']);
        $eTime = strtotime($data['stoptime']);
        $sTime = strtotime($data['starttime']);
        $diff  = $eTime - $sTime;
        $param = array(
            //'connected_time' => $sTime,
            'hangup_time' => $eTime,
            'duration'    => $diff
        );

        $row          = Gionee_Service_VoIPLog::getBy($where);
        $userVoipInfo = Gionee_Service_VoIPUser::getInfoByPhone($row['caller_phone']);
        list($leftSec, $upData) = $this->_calcLeftSec($userVoipInfo['m_sys_sec'], $userVoipInfo['exchange_sec'], $diff);
        if (!empty($upData) && $leftSec >= 0) {
            Gionee_Service_VoIPUser::set($upData, $userVoipInfo['id']);
        }


        Gionee_Service_VoIPLog::updateBy($param, $where);
        //Gionee_Service_VoIP::log(array(__METHOD__, $param, $where,$upData));
        $outArr = array(
            'response' => array(
                'retcode' => '0',
                'reason'  => '成功',
            ),
        );

        return $outArr;
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


}
