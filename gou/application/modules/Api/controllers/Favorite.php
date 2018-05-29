<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Ryan
 * Class Apk_UserController
 */
class FavoriteController extends Api_BaseController {

    private $perpage=10;
    public $actions = array(
        'story_detailUrl'=>'/favorite/detail',
    );


    /**
     * 知物收藏
     */
    public function storyAction() {

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
        list($total,$favorite) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));
        $favorite = Common::resetKey($favorite, 'item_id');

        //知物列表
        $sid = array_keys($favorite);
        $authors= Gou_Service_UserAuthor::getAuthors();
        list(, $data) = Gou_Service_Story::getList($page, $this->perpage, array('item_id'=>array("IN", $sid)));

        $webroot = Common::getWebRoot();
        $staticroot = Common::getAttachPath();
        $result=array();
        foreach ($data as $v) {
            $item = array(
              'id' => $v['id'],
              'title' => html_entity_decode($v['title']),
              'summary' => html_entity_decode($v['summary']),
              'praise' => Common::parise($v['praise']),
              'uid' => $uid,
              'favorite' => Common::parise($v['favorite']),
              'fav_id' => $favorite[$v['id']]['id'],
              'is_favorite' => true,
              'author' => $authors[$v['author_id']]['nickname'],
              'avatar' => sprintf('%s%s', $staticroot, $authors[$v['author_id']]['avatar']),
              'url' => $webroot . $this->actions['story_detailUrl'] . '?id=' . $v['id'] . '&uid=' . $uid,
              'start_time' => $v['start_time'],
              'create_time' => $favorite[$v['id']]['create_time'],
            );

            if (!empty($v['images'])) {
                $v['images'] = explode(",", $v['images']);
                foreach ($v['images'] as &$img) {
                    $img = sprintf('%s%s', $staticroot, $img);
                }
                $item['images']=$v['images'];
            }

            array_push($result,$item);
        }
        $result = Common::resetKey($result,'create_time');
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
        $uid = $this->getInput("uid");
        if($this->getInput('perpage'))$this->perpage=intval($this->getInput('perpage'));
        // 1=>'知物', 2=>'商品', 3=>'店铺', 4=>'网页'

        if(!$uid)  $this->output(1, 'uid不能为空', array('list'=>array()));
        if ($page < 1) $page = 1;
        $type = 4;

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));

        $items = array();
        foreach ($result as $v) {
            $row = json_decode($v['data'],true);
            $item = array(
              'id' => $v['id'],
              'uid' => $v['uid'],
              'type' => $v['type'],
              'url' => html_entity_decode($v['url']),
              'create_time' => $v['create_time'],
              'title' => $row['title'],
              'src' => $v['src'],
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

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));

        $items = array();
        foreach ($result as $v) {
            $row =json_decode($v['data'],true);
            $item = array(
              'id'=>$v['id'],
              'uid'=>$v['uid'],
              'type'=>$v['type'],
              'url'=>html_entity_decode($v['url']),
              'create_time'=>$v['create_time'],
              'image' => $v['image'],
              'item_id' => $v['item_id'],
              'title' => $row['title'],
              'src' => $v['src'],
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

        list($total,$result) = User_Service_Favorite::getList($page, $this->perpage, array('type'=>$type,'uid'=>$uid,array('create_time'=>'DESC')));
        if($total==0) $this->output(0, '列表为空', array('list'=>array()));

        $items = array();
        foreach ($result as $v) {
            $row =json_decode($v['data'],true);
            $item = array(
              'id' => $v['id'],
              'uid' => $v['uid'],
              'type' => $v['type'],
              'url' => $v['url'],
              'create_time' => $v['create_time'],
              'image' => $v['image'],
              'src' => $v['src'],
              'title' => $row['title'],
              'price' => $row['price'],
            );
            array_push($items,$item);
        }

        $pager = ($page - 1) * $this->perpage;
        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

        $this->output(0, '', array('list' => $items, 'hasnext' => $hasnext, 'curpage' => $page));
	}

    public function addshopAction(){
        $uid= $this->getInput('uid');
        $item_id= $this->getInput('shop_id');

        $row = Client_Service_Shops::getShops($item_id);
        if(!$uid)$this->output(1, 'uid不能为空', array('result'=>false));
        if (empty($row['shop_url'])) $this->output(1, 'url不能为空', array('result'=>false));
        $exist = User_Service_Favorite::getBy(array('md5_url'=>md5($row['shop_url']),'uid'=>$uid));
        if (!empty($exist)) $this->output(1, '请勿重复收藏', array('result'=>false));

        $extend = json_encode(
          array(
            'title' => $row['shop_title'],
            'price' => $row['price'],
          )
        );

        $data = array(
          'uid' => $uid,
          'type' => 3,
          'item_id' => $item_id,
          'url' => $row['shop_url'],
          'md5_url' => md5($row['shop_url']),
          'image' => $row['logo'],
          'status' => 1,
          'src' => 'taobao',
          'goods_id' => $row['shop_id'],
          'title' => $row['shop_title'],
          'data' => $extend
        );

        $ret = User_Service_Favorite::addFavorite($data);
        if(!$ret){
            $this->output(1, '收藏失败，请重试', array('result'=>false));
        }
        Client_Service_Shops::updateFavCount($item_id);
        $this->output(0, '添加收藏成功', array('id'=>$ret));

    }

    public function addAction(){
        $data=$this->getInput(array('uid','url'));

        if(empty($data['uid']))  $this->output(1, 'uid不能为空', array('result'=>false));
        if(empty($data['url']))  $this->output(1, 'url不能为空', array('result'=>false));

        $row = User_Service_Favorite::getBy(array('md5_url'=>md5($data['url']),'uid'=>$data['uid']));
        if($row)$this->output(0, '该链接已经收藏', array('result'=>false));

        $data['md5_url']=md5($data['url']);
        $ret = User_Service_Favorite::addFavorite($data);

        if($ret){
            //get data
            Client_Service_Spider::getData($ret);
            $this->output(0, '添加收藏成功', array('result'=>true));
        }

        $this->output(1, '收藏失败，请重试', array('result'=>false));
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
                if(!empty($item_id))
                Client_Service_Shops::updateFavCount($item_id,-1);
                break;
            default:
                break;
        }

        $this->output(0,'取消收藏成功',array('id'=>$id));
    }

}
