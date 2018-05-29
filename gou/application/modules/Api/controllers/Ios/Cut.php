<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Ios_CutController extends Api_BaseController {
	
    
    /**
     * 砍价列表
     */
	public function listAction() {
		//get uid
		$uid = Cut_Service_Goods::getUid('ios');
		$page = $this->getInput('page');
		if(!$page) $page = 1;
		$perpage = 10;
		$time = Common::getTime();
		
		if(!$uid)  $this->clientOutput(-1, '非法请求!');
		
	    //计算正在进行的条数
	    $cutting_count = Cut_Service_Goods::getNotCutCount();
		
	    if($cutting_count) {
	        $params = array('status'=>array('!=',2), 'start_time'=>array('<', $time));
	        $sort = array('start_time'=>'DESC', 'id'=>'DESC');
	        list($total, $cut1) = Cut_Service_Goods::getList($page, $perpage, $params, $sort);
	        
	        $cut_list = $cut1;
	        if($page == 1) {
	            //一条预热
	            $cut2 = Cut_Service_Goods::getBy(array('status'=>1, 'start_time'=>array('>', $time)), array('start_time'=>'ASC', 'id'=>'ASC'));
	            if($cut2) $cut_list = array_merge(array($cut2), $cut1);
	        }
	    } else {
	        $params = array('status'=>array('!=',2));
	        $sort = array('start_time'=>'DESC', 'id'=>'DESC');
	        list($total, $cut_list) = Cut_Service_Goods::getList($page, $perpage, $params, $sort);
	    }
	    
	    $data = array();
	    $webroot = Common::getWebRoot();
	    
	    if($cut_list) {
	        $cut_ids = array_keys(Common::resetKey($cut_list, 'id'));	        
	        $goods_ids = array_keys(Common::resetKey($cut_list, 'store_id'));
	        
	        //goods
	        list(, $goods) = Cut_Service_Store::getsBy(array('id'=>array('IN', $goods_ids)));
	        $goods = Common::resetKey($goods, 'id');
	        
	        //shops
	        $shop_ids = array_keys(Common::resetKey($goods, 'shop_id'));
	        list(, $cut_shops) = Cut_Service_Shops::getsBy(array('id'=>array('IN',$shop_ids)), array('id'=>'DESC'));
	        $cut_shops = Common::resetKey($cut_shops, 'id');
	        
	        $goods = $this->_cookGoods($cut_shops, $goods);
	        $status = Cut_Service_Goods::getCutStatus($cut_ids, $uid);
	        
	        foreach ($cut_list as $key=>$value) {
	            //current price
	            $current_price = $value['price'];
	            $data[$key]['id'] = $value['id'];
	            $data[$key]['shop_title'] = html_entity_decode($goods[$value['store_id']]['shop_title']);
	            $data[$key]['shop_url'] = html_entity_decode($goods[$value['store_id']]['shop_url']);
	            $data[$key]['shop_logo'] = Cut_Service_Goods::getImageLink(html_entity_decode($goods[$value['store_id']]['shop_logo']));
	            $data[$key]['goods_img'] = Cut_Service_Goods::getImageLink($goods[$value['store_id']]['img'],400);
	            $data[$key]['goods_title'] = html_entity_decode($goods[$value['store_id']]['title']);
	            $data[$key]['share_title'] = html_entity_decode($goods[$value['store_id']]['share_title']);
	            $data[$key]['detail_url'] = $webroot.'/cut/detail?id='.$value['id'];
				//用于分享
	            $data[$key]['share_url'] = $webroot.'/cut/detail?id='.$value['id'].'&fuid='.Common::encrypt($uid);
	            $data[$key]['price'] = $goods[$value['store_id']]['price'];
	            $data[$key]['current_price'] = $status[$value['id']]['current_price'];
	            $data[$key]['is_cut'] =  $status[$value['id']]['is_cut'] ? "true" : "false";
	            $data[$key]['cut_code'] =  $status[$value['id']]['cut_code'];
	            $data[$key]['cut_msg'] =  $status[$value['id']]['cut_msg'];
	            $data[$key]['cut_info'] =  $status[$value['id']]['cut_info'];
	            $data[$key]['tips'] =  $status[$value['id']]['tips'];
	            
	        }
	        
	    }
	    $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
	    $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	    
	}
	
	/**
	 * 砍价
	 */
	public function cutAction() {
	    $goods_id = $this->getInput("id");
	    $uid =  Cut_Service_Goods::getUid('ios');
	    $ios_uid = Common::getIosUid();
	    $referer = Util_Http::getServer('HTTP_REFERER');
	    
	    if(!$uid) $this->output(-1, '非法请求');
	    $goods = Cut_Service_Goods::getGoods($goods_id);
	    
	    if(!$goods) $this->output(-1, '商品不存在');
	    
	    $refer = $referer ? 'detail' : '';
	    $status_arr = Cut_Service_Goods::getCutStatus($goods_id, $uid, $refer);
	    $status = $status_arr[$goods_id];
	    $status['is_client'] = $ios_uid ? true : false;
	    $status['detail_url'] = Common::getWebRoot().'/cut/detail?id='.$goods_id;
	    $buy_url = Common::getWebRoot().'/cut/buy?id='.$goods['id'];
	    
        if($status['cut_code'] == 1) {
            $this->output(0, '还没有开始哦，再忍耐一下的', $status);
        } 
        if ($status['cut_code'] == 6){
            $this->output(0, '今天没有了，看看其他的吧', $status);
        } 
        if ($status['cut_code'] == 3){
            $status['buy_url'] = $buy_url;
            $this->output(0, '您已砍过价,再找朋友一起来吧', $status);
        } 
        if ($status['cut_code'] == 4){
            $status['buy_url'] = $buy_url;
            $this->output(0, '最低价啦，赶紧抢购吧', $status);
        }
        
	    //砍价
	    $ret = Cut_Service_Goods::cut($goods_id, $uid, $refer);
	    if(!$ret) $this->output(-1, '砍价失败');
	    
	    $msg = '砍掉'.$ret['range'].'元，再找朋友一起来吧';
	    
	    //每日砍价积分
		$ret['is_score'] = false;
		if($ios_uid){
			$s_ret = Gou_Service_ScoreLog::score(2, $ios_uid);
			if($s_ret) {
				$score_type = Gou_Service_ScoreLog::scoreType();
				$msg .= "\n新增".$score_type[2]['score'].'分';
				$ret['is_score'] = true;
			}
		}

	    //推荐砍价商品给当前用户的好友的好友帮忙砍价积分处理
	    $fuid = $this->getInput('fuid');
	    if($fuid) Gou_Service_ScoreLog::score(4, Common::encrypt(urldecode($fuid), 'DECODE'));
	        
	    if($ret['is_buy']) $ret['buy_url'] = $buy_url;
	    $this->output(0, $msg, $ret);
	}
	
	
	/**
	 * 查询当前砍价状态
	 */
	public function getStatusAction() {
	    $goods_id = $this->getInput("id");
	    $uid =  Cut_Service_Goods::getUid('ios');
	    $ios_uid = Common::getIosUid();
	    $referer = Util_Http::getServer('HTTP_REFERER');
	    $version = COmmon::getIosClientVersion();
	     
	    if(!$uid) $this->output(-1, '非法请求');
	    $goods = Cut_Service_Goods::getGoods($goods_id);
	     
	    if(!$goods) $this->output(-1, '商品不存在');
	    
	    $fuid = $this->getInput('fuid');
	     
	    $refer = $referer ? 'detail' : '';
	    $status_arr = Cut_Service_Goods::getCutStatus($goods_id, $uid, $refer);
	    $status = $status_arr[$goods_id];
	    
	    $buy_url = Common::getWebRoot().'/cut/buy?id='.$goods['id'];
	    $cut_url = Common::getWebRoot().'/api/ios_cut/cut?id='.$goods['id'].'&fuid='.$fuid;
	     
        if ($status['is_cut']) $status['cut_url'] = $cut_url;
        if ($status['is_buy']) $status['buy_url'] = $buy_url;
        $status['is_client'] = ($ios_uid && ($version >= 130)) ? true : false;
	    $this->output(0, '', $status);
	}
	
	
	/**
	 * 砍价订单列表
	 */
	public function orderListAction(){
	    $uid =  Cut_Service_Goods::getUid('ios');
	    $page = $this->getInput('page');
	    if(!$page) $page = 1;
	    $perpage = 10;
	    
	    $data = array();
	    $webroot = Common::getWebRoot();
        list($total,$orders) = Gou_Service_Order::getList($page, $perpage, array('out_uid'=>$uid,  'show_type'=>3, 'order_type'=>5));

        if($orders) {
            $orders_list = Common::resetKey($orders, 'id');
            $order_ids = array_keys($orders_list);
    
            $orders_list = Common::resetKey($orders, 'goods_id');
            $goods_ids = array_keys($orders_list);
    
            if($goods_ids) {
                list(,$goods) = Cut_Service_Goods::getsBy(array('id'=>array('IN', $goods_ids)), array('id'=>'DESC'));
                $goods = Common::resetKey($goods, 'id');
    
                $order_address = Gou_Service_Order::getOrdersAddress($order_ids);
                $order_address = Common::resetKey($order_address, 'order_id');
                
                list(, $store) = Cut_Service_Store::getsBy(array('id'=>array('IN', array_keys(Common::resetKey($goods, 'store_id')))));
                $store = COmmon::resetKey($store, 'id');
            }

            $user_uid = User_Service_Uid::getByUid($uid);
            $nickname = $user_uid['nickname']?$user_uid['nickname']:'购物大厅网友';
    
            $status = Gou_Service_Order::orderStatus();
            foreach ($orders as $key=>$value) {
                //$data[$key]['img'] = Common::getAttachPath() .$store[$goods[$value['goods_id']]['store_id']]['img'];
                $data[$key]['img'] = Cut_Service_Goods::getImageLink($store[$goods[$value['goods_id']]['store_id']]['img'],60);
                $data[$key]['title'] = html_entity_decode($goods[$value['goods_id']]['title']);
                $data[$key]['price'] = $value['real_price'];
                $address = Gou_Service_Order::getOrderAddress($value['id']);
                $data[$key]['address'] = $address['adds'].$address['detail_address'];
                $data[$key]['buyer_name'] = $value['buyer_name'];
                $data[$key]['phone'] = $value['phone'];
                //$data[$key]['detail_url'] = $webroot.'/cutorder/detail?trade_no='.$value['trade_no'];
                $data[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
                
                if($value['status'] == 1) {
                    $data[$key]['edit_url'] = $webroot.'/cutorder/edit?trade_no='.$value['trade_no'];
                    $data[$key]['pay_url'] = $webroot . '/cutorder/pay?token=' . $value['token'];
                    $data[$key]['cancel_url'] = $webroot.'/cutorder/cancel?trade_no='.$value['trade_no'];
                  }
                  //快递
                  if($value['status'] == 4 && $value['express_code']) {
                      $express = explode(',', html_entity_decode($value['express_code']));
                      $data[$key]['express_url'] = 'http://m.kuaidi100.com/index_all.html?type='.$express[0].'&postid='.$express[1].'&callbackurl='.urlencode($webroot.'/cutorder/list');
                  }    
                               
                $data[$key]['status'] = $status[$value['status']];
                if($value['status'] == 10) $data[$key]['status'] = '支付超时退款中';
                if($value['status'] == 12) $data[$key]['status'] = '支付超时已退款';

                $data[$key]['hot_order'] = '';
                if($value['status'] == 4 || $value['status'] == 5){
                    $story = Gou_Service_Story::getBy(array('order_id'=>$value['id']));
                    $data[$key]['hot_order'] = array('oid'=>$value['id'], 'id'=>$story?$story['id']:'', 'nickname'=>$nickname);
                }
            }
	    }

	    $hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
	    $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	
	/**
	 *
	 * @param array $shops
	 * @param array $goods
	 */
	private function _cookGoods($shops, $goods) {
	    foreach ($goods as $key => $value) {
	        $goods[$key]['shop_title'] = $shops[$value['shop_id']]['shop_title'];
	        $goods[$key]['shop_url'] = $shops[$value['shop_id']]['shop_url'];
	        $goods[$key]['shop_logo'] = $shops[$value['shop_id']]['logo'];
	    }
	    return $goods;
	}
	
}
