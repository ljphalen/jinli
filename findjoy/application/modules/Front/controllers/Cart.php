<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CartController extends Front_BaseController {

    public $actions = array(
        'buyUrl' => '/cart/buy',
    );

	public $perpage = 20;
	
	/**
	 * 购物车列表
	 */
    public function indexAction() {
        $this->nocache_headers();
        $userInfo = $this->userInfo;

        list($total, $cart) = Fj_Service_Cart::getsBy(array('uid'=>$userInfo['id']), array('id'=>'DESC'));

        $cart_num = $total_price = 0;

        if($total > 0){
            $goods_ids = array_keys(Common::resetKey($cart,'goods_id'));

            list(, $goods) = Fj_Service_Goods::getsBy(array('id'=>array('IN',$goods_ids)), array('id'=>'DESC'));
            $goods = Common::resetKey($goods, 'id');

            list($cart_num, $total_price) = Fj_Service_Cart::getCartInfo(array_keys(Common::resetKey($cart, 'id')));
        }

        $this->assign('cart', $cart);
        $this->assign('goods', $goods);
        $this->assign('total_price', $total_price);
        $this->assign('cart_num', $cart_num);
        $this->assign('title', '我的购物车 - 大红帽');
    }
    
    /**
     * 提交订单
     */
    public function buyAction() {
        $this->nocache_headers();
        $userInfo = $this->userInfo;
        $cart_ids = $this->getPost('cart_ids');
        $webroot = Common::getWebRoot();
        if(!is_array($cart_ids)) $this->redirect($webroot.'/cart/index');
        
        list($total, $cart_list) = Fj_Service_Cart::getsBy(array('uid'=>$this->userInfo['id'], 'id'=>array('IN', $cart_ids)), array('id'=>'DESC'));
        $ids = array_keys(Common::resetKey($cart_list, 'id'));
        
        //购物车商品数量 商品价格
        list($cart_num, $total_price) = Fj_Service_Cart::getCartInfo($cart_ids);
        
        //查询该用户是否有下单记录
        $order = Fj_Service_Order::getBy(array('uid'=>$userInfo['id']));
        if($order) {
            $this->assign('buyer_name', $order['buyer_name']);
            $this->assign('phone', $order['phone']);
        }
    
        if($total > 0){
            $goods_ids = array_keys(Common::resetKey($cart_list,'goods_id'));    
            list(, $goods) = Fj_Service_Goods::getsBy(array('id'=>array('IN', $goods_ids)), array('id'=>'DESC'));
            $goods = Common::resetKey($goods, 'id');
        }
    
        $this->assign('cart', $cart_list);
        $this->assign('cart_ids', $cart_ids);
        $this->assign('goods', $goods);
        $this->assign('cart_num', $cart_num);
        $this->assign('total_price', $total_price);
        $this->assign('title', '填写订单信息');
    }

    /**
     * 添加购物车
     */
    public function addAction() {
        $userInfo = $this->userInfo;
        $info = $this->getPost(array('goods_id', 'type', 'goods_num'));
        if(!$info['goods_num']) $info['goods_num'] = 1;

        if (!$userInfo) $this->output(-1, '非法请求.');
        if (!$info['goods_id'] || !$info['goods_num'] || $info['goods_num'] < 0) {
            $this->output(-1, '参数错误.');
        }

        //判断商品是否可以下单
        $goods = Fj_Service_Goods::getGoods($info['goods_id']);
        if(!$goods)  $this->output(-1, '参数错误，商品不存在.');
        if($goods['status'] != 1)  $this->output(-1, '商品已下架.');
        
        //加数量
        /* if($info['type'] == 1) {
            if($goods['stock_num'] < 0)  $this->output(-1, '商品库存不足.');
        } */

        $cart = Fj_Service_Cart::getBy(array('goods_id'=>$info['goods_id'], 'uid'=>$userInfo['id']));
        if($cart) {
            $result = $this->updateCartNum($cart, $info['type'], $info['goods_num']);
            if(!$result) $this->output(-1, '操作失败1.');
        } else {
            $data = array(
                'uid'=>$userInfo['id'],
                'open_id'=>$userInfo['open_id'],
                'goods_id'=>$info['goods_id'],
                'goods_num'=>$info['goods_num'],
                'price'=>$goods['price'],
                'create_time'=>Common::getTime()
            );
            $ret = Fj_Service_Cart::add($data);
            if(!$ret) $this->output(-1, '操作失败2.');
        }
        
        //购物车商品数量 商品价格
        list(, $cart_list) = Fj_Service_Cart::getsBy(array('uid'=>$this->userInfo['id']), array('id'=>'DESC'));
        $cart_ids = array_keys(Common::resetKey($cart_list, 'id'));
        list($cart_num, $total_price) = Fj_Service_Cart::getCartInfo($cart_ids);

        $cart_item = Fj_Service_Cart::getBy(array('uid'=>$this->userInfo['id'], 'goods_id'=>$info['goods_id']));
        $this->output(0,'操作成功.', array('cart_num'=>$cart_num, 'total_price'=>$total_price, 'goods_num'=>intval($cart_item['goods_num'])));
    }
    
    
    /**
     * 更新购物车商品数量
     */
    private function updateCartNum($cart, $type, $goods_num) {
        if($type == 1) {
            //增加数量 
            $update_data = array('goods_num'=>$cart['goods_num'] + $goods_num);
        } else {
            //减数量
            if($cart['goods_num'] - $goods_num < 1) return false;
            $update_data = array('goods_num'=>$cart['goods_num'] - $goods_num);
        } 
        $ret = Fj_Service_Cart::update($update_data, $cart['id']);
        if(!$ret) return false;        
        return true;
    }
    
    /**
     * 删除购物车
     */
    private function delAction() {
        $cart_ids = $this->getPost('cart_ids');
        if(!is_array($cart_ids)) $this->output(-1, '参数错误.');
        list(, $cart_list) = Fj_Service_Cart::getsBy(array('uid'=>$this->userInfo['id'], 'id'=>array('IN', $cart_ids)), array('id'=>'DESC'));
        $ids = array_keys(Common::resetKey($cart_list, 'id'));
        $ret = Fj_Service_Cart::deleteBy(array('id'=>array('IN', $ids)));
        
        if(!$ret) $this->output(-1, '操作失败.');
        
        //购物车商品数量 商品价格
        list(, $cart_list) = Fj_Service_Cart::getsBy(array('uid'=>$this->userInfo['id']), array('id'=>'DESC'));
        $cart_ids = array_keys(Common::resetKey($cart_list, 'id'));
        list($cart_num, $total_price) = Fj_Service_Cart::getCartInfo($cart_ids);
        $this->output(0,'操作成功.', array('cart_num'=>$cart_num, 'total_price'=>$total_price));
        
    }

}