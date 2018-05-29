<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class GoodsController extends Api_BaseController {
	
	public $perpage = 20;
	
    public function indexAction() {
    	/* $page = $this->getInput('page');
    	$brand_id = $this->getInput('brand_id');
    	$is_recommend = $this->getInput('is_recommend');
    	$category_id = $this->getInput('category_id');
    	$keyword = $this->getInput('keyword'); */
        $refer = $this->getInput('refer');
    	
    	$params = $this->getInput(array('keyword','category_id' ,'brand_id', 'page', 'is_recommend'));

        if(!$params['page']) $page = 1;

        $search = array();
        $search['status'] = 1;
        if($params['brand_id']) $search['brand_id'] = $params['brand_id'];
        if($params['category_id']) {
            //查询有没有子分类
            $cate_list = Dhm_Service_Category::getSubCategory($params['category_id']);
            $cate_ids = array();
            if($cate_list) {
                $cate_list = Common::resetKey($cate_list, 'id');
                /* $cates = Dhm_Service_Category::getsBy(array('id'=>array('IN', array_keys($cate_list))), array('id'=>'DESC'));
                $cates = Common::resetKey($cates, 'id'); */
                $cate_ids = array_keys($cate_list);
            }
            array_push($cate_ids, $params['category_id']);
            $search['category_id'] = array('IN', $cate_ids);
        }
        if($params['keyword']) {
            $params['keyword'] = urldecode($params['keyword']);
            if($params['keyword']) Dhm_Service_Search::addKey($params['keyword']);
            $search['title'] = array('LIKE', urldecode($params['keyword']));
        }
        if($params['is_recommend']) $search['is_recommend'] = $params['is_recommend'];

        list($total, $list) = Dhm_Service_Goods::getList($page, $this->perpage, $search, array('sort'=>'DESC', 'id'=>'DESC'));

        if($total==0) $this->output(0, '列表为空', array('list'=>array()));

        //brands
        $list_b = Common::resetKey($list, 'brand_id');
        list(, $brands) = Dhm_Service_Brand::getsBy(array('id'=>array('IN', array_keys($list_b))));
        $brands = COmmon::resetKey($brands, 'id');

        //country
        $list_c = Common::resetKey($list, 'country_id');
        list(, $countrys) = Dhm_Service_Country::getsBy(array('id'=>array('IN', array_keys($list_c))));
        $countrys = COmmon::resetKey($countrys, 'id');

        $attachroot = Common::getAttachPath();
        $webroot = Common::getwebroot();

        if($refer) {
            $refer = $this->getInput('refer');
        } else {
            $refer = $webroot. '/goods/?' . http_build_query($params) . '&';
        }

        $data = array();
        foreach($list as $key=>$value){
            list($min, $max) = $this->getRangePrice($value['id']);
            $data[] = array(
                'id' => $value['id'],
                'title' => html_entity_decode($value['title']),
                'img' => Common::getImageUrl($value['img']),
                'min_price' => Common::money($min),
                'max_price' => Common::money($max),
                'brand' => $brands[$value['brand_id']]['name'],
                'country' => $countrys[$value['country_id']]['name'],
                'logo' => $attachroot.$countrys[$value['country_id']]['logo'],
                'link' =>sprintf('%s/goods/detail?id=%d&refer=%s',$webroot,$value['id'], $refer ? urlencode($refer) : '')
            );
        }

        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $data, 'hasnext' => $hasnext, 'curpage' => $page));
    }
    
    /**
     * 
     * @param unknown $goods_id
     * @return multitype:mixed Ambigous <string, mixed>
     */
    private function getRangePrice($goods_id) {
        list(,$goods_mall) = Dhm_Service_GoodsMall::getsBy(array('goods_id'=>$goods_id),array('id'=>'DESC'));
        $min = $max = array();
        $min_price = $max_price = '0.00';
        if($goods_mall) {
            foreach ($goods_mall as $key=>$value){
                $min[] = Common::money($value['min_price']);
                $max[] = Common::money($value['max_price']);
            }
        }
        
        if($min) $min_price = min($min);
        if($max) $max_price = max($max);
        
        return array( $min_price, $max_price);
    }
}