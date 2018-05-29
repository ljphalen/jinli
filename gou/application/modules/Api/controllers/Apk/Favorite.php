<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Ryan
 * Class Apk_UserController
 */
class Apk_FavoriteController extends Api_BaseController {

    private $perpage = 10;
    public $actions = array(
        'story_detailUrl'=>'/story/item',
    );


    // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'
    public function addAction(){
        $data=$this->getInput(array('uid','url'));
        if(empty($data['uid']))  $this->output(1, '收藏失败,请重试', array('result'=>false));
        if(empty($data['url']))  $this->output(1, '收藏失败,请重试', array('result'=>false));
        //判断重复
        $row  = Client_Service_Spider::check($data['uid'],$data['url']);
        $info = User_Service_Favorite::getBy($row);
        if($info)$this->output(0, '该链接已经收藏', array('result'=>true));
        $row['url']=$data['url'];
        $ret  = User_Service_Favorite::addFavorite($row);
        if($ret){
            Client_Service_Spider::getData($ret,$data['url']);

            //get data
            $this->output(0, '添加收藏成功', array('result'=>true));
        }

        $this->output(1, '收藏失败，请重试', array('result'=>false));
    }

    /**
     * 知物收藏
     */
    public function storyAction(){
        $page = intval($this->getInput('page'));
        $uid = $this->getInput("uid");
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));

        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'
        $type = 1;

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        if (!$type) $type = 1;

        /**
         * 收藏列表
         */
        list($total, $favorite) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid),array('create_time'=>'DESC'));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));
        $favorite = Common::resetKey($favorite, 'item_id');

        //知物列表
        $authors= Gou_Service_UserAuthor::getAuthors();
        list(, $data) = Gou_Service_Story::getsBy(array('id'=>array("IN", array_keys($favorite))));
        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();

        $uids = array_keys(Common::resetKey($data, 'uid'));
        $user_uids = User_Service_Uid::getUsersFmtByUid($uids);

        $result=array();
        foreach ($data as $v) {
            $item = array(
                'id'             => $v['id'],
                'title'          => html_entity_decode($v['title']),
                'summary'        => html_entity_decode($v['summary']),
                'uid'            => $uid,
                'praise'         => Common::parise($v['praise']),
                'favorite'       => Common::parise($v['favorite']),
                'like'           => Common::parise(sprintf('%s', $v['favorite'] + $v['praise'])),
                'comment'        => Common::parise($v['comment']),
                'fav_id'         => $favorite[$v['id']]['id'],
                'recommend'      => false,
                'is_favorite'    => true,
                'author'         => $authors[$v['author_id']]['nickname'],
                'avatar'         => sprintf('%s%s', $staticroot, $authors[$v['author_id']]['avatar']),
                'url'            => $webroot . $this->actions['story_detailUrl'] . '?id=' . $v['id'] . '&uid=' . $uid,
                'start_time'     => $v['start_time'],
                'create_time'    => $favorite[$v['id']]['create_time'],
            );

            if($v['channel'] == 1){
                $item['author'] = $user_uids[$v['uid']]['nickname'];
                $item['avatar'] = $user_uids[$v['uid']]['avatar'];
            }elseif($v['channel'] == 0){
                $item['author'] = $authors[$v['author_id']]['nickname'];
                $item['avatar'] = sprintf('%s%s', $staticroot, $authors[$v['author_id']]['avatar']);
            }

            if (!empty($v['images'])) {
                $v['images'] = explode(",", $v['images']);
                foreach ($v['images'] as &$img) {
                    $img = sprintf('%s%s', $staticroot, $img);
                }
                $item['images'] = $v['images'];
            }

            array_push($result, $item);
        }
        $result = Common::resetKey($result, 'create_time');
        krsort($result);
        $result=array_values($result);
        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $result, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    /**
     * 网页收藏
     */
    public function webAction() {
        $page = intval($this->getInput('page')); 
        $uid = $this->getInput("uid");
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));
        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        $type = 4;

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid),array('create_time'=>'DESC'));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));
        $url_id = array_keys(Common::resetKey($result,'item_id'));
        list(,$data)= Third_Service_Web::getsBy(array('url_id'=>array('IN',$url_id)));
        $data=Common::resetKey($data,'url_id');

        $items = array();
        foreach ($result as $v) {
            if(empty($data[$v['item_id']]))continue;
            $row =$data[$v['item_id']];
            $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
            $item = array(
              'id' => $v['id'],
              'uid' => $v['uid'],
              'type' => $v['type'],
              'url' => html_entity_decode($row['url']),
              'create_time' => $v['create_time'],
              'title' => html_entity_decode($row['title']),
              'src' => !empty($channels[$row['channel_id']]['name'])?$channels[$row['channel_id']]['name']:Third_Service_Web::getDomain($row['url']),
            );
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
        $uid = $this->getInput("uid");
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));
        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        $type = 3;

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid),array('create_time'=>'DESC'));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));
        $shop_id = array_keys(Common::resetKey($result,'item_id'));
        list(,$shop)= Third_Service_Shop::getsBy(array('shop_id'=>array('IN',$shop_id)));
        $shop=Common::resetKey($shop,'shop_id');
        $items = array();
        foreach ($result as $v) {
            if(empty($shop[$v['item_id']]))continue;
            $row =$shop[$v['item_id']];
            $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
            $item = array(
              'id'=>$v['id'],
              'uid'=>$v['uid'],
              'type'=>$v['type'],
              'item_id'=>$v['item_id'],
              'goods_id'=>$v['item_id'],
              'create_time'=>$v['create_time'],
              'url'=> empty($v['url']) ? Third_Service_Shop::getShopsUrl($v["item_id"], $row["channel_id"]) : html_entity_decode($v["url"]),
              'image' => User_Service_Favorite::getImageUrl($row['logo']),
              'title' => html_entity_decode($row['name']),
              'src' => $channels[$row['channel_id']]['name'],
            );
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
        $uid = $this->getInput("uid");
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));
        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        $type = 2;

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid),array('create_time'=>'DESC'));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));

        $items = array();
        $goods_id = array_keys(Common::resetKey($result,'item_id'));
//        list(,$goods)= Third_Service_Goods::getsBy(array('goods_id'=>array('IN',$goods_id)));
        foreach ($goods_id as $id) {
            $goods[] = Third_Service_Goods::get($id);
        }
        $goods=Common::resetKey($goods,'goods_id');
        foreach ($result as $v) {
            if(empty($goods[$v['item_id']]))continue;
            $row =$goods[$v['item_id']];
            $channels = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
            $item = array(
              'id' => $v['id'],
              'uid' => $v['uid'],
              'type' => $v['type'],
              'url' =>empty($v['url']) ? Third_Service_Goods::getGoodsUrl($v['item_id'],$row['channel_id']): html_entity_decode($v['url']),
              'create_time' => $v['create_time'],
              'image' => User_Service_Favorite::getImageUrl($row['img']),
              'src' => $channels[$row['channel_id']]['name'],
              'title' => html_entity_decode($row['title']),
              'price' => $row['price'],
            );
            array_push($items,$item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $items, 'hasnext' => $hasnext, 'curpage' => $page));
	}

    /**
     * 淘宝好店
     */
    public function addshopAction(){
        $uid= $this->getInput('uid');
        $uid = Common::getAndroidtUid();
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
            'channel_id' => 0,
        );
        $ret = User_Service_Favorite::addFavorite($data);

        if(!$ret){
            $this->output(1, '收藏失败，请重试', array('result'=>false));
        }
        Client_Service_Shops::updateFavCount($item_id);
        $this->output(0, '添加收藏成功', array('id'=>$ret));

    }
    /**
     * 良品收藏
     */
    public function addProdAction(){
        $uid= $this->getInput('uid');
        $uid = Common::getAndroidtUid();

        $item_id= $this->getInput('item_id');

        $topApi  = new Api_Top_Service();
        $info = $topApi->getTbkItemInfo(array('num_iids'=>$item_id));
        Mall_Service_Goods::inc('fav_count',array('num_iid'=>$item_id));
        if (!$uid) $this->output(1, 'uid不能为空', array('result' => false));
        if (!$info) $this->output(1, '商品已经下架', array('result' => false));

        $exist = User_Service_Favorite::getBy(array('item_id'=>$item_id, 'uid'=>$uid, 'type'=>2));
        if (!empty($exist)) $this->output(1, '请勿重复收藏', array('result'=>false));

        $item = Third_Service_Goods::get($item_id);

        $row['channel_id']     = 1;
        $row['status']         = 2;
        $row['goods_id']       = $item_id;
        $row['img']            = $info['pic_url'];
        $row['title']          = $info['title'];
        $row['price']          = $info['price'];
        $row['update_time']    = Common::getTime();
        $row['favorite_count'] = $item['favorite_count']+1;
        if (empty($item)) {
            $ret = Third_Service_Goods::addGoods($row);
        } else {
            $ret = Third_Service_Goods::update($row, $item_id);
        }

        $data = array(
            'uid'        => $uid,
            'type'       => 2,
            'status'     => 2,
            'item_id'    => $item_id,
            'channel_id' => 0,
            'price'      => $info['discount_price'],
            'diff_price' => $info['diff_price']
        );
        $ret = User_Service_Favorite::addFavorite($data);

        if(!$ret){
            $this->output(1, '收藏失败，请重试', array('result'=>false));
        }
        $this->output(0, '添加收藏成功', array('fav_id'=>$ret));

    }

    public function removeAction(){
        $id=intval($this->getInput('id'));
        $type=intval($this->getInput('type'));
        $item_id=$this->getInput('item_id');


        $row = User_Service_Favorite::getBy(array('id'=>$id));
        if(!$row){
            $this->output(1,'已取消',array('id'=>$id));
        }
        $ret = User_Service_Favorite::deleteFavorite($id);
        if(!$ret){
            $this->output(1,'取消收藏失败',array('id'=>$id));
        }

        switch($type){
            case 1:
                Gou_Service_Story::updateFavorite($item_id,-1);
                Gou_Service_Config::setValue('Story_Version', Common::getTime());
                break;
            case 3:
                break;
            default:
                break;
        }

        $this->output(0,'取消收藏成功',array('id'=>$id));
    }

    /**
     * 获取用户收藏的商品/店铺/网页等数据
     * @return json
     */
    public function favoriteAction(){
        $uid = Common::getAndroidtUid();
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;

        $perpage = $this->getInput('perpage') ? $this->getInput('perpage') : $this->perpage;

        $type   = array('IN', array(2, 3, 4));

        $sort   = array('diff_price' => 'DESC', 'create_time' => 'DESC');
        $params = array('type' => $type, 'uid' => $uid);
        list($total, $result) = User_Service_Favorite::getList($page, $perpage, $params, $sort);

        if ($total == 0) $this->output(0, '列表为空', array('list' => array()));

        $fweb = $fshop = $fgoods = array();
        $ids = array();
        foreach($result as $item){
            $ids[$item['type']][]=$item['item_id'];
        }

        $web_id   = array_filter(array_unique($ids[4]));
        $shop_id  = array_filter(array_unique($ids[3]));
        $goods_id = array_filter(array_unique($ids[2]));
        $data_goods = array();
        foreach ($goods_id as $id) {
            $data_goods[]  = Third_Service_Goods::get($id);
        }

        list(, $data_web)  = Third_Service_Web::getsBy(array('url_id' => array('IN', $web_id)));
        list(, $data_shop) = Third_Service_Shop::getsBy(array('shop_id' => array('IN', $shop_id)));

        $data_web          = Common::resetKey($data_web, 'url_id');
        $data_shop         = Common::resetKey($data_shop, 'shop_id');
        $data_goods        = Common::resetKey($data_goods, 'goods_id');

        $items = array();
        foreach ($result as $v) {
            $item = array(
                'id'        => $v['id'],
                'type'      => $v['type'],
                'item_id'   => $v['item_id'],
                'type_name' => '',
                'title'     => '',
                'image'     => '',
                'src'       => '',
                'url'       => '',
                'price'     => '',
                'reduce'    => '',
            );
            switch($v['type']){
                case 2: //商品
                    list($pl, $pr) = explode('.', $v['diff_price']);
                    $item['type_name'] = '商品';
                    $item['url']       = $v['url'];
                    $item['price']     = $v['price'];
                    $item['reduce']    = $v['diff_price'] >= 1?'降' . ($pr[0] != 0 && $pr > 0?number_format($v['diff_price'], 1):$pl):'';

                    if (isset($data_goods[$v['item_id']])) {
                        $row           = $data_goods[$v['item_id']];
                        $channels      = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
                        $item['title'] = html_entity_decode($row['title']);
                        $item['image'] = User_Service_Favorite::getImageUrl($row['img']);
                        $item['src']   = $channels[$row['channel_id']]['name'];
                        $item['url']   = empty($v['url'])?Third_Service_Goods::getGoodsUrl($goods_id,$row['channel_id']):html_entity_decode($v['url']);
                        $item['price'] = $row['price'];
                    }
                    break;
                case 3://店铺
                    $item['type_name'] = '店铺';
                    $item['url']       = "#";
                    if(isset($data_shop[$v['item_id']])){
                        $row           = $data_shop[$v['item_id']];
                        $channels      = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
                        $item['url']   = Third_Service_Shop::getShopsUrl($v['item_id'],$row['channel_id']);
                        $item['title'] = html_entity_decode($row['name']);
                        $item['image'] = User_Service_Favorite::getImageUrl($row['logo']);
                        $item['src']   = $channels[$row['channel_id']]['name'];
                    }
                    break;
                case 4://网页
                    $item['type_name'] = '网页';
                    $item['url']       = html_entity_decode($v['url']);

                    if(isset($data_web[$v['item_id']])){
                        $row           = $data_web[$v['item_id']];
                        $channels      = Common::resetKey(Client_Service_Spider::channels(),'channel_id');
                        $item['title'] = html_entity_decode($row['title']);
                        $item['image'] = Client_Service_Spider::getChannelLogo(html_entity_decode($row['url']));
                        $item['src']   = !empty($channels[$row['channel_id']]['name'])?$channels[$row['channel_id']]['name']:Client_Service_Spider::getChannelName(html_entity_decode($row['url']));
                        $item['url']   = html_entity_decode($row['url']);
                    }
                    break;
            }
            array_push($items, $item);
        }

        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list' => $items, 'hasnext' => $hasnext, 'curpage' => $page));
    }

    /**
     * 判断用户收藏商品列表是否有降价提示
     */
    public function isReduceAction(){
        $uid = Common::getAndroidtUid();
        $rs = User_Service_Favorite::getBy(array('uid'=>$uid, 'type'=>2, 'status'=>1));

        if(empty($rs))
            $this->output(0, '无降价', array('reduce'=>false, 'msg'=>''));
        else{
            //统计当客户端出现商品降价提示的pv
            $cache = Common::getCache();
            $cache->increment('gou_reduce_goods');
            $this->output(0, '有降价', array('reduce'=>true, 'msg'=>'有降价'));
        }
    }

    /**
     * 取消用户收藏列表的降价状态
     */
    public function removeReduceAction(){
        $uid = Common::getAndroidtUid();
        if(User_Service_Favorite::removeReduce($uid))
            $this->output(0, '降价状态已移除');

        $this->output(1, '数据操作有误');
    }
}
