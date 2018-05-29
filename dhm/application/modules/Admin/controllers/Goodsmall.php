<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class GoodsmallController extends Admin_BaseController
{

    public $actions = array(
        'listUrl' => '/Admin/Goodsmall/index',
        'addUrl' => '/Admin/Goodsmall/add',
        'addPostUrl' => '/Admin/Goodsmall/add_post',
        'editUrl' => '/Admin/Goodsmall/edit',
        'editPostUrl' => '/Admin/Goodsmall/edit_post',
        'deleteUrl' => '/Admin/Goodsmall/delete',
        'transUrl' => '/Admin/Goodsmall/translate',
        'importUrl' => '/Admin/Goodsmall/import',
        'importPostUrl' => '/Admin/Goodsmall/import_post',
        'uploadUrl' => '/Admin/Country/upload',
        'uploadPostUrl' => '/Admin/Country/upload_post',
    );

    public $perpage = 20;

    public function indexAction()
    {
        $page = intval($this->getInput('page'));

        $perpage = $this->perpage;
        $params=$this->getInput(array("title", "goods_id", "mall_id"));
        $search = array();

        if (!empty($params['title'])) {
            list(,$goods)= Dhm_Service_Goods::getsBy(array("title" => array("LIKE", $params['title'])));
            $goods_ids = array_keys(Common::resetKey($goods, 'id'));
        }
        if (empty($goods_ids)) {
            if ($params['goods_id']) $search['goods_id'] = $params['goods_id'];
        } else {
            if ($params['goods_id']) {
                array_splice($goods_ids, $params['goods_id']);
                $search['goods_id'] = array("IN", $goods_ids);
            } else {
                $search['goods_id'] = array("IN", $goods_ids);
            }
        }
        if ($params['mall_id'])     $search['mall_id']    = $params['mall_id'];
        list($total, $rows)  = Dhm_Service_GoodsMall::getList($page, $perpage, $search);
        if(!empty($rows)){
            $goods_ids = array_keys(Common::resetKey($rows,'goods_id'));
            list(,$goods) = Dhm_Service_Goods::getsBy(array('id'=>array('IN',$goods_ids)));
            $this->assign('goods',     Common::resetKey($goods,'id'));
        }

        $malls = Dhm_Service_Mall::getAll();
        $this->assign('malls',     Common::resetKey($malls,'id'));
        $this->assign('data',      $rows);
        $url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
        $this->assign('pager',     Common::getPages($total, $page, $perpage, $url));
        $this->assign('search',    $params);
        $this->cookieParams();
    }

    /**
     * 添加国家
     */
    public function addAction() {
        $good_id = $this->getInput("goods_id");
        $row     = Dhm_Service_Goods::get($good_id);
        $malls   = Dhm_Service_Mall::getAll();
        list(,$country) = Dhm_Service_Country::getAll();
        $country = Common::resetKey($country, 'id');
        $this->assign("malls", $malls);
        $this->assign("row", $row);
        $this->assign("country", $country);
    }

    /**
     * 处理添加
     */
    public function add_postAction()
    {

        $info      = $this->getPost(array('goods_id', 'mall_id', 'min_price', 'max_price', 'url'));
        $info      = $this->_cookData($info);
        $condition = array('goods_id' => $info['goods_id'], 'mall_id' => $info['mall_id']);

        if($info['min_price']>$info['max_price']){
            $tmp = $info['min_price'];
            $info['min_price'] = $info['max_price'];
            $info['max_price'] = $tmp;
        }

        $goods = Dhm_Service_Goods::get($info['goods_id']);

        if($info['min_price']<$goods['min_price']){
            $goods['min_price'] = $info['min_price'];
        }
        if($info['max_price']>$goods['max_price']){
            $goods['max_price'] = $info['max_price'];
        }
        Dhm_Service_Goods::update($goods,$info['goods_id']);

        $row       = Dhm_Service_GoodsMall::getBy($condition);
        if (empty($row)) {
            $result = Dhm_Service_GoodsMall::add($info);
        } else {
            $result = Dhm_Service_GoodsMall::updateBy($info, $condition);
        }

        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 编辑国家
     */
    public function editAction() {
        $id = $this->getInput('id');
        $info = Dhm_Service_GoodsMall::get($id);
        $row  = Dhm_Service_Goods::get($info['goods_id']);
        $mall = Dhm_Service_Mall::get($info['mall_id']);

        $this->assign("mall",$mall);
        $this->assign("row",$row);
        $this->assign('info', $info);
    }

    public function translateAction(){
        $kwd     = $this->getInput("kwd");
        $to_lang = $this->getInput("lang_code");
        $to_lang = $to_lang ? $to_lang : "en";
        $ret     = Api_Baidu_Fanyi::translate('zh', $to_lang, $kwd);
        $this->output(0, "", $ret);
    }

    /**
     * 处理编辑
     */
    public function edit_postAction() {
        $info = $this->getPost(array('id', 'goods_id', 'mall_id', 'min_price', 'max_price', 'url'));
        $info = $this->_cookData($info);
        $ret = Dhm_Service_GoodsMall::update($info, intval($info['id']));
        if (!$ret) $this->output(-1, '操作失败');
        $this->output(0, '操作成功.');
    }

    public function deleteAction()
    {
        $id   = $this->getInput('id');
        $info = Dhm_Service_GoodsMall::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
        $ret  = Dhm_Service_GoodsMall::delete($id);
        if (!$ret) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }


    private function _getCNYRate($mall_id){
        $mall    = Dhm_Service_Mall::get($mall_id);
        if(strtolower($mall['name'])=='ebay'){
            return 1;
        }
        $country = Dhm_Service_Country::get($mall['country_id']);
        $rate = Util_Exchange::getExchangeRate($country['currency'],"CNY");
        return $rate;
    }

    private function _getCNYPrice($price, $rate)
    {
        $rate  = $rate?$rate:1;
        $price = html_entity_decode($price);
        $price = str_replace(',', '', $price);
        $price = floatval($price) * $rate;
        return $price;
    }

    /**
     * @param  array $info
     * @return mixed
     */
    private function _cookData($info)
    {
        if (!$info['goods_id']) $this->output(-1, '商品不能为空.');
        if (!$info['mall_id'])  $this->output(-1, '商家不能为空.');
        $rate = $this->_getCNYRate($info['mall_id']);
        $info['min_price'] = $this->_getCNYPrice($info['min_price'],$rate);
        $info['max_price'] = $this->_getCNYPrice($info['max_price'],$rate);
        $info['url']       = html_entity_decode($info['url']);
        $mall = Dhm_Service_Mall::get($info['mall_id']);
        $info['type_id'] = $mall['type_id'];
        return $info;
    }
}
