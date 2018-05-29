<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Api_BaseController {

	public $perpage = 20;
	
	/**
	 * 首页广告列表
	 * @return json
	 */
    public function adAction() {
        //默认条数
        $perpage = 3;
        if($this->getInput('perpage')) $perpage=intval($this->getInput('perpage'));
    
        list($total, $list) = Fj_Service_Ad::getList(1, $perpage, array('status'=>1, 'end_time'=>array('>=', Common::getTime())));
    
        if($total==0) $this->output(0, '列表为空');
    
        $data = array();
        $attachroot = Common::getAttachPath();
        foreach($list as $key=>$value){
            $data[] = array(
            'img' => sprintf('%s%s', $attachroot, $value['img']),
            'link'=> html_entity_decode($value['link']),
            );
        }
    
       $this->output(0, '', $data);
    }
	

    /**
     * 首页商品列表
     * @return json
     */
    public function goodsAction() {
        $page = $this->getInput('page');
        if(!$page) $page = 1;

        if($this->getInput('perpage')) $this->perpage=intval($this->getInput('perpage'));

        list($total, $list) = Fj_Service_Goods::getList($page, $this->perpage, array('ishot'=>1, 'start_time'=>array('<=', Common::getTime()), 'end_time'=>array('>=', Common::getTime())));

        if($total==0) $this->output(0, '列表为空', array('list'=>array()));

        $attachroot = Common::getAttachPath();
        $webroot = Common::getwebroot();
        foreach($list as &$item){
            $item['img'] = sprintf('%s%s', $attachroot, $item['img']);
            $item['detail_url'] = $webroot.'/goods/detail?id='.$item['id'];
            $item['title'] = Util_String::substr(html_entity_decode($item['title']), 0, 22, '', true);
        }

        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    /**
     * 增加点击量
     */
    public function adClickAction(){
        $id = $this->getInput('id');
        Fj_Service_Ad::clickIncrement(array('id'=>$id));
    }
}