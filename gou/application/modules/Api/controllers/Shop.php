<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh
 *
 */
class ShopController extends Api_BaseController {
	
	public $perpage = 10;
	public $actions = array(
				'indexUrl'=>'/api/shop/index',
				'detailUrl'=>'/shop/detail',
				'tjUrl'=>'/index/tj'
	);
	public $cacheKey = 'Front_Shop_index';

    public function tagAction(){
        list(,$shops) = Client_Service_Shops::getsBy(array('channel_id'=>1,'tag_id'=>array('<>','')));
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
        if ($page < 1) $page = 1;
        $pager = ($page - 1) * $this->perpage;

        $uid = $this->getInput('uid');
        $tag_id = $this->getInput('tag_id');

        $webroot = Common::getWebRoot();
        $tjUrl = $webroot.$this->actions['tjUrl'];
        $staticroot = Yaf_Application::app()->getConfig()->staticroot;

        $search = array('status'=>1, 'channel_id'=>1);
        if(!empty($tag_id))$search['tag_id']=array('LIKE',"-$tag_id-");
        //shops
        list($total, $shops) = Client_Service_Shops::getList($page, $this->perpage, $search);

        $data = array();
        $i = 1;

        foreach ($shops as $key=>$val) {
            $data[$key]['id'] = $val['id'];
            $data[$key]['shop_id'] = $val['shop_id'];
            $data[$key]['num'] = ($pager == 0 && $i < 10) ? '0'.$i : $i + $pager;
            $data[$key]['title'] = Util_String::substr($val['shop_title'], 0, 12, '', true);
            $data[$key]['shop_url'] = html_entity_decode($val['shop_url']);
            $data[$key]['goods']=array();
            if($val['goods_img']) $data[$key]['goods'] = explode(',', $val['goods_img']);
            $data[$key]['description'] = html_entity_decode($val['description']);
            if(strpos($val['logo'], 'http://') !== false) {
                $logo = $val['logo'].'_120x120.jpg';
            }else{
                $logo = Common::getAttachPath().$val['logo'];
              }
            $data[$key]['logo'] = $logo;
            $i ++;
        }

        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
    }

	public function detailAction() {
		$nick = urldecode($this->getInput('nick'));
		
		$topApi  = new Api_Top_Service();
		
		//shop converts
		$shop_convert = $topApi->TaobaokeMobileShopsConvert(array('seller_nicks'=>$nick, 'is_mobile'=>"true"));
		//items
		$items = $topApi->taobaokeMobileItemsRelate(array('relate_type'=>4, 'seller_id'=>$shop_convert['user_id'], 'sort'=>'commissionNum_desc', 'is_mobile'=>"true"));
		
		$data = array();
		foreach ($items as $key=>$value) {
			$data[$key]['url'] = $value['click_url'];
			$data[$key]['img'] = $value['pic_url'].'_160x160.'.end(explode(".",  $value['pic_url']));
			$data[$key]['price'] = $value['promotion_price'];
		}
		
		$this->output(0, '', array('list'=>$data));
	}
}
