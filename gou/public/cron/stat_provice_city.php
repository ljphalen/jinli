<?php include 'common.php';
/**
 *  * 点击量统计
 *   */
$total = 0;
$url = 'http://ip.taobao.com/service/getIpInfo.php?ip=';
do {
    list($total, $result)= Stat_Service_Log::getList(0, 100, array('province_id'=>0));
    echo $total . " need deal.\n";
    foreach($result as $key=>$value) {
        //ip为0.0.0.0
        if ($value['ip'] == '' || ip2long($value['ip']) == 0) {
            $ipinfo = array('region'=>'-', 'city'=>'-', 'region_id'=>1, 'city_id'=>1);
        } else {
            $params = array('start_long'=>array('<', ip2long($value['ip'])), 'end_long'=>array('>', ip2long($value['ip'])));
            //$ipinfo = Stat_Service_IpTable::getBy($params);

            //if (!$ipinfo) {
                $ret = Util_Http::get($url.$value['ip']);
                if ($ret->state == 200) {
                    $res = json_decode($ret->data, true);
                    if ($res['code'] == 0) {
                        $ipinfo = $res['data'];
                        /*Stat_Service_IpTable::add(array(
                            'start'=>$ipinfo['start'],
                            'start_long'=>ip2long($ipinfo['start']),
                            'end'=>$ipinfo['end'],
                            'end_long'=>ip2long($ipinfo['end']),
                            'country'=>$ipinfo['country'],
                            'province'=>$ipinfo['province'],
                            'city'=>$ipinfo['city'],
                            'district'=>$ipinfo['district'],
                            'isp'=>$ipinfo['isp'],
                        ));*/
                    } else {
                        echo $res['code'] . " get remote ipinfo faild.\n";
                    }
                /*} else {
                    echo $value['id']. " get info request with state:".$ret->state.".\n";
                }*/
            }
        }

        if ($ipinfo) {
            $ret = Stat_Service_Log::update(array(
                'province'=>$ipinfo['region'],
                'province_id'=>$ipinfo['region_id'],
                'city'=>$ipinfo['city'],
                'city_id'=>$ipinfo['city_id'],
            ), $value['id']);
            if ($ret) echo $value['id']. " update ip done.\n";
        }
    }
    sleep(1);
} while ($total > 100); 

