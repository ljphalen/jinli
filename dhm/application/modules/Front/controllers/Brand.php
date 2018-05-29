<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class BrandController extends Front_BaseController {
    
    /**
     * 列表
     */
    public function indexAction() {
        //分类
        list(, $cates) = Dhm_Service_Category::getsBy(array('root_id'=>0, 'parent_id'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
        
        //品牌
        list($total, $brand_list) = Dhm_Service_Brand::getsBy(array('status'=>1, 'is_top'=>1), array('sort'=>'DESC', 'id'=>'DESC'));
        $list = $this->_cookData($cates, $brand_list);
        
        $this->assign('list', $list);
        $this->assign('total', $total);
        $this->assign('title', '大红帽品牌汇，全球知名海淘品牌一站汇聚');
        $this->assign('keywords', '海淘品牌、海淘、代购、什么值得买');
        $this->assign('description', '大红帽品牌汇，汇聚全球知名品牌，详细的介绍海淘购物涉及到的品牌详情、评价、产品、图片、价格等，为你提供最全面的海淘购物品牌介绍，方便网友海淘全球品牌');
    }
    
    
    /**
     * 详情
     */
    public function detailAction() {
        $id = $this->getInput('id');
        if($id) {
            $brand = Dhm_Service_Brand::get($id);
        } else {
            $brand = Dhm_Service_Brand::getBy(array('is_top'=>1, 'status'=>1), array('sort'=>'DESC'));
        }
        
        $webroot = Common::getWebRoot();
        if(!$brand) $this->redirect($webroot);
        
        //update tj
        Dhm_Service_Brand::updateTJ($brand['id']);
        
        $this->assign('brand', $brand);
        $this->assign('id', $id);
        $this->assign('title', $brand['name'].'_大红帽：全球海淘资讯第一站');
        $this->assign('keywords', $brand['name'].'、海淘、代购、全球购、香港购物、跨境电商、海淘资讯、什么值得买');
        $this->assign('description', $brand['brand_desc']);
    }
    
    /**
     *
     * @param array $roots
     * @param array $childs
     */
    private function _cookData($cates, $brand_list) {
        $tmp = Common::resetKey($cates, 'id');
        $data = array();
        foreach ($brand_list as $key => $v) {
            $tmp[$v['category_id']]['brand'][] = array(
                            'id'=>$v['id'],
                            'name'=>Util_String::substr(html_entity_decode($v['name']), 0, 8),
                            'logo'=>$v['logo']
            );
        }
        return $tmp;
    }
}