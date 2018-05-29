<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CutController extends Apk_BaseController {

    public $action=array(
        'viewUrl'=>'/cut/view/'
    );
    public $shop_type = array(
      1 => '淘宝',
      2 => '天猫'
    );
    /**
     * 砍价详情
     */
    public function detailAction() {
        $id = intval($this->getInput('id'));
        $goods = Cut_Service_Goods::getGoods($id);
        $uid = Common::getAndroidtUid();

        if (!$goods) $this->output('-1', 'goods not exist');
        //小编
        $staticroot = Common::getAttachPath();
        $author = Gou_Service_UserAuthor::get($goods['author_id']);
        $author['avatar'] = $staticroot . $author['avatar'];

        //商品库
        $store = Cut_Service_Store::getStore($goods['store_id']);
        $shop = Cut_Service_Shops::getShops($goods['shop_id']);
        $shop['logo']= Cut_Service_Goods::getImageLink($shop['logo']);
        list(, $ext) = Cut_Service_Store::getsBy(array('shop_id' => $shop['shop_id'], 'type' => 1));
        $topApi  = new Api_Top_Service();
        $iids = array_keys(Common::resetKey($ext,'num_iid'));
        $convert = $topApi->tbkMobileItemsConvert(array('num_iids'=>implode(',',$iids)));
        $convert = Common::resetKey($convert,'num_iid');
        $taoke_info['click_url'] = $convert['click_url'];
        $regular = Gou_Service_Config::getValue('gou_cut_regular_txt');
        $attachPath = Common::getAttachPath();
        $store['pic_url']= Cut_Service_Goods::getImageLink($store['img'],400);
        
        // down load url
        $download_url = 'http://goudl.gionee.com/apps/shoppingmall/GN_Gou-banner.apk';
        if(strpos(Util_Http::getServer('HTTP_USER_AGENT'), 'MicroMessenger') !== false ) {
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
        $this->assign('sign', md5('3'.$site_config['secretKey']));
        $this->assign('title', $store['title']);
    }
    
    /**
     * goods detail
     */
    public function viewAction() {
        $id = intval($this->getInput('id'));
        $goods = Cut_Service_Goods::getGoods($id);
         
        if(!$goods) $this->output('-1', 'goods not exist');
        $store = Cut_Service_Store::getStore($goods['store_id']);
         
        $this->assign('goods', $goods);
        $this->assign('store', $store);
        $this->assign('title', '商品详情描述');
    }
    
    

    /**
     * buy
     */
    public function buyAction() {
        $id = intval($this->getInput('id'));
        $goods = Cut_Service_Goods::getGoods($id);
        $uid =  Cut_Service_Goods::getUid();
        $android_uid = Common::getAndroidtUid();
         
        if(!$goods) $this->output('-1', 'goods not exist');
        $store = Cut_Service_Store::getStore($goods['store_id']);
        $store['pic_url']= Cut_Service_Goods::getImageLink($store['img'],400);
        
        //判断商品是否可以下单
        $status_arr = Cut_Service_Goods::getCutStatus($goods['id'], $uid, 'detail');
        $status = $status_arr[$goods['id']];
        
        if(in_array($status['cut_code'], array('0,1,2,5,6'))) $this->redirect(Common::getWebRoot().'/cut/detail?id='.$goods['id']);
        $goods['current_price'] = $status['current_price'];
        
        
        //查看当前用户是否有下单记录
        $address = array();
        $order = Gou_Service_Order::getBy(array('order_type'=>5, 'out_uid'=>$android_uid));
        if($order) {
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
    public function ruleAction() {
        $this->assign('title', '砍价规则');
    }
    
}