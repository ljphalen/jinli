<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Apk_WinprizeController extends Api_BaseController {


    /**
     * 活动记录列表
     */
    public function activityAction(){
        $perpage = 7;
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');
        $condition = array('uid' => $uid,'shortest_time'=>array('>',0));
        $sort =  array('create_time' => 'DESC');
        list(, $data) = Cut_Service_User::getList($page, $perpage, $condition, $sort);
        $tmp = Common::resetKey($data, 'goods_id');
        $goods_condition = array('id' => array('IN', array_keys($tmp)));
        list(, $goods) = Cut_Service_Goods::getsBy($goods_condition, array('start_time'=>'DESC'));

        $act_con = array('uid' => $uid,'fin_time'=>array('>',0), 'goods_id' => array('IN', array_keys($tmp)));
        list(, $activitys) = Cut_Service_Game::getsBy($act_con, array('fin_time' => 'ASC'));
        $activity = array();
        foreach ($activitys as $ac) {
            $act['fin_time']    = $ac['fin_time'];
            $act['goods_id']    = $ac['goods_id'];
            $act['create_time'] = $this->getTimeString($ac['create_time'],1);
            $activity[]=$act;
        }

        $activity = $this->groupByKey($activity, 'goods_id');
        $tmp = Common::resetKey($goods,'store_id');
        list(, $store) = Cut_Service_Store::getsBy(array('id' => array('IN', array_keys($tmp))));
        $store = Common::resetKey($store,'id');
        $rows = array();
        foreach ($goods as $k => $v) {
            $row['id'] = $v['id'];
            $row['no'] =  $v['no'];
            $row['title'] = $v['title'];
            $row['date'] = $this->getTimeString($v['start_time']);
            $row['image'] = Cut_Service_Goods::getImageLink($store[$v['store_id']]['img'],400);
            $row['activity'] = isset($activity[$v['id']])?array_slice($activity[$v['id']],0,3):array();
            $rows[] = $row;
        }
//        $hasnext = (ceil((int)$total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $rows, 'hasnext' => false, 'curpage' => $page,));
    }

    public function getTimeString($time,$type = 0)
    {
        if (!is_numeric($time)) {
            $time = strtotime($time);
        }

        if($type){
            return date('H:i',$time);
        }
        $time = strtotime(date('Y-m-d',$time));
        $time_diff = time() - $time;
        if ($time_diff >= 172800) { //大于2天
            $day = date('m-d', $time);
        } elseif ($time_diff >= 86400) {//大于1天
            $day = "昨天";
        } else {
            $day = "今天";
        }

        return $day;
    }


    /**
     * 助力
     * @input goods_id
     * @input uid
     */
    public function refuelAction(){
        $goods_id = $this->getInput('goods_id');
        $uid = $this->getInput('uid');
        if(!$goods_id||!$uid){
            $this->output(-1, '非法请求.');
        }
        
        $num_sid = unserialize(Util_Cookie::get('num_sid', true));
        $num_sid = $num_sid?$num_sid:array();
        if(!empty($num_sid)&&in_array($uid.$goods_id,$num_sid)){
            $this->output(-1,'不能重复加速');
        }
        array_push($num_sid,$uid.$goods_id);
        Util_Cookie::set('num_sid', serialize(array_unique($num_sid)), true, strtotime("+7 days"), '/', $this->getDomain());
        Cut_Service_User::increment(array('goods_id' => $goods_id, 'uid' => $uid), 'speedup');
        $this->output(0,'成功为好友增加10点速度值');
    }


    /**
     * 获取助力状态
     * @input goods_id
     * @input uid
     */
    public function getrefuelStatusAction(){
        $goods_id = $this->getInput('goods_id');
        $uid = $this->getInput('uid');

        $goods = Cut_Service_Goods::get($goods_id);

        if ($goods['start_time'] > time()) { //未开始
            $class = 'cheer';
        } elseif ($goods['end_time'] < time()) { //已结束
            $class = 'finish';
        } else {//正在进行
            $class = 'cheer';
        }

        $num_sid = unserialize(Util_Cookie::get('num_sid', true));
        if (in_array($uid . $goods['id'], $num_sid) && $class != 'finish') {
            $class = 'want';
        }

        $goods['time_str'] = $this->getTimeString($goods['start_time'], $goods['end_time']);
        $store = Cut_Service_Store::getStore($goods['store_id']);
        $goods['image'] = Cut_Service_Goods::getImageLink($store['img'], 400);


        $this->output(0,'',array($class));
    }
    /**
     * 中奖记录
     */
    public function awardAction(){
        $perpage = 10;
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');


        list($total,$goods) = Cut_Service_Goods::getList($page,$perpage,array('uid'=>$uid,'status'=>array('IN',array(2,3,4))),array('end_time'=>'DESC'));
        if(empty($goods)){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => $page,));
        }
        $goods_ids = array_keys(Common::resetKey($goods,'id'));
        list(,$orders) = Gou_Service_Order::getsBy(array('show_type'=>4,'goods_id'=>array("IN",$goods_ids)));


        $orders = Common::resetKey($orders,'goods_id');

        $store_ids = array_keys(Common::resetKey($goods,'store_id'));
        list(, $store) = Cut_Service_Store::getsBy(array('id' => array('IN', $store_ids)));
        $store = Common::resetKey($store, 'id');

        $order_ids = array_keys(Common::resetKey($orders,'id'));
        $addr = Gou_Service_Order::getOrdersAddress($order_ids);
        $addr = Common::resetKey($addr, 'order_id');
        $rows = array();
        foreach ($goods as $v) {
            $order = $orders[$v['id']];
            if(empty($order)) {
                if($v['status'] == 2||$v['status'] == 3){
                    $status = 0;
                }else {
                    $status = 4;//已关闭
                }
            }else{
                if ($order['status'] <= 3) {
                    $status = 1;//代发货
                } elseif ($order['status'] > 3&&$order['status']<5) {
                    $status = 2;//已发货
                }elseif($order['status']==5){
                    $status = 3;//已签收
                }elseif($order['status']==6){
                    $status = 4;//已关闭
                }
            }
            $address = $addr[$order['id']];
            if(!empty($address)){
                $item['mobile']     = $address['mobile'];
                $item['buyer_name'] = $address['buyer_name'];
                $item['address']    = $this->_getAddress($address);
            }
            $item['goods_id']      = $v['id'];
            //快递
            if($order['express_code']) {
                $express = explode(',', html_entity_decode($order['express_code']));
                $item['express_code']  = $express[1];
                $item['express_url']   = 'http://m.kuaidi100.com/index_all.html?type='.$express[0].'&postid='.$express[1].'&callbackurl='.urlencode($webroot.'/cutorder/list');
            }
            $item['title']         = $v['title'];
            $item['no']            = $v['no'];
            $item['status']        = $status;
            $item['image']         = Cut_Service_Goods::getImageLink($store[$v['store_id']]['img'],400);;
            $item['shortest_time'] = $v['shortest_time'];
            $rows[] =$item;
        }

        $hasnext = (ceil((int)$total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $rows, 'hasnext' => $hasnext, 'curpage' => $page,));
    }


    /**
     * 中奖记录
     */
    public function awardlistAction(){
        $perpage = 10;
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;
        $uid = Common::getAndroidtUid();
        if(!$uid) $this->output(-1, '非法请求.');
        list($total,$orders) = Gou_Service_Order::search($page, $perpage, array('out_uid' => $uid,'show_type'=>4));
        $orders = Common::resetKey($orders,'goods_id');
        if($page==1){
            list(, $not_send) = Cut_Service_Goods::getsBy(array('uid'=>$uid,'status'=>array('IN', array(2,3))));
            $goods_ids = array_merge(array_keys(Common::resetKey($not_send,'id')),array_keys($orders));
        }else{
            $goods_ids = array_keys($orders);
        }
        if(empty($goods_ids)){
            $this->output(0, '', array('list' => array(), 'hasnext' => false, 'curpage' => $page,));
        }

        //获取已经中奖未读取
        list(,$goods) = Cut_Service_Goods::getsBy(array('id'=>array('IN',array_unique($goods_ids))),array('end_time'=>'DESC'));
        $store_ids = array_keys(Common::resetKey($goods,'store_id'));
        list(, $store) = Cut_Service_Store::getsBy(array('id' => array('IN', $store_ids)));
        $store = Common::resetKey($store, 'id');


        $order_ids = array_keys(Common::resetKey($orders,'id'));
        $addr = Gou_Service_Order::getOrdersAddress($order_ids);
        $addr = Common::resetKey($addr, 'order_id');
        $rows = array();
        foreach ($goods as $v) {
            $order = $orders[$v['id']];
            $status = 0;//已发货
            if(!empty($order)){
                if ($order['status'] <= 3) {
                    $status = 1;//代发货
                } elseif ($order['status'] > 3&&$order['status']<5) {
                    $status = 2;//已发货
                }elseif($order['status']==5){
                    $status = 3;//已签收
                }elseif($order['status']==6){
                    $status = 4;//已关闭
                }
            }elseif ($v['status'] == 2||$v['status'] == 3) {
                $status = 0;
            }
            $address = $addr[$order['id']];
            if(!empty($address)){
                $item['mobile']     = $address['mobile'];
                $item['buyer_name'] = $address['buyer_name'];
                $item['address']    = $this->_getAddress($address);
            }
            $item['goods_id']      = $v['id'];
            //快递
            if($order['express_code']) {
                $express = explode(',', html_entity_decode($order['express_code']));
                $item['express_code']  = $express[1];
                $item['express_url']   = 'http://m.kuaidi100.com/index_all.html?type='.$express[0].'&postid='.$express[1].'&callbackurl='.urlencode($webroot.'/cutorder/list');
            }
            $item['title']         = $v['title'];
            $item['no']            = $v['no'];
            $item['status']        = $status;
            $item['image']         = Cut_Service_Goods::getImageLink($store[$v['store_id']]['img'],400);;
            $item['shortest_time'] = $v['shortest_time'];
            $rows[] =$item;
        }

        $hasnext = (ceil((int)$total / $perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $rows, 'hasnext' => $hasnext, 'curpage' => $page,));
    }

    private function _getAddress($address){
        $province =explode('|',$address['province'])[0];
        $city =explode('|',$address['city'])[0];
        $country =explode('|',$address['country'])[0];
        return  sprintf("%s%s%s%s",$province,$city,$country,$address['detail_address']);
    }

    /**
     * 助攻
     */
    public function groupByKey($source,$key){
        foreach ($source as $row) {
            $res[$row[$key]][]=$row;
        }
        return $res;
    }

    /**
     * 列表
     */
	public function listAction() {
		//get uid
		$uid = Cut_Service_Goods::getUid();
		$time = Common::getTime();

		if(!$uid)  $this->clientOutput(-1, '非法请求!');

		//正在进行
	    list(, $cut1) = Cut_Service_Goods::getList(1, 1, array('status'=>1, 'start_time'=>array('<=', $time), 'end_time'=>array('>=', $time)), array('start_time'=>'ASC', 'id'=>'ASC'));
		//一条预热

		list(, $cut2) = Cut_Service_Goods::getList(1, 5, array('status'=>1, 'start_time'=>array('>', $time)), array('start_time'=>'ASC', 'id'=>'ASC'));

        //已结束
        $close_time = strtotime(Gou_Service_Config::getValue('cut_close_time'));
        $params = array('end_time' => array('<', Common::getTime()), 'start_time' => array('>', $close_time));
        $sort = array('end_time' => 'DESC', 'id' => 'DESC');
        list(, $cut3) = Cut_Service_Goods::getList(1, 5, $params, $sort);

        $cut_list = array_merge($cut1, $cut2, $cut3);

	    $data = array();
	    $webroot = Common::getWebRoot();

	    if($cut_list) {
	        $cut_ids = array_keys(Common::resetKey($cut_list, 'id'));
	        $goods_ids = array_keys(Common::resetKey($cut_list, 'store_id'));

	        //goods
	        list(, $goods) = Cut_Service_Store::getsBy(array('id'=>array('IN', $goods_ids)));
	        $goods = Common::resetKey($goods, 'id');

	        foreach ($cut_list as $key=>$value) {
	            //时间处理
	            $start_time = Cut_Service_Goods::formatTime($value['start_time']);
	            $end_time = Cut_Service_Goods::formatTime($value['end_time']);

                if (date("Ymd", $value["start_time"]) == date("Ymd", $value["end_time"])) {
                    $end_time = date("H:i", $value["end_time"]);
                }
	          /*  if(Util_String::substr($start_time, 0, 2) == Util_String::substr($end_time, 0, 2)) {
	                $len = Util_String::strlen($end_time);
	                $end_time = Util_String::substr($end_time, 2, $len);
	            }*/
                $goods_item =$goods[$value['store_id']];

	            $data[$key]['id'] = $value['id'];
	            $data[$key]['no'] = '第'.$value['no'].'期';
	            $data[$key]['goods_img'] = Cut_Service_Goods::getImageLink($goods[$value['store_id']]['img'],400);
	            $data[$key]['goods_title'] = html_entity_decode($goods_item['title']);
	            $data[$key]['share_title'] = $goods_item['share_title']?html_entity_decode($goods_item['share_title']):html_entity_decode($goods_item['title']);
	            $data[$key]['rank_url'] = $webroot.'/winprize/rank?id='.$value['id'];
	            $data[$key]['share_url'] = $webroot.'/winprize/commonon?id='.$value['id'].'&fuid='.Common::encrypt($uid);
	            $data[$key]['price'] = number_format($value['price'], 2);
                $data[$key]['join_count'] = '';
	            if($value['status'] > 0) {
	                $data[$key]['join_count'] =  '已有'.intval($value['join_count']).'人参加';
	            }
	            $data[$key]['shortest_time'] =  number_format($value['shortest_time'], 2);
	            $data[$key]['start_time'] =  $start_time;
	            $data[$key]['end_time'] =  $end_time;
	            $data[$key]['status_code'] =  $this->_getStatusCode($value);
	        }
	    }

	    //notice, 如果没有时，显示 邀请好友加油可以提高游戏速度喔！
        $params = array('end_time'=> array('<', Common::getTime()), 'start_time'=>array('>', $close_time), 'uid' => array('!=', ''));
        $sort = array('end_time'=>'DESC', 'id'=>'DESC');
        list(, $cut3) = Cut_Service_Goods::getList(1, 3, $params, $sort);
	    if($cut3) {
//	        $list = array_slice($cut3, 0, 3);
            //users
            $uids = array_keys(Common::resetKey($cut3, 'uid'));
            //$users = User_Service_Uid::getsBy(array('uid' => array('IN', $uids)));
            $users = User_Service_Uid::getUsersFmtByUid($uids);
            $users = Common::resetKey($users, 'uid');
            $notices = array();
            foreach ($cut3 as $key => $value) {
                $notices[] = array(
                    'id' => $value['id'],
                    'content' => '恭喜<font color="red">' . Util_String::substr($users[$value['uid']]['nickname'], 0, 5, '', true) . '</font>成为第' . $value['no'] . '期速度之王，免费获得奖品'
                );
            }
	    } else {
	        $notices = array(0=>array('id'=>0, 'content'=>'邀请好友加油可以提高游戏速度喔！'));
	    }

	    $this->output(0, '', array('list'=>$data, 'notice'=>$notices));
	}
	
	
	

    /**
     * @param $value
     * @return int status 0:未开始 1:进行中 2:已结束
     */
	private function _getStatusCode($value) {
	    $time = Common::getTime();
	    //已结束
	    if($value['end_time'] < $time || $value['status'] >= 2)  return 2;
	    //未开始
	    if($value['start_time'] > $time) return 0;
	    
	    //正在进行
	    if($value['start_time'] <= $time && $time < $value['end_time'] && $value['status'] == 1)  return 1;
	}

    /**
     * 游戏详情
     */
    public function detailAction(){
        $uid = Common::getAndroidtUid(); //$uid = 'e9522343639800000e2849ada8177e79';
        if(!$uid) $this->output(-1, '非法请求.');

        $id = intval($this->getInput('id'));  //$id = 531;
        if(!$id) $this->output(-1, '非法请求.');

        $goods = Cut_Service_Goods::get($id);
        if(!$goods) $this->output(-1, '操作失败[1].');

        //活动状态
        $goods_status = $this->_getStatusCode($goods);

        //玩家信息
        $user = Cut_Service_User::getByUidAndGoods($uid, $id);
        if($user){
            $new_user = false;
        }else{
            $new_user = true;
            $user = array('uid' => $uid, 'goods_id' => $id);
            if($goods_status === 0 || $goods_status === 1){
                list($result, $user) = Cut_Service_User::addUser($user);
                if(!$result) $this->output(-1, '操作失败[2].');
            }
        }

        //活动日期
        $goods_date_prefix = '今日';
        $today_time_s = strtotime(date('Y-m-d', Common::getTime()));
        if($goods['start_time'] < $today_time_s || $goods['start_time'] >= ($today_time_s+86400)) $goods_date_prefix = date('m-d', $goods['start_time']);
        $goods_time = sprintf('%s %s-%s', $goods_date_prefix, date('H:i', $goods['start_time']), date('H:i', $goods['end_time']));

        //获取暴击参数(是否暴击和暴击倍数)
//        list($is_critical_hit, $critical_hit_times) = $this->_lottery();
        $critical_hit_pro = intval(Gou_Service_Config::getValue('cut_game_probability'))/100;
        $critical_hit_times = intval(Gou_Service_Config::getValue('cut_game_times'));
        if($critical_hit_pro > 1) $this->output(-1, '参数错误1.');

        //剩余可用概率
        $remain_pro = 1 - $critical_hit_pro;

        //活动参数
        $goods['min_price'] = floatval($goods['min_price']);
        $goods['range'] = floatval($goods['range']);
        if(($goods['range'] - $goods['min_price']) < 0) $this->output(-1, '参数错误2.');

        $increase = $goods['increase'];
        $play_count = $user['count'];

        //前3个商品已经有过第一名的, 就不加速
        list(, $last_over_goods) = Cut_Service_Goods::getList(1, 3, array('end_time' => array('<', Common::getTime())), array('end_time' => 'DESC', 'id' => 'DESC'));
        $last_uids = array_keys(Common::resetKey($last_over_goods, 'uid'));
        unset($last_over_goods);
        if(!in_array($uid, $last_uids)){
            $goods['min_price'] += $increase*$play_count;
            $goods['range'] += $increase*$play_count;
        }

        $range_l_r = $goods['min_price'] + floatval(number_format(($goods['range'] - $goods['min_price'])/2, 2));
        $range_r_l = $goods['range'] - floatval(number_format(($goods['range'] - $goods['min_price'])/2, 2));

        $params = array(
            'range_l_l' => $goods['min_price'],
            'range_l_r' => $range_l_r,
            'range_r_l' => $range_r_l,
            'range_r_r' => $goods['range'],
        );

        //默认概率
        if($critical_hit_pro){
            $params['range_l_perc'] = $remain_pro;          //左区间默认概率
            $params['range_r_perc'] = 0;                    //左区间默认概率
        }

        //根据加速等级分布左右区间概率
        if($user['speedup'] > 10) $user['speedup'] = 10;
        if($user['speedup'] >= 0){
            $params['range_l_perc'] = (1 - $user['speedup']/10) * $remain_pro;
            $params['range_r_perc'] = ($user['speedup']/10) * $remain_pro;
        }
//        Common::log($params, 'cut.log');
        $shortest_time = $goods['shortest_time'] ? $goods['shortest_time'] : '暂无数据';

        //复活机会处理
        $remain_time = '';
        $remain_perc = 0;
        $opt_num = 0;
        if($user['opt_num'] == 0) list($opt_num, $remain_time, $remain_perc) = $this->_remain($user);

        $webroot = Common::getWebRoot();
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;

        $data = array(
            'id' => $id,
            'speedup' => intval($user['speedup']),
            'shortest_time' => number_format($shortest_time, 2),
            'opt_num' => intval($user['opt_num']) + $opt_num,
            'remain_perc' => $remain_perc,
            'remain_time' => $remain_time,
            'rank_url' => $webroot . '/winprize/rank?id=' . $id,
            'activity_time' => $goods_time,
            'activity_status' => $goods_status,
            'price' => $goods['price'],
            'params' => $this->_encrypt(json_encode($params)),
//            'params' => $params,
            'share_icon' => $staticroot . '/apps/gou/assets/img/logo.jpg',
            'share_title' => '好朋友就要讲义气！帮我加个油，让我登顶速度之王，赢得免费大奖！',
            'share_url' => $webroot. '/winprize/refuel?id='. $id.'&uid='.$uid,
            'critical_hit_pro' => $critical_hit_pro,
            'critical_hit_times' => $critical_hit_times
        );

        if($data['opt_num'] && $data['activity_status']){
            $log_msg = array(
                'uid' => $uid,
                'speedup' => $data['speedup'],
                'opt_num' => $data['opt_num'],
                'params' => $params,
                'critical_hit_pro' => $data['critical_hit_pro'],
                'critical_hit_times' => $data['critical_hit_times'],
                'play_count' => $play_count,
            );

            Common::getMongo()->insert('log',
                array(
                    'type'  => 8,
                    'msg'   => json_encode($log_msg),
                    'file'  => 'winprize-start',
                    'line'  => 0,
                    'time'  => time(),
                    'ip'   => ''
                )
            );
        }

        //获取商品的分享标题和图片
        $store = Cut_Service_Store::get($goods['store_id']);
        if($store){
            $data['share_title'] = $store['share_title'];
            $data['share_icon'] = Cut_Service_Goods::getImageLink($store['img'], 400);

        }

//        Common::log($data, 'cut.log');
        $this->output(0, '', $data);
    }

    /**
     * 复活机会处理
     * @param $user
     * @return array|false
     */
    private function _remain($user){
        list($remain_time, $remain_perc, $remain_opt_num) = Cut_Service_User::remainTime($user['remain_time']);
//        Common::log($remain_opt_num, 'cut.log');
        if($remain_opt_num){
//            $result = Cut_Service_User::update(array('opt_num' => $remain_opt_num, 'remain_time' => 0), $user['id']);
//            if($result) return array($remain_opt_num, '', 0);
            return array($remain_opt_num, '', 0);
//            $this->output(-1, '操作失败[4].');
        }
        return array(0, $remain_time, $remain_perc);
    }

    /**
     * 获取游戏暴击参数
     * @return array
     */
    private function _lottery(){
        $cut_game_probability = intval(Gou_Service_Config::getValue('cut_game_probability'));
        $cut_game_times = intval(Gou_Service_Config::getValue('cut_game_times'));

        if(!$cut_game_probability) return array(false, $cut_game_times);

        $awards = array(
            '1' => array('pro' => $cut_game_probability,  'info' => sprintf('%d%%概率', $cut_game_probability)),
            '2' => array('pro' => (100 - $cut_game_probability), 'info' => sprintf('%d%%概率', 100 - $cut_game_probability)),
        );

        Util_Lottery::setProField('pro');
        Util_Lottery::setAwards($awards);
        $lottery = Util_Lottery::roll();

        if($lottery['code'] == 0 && $lottery['roll_key'] == 1) return array(true, $cut_game_times);
        return array(false, $cut_game_times);
    }

    /**
     * des加密
     * @param $encrypt
     * @return string
     */
    private function _encrypt($encrypt){
        $salt = 'a3408da8';
        //$encrypt = utf8_encode ( $encrypt );
        // 根據 PKCS#7 RFC 5652 Cryptographic Message Syntax (CMS) 修正 Message 加入 Padding
        $block = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_ECB);
        $pad = $block - (strlen($encrypt) % $block);
        $encrypt .= str_repeat(chr($pad), $pad);

        // 不需要設定 IV 進行加密
        $passcrypt = mcrypt_encrypt(MCRYPT_DES, $salt, $encrypt, MCRYPT_MODE_ECB);
        return base64_encode($passcrypt);
    }


    /**
     * 玩游戏
     */
    public function playAction(){
        $uid = Common::getAndroidtUid(); //$uid = 'e9522343639800000e2849ada8177e79';
        if(!$uid) $this->output(-1, '非法请求.');

        $id = intval($this->getInput('id')); //活动ID $id = 8;
        if(!$id) $this->output(-1, '非法请求.');

        $goods = Cut_Service_Goods::get($id);
        if(!$goods) $this->output(-1, '该活动不存在.');

        if($this->_getStatusCode($goods) !== 1) $this->output(-1, '该活动已结束.');

        $user = Cut_Service_User::getBy(array('uid' => $uid, 'goods_id' => $id));
        if(!$user) $this->output(-1, '非法请求.');

        $user_log = array();
        if ($user['opt_num'] <= 0) {
            list($remain_time, $remain_perc, $remain_opt_num) = Cut_Service_User::remainTime($user['remain_time']);
            if ($remain_opt_num) {
                $user['opt_num'] = $remain_opt_num;
                $user_log['remain_time'] = 0;
            } else {
                $this->standOutput(0, '您的机会次数已用完.');
            }
        }

        //添加游戏用户数
        if($user['count'] == 0){
            $join_count = $goods['join_count'] + 1;
            $result = Cut_Service_Goods::updateGoods(array('join_count' => $join_count), $id);
            if(!$result) $this->output(-1, '操作失败[2].');
        }

        //更新玩家的机会次数和如果是机会为0, 则激活恢复时间
        $opt_num = $user['opt_num'] -1;
        $user_log['opt_num'] = $opt_num;
        if($opt_num == 0) $user_log['remain_time'] = Common::getTime();

        $user_log['count'] = $user['count'] + 1;

        $result = Cut_Service_User::update($user_log, $user['id']);
        if($result) $this->standOutput(0, '');
        $this->output(-1, '操作失败.');
    }

    /**
     * 游戏结束
     */
    public function overAction(){
        $uid = Common::getAndroidtUid(); //$uid = 'e9522343639800000e2849ada8177e79';
        if(!$uid) $this->output(-1, '非法请求.');

        $id = intval($this->getPost('id')); //活动ID $id = 8;
        if(!$id) $this->output(-1, '非法请求.');

        $input = $this->getPost(array('id', 'fin_time', 'sign'));
        $input['uid'] = $uid;
        $input = $this->_cookData($input);

        $user = Cut_Service_User::getByUidAndGoods($uid, $id);
        if(!$user){
            $user = array('uid' => $uid, 'goods_id' => $id);
            list($result, $user) = Cut_Service_User::addUser($user);
            if(!$result) $this->output(-1, '操作失败[1].');
            $this->output(-1, '操作失败[2].');
        }

        if($user['opt_num'] < 0) $this->output(-1, '非法请求.');

        if($user['speedup'] > 10) $user['speedup'] = 10;

        $goods = Cut_Service_Goods::get($id);
        if(!$goods) $this->output(-1, '操作失败[3].');

        if($this->_getStatusCode($goods) !== 1) $this->output(-1, '该活动已结束.');

        //最短完成时间的验证
        $min_play_times_by_second = 15; //per second
        if($input['fin_time'] < floatval(number_format($goods['price']/$goods['range']/$min_play_times_by_second, 2))) {
            $cut_blacklist = Gou_Service_ConfigTxt::getValue('gou_cut_blacklist_txt');
            $cut_blacklist .= ',' . $uid;
            Gou_Service_ConfigTxt::setValue('gou_cut_blacklist_txt', $cut_blacklist);
            $this->output(-1, '参数有误.');
        }

        $last_shortest_time = $goods['shortest_time'];
        $last_uid = $goods['uid'];

        //游戏结束的各种参数的更新(事务)
        list($user_update, $goods_update) = Cut_Service_Game::over($user, $goods, $input['fin_time']);

        if($user_update) $user = array_merge($user, $user_update);
        if($goods_update) $goods = array_merge($goods, $goods_update);

        //活动状态
        $goods_status = $this->_getStatusCode($goods);

        //活动日期
        $goods_date_prefix = '今日';
        $today_time_s = strtotime(date('Y-m-d', Common::getTime()));
        if($goods['start_time'] < $today_time_s || $goods['start_time'] >= ($today_time_s+86400)) $goods_date_prefix = date('m-d', $goods['start_time']);
        $goods_time = sprintf('%s %s-%s', $goods_date_prefix, date('H:i', $goods['start_time']), date('H:i', $goods['end_time']));

        //时间差和是否第一名
        $diff_time = $input['fin_time'] - $last_shortest_time;
        $is_numone = false;
        if($diff_time < 0) $is_numone = true;
        if($last_shortest_time == 0) $is_numone = true;

        //第一名是否是同一人
        $is_same_user = false;
        if($goods_update['uid'] == $last_uid) $is_same_user = true;

        //超过百分比
        $over_perc = Cut_Service_User::overPercent($id, $input['fin_time']);
        if($is_numone){
            $rank_txt = '恭喜你勇夺第一，请时刻关注排行信息哦';
        }elseif($over_perc >= 0.5){
            $rank_txt = sprintf('超过全国%s%%的用户，距离第一名仅差%s"', $over_perc*100, substr($diff_time, 0, 4));
        }else{
            $rank_txt = '请再接再厉';
        }

        $data = array(
            'id' => $id,
            'speedup' => intval($user['speedup']),
            'shortest_time' => number_format($goods['shortest_time'], 2),
            'opt_num' => intval($user['opt_num']),
            'remain_time' => intval($user['remain_time']),
            'rank_url' => sprintf('/apk/detail?id=%d', $id),
            'activity_time' => $goods_time,
            'activity_status' => $goods_status,
            'price' => $goods['price'],
            'fin_time' => $input['fin_time'],
            'over_perc' => $over_perc,
            'diff_time' => $diff_time,
            'is_numone' => $is_numone,
            'is_same_user' => $is_same_user,
            'last_uid' => $last_uid,
            'rank_txt' => $rank_txt,
        );
        //Common::log($data, 'cut.log');
        $this->output(0, '', $data);
    }

    private function _cookData($data){
        Common::getMongo()->insert('log',
            array(
                'type'  => 8,
                'msg'   => json_encode($data),
                'file'  => 'winprize-over',
                'line'  => 0,
                'time'  => time(),
                'ip'    => ''
            )
        );
        //验证签名
        $encrypt_str = $data['uid'] . $data['id'] . $data['fin_time'] . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';
        if(md5($encrypt_str) !== $data['sign']) $this->output(-1, '非法请求.');

        $data['id'] = intval($data['id']);
        $data['fin_time'] = floatval($data['fin_time']);

        $cut_blacklist = Gou_Service_ConfigTxt::getValue('gou_cut_blacklist_txt');
        $blacklist = explode(",", $cut_blacklist);
        $tlimit = Gou_Service_Config::getValue('cut_game_tlimit');
//        Common::log($blacklist, 'blacklist.log');
        if($data['fin_time'] < floatval($tlimit)){
            $blacklist[] = $data['uid'];
            $cut_blacklist = implode(',', $blacklist);
            Gou_Service_ConfigTxt::setValue('gou_cut_blacklist_txt', $cut_blacklist);
            $this->output(-1, '数据异常.');
        }
        if(in_array($data['uid'], $blacklist)) $this->output(-1, '数据异常.');
        return $data;
    }

    /**
     * 第一名push通知
     */
    public function pushNoOneAction(){
        $uid = Common::getAndroidtUid(); //$uid = 'e9522343639800000e2849ada8177e79';
        if(!$uid) $this->output(-1, '非法请求.');

        $id = intval($this->getPost('id')); //活动ID $id = 8;
        if(!$id) $this->output(-1, '非法请求.');

        $input = $this->getPost(array('id', 'fin_time', 'is_same_user', 'last_uid', 'sign'));
        //Common::log($input, 'push.log');
        $input['uid'] = $uid;
        $input = $this->_cookDataPush($input);
        $goods = Cut_Service_Goods::get($id);

        //给不同的第一名发push
        if(floatval($goods['shortest_time']) && $input['is_same_user'] == 'false'){
            //push
            $push_user = User_Service_Uid::getByUid($input['last_uid']);
            //Common::log($push_user, 'push.log');
            if ($push_user && $push_user['baidu_uid']) {
                $custom_content = array('action' => 'com.gionee.client.BargainGame', 'game_id' => $goods['id']);
                $title = '高能预警！速度之王称号被夺！';
                $content = '您在购物大厅的活动记录已被超越，快去迎战>>';
                Api_Baidu_Push::pushMessage($push_user['baidu_uid'], $title, $content, 3, $custom_content);
            }
        }
        $this->standOutput(0, '');
    }

    private function _cookDataPush($data){
        //验证签名
        $encrypt_str = $data['uid'] . $data['id'] . $data['fin_time'] . 'NTQzY2JmMzJhYTg2N2RvY3Mva2V5';
        if(md5($encrypt_str) !== $data['sign']) $this->output(-1, '非法请求.');

        $data['id'] = intval($data['id']);
        $data['fin_time'] = floatval($data['fin_time']);
        return $data;
    }

    /**
     * 是否有礼品
     */
    public function isHasGiftAction(){
        $uid = Common::getAndroidtUid();
        if (!$uid) $this->output(-1, '非法请求.');

        $count = Cut_Service_Goods::getCount(array('status' => array("IN", array(2, 3)), 'uid' => $uid));

//        Common::log($count, 'push.log');
        if ($count) $this->output(0, '', array('has' => true, 'msg' => '有大礼'));
        $this->output(0, '', array('has' => false, 'msg' => '有大礼'));
    }

    public function closeTipAction(){
        $uid = Common::getAndroidtUid();
        if (!$uid) $this->output(-1, '非法请求.');
        $row = Cut_Service_Goods::updateBy(array('status' => 3), array('status' => 2, 'uid' => $uid));
        if ($row) $this->output(0, '');
        $this->output(0, '');
    }
}
