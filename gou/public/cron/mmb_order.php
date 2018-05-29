<?php 
include 'common.php';
$cache = Common::getCache();
$do = $cache->get("mmb_order_sync");
if ($do != "do") exit("nothing to do.\n");

$cache->set("mmb_order_sync", "doing");

function mmbOrderByDay($day) {
    $url = sprintf('http://qudao.ebinf.com/mmb-union/cpsStat.jsp?uuuu=jinliquan&pppp=maiM_17Gou&startDate=%s', $day);
    $dom = new DOMDocument('1.0', 'UTF8');
    $d = Util_Http::get($url);
    $d = str_replace(array('/\s/', '/bgcolor=".*?"/'), array("", ''), $d->data);
    $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $d);
    $dom->preserveWhiteSpace = false;
    $trs = $dom->getElementsByTagName('tr');
    $tj_arr = array();
    foreach($trs as $tr) {
        $id = intval(strip_tags($tr->childNodes->item(0)->textContent));
        $price = strip_tags($tr->childNodes->item(4)->textContent);
        $status = strip_tags($tr->childNodes->item(12)->textContent);
        $slit_price = strip_tags($tr->childNodes->item(16)->textContent);
        $channel_code = strip_tags($tr->childNodes->item(22)->textContent);
        if ($id) {
            $tj_arr[$channel_code]['channel_id'] = 3;
            ++$tj_arr[$channel_code]['order_total'];
            $tj_arr[$channel_code]['price_total'] += $price;
            $tj_arr[$channel_code]['channel_code'] = $channel_code;
            $tj_arr[$channel_code]['price_slit'] += $slit_price;
            if ($status == '已妥投' || $status == '已发货') {
                ++$tj_arr[$channel_code]['sure_order_total'];
                $tj_arr[$channel_code]['sure_price_total']+=$price;
            }
            $tj_arr[$channel_code]['dateline'] = $day;
        }
    }
    return $tj_arr;
}

for ($i = 0; $i<30; $i++) {
    $t = date('Y-m-d', strtotime(sprintf('-%d day', $i)));
    $data = mmbOrderByDay($t);
    if ($data) {
        foreach($data as $order) {
            $ret = Stat_Service_Order::getBy(array(
                'channel_code'=>$order['channel_code'],
                'channel_id'=>$order['channel_id'],
                'dateline'=>$order['dateline']));
            if ($ret) {
                Stat_Service_Order::update($order, $ret['id']);
                //echo $t,",",$ret['id'], " update done.\n";
                continue;
            }
            Stat_Service_Order::add($order);
            //echo $t,",",$order['channel_code'], " add done.\n";
        }
        $str = $t . " data update or add done.";
        Common::log($str, "mmb_order.log");
    }
    sleep(1);
}
$cache->set("mmb_order_sync", "done");
exit;
?>
