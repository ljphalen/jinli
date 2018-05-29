<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ForumController extends Front_BaseController {
	
	public function detailAction() {
		
		$id = $this->getInput("id");
		$forum = Gou_Service_Forum::getForum($id);
		
		if(!$forum) exit('帖子不存在');
		
		//forum_img 
		$forum_imgs = Gou_Service_ForumImg::getImagesByForumId($forum['id']);
		
		//forum_reply
		list($reply_count,$forum_reply) = Gou_Service_ForumReply::getList(1, 10, array('forum_id'=>$forum['id']), array('id'=>'ASC'));
		
		if($reply_count != $forum['reply_count']) Gou_Service_Forum::updateForum(array('reply_count'=>$reply_count), $forum['id']);
		
		//goods
		$time = Common::getTime();
		$params = array('status'=>1, 'start_time'=>array('<', $time), 'end_time'=>array('>', $time), 'category_id'=>8);
		list(, $goods) = Gou_Service_Goods::getList(1, 4, $params);
		
		/* $num_iids = '';
		foreach ($goods as $key=>$value) {
			$num_iids .= strlen($num_iids) ? ','.$value['num_iid'] : $value['num_iid'];
		}
		
		$topApi  = new Api_Top_Service();
		$taobaoke_items = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$num_iids));
		
		$items = Common::resetKey($taobaoke_items, 'num_iid');
		 */
		$topApi  = new Api_Top_Service();
		$webroot = Common::getWebRoot();
		$data = array();
		foreach ($goods as $key=>$value) {
			$infos = $topApi->taobaokeMobileItemsConvert(array('num_iids'=>$value['num_iid']));
			$data[$key]['title'] = $value['title'];
			$data[$key]['img'] = strpos($value['img'], 'http://') === false ? $webroot. '/attachs' .$value['img'] : $value['img'].'_180x180.'.end(explode(".",  $value['img']));
			$data[$key]['link'] = $infos['click_url'];
			$data[$key]['price'] = $value['price'];
		}
		
		$this->assign('goods_data', $data);		
		$this->assign('forum_img', $forum_imgs);
		$this->assign('forum', $forum);
		$this->assign('forum_reply', $forum_reply);
		$this->assign('title', '帖子详情');
	}
}
