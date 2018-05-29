<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Front_BaseController {
    public function indexAction() {
        
        $time = Common::getTime();
        //广告
        list(, $ads) = Dhm_Service_Ad::getList(1, 5, array('ad_type'=> 1,'status'=>1, 'end_time'=>array('>=', $time)));
        
        //运营位左
        list(, $adl) = Dhm_Service_Ad::getList(1, 2, array('ad_type'=> 2,'status'=>1, 'end_time'=>array('>=', $time)));
        
        //运营位右
        list(, $adr) = Dhm_Service_Ad::getList(1, 2, array('ad_type'=> 3,'status'=>1, 'end_time'=>array('>=', $time)));
        
        //category
        list(, $categorys) = Dhm_Service_Category::getsBy(array('root_id'=>0, 'parent_id'=>0), array('sort'=>'DESC', 'id'=>'DESC'));
        
        //info
        list(, $info) = Dhm_Service_Info::getList(1, 5, array('status'=>1, 'type'=>0, 'start_time'=>array('<=', $time)), array('is_recommend'=>'DESC','start_time'=>'DESC'));
        $this->assign('ads', $ads);
        $this->assign('adl', $adl);
        $this->assign('adr', $adr);
        $this->assign('category', $categorys);
        $this->assign('info', $info);
        list($title, $keywords, $description) = $this->getMate();
        $this->assign('title', $title);
        $this->assign('keywords', $keywords);
        $this->assign('description', $description);
    }
    
    
    private function getMate() {
        return array(
        	Dhm_Service_Config::getValue('dhm_page_title'),
            Dhm_Service_Config::getValue('dhm_page_keywords'),
            Dhm_Service_Config::getValue('dhm_page_description')
        );
    }
}