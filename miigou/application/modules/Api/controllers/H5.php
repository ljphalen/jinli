<?php
if (!defined('BASE_PATH')) exit('Access Denied!');


/**
 * 
 * @author tiansh
 *
 */
class H5Controller extends Api_BaseController {
	
	public $actions =array(
				'topic' => '/index/topic',
				'tjUrl'=>'/index/tj'
			);
	
	public $perpage = 8;
	
	/**
	 * 查询商品
	 * 
	 */
	public function indexAction() {
		/*$page = intval($this->getInput('page'));
		if ($page < 1) $page = 2;
		
		$kid = $this->getInput("kid");
		$type = Common::getConfig('typeConfig','goods_type');
		
		$params = array();
		$time = Common::getTime();
		if(in_array($kid, array_keys($type))) {
			if($kid == 1 && $page < 7) {
				$params = array('status'=>1, 'start_time'=>array('<', $time), 'end_time'=>array('>', $time));
				
				list(, $goods) = Gou_Service_Goods::getList($page, $this->perpage, $params);
				$num_iids = '';
				foreach ($goods as $key=>$value) {
					$num_iids .= strlen($num_iids) ? ','.$value['num_iid'] : $value['num_iid'];
				}
				$topApi  = new Api_Top_Service();
				$taobaoke_items = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$num_iids, 'is_mobile'=>'true'));
				$items = Common::resetKey($taobaoke_items['taobaoke_items']['taobaoke_item'], 'num_iid');
				
				$webroot = Common::getWebRoot();
				$data = array();
				foreach ($goods as $key=>$value) {
					$data[$key]['title'] = Util_String::substr($value['title'], 0, 22, '', true);
					$data[$key]['img'] = strpos($value['img'], 'http://') === false ? $webroot. '/attachs' .$value['img'] : $value['img'].'_180x180.'.end(explode(".",  $value['img']));
					$data[$key]['link'] = $items[$value['num_iid']]['click_url'];
					$data[$key]['price'] = $items[$value['num_iid']]['promotion_price'];
				}
				
				$this->output(0, '', array('list'=>$data, 'hasnext'=>true, 'curpage'=>$page));
				
			} else {
				$keyword = $type[$kid];
			}
				
		} else {
			$keyword = '情趣用品';
		}
		
		$topApi  = new Api_Top_Service();
		
		$ret = $topApi->findTaobaokes(array('page_no'=>$page, 'page_size'=>$this->perpage, 'keyword'=>$keyword, 'is_mobile'=>"true", 'sort'=>"commissionNum_desc"));
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($ret['taobaoke_items']['taobaoke_item'] as $key=>$value) {
			$data[$key]['title'] = Util_String::substr(preg_replace('/<\/?span([^>]+)?>|\s+/',"",$value['title']), 0, 22, '',true);
			$data[$key]['img'] = $value['pic_url'].'_180x180.jpg';
			$data[$key]['link'] = $value['click_url'];
			$data[$key]['price'] = $value['promotion_price'];
		}
		$hasnext = (ceil((int) $ret['total_results'] / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
		*/
		
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 2;
		
		$kid = $this->getInput("kid");
		$type = Common::getConfig('typeConfig','goods_type');
		$keyword = $type[$kid];
		if(!$keyword) $keyword = '情趣用品';
		
		$topApi  = new Api_Top_Service();
		
		$ret = $topApi->taobaoTbkItemsGet(array('page_no'=>$page, 'page_size'=>$this->perpage, 'keyword'=>$keyword, 'is_mobile'=>"true", 'sort'=>"commissionNum_desc"));
		$goods = $ret['tbk_items']['tbk_item'];
		$total = $ret['total_results'];
		
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$infos = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$data[$key]['title'] = Util_String::substr(preg_replace('/<\/?span([^>]+)?>|\s+/',"",$value['title']), 0, 24, '',true);
			$data[$key]['img'] = $value['pic_url'].'_180x180.jpg';
			$data[$key]['link'] = $infos['click_url'];
			$data[$key]['price'] = $value['price'];
		}
		$hasnext = (ceil((int) $ret['total_results'] / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
	
	/**
	 * 帖子评论接口
	 */
	public function forum_detailAction() {
		$page = intval($this->getInput('page'));
		if ($page < 1) $page = 2;
		$perpage = 10;
		$id = $this->getInput("id");
		
		//forum_reply
		list($total, $forum_reply) = Gou_Service_ForumReply::getList($page, $perpage, array('forum_id'=>$id), array('id'=>'ASC'));
		
		$data = array();
		if($forum_reply) {
			$i = ($page - 1) * $perpage + 1;
			foreach ($forum_reply as $key=>$value) {
				$data[$key]['username'] = '用户'.$value['username'];
				$data[$key]['create_time'] = date('m-d H:i',$value['create_time']);
				$data[$key]['num'] = $i;
				$data[$key]['content'] = html_entity_decode($value['content']);
				$i ++;
			}
		}
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
	}
	
}
