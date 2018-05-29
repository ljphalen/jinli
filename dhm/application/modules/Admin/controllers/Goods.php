<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * Enter description here ...
 * @author tiansh
 *
 */
class GoodsController extends Admin_BaseController
{

    public $actions = array(
        'listUrl' => '/Admin/Goods/index',
        'addUrl' => '/Admin/Goods/add',
        'addPostUrl' => '/Admin/Goods/add_post',
        'editUrl' => '/Admin/Goods/edit',
        'editPostUrl' => '/Admin/Goods/edit_post',
        'deleteUrl' => '/Admin/Goods/delete',
        'getCateTagUrl' => '/Admin/Tag/get',
        'mallListUrl' => '/Admin/Goodsmall/index',
        'mallAddUrl' => '/Admin/Goodsmall/add',
        'uploadUrl' => '/Admin/Goods/upload',
        'uploadImgUrl' => '/Admin/Goods/uploadImg',
        'uploadPostUrl' => '/Admin/Goods/upload_post',
    );

    public $perpage = 20;
    public $status = array(
        1 => "有效",
        0 => "无效"
    );

    public function init()
    {
        parent::init();
        list(, $category) = Dhm_Service_Category::getAll();
        $tmp = array();
        foreach ($category as $key => $cate) {
            if ($cate['root_id'] == 0) {//根类
                $tmp['root'][] = $cate;
            } elseif ($cate['parent_id'] == 0) {//父类
                $tmp['parent'][$cate['root_id']][] = $cate;
            } else {//子类
                $tmp['child'][$cate['parent_id']][] = $cate;
            }
        }

        $this->category  = $tmp;
        $this->categorys = Common::resetKey($category,"id");

        list(, $this->countrys) = Dhm_Service_Country::getAll();
        list(, $this->tags)     = Dhm_Service_Tag::getAll();
        list(, $this->brands)   = Dhm_Service_Brand::getsBy(array("status" => 1));

    }


    public function indexAction()
    {
        $page = intval($this->getInput('page'));

        $perpage = $this->perpage;
        $params=$this->getInput(array("country_id", "category_id", "is_recommend", "brand_id", 'tag_id', 'title', 'status'));

        $search = array();
        if(!isset($params['status']))       $params['status']       = -1;
        if(!isset($params['is_recommend'])) $params['is_recommend'] = -1;
        $category = $this->_getCat($params['category_id']);
        if ($params['country_id'])        $search['country_id']     = $params['country_id'];
        if ($params['brand_id'])          $search['brand_id']       = $params['brand_id'];
        if ($params['tag_id'])            $search['tag_id']         = array('LIKE', $params['tag_id']);
        if ($params['title'])             $search['title']          = array('LIKE', $params['title']);
        if ($params['category_id'])       $search['category_id']    = $category;
        if ($params['status']!=-1)        $search['status']         = $params['status'];
        if ($params['is_recommend']!=-1)  $search['is_recommend']   = $params['is_recommend'];
        $sort = array('sort'=>'DESC','id'=>'DESC');
        list($total, $goods)  = Dhm_Service_Goods::getList($page, $perpage, $search, $sort);

        if(!empty($goods)){
            $goods        = Common::resetKey($goods, "id");
            list(, $mall) = Dhm_Service_GoodsMall::getsBy(array('goods_id'=>array("IN",array_keys($goods))));
            $mall         = array_map(function($v,$v){return $v['goods_id'];}, Common::resetKey($mall, 'id'));
        }


        list(,$tags)          = Dhm_Service_Tag::getAll();
        $tag = Common::resetKey($tags, 'id');
        $tag = array_map(function($v){return $v['name'];}, $tag);

        $country  = Common::resetKey($this->countrys, 'id');
        $brands   = Common::resetKey($this->brands,   'id');
        $category = Common::resetKey(Dhm_Service_Category::getAll()[1], 'id');

        foreach ($goods as &$v) {
            if(empty($v['tag_ids'])) continue ;
            $tag_id = array_filter(explode(',', $v['tag_ids']));
            $vTag = array_intersect_key($tag, array_flip($tag_id));
            $v['tag'] = implode(',', $vTag);
        }


        $this->assign('tag',       $tag);
        $this->assign('data',      $goods);
        $this->assign('categorys', $category);
        $this->assign('countrys',  $country );
        $this->assign('brands',    $brands);
        $this->assign('tags',      $this->tags);
        $this->assign('mall',      array_count_values($mall));
        $this->assign('status',    $this->status);
        $url = $this->actions['listUrl'] .'/?'. http_build_query($params) . '&';
        $this->assign('pager',     Common::getPages($total, $page, $perpage, $url));
        $this->assign('search',    $params);
        $this->cookieParams();
    }

    /**
     * 获取分类的筛选条件
     * @param $category_id
     * @return array
     */
    private function _getCat($category_id)
    {
        if (empty($category_id)) return $category_id;
        $row  = Dhm_Service_Category::get($category_id);
        $cats = array();
        if (empty($row['root_id'])) {
            list(, $cats) = Dhm_Service_Category::getsBy(array('root_id' => $category_id));
        } elseif (empty($row['parent_id'])) {
            list(, $cats) = Dhm_Service_Category::getsBy(array('parent_id' => $category_id));
        }
        $rows = array_keys(Common::resetKey($cats, 'id'));
        if (empty($rows)) {
            return $category_id;
        }
        array_splice($rows, 0, 0, $category_id);
        return array('IN', $rows);
    }

    public function addAction()
    {
        $this->assign('category', $this->category);
        $this->assign('country',  $this->countrys);
        $this->assign('brands',   $this->brands);
        $this->assign('tags',     $this->tags);
        $this->assign('status',   $this->status);
        $this->assign('dir',      'goods');
        $this->assign('ueditor',  true);
    }

    public function add_postAction()
    {
        $goods    = array("id", "sort", "title", "is_recommend", "mall_from", "img", "cover_img", "min_price", "max_price", "country_id", "brand_id", "status", "content", "tag_ids");
        $info     = $this->getPost($goods);
//        print_r($info)
        $category = $this->getPost(array("root_id", "parent_id", "child_id"));
        $images   = $this->getPost(array("image", "images"));
        $mall     = $this->getPost(array("url", "min_price", "max_price"));
        $info['category_id'] = $this->_getCategoryId($category);

        $data = $this->_cookData($info);
        $mall_from = $this->_getMallId($info['mall_from']);

        if(empty($mall_from['id'])){
            $this->output(-1, '请在商家列表中添加.'.$info['mall_from']);
        }else{
            $mall["mall_id"] = $mall_from['id'];
            $mall["type_id"] = $mall_from['type_id'];
        }
        $goods_id = Dhm_Service_Goods::add($data);
        if (!$goods_id) {
            $this->output(-1, '操作失败.');
        }
        $this->_updateImages($images, $goods_id);


        $this->_updateMall($mall, $goods_id);
        $this->output(0, '操作成功.');
    }

    public function editAction()
    {
        $id   = $this->getInput('id');
        $info = Dhm_Service_Goods::get(intval($id));

        list(, $images) = Dhm_Service_GoodsImage::getsBy(array('goods_id'=>$id));
        $images = array_keys(Common::resetKey($images,"img"));

        $cate = $this->categorys[$info['category_id']];
        //一级分类
        if(empty($cate['root_id'])){
            $cate['root_id'] = $cate['id'];
        }elseif(empty($cate['parent_id'])){//二级分类
            $cate['parent_id'] = $cate['id'];
            $parent = $this->category['parent'][$cate['root_id']];
            $this->assign('parent',   $parent);
        }else{//三级分类
            $parent = $this->category['parent'][$cate['root_id']];
            $child  = $this->category['child'][$cate['parent_id']];
            $this->assign('parent',   $parent);
            $this->assign('child',    $child);
        }
        $info['cate']=$cate;
        list(, $tags) = Dhm_Service_Tag::getsBy(array('id'=>array('IN',explode(',',$info['tag_ids']))));
        list(, $mall) = Dhm_Service_GoodsMall::getsBy(array("goods_id"=>$id, "mall_id"=>1));
        $images = implode(',',$images);

        $this->assign('category', $this->category);
        $this->assign('country',  $this->countrys);
        $this->assign('brands',   $this->brands);
        $this->assign('tags',     $tags);
        $this->assign('status',   $this->status);
        $this->assign('info',     $info);
        $this->assign('images',   $images);
        $this->assign('mall',     $mall);

        $this->assign('dir', 'goods');
        $this->assign('ueditor', true);
    }

    public function edit_postAction()
    {
        $goods    = array("id", "sort", "title", "is_recommend", "img", "cover_img", "min_price", "max_price", "country_id", "brand_id", "status", "content", "tag_ids");
        $info     = $this->getPost($goods);
        $category = $this->getPost(array("root_id", "parent_id", "child_id"));
        $images   = $this->getPost(array("image", "images"));
        $mall     = $this->getPost(array("url", "min_price", "max_price"));

        $info['category_id'] = $this->_getCategoryId($category);
        $data = $this->_cookData($info);

        $goods_id = Dhm_Service_Goods::update($data, $data["id"]);
        if (!$goods_id) {
            $this->output(-1, '操作失败.');
        }
        $this->_updateImages($images,$info['id']);

        $mall["mall_id"] = 1;
        $this->_updateMall($mall, $goods_id);
        $this->output(0, '操作成功.');
    }

    public function deleteAction()
    {
        $id   = $this->getInput('id');
        $info = Dhm_Service_Goods::get($id);
        if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
        Dhm_Service_GoodsImage::delBy(array('goods_id' => $id));
        $ret  = Dhm_Service_Goods::delete($id);
        if (!$ret) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     * 上传页面
     */
    public function uploadAction()
    {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }


    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'goods');
        $adminroot = Yaf_Application::app()->getConfig()->adminroot;
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => $adminroot.'/attachs/' .$ret['data'])));
    }

    /**
     * 处理上传
     */
    public function upload_postAction()
    {
        $ret = Common::upload('img', 'goods');
        $imgId = $this->getPost('imgId');
        $this->assign('code', $ret['data']);
        $this->assign('msg', $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    private function _getMallId($mall_from){
        $row = Dhm_Service_Mall::getBy(array('name'=>array("LIKE", $mall_from)));
        return $row;
    }

    /**
     * 确定当前产品分类id
     * @param array $data
     * @return int
     */
    private function _getCategoryId($data)
    {
        if (!empty($data["child_id"])) {
            return $data["child_id"];
        }
        if (!empty($data["parent_id"])) {
            return $data["parent_id"];
        }
        if (!empty($data["root_id"])) {
            return $data["root_id"];
        }

        return 0;
    }

    /**
     * @param  array $info
     * @param  int $goods_id
     * @return mixed
     */
    private function _updateMall($info, $goods_id)
    {
        $info["goods_id"] = $goods_id;
        $info['url']    = html_entity_decode($info['url']);
        $row = Dhm_Service_GoodsMall::getBy(array("goods_id" => $info["goods_id"], "mall_id" => $info["mall_id"]));
        if(empty($row)){
            Dhm_Service_GoodsMall::add($info);
        }else{
            Dhm_Service_GoodsMall::update($info, $row['id']);
        }
    }

    /**
     * @param  array $info
     * @param  int $goods_id
     * @return mixed
     */
    private function _updateImages($info, $goods_id)
    {
        if (empty($info['image'])) {
            $images = explode(',', html_entity_decode($info['images']));
        } else {
            $images = $info['image'];
        }
        if (empty($images)) return false;

        Dhm_Service_GoodsImage::delBy(array('goods_id' =>$goods_id));
        foreach ($images as $image) {
            Dhm_Service_GoodsImage::add(array("img" => $image, "goods_id" => $goods_id));
        }

        return $info;
    }

    /**
     * @param  array $info
     * @return mixed
     */
    private function _cookData($info)
    {
        if (!$info['title']) $this->output(-1, '名称不能为空.');
        if(empty($info['id'])){
            $row = Dhm_Service_Goods::getBy(array('title'=>$info['title']));
            if(!empty($row))$this->output(-1, '商品名称重复.');
        }else{
            $row = Dhm_Service_Goods::getBy(array('title'=>$info['title'],'id'=>array('<>',$info['id'])));
            if(!empty($row))$this->output(-1, '商品名称重复.');
        }
        $info['img']       = html_entity_decode($info['img']);
        if(!empty($info['cover_img']))
        $info['img']       = html_entity_decode($info['cover_img']);
        $info['title']     = html_entity_decode($info['title']);
        $info['min_price'] = html_entity_decode($info['min_price']);
        $info['max_price'] = html_entity_decode($info['max_price']);
        $info['content']   = html_entity_decode($info['content']);
        $info['content'] = $this->updateImgUrl($info['content']);
        $info["tag_ids"]   = implode(',', $info['tag_ids']);
        return $info;
    }
}
