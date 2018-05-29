<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class goodsController extends Front_BaseController {

    /**
     * 商品详情
     */
    public function detailAction() {

        $id = intval($this->getInput('id'));
        $goods = Fj_Service_Goods::getGoods($id);

        //购物车商品信息
        list($total, $carts) = Fj_Service_Cart::getsBy(array('uid'=>$this->userInfo['id']), array('id'=>'DESC'));

        $cart_num = 0;
        if($total > 0){
            $cart_ids = array_keys(Common::resetKey($carts, 'id'));
            list($cart_num, ) = Fj_Service_Cart::getCartInfo($cart_ids);
        }

        //当前汇率
        $hk_exrate = Fj_Service_Config::getValue('fj_currency_rate_hk');
        
        //update hits
        Fj_Service_Goods::updateHits($goods['id']);
        
        //销量和评论数
        $goods['sale_num'] = abs(ceil($goods['hits'] * (-0.055 * log($goods['price'],2.71828) + 0.5016)));
        $goods['comment_num'] = abs(ceil($goods['sale_num'] * (0.0421 * log($goods['price'],2.71828) - 0.0523)));

        $id = Common::encrypt($this->userInfo['id'],'ENCODE');
        $this->assign('id', $id);
        
        $this->assign('cart_num', $cart_num);
        $this->assign('goods', $goods);
        $this->assign('hk_exrate', sprintf('1港币兑%s人民币', $hk_exrate));
        $this->assign('title', $goods['title'].' - 大红帽');
    }
}