<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GoodsController extends Front_BaseController {
    public function indexAction()
    {

        $params = $this->getInput(array('keyword','category_id' ,'brand_id'));

        if ($params['keyword']) $search['keyword'] = $params['keyword'];
        if ($params['category_id']) {
            $search['category_id'] = $params['category_id'];
            $info = Dhm_Service_Category::get($params['category_id']);
            $back = '';
        }
        if ($params['brand_id']) {
            $search['brand_id'] = $params['brand_id'];
            $info = Dhm_Service_Brand::get($params['brand_id']);
            $back = '/brand';
            //update tj
            Dhm_Service_Brand::updateTJ($params['brand_id']);
        }

        $url = Common::getWebRoot() . '/api/goods/index?' . http_build_query($search) . '&';
        $page_title = $info ? $info['name'] . '_' : '';

        $this->assign('url', $url);
        $this->assign('info', $info);
        $this->assign('back', $back);
        $this->assign('search', $search);
        $this->assign('title', $page_title . '海淘、大红帽全球海淘资讯第一站');
        $this->assign('keywords', $info['name'] . ' 海淘，海淘攻略，代购，香港购物、全球购、跨境电商、海外代购、什么值得买');
        $this->assign('description', '海淘资讯、海淘优惠商品、资讯、优惠券、海淘攻略');
    }
    
    /**
     * 详情
     */
    public function detailAction() {
        $id = $this->getInput('id');
        $refer = $this->getInput('refer');
        $info = Dhm_Service_Goods::get($id);
        $webroot = Common::getWebRoot();
        if(!$id || !$info) $this->redirect($webroot);
        
        //图片
        list($total_img, $imgs) = Dhm_Service_GoodsImage::getsBy(array('goods_id'=>$info['id']));
        
        //brand
        $brand = Dhm_Service_Brand::get($info['brand_id']);
        
        //goods_mall
        list(, $malls) = Dhm_Service_GoodsMall::getsBy(array('goods_id'=>$info['id']), array('id'=>'DESC'));
        
        //price
        $min = $max = array();
        foreach ($malls as $key=>$value){
            $min[] = Common::money($value['min_price']);
            $max[] = Common::money($value['max_price']);
        }
        
        $min_price = min($min);
        $max_price = max($max);
        
        $malls = Common::resetKey($malls, 'mall_id');
        
        if($malls) {
            list(, $mall_list) = Dhm_Service_Mall::getsBy(array('id'=>array('IN',array_keys($malls))), array('id'=>'DESC'));
            $mall_list = Common::resetKey($mall_list, 'id');
        }
        
        if($mall_list) {
            $mall_countrys = Common::resetKey($mall_list, 'country_id');
            list(, $countrys) = Dhm_Service_Country::getsBy(array('id'=>array('IN',array_keys($mall_countrys))), array('id'=>'DESC'));
            $countrys = Common::resetKey($countrys, 'id');
        }
        
        //update tj
        Dhm_Service_Goods::updateTJ($info['id']);
        
    
        $this->assign('info', $info);
        $this->assign('imgs', $imgs);
        $this->assign('refer', $refer);
        $this->assign('total_img', $total_img <= 3 ? $total_img : 3);
        $this->assign('brand', $brand);
        $this->assign('malls', $malls);
        $this->assign('min_price', $min_price);
        $this->assign('max_price', $max_price);
        $this->assign('price', $this->getPrice($min_price, $max_price));
        $this->assign('countrys', $countrys);
        $this->assign('mall_list', $mall_list);
        $this->assign('title', $info['title'].'大红帽：全球海淘资讯第一站');
        $this->assign('keywords', '海淘、代购、全球购、香港购物、什么值得买、'.$brand['name']. '、'.$info['title']);
        $this->assign('description', $brand['name']. '、'.$info['title']);
    }
    
    private function getPrice($min, $max) {
        if($min == $max) {
            return Common::money($min);
        }
        return Common::money($min).'-'.Common::money($max);
    }
    
}