<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author tiansh
 * Class Ios_FavoriteController
 */
class Ios_FavoriteController extends Api_BaseController {

    private $perpage=10;
    public $actions = array(
        'story_detailUrl'=>'/story/detail',
        'story_detailUrlComment'=>'/story/item',
    );

    public function addAction(){
        $data=$this->getInput(array('uid','url'));
        if(empty($data['uid']))  $this->output(1, '收藏失败,请重试', array('result'=>false));
        if(empty($data['url']))  $this->output(1, '收藏失败,请重试', array('result'=>false));
        //判断重复
        $row  = Client_Service_Spider::check($data['uid'],$data['url']);
        $info = User_Service_Favorite::getBy($row);
        if($info)$this->output(0, '该链接已经收藏', array('result'=>true));
        $row['channel_id']=1;
        $row['url']=$data['url'];
        $ret  = User_Service_Favorite::addFavorite($row);
        if($ret){
            Client_Service_Spider::getData($ret,$data['url']);
            $this->output(0, '添加收藏成功', array('result'=>true));
        }

        $this->output(1, '收藏失败，请重试', array('result'=>false));
    }

    /**
     * 知物收藏
     */
    public function storyAction() {

        $page = intval($this->getInput('page'));
        //$uid = $this->getInput("uid");
        $uid = COmmon::getIosUid();
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));

        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'
        $type = 1;

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        if (!$type) $type = 1;

        // 收藏列表  兼容老版本
        list($total,$favorite) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if ($total == 0) {
            if (Common::getIosClientVersion() > 110) {
                $this->output(0, '');
            } else {
                $this->emptyOutput(0, '列表为空');
            }
        }
        //知物列表
        $favorite = Common::resetKey($favorite, 'item_id');
        $authors= Gou_Service_UserAuthor::getAuthors();
        list(, $story) = Gou_Service_Story::getsBy(array('id'=>array("IN", array_keys($favorite))), array('id'=>'DESC'));
        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();
        $data=array();
        foreach ($story as $k=>$v) {
            $action = $this->actions['story_detailUrl'];
            if(Common::getIosClientVersion() > 110) {
                $action = $this->actions['story_detailUrlComment'];
            }
              $data[$k]['id'] = $v['id'];
              $data[$k]['title'] = html_entity_decode($v['title']);
              if($v['img']) $data[$k]['image'] = $staticroot.$v['img'];
              $data[$k]['summary'] = html_entity_decode($v['summary']);
              $data[$k]['praise'] = Common::parise($v['praise']);
              $data[$k]['uid'] = $uid;
              $data[$k]['favorite'] = Common::parise($v['favorite']);
              $data[$k]['fav_id'] = $favorite[$v['id']]['id'];
              $data[$k]['is_favorite'] = true;
              $data[$k]['author'] = $authors[$v['author_id']]['nickname'];
              $data[$k]['avatar'] = sprintf('%s%s', $staticroot, $authors[$v['author_id']]['avatar']);
              $data[$k]['url'] = $webroot . $action . '?id=' . $v['id'] . '&uid=' . $uid;
              $data[$k]['start_time'] = $v['start_time'];
              $data[$k]['create_time'] = $favorite[$v['id']]['create_time'];
              $data[$k]['like'] = Common::parise(sprintf('%s',$v['favorite']+$v['praise']));
              $data[$k]['comment'] = Common::parise($v['comment']);
        }
        
        $result = Common::resetKey($data,'create_time');
        krsort($result);
        $result=array_values($result);

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $result, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'
    /**
     * 网页收藏
     */
    public function webAction() {

        $page = intval($this->getInput('page')); 
        //$uid = $this->getInput("uid");
        $uid = COmmon::getIosUid();
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));
        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        $type = 4;

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if($total==0) {
            if(Common::getIosClientVersion() > 110) {
                $this->output(0, '');
            }else{
                $this->emptyOutput(0, '列表为空');
            }
        }
        $url_id = array_keys(Common::resetKey($result,'item_id'));
        list(,$data)= Third_Service_Web::getsBy(array('url_id'=>array('IN',$url_id)));
        $data=Common::resetKey($data,'url_id');
        $items = array();
        foreach ($result as $v) {
            if(empty($data[$v['item_id']]))continue;
            $row =$data[$v['item_id']];
            $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
            $item['id']          = $v['id'];
            $item['uid']         = $v['uid'];
            $item['type']        = $v['type'];
            $item['create_time'] = $v['create_time'];
            $item['url']         = html_entity_decode($row['url']);
            $item['title']       = html_entity_decode($row['title']);
            $item['src']         = !empty($channels[$row['channel_id']]['name']) ? $channels[$row['channel_id']]['name'] : Third_Service_Web::getDomain($row['url']);
            array_push($items,$item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $items, 'hasnext' => $hasnext, 'curpage' => $page));
	}
    /**
     * 店铺列表
     */
    public function shopAction() {

        $page = intval($this->getInput('page'));
        //$uid = $this->getInput("uid");
        $uid = COmmon::getIosUid();
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));
        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        $type = 3;

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if ($total == 0) {
            if (Common::getIosClientVersion() > 110) {
                $this->output(0, '');
            } else {
                $this->emptyOutput(0, '列表为空');
            }
        }
        $shop_id = array_keys(Common::resetKey($result,'item_id'));
        list(,$shop)= Third_Service_Shop::getsBy(array('shop_id'=>array('IN',$shop_id)));
        $shop=Common::resetKey($shop,'shop_id');

        $items = array();
        foreach ($result as $v) {
            if(empty($shop[$v['item_id']]))continue;
            $row =$shop[$v['item_id']];
            $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
            $item_url = empty($v['url']) ? Third_Service_Shop::getShopsUrl($v["item_id"], $row["channel_id"]) : html_entity_decode($v["url"]);

            $item['id']          = $v['id'];
            $item['url']         = $item_url;
            $item['uid']         = $v['uid'];
            $item['type']        = $v['type'];
            $item['src']         = $channels[$row['channel_id']]['name'];
            $item['item_id']     = $v['item_id'];
            $item['title']       = html_entity_decode($row['name']);
            $item['goods_id']    = $v['item_id'];
            $item['image']       = User_Service_Favorite::getImageUrl($row['logo']);
            $item['create_time'] = $v['create_time'];
            array_push($items,$item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $items, 'hasnext' => $hasnext, 'curpage' => $page));
	}

    /**
     * 商品列表
     */
    public function goodsAction() {

        $page = intval($this->getInput('page'));
        //$uid = $this->getInput("uid");
        $uid = COmmon::getIosUid();
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));
        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        $type = 2;

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if($total==0) {
                if(Common::getIosClientVersion() > 110) {
                    $this->output(0, '');
                }else{
                    $this->emptyOutput(0, '列表为空');
                }
            }

        $goods_id = array_keys(Common::resetKey($result,'item_id'));
//        list(,$goods)= Third_Service_Goods::getsBy(array('goods_id'=>array('IN',$goods_id)));
        foreach ($goods_id as $id) {
            $goods[] = Third_Service_Goods::get($id);
        }
        $goods=Common::resetKey($goods,'goods_id');

        $items = array();

        foreach ($result as $v) {
            if(empty($goods[$v['item_id']]))continue;
            $row =$goods[$v['item_id']];
            $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
            $item_url = empty($v['url']) ? Third_Service_Goods::getGoodsUrl($v['item_id'], $row['channel_id']) : html_entity_decode($v['url']);
            $item['id']          = $v['id'];
            $item['uid']         = $v['uid'];
            $item['type']        = $v['type'];
            $item['url']         = $item_url;
            $item['image']       = User_Service_Favorite::getImageUrl($row['img']);
            $item['src']         = $channels[$row['channel_id']]['name'];
            $item['title']       = html_entity_decode($row['title']);
            $item['price']       = $row['price'];
            $item['create_time'] = $v['create_time'];
            array_push($items, $item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $items, 'hasnext' => $hasnext, 'curpage' => $page));
	}

    public function addshopAction(){
        $uid= $this->getInput('uid');
        $item_id= $this->getInput('item_id');


        if(!$uid)$this->output(1, 'uid不能为空', array('result'=>false));

        $row = Client_Service_Shops::getBy(array('shop_id'=>$item_id));
        if(!$row) $this->output(1, '店铺不存在', array('result'=>false));
        if (empty($row['shop_url'])) $this->output(1, 'url不能为空', array('result'=>false));

        $exist = User_Service_Favorite::getBy(array('item_id'=>$row['shop_id'], 'uid'=>$uid, 'type'=>3));

        if (!empty($exist)) $this->output(1, '请勿重复收藏', array('result'=>false));

        $extend = array(
            'imgs' => explode(',', html_entity_decode($row['goods_img'])),
            'title' => $row['shop_title']
        );

        $shop['shop_id']    = $row['shop_id'];
        $shop['logo']       = $row['logo'];
        $shop['name']       = $row['shop_title'];
        $shop['channel_id'] = $row['shop_type'];
        $shop['status']     = 2;
        $shop['data']       = json_encode($extend);

        $info = Third_Service_Shop::getBy(array('shop_id'=>$row['shop_id']));
        if($info) {
            Third_Service_Shop::update($shop, $info['id']);
        }else{
            Third_Service_Shop::addShop($shop);
        }

        $data = array(
            'uid' => $uid,
            'type' => 3,
            'item_id' => $row['shop_id'],
            'status' => 2,
            'channel_id' => 1,
        );
        $ret = User_Service_Favorite::addFavorite($data);

        if(!$ret){
            $this->output(1, '收藏失败，请重试', array('result'=>false));
        }
        Client_Service_Shops::updateFavCount($item_id);
        $this->output(0, '添加收藏成功', array('id'=>$ret));

    }

    public function removeAction(){
        $id=intval($this->getInput('id'));
        //$uid = $this->getInput("uid");
        $uid = COmmon::getIosUid();
        $type=intval($this->getInput('type'));
        $item_id=$this->getInput('item_id');

        $row = User_Service_Favorite::getBy(array('id'=>$id));
        if(!$row){
            $this->output(1,'已取消',array('id'=>$id));
        }
        
        if($row['uid'] != $uid) $this->output(1,'取消收藏失败',array('id'=>$id));
        
        $ret = User_Service_Favorite::deleteFavorite($id);
        if(!$ret){
            $this->output(1,'取消收藏失败',array('id'=>$id));
        }

        if($row['type'] == 1) {
            Gou_Service_Story::updateFavorite($row['item_id'],-1);
            Gou_Service_Config::setValue('Story_Version', Common::getTime());
        }

        $this->output(0,'取消收藏成功',array('id'=>$id));
    }

}
