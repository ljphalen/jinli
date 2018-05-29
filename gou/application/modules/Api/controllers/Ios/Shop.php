<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class Ios_ShopController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
				'indexUrl'=>'/api/shop/index',
				'detailUrl'=>'/shop/detail',
				'tjUrl'=>'/index/tj'
	);
	public $cacheKey = 'Ios_Shop_index';

    public function tagAction(){
        list(,$shops) = Client_Service_Shops::getsBy(array('channel_id'=>2,'tag_id'=>array('<>','')));
        $tags = array();
        foreach ($shops as $v) {
            if(empty($v['tag_id'])) continue ;
            $tmp = explode(',',str_replace('-','',$v['tag_id']));
            $tags = array_merge($tags,$tmp);
        }
        $search['id']=array('IN',array_unique($tags));
        $tag = Client_Service_Tag::getsBy($search,array('sort'=>'DESC'));
        $this->output(0, '', array('list'=>$tag));
    }

	public function indexAction() {
		$page = intval($this->getInput('page'));
        $uid = $this->getInput('uid');
        $tag_id = $this->getInput('tag_id');
		if ($page < 1) $page = 1;
		$webroot = Common::getWebRoot();
		$tjUrl = $webroot.$this->actions['tjUrl'];
		$staticroot = Yaf_Application::app()->getConfig()->staticroot;

        $search = array('status'=>1, 'channel_id'=>6);
        if(!empty($tag_id))$search['tag_id']=array('LIKE',"-$tag_id-");
		//shops
		list($total, $shops) = Client_Service_Shops::getList($page, $this->perpage, $search);

        if(!empty($uid)){
            $fav = array();
            $item_id_arr = array_keys(Common::resetKey($shops,'shop_id'));
            $cond = array(
              'uid'=>$uid,
              'item_id'=>array('IN',array_filter($item_id_arr))
            );
            //fav in list by uid
            list(,$favList) = User_Service_Favorite::getsBy($cond,array());

            $favList = Common::resetKey($favList,'item_id');
            foreach ($favList as $k=>$v) {
                $fav[$k]=$v['id'];
            }
        }

		$data = array();
		$i = 1;
		$pager = ($page - 1) * $this->perpage;
		foreach ($shops as $key=>$val) {
			$item['id'] = $val['id'];
            if(isset($fav)){
                $item['is_favorite']=0;
                if(!empty($fav[$val['shop_id']])){
                    $item['is_favorite']=1;
                    $item['fav_id']=$fav[$val['shop_id']];
                }
            }

			$item['num'] = ($pager == 0 && $i < 10) ? '0'.$i : $i + $pager;
			$item['title'] = Util_String::substr($val['shop_title'], 0, 12, '', true);

			if(strpos($val['logo'], 'http://') !== false) {
			    $logo = $val['logo'].'_120x120.jpg';
			}else{
			    $logo = Common::getAttachPath().$val['logo'];
			}
			$item['logo'] = $logo;
			$item['tag_id'] = $val['tag_id'];

            $item['shop_id'] = $val['shop_id'];
			$item['shop_url'] = html_entity_decode($val['shop_url']);

            $item['goods']=array();
            if($val['goods_img']) $item['goods'] = explode(',', $val['goods_img']);
			$item['description'] = html_entity_decode($val['description']);
            $data[]=$item;
			$i ++;
		}
		$hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	public function detailAction() {
		$nick = urldecode($this->getInput('nick'));
		
		$topApi  = new Api_Top_Service();
		
		//shop converts
		$shop_convert = $topApi->TaobaokeShopsConvert(array('seller_nicks'=>$nick, 'is_mobile'=>"true"));
		//items
		$items = $topApi->taobaokeItemsRelate(array('relate_type'=>4, 'seller_id'=>$shop_convert['user_id'], 'sort'=>'commissionNum_desc', 'is_mobile'=>"true"));
		
		$data = array();
		foreach ($items as $key=>$value) {
			$data[$key]['url'] = $value['click_url'];
			$data[$key]['img'] = $value['pic_url'].'_160x160.'.end(explode(".",  $value['pic_url']));
			$data[$key]['price'] = $value['promotion_price'];
		}
		
		$this->output(0, '', array('list'=>$data));
	}
}
