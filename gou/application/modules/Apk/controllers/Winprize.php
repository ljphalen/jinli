<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class WinprizeController extends Apk_BaseController
{

    public $action = array(
        'viewUrl' => '/Winprize/view/',
        'rankUrl' => '/Winprize/rank/',
        'activityUrl' => '/Winprize/activity/',
        'awardUrl' => '/Winprize/awardlist/',
        'shareUrl' => '/Winprize/share/'
    );
    public $shop_type = array(
        1 => '淘宝',
        2 => '天猫'
    );

    /**
     * 砍价详情
     */
    public function detailAction()
    {
        $id = intval($this->getInput('id'));
        $goods = Cut_Service_Goods::getGoods($id);
        $uid = Common::getAndroidtUid();
        if (!$uid) exit('非法请求!');
        if (!$goods) $this->output('-1', 'goods not exist');
        //小编
        $staticroot = Common::getAttachPath();
        $author = Gou_Service_UserAuthor::get($goods['author_id']);
        $author['avatar'] = $staticroot . $author['avatar'];

        //商品库
        $store = Cut_Service_Store::getStore($goods['store_id']);
        $shop = Cut_Service_Shops::getShops($goods['shop_id']);
        $shop['logo'] = Cut_Service_Goods::getImageLink($shop['logo']);
        list(, $ext) = Cut_Service_Store::getsBy(array('shop_id' => $shop['shop_id'], 'type' => 1));
        $topApi = new Api_Top_Service();
        $iids = array_keys(Common::resetKey($ext, 'num_iid'));
        $convert = $topApi->tbkMobileItemsConvert(array('num_iids' => implode(',', $iids)));
        $convert = Common::resetKey($convert, 'num_iid');
        $taoke_info['click_url'] = $convert['click_url'];
        $regular = Gou_Service_Config::getValue('gou_cut_regular_txt');
        $attachPath = Common::getAttachPath();
        $store['pic_url'] = Cut_Service_Goods::getImageLink($store['img'], 400);

        // down load url
        $download_url = 'http://goudl.gionee.com/apps/shoppingmall/GN_Gou-banner.apk';
        if (strpos(Util_Http::getServer('HTTP_USER_AGENT'), 'MicroMessenger') !== false) {
            $download_url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.gionee.client';
        }


        $this->assign('convert', $convert);
        $this->assign('goods', $goods);
        $this->assign('shop', $shop);
        $this->assign('action', $this->action);
        $this->assign('store', $store);
        $this->assign('author', $author);
        $this->assign('regular', $regular);
        $this->assign('ext', $ext);
        $this->assign('uid', $uid);
        $this->assign('shop_type', $this->shop_type);
        $this->assign('download_url', $download_url);
        $this->assign('fuid', $this->getInput('fuid'));

        $site_config = Common::getConfig('siteConfig');

        //对积分类型3进行签名
        $this->assign('sign', md5('3' . $site_config['secretKey']));
        $this->assign('title', $store['title']);
    }


    public function rankAction()
    {
        $goods_id = $this->getInput('id');
        $goods = Cut_Service_Goods::get($goods_id);
        $goods['time_string'] = $this->getTimeString($goods['start_time'],$goods['end_time']);
        $data = $this->_getRandData($goods_id);
        $uids = array_keys(Common::resetKey($data,'uid'));
        $uid = User_Service_Uid::getUsersFmtByUid($uids);
        $this->assign('goods', $goods);
        $this->assign('uid',   $uid);
        $this->assign('data',  $data);
        $this->assign('title', '排行榜');
    }

    private function _getRandData($goods_id){
        $uid = Common::getAndroidtUid();
        $condition  = array('goods_id' => $goods_id,'shortest_time'=>array('>',0));
        $sort = array('shortest_time' => 'ASC','create_time'=>'ASC');
        //get top ten
        list($count, $data) = Cut_Service_User::getList(1, 10, $condition , $sort);
        if(empty($data)) {
            return false;
        }
        //get current user rank
        $data[0]['win'] = 1;
        if(!$uid){
            return $data;
        }
        $row = Cut_Service_User::getBy(array('uid' => $uid,'shortest_time'=>array('>',0),'goods_id'=>$goods_id));
        if(empty($row)){
            return $data;
        }
        $poz = Cut_Service_User::getpoz($row);
        $is_last = ($count ==$poz+1)?1:0;
        //获取前面多少人

        $row['current'] = 1;
        if ($poz > 9) {//超过10
            $above = Cut_Service_User::getAbove($row, $poz);
            $below = Cut_Service_User::getBelow($row, $poz);
            $data = $is_last?array_slice($data, 0, 6):array_slice($data, 0, 5);
            $data[] = array('ellipsis-text' => 1);//附加省略
            $data[$poz-1] = $above;//上一个
            $data[$poz]   = $row;//当前
            if(!$is_last){
                $data[$poz+1]    = $below;
                $data[]  = array('ellipsis-text' => 1);
            }
        } else {
            if($poz!=0){
                $data[$poz]['current'] = 1;
            }
        }
        return $data;
    }

    public function activityAction()
    {
        $uid = Common::getAndroidtUid();
        if (!$uid) exit('非法请求!');
        //中奖未创建订单记录
        list(,$rows) = Cut_Service_Goods::getsBy(array('uid' => $uid, 'status' => array('IN',array(2,3))));
        if (!empty($rows)) {
            $status = array_keys(Common::resetKey($rows,'status'));
            $no_send = in_array(2,$status)?1:0;
            $no_tip =  in_array(3,$status)?1:0;
            $this->assign('no_send', $no_send);
            $this->assign('no_tip', $no_tip);
            $this->assign('row', $rows);
        }


        $this->assign('awardUrl', $this->action['awardUrl']);
        $this->assign('title', '我的活动记录');
    }


    public function downloadAction()
    {
        $download_url = 'http://goudl.gionee.com/apps/shoppingmall/GN_Gou-banner.apk';
        if (strpos(Util_Http::getServer('HTTP_USER_AGENT'), 'MicroMessenger') !== false) {
            $download_url = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.gionee.client';
        }
        $this->assign('title', '砍价改版啦');
        $this->assign('download_url', $download_url);
    }


    public function awardAction()
    {
        $uid = Common::getAndroidtUid();
        if (!$uid) exit('非法请求!');
        $row = Gou_Service_Order::getBy(array('show_type' => 4, 'out_uid' => $uid), array('id' => 'DESC'));
        $info = Gou_Service_Order::getOrderAddress($row['id']);
        $goods_id = $this->getInput("goods_id");
        $this->assign('goods_id', $goods_id);
        $this->assign('info', $info);
        $this->assign('title', '提交个人信息');
    }


    public function awardlistAction()
    {
        $this->assign('title', '我的获奖记录');
    }

    public function refuelAction()
    {
        $goods_id = $this->getInput('id');
        $uid = $this->getInput('uid');

        $goods = Cut_Service_Goods::get($goods_id);

        //不存在或者过期
        if (empty($goods) || $goods['end_time'] < time()) {
            $tmp = Cut_Service_Goods::getBy(array('status' => 1), array('start_time' => "ASC"));
            $goods = !empty($tmp) ? $tmp : $goods;
        }


        if ($goods['start_time'] > time()) { //未开始
            $is_current = 0;
            $class = 'cheer';
        } elseif ($goods['end_time'] < time()) { //已结束
            $is_current = 1;
            $class = 'finish';

        } else {//正在进行
            $is_current = 1;
            $class = 'cheer';
        }

        $goods['time_str'] = $this->getTimeString($goods['start_time'], $goods['end_time']);
        $store = Cut_Service_Store::getStore($goods['store_id']);
        $goods['image'] = Cut_Service_Goods::getImageLink($store['img'], 400);

        $num_sid = unserialize(Util_Cookie::get('num_sid', true));
        if (in_array($uid . $goods['id'], $num_sid) && $class != 'finish') {
            $class = 'want';
        }

        $this->assign('goods', $goods);
        $this->assign('uid', $uid);
        $this->assign('is_current', $is_current);
        $this->assign('class', $class);
        $this->assign('title', '为好友助力');
    }

    public function getTimeString($start_time, $end_time)
    {
        if (!is_numeric($start_time)) {
            $start_time = strtotime($start_time);
        }
        $time = strtotime(date('Y-m-d',$start_time));
        $time_diff = strtotime(date('Y-m-d')) - $time;
        if ($time_diff >= 172800||$time_diff <= -172800) { //大于2天
            $day = date('m-d', $time);
        } elseif ($time_diff >= 86400) {//大于1天
            $day = "昨天";
        } elseif ($time_diff <=-86400){
            $day = "明天";
        }else{
            $day = "今天";
        }
        $start_time_string = date('H:i', $start_time);
        $end_time_string = date('H:i', $end_time);

        return sprintf("%s %s-%s", $day, $start_time_string, $end_time_string);
    }

    /**
     * goods detail
     */
    public function viewAction()
    {
        $id = intval($this->getInput('id'));
        $goods = Cut_Service_Goods::getGoods($id);

        if (!$goods) $this->output('-1', 'goods not exist');
        $store = Cut_Service_Store::getStore($goods['store_id']);

        $this->assign('goods', $goods);
        $this->assign('store', $store);
        $this->assign('title', '商品详情描述');
    }


    /**
     * buy
     */
    public function buyAction()
    {
        $id = intval($this->getInput('id'));
        $goods = Cut_Service_Goods::getGoods($id);
        $uid = Cut_Service_Goods::getUid();
        $android_uid = Common::getAndroidtUid();

        if (!$goods) $this->output('-1', 'goods not exist');
        $store = Cut_Service_Store::getStore($goods['store_id']);
        $store['pic_url'] = Cut_Service_Goods::getImageLink($store['img'], 400);

        //判断商品是否可以下单
        $status_arr = Cut_Service_Goods::getCutStatus($goods['id'], $uid, 'detail');
        $status = $status_arr[$goods['id']];

        if (in_array($status['cut_code'], array('0,1,2,5,6'))) $this->redirect(Common::getWebRoot() . '/cut/detail?id=' . $goods['id']);
        $goods['current_price'] = $status['current_price'];


        //查看当前用户是否有下单记录
        $address = array();
        $order = Gou_Service_Order::getBy(array('order_type' => 5, 'out_uid' => $android_uid));
        if ($order) {
            $address = Gou_Service_Order::getOrderAddress($order['id']);
        }
        $this->assign('address', $address);
        $this->assign('goods', $goods);
        $this->assign('store', $store);
        $this->assign('title', '创建订单');
    }

    /**
     * goods detail
     */
    public function ruleAction()
    {
        $this->assign('title', '活动规则');
    }

}