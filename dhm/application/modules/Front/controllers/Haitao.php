<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class HaitaoController extends Front_BaseController {
    public function indexAction() {
        
        //海淘
        list($total, $list) = Dhm_Service_Mall::getsBy(array('type_id'=>1, 'status'=>1), array('sort'=>'DESC', 'id'=>'DESC'));
        $this->assign('list', $list);
        $this->assign('total', $total);
        $this->assign('title', '大红帽海淘世界，全方位的海淘网站导航');
        $this->assign('keywords', '海淘网站、海淘、海淘攻略、代购、跨境电商、香港购物、全球购、什么值得买');
        $this->assign('description', '大红帽全方位的海淘网站导航频道，涵盖各大知名海淘网站，海淘更便捷');
    }
}