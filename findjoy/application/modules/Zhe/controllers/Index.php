<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class IndexController extends Common_BaseController {

    public $perpage = 51;
    public $actions = array(
        'listUrl'=>'/zhe/index/index'
    );

    public function indexAction()
    {
        $page = intval($this->getInput('page'));
        $category_id = intval($this->getInput('cid'));
        $pos = intval($this->getInput('pos'));

        if(!$page) $page = 1;
        if ($category_id) {
            $params['category_id'] = $category_id;
            $search['cid'] = $category_id;
        }
        if ($pos) {
            if ($pos == 3){
                $params['zhe'] = array('>', 1);
            } else {
                $params['price_pos'] = $pos;
            };
            $search['pos'] = $pos;
        }
        $search["page"] = $page;

        list($total, $result) = Craw_Service_Goods::getList(intval($page), $this->perpage, $params, array('sale_num'=>'DESC'));
        $totalPage =ceil((int) $total / $this->perpage);

        $pages = Common::getNPages($search['page'], $totalPage);
        $this->assign('totalPage', $totalPage);
        $this->assign('pages', $pages);
        $this->assign('search', $search);
        $this->assign('result', $result);
    }
}