<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class InfoController extends Front_BaseController {
    public $type = array(
        0=>'海淘资讯',1=>'海淘攻略'
    );
    public function indexAction() {
        
        $this->assign('title', '大红帽，专业提供海淘资讯信息');
        $this->assign('keywords', '海淘资讯、海淘教程、代购、跨境电商、香港购物、海淘经验、什么值得买');
        $this->assign('description', '大红帽资讯中心，分享海淘经验和海淘相关热点资讯，每一篇资讯都由大红帽小编精心撰写，是最用心最专业的海淘购物指南频道。大红帽资讯中心涵盖母婴用品、美妆个护、电子数码、箱包配饰、药用保健、日常生活等分类。');
    }
    public function guidesAction()
    {
        $this->assign('title', '大红帽海淘攻略，最受欢迎的海淘攻略大全');
        $this->assign('keywords', '海淘攻略、海淘教程、海淘、代购、海淘经验、跨境电商、香港购物、什么值得买');
        $this->assign('description', '海淘攻略，汇总大红帽小编精心撰写的所有海淘攻略教程，涵盖海淘新手攻略、商品优惠攻略、转运直邮攻略等，每篇海淘攻略皆以图文形式呈现，海淘攻略频道旨在帮助网友们依托转运公司购买海外商品，不依赖代购商家。');
    }

    /**
     * 详情
     */
    public function detailAction() {
        $id = $this->getInput('id');
        $is_share = $this->getInput('is_share');
        $info = Dhm_Service_Info::get($id);
        $webroot = Common::getWebRoot();
        if(!$id || !$info) $this->redirect($webroot);
        $refer = $this->getInput('refer');

        //update tj
        Dhm_Service_Info::updateTJ($info['id']);
        
        //分享图片
        $share_img = '';
        if($info['images']) {
            $images = explode(",", $info['images']['images']);
            $share_img = $images[0];
        }
        $footer =false;
        if($info['footer_id']){
            $footer = Dhm_Service_Footer::get($info['footer_id']);
        }
        $this->assign('info',        $info);
        $this->assign('is_share',        $is_share);
        $this->assign('footer',      $footer);
        $this->assign('share_img',   $share_img);
        $this->assign('refer',       $refer ? $refer : $webroot);
        $this->assign('title',       html_entity_decode($info['title'])." ".$this->type[$info['type']]);
        $this->assign('keywords',    '海淘资讯、海淘攻略、海淘教程、什么值得买');
        $this->assign('description', html_entity_decode($info['title']).'_分享自大红帽海淘资讯中心，由大红帽小编深度了解而撰写，提供给大家最专业的海淘资讯。');
    }
    
}