<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * @author tiansh 客户端2.0接口
 *
 */
class ClientController extends Api_BaseController {
	
	public $default_uid = '10000';
	
	/**
	 * 关键字
	 */
	public function keywordsAction() {
		list(,$list) =  Gou_Service_Keywords::getList(1, 10, array('status'=>1));
		$data = array();
		foreach ($list as $key=>$value) {
			$data[] = $value['keyword'];
		}
		
		$keyword = Gou_Service_Config::getValue('search_keyword');
		
		$this->clientOutput(0, '',  array('keywords'=>$data, 'keyword'=>$keyword, 'taobao_search_url'=>'http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q='));
	}
	
	/**
	 * 搜索
	 */
	public function searchAction() {
		$keyword = trim($this->getPost('keyword'));
		
		if($keyword) {
			$info = array(
					'keyword'=>$keyword,
					'keyword_md5'=>md5($keyword),
					'create_time'=>Common::getTime(),
					'dateline'=>date('Y-m-d', Common::getTime())
			);
			Gou_Service_KeywordsLog::addKeywordsLog($info);
		}
		//$url = sprintf('http://r.m.taobao.com/s?p=mm_31749056_3153305_10442728&q=%s', urlencode($keyword));
		$this->clientOutput(0, '',  'success');
	}
	
	/**
	 * 发帖
	 */
	public function forum_postAction() {
		$category_id = $this->getPost('category_id');
		$title = $this->getPost('title');
		$content = $this->getPost('content');
		$sign = $this->getPost('sign');
		$user_mac = $this->getPost('user_mac');
		$client_version = $this->getPost('client_version');
		
		if(!$sign) $this->clientOutput(101, '非法请求');
		if(!$title) $this->clientOutput(102, '请填写帖子标题');
		if(!$content) $this->clientOutput(103, '请填写帖子内容');
		if(!$category_id) $this->clientOutput(104, '请选择帖子分类');
		if(!$this->chkSign($sign, $user_mac)) $this->clientOutput(105, '非法请求');
		if(!$user_mac) $this->clientOutput(106, '非法请求');
		
		$user = Gou_Service_ForumUser::getBy(array('md5_mac'=>md5($user_mac)));
		if(!$user) {
			$user_data = array(
				'user_mac'=>$user_mac,
				'md5_mac'=>md5($user_mac),
			);
			$uid = Gou_Service_ForumUser::addForumUser($user_data);
			if(!$uid) $this->clientOutput(107, '发帖失败');
			$user = Gou_Service_ForumUser::getForumUser($uid);
		}
		
		$data = array(
				'title'=>$title,
				'user_id'=>$user['id'],
				'user_name'=>$this->default_uid + $user['id'],
				'content'=>$content,
				'category_id'=>$category_id,
				'create_time'=>Common::getTime(),
		);
		$forum_id = Gou_Service_Forum::addForum($data);
		if(!$forum_id) $this->clientOutput(107, '发帖失败');
		$this->clientOutput(0, '', array('forum_id'=>$forum_id));
	}
	
	/**
	 * 发帖
	 */
	public function forum_img_postAction() {
		$forum_id = $this->getPost('forum_id');
// 		$img = $this->getPost('img');
		$sort = $this->getPost('sort');
		$sign = $this->getPost('sign');
		$user_mac = $this->getPost('user_mac');
	
		if(!$sign) $this->clientOutput(101, '非法请求');
// 		if(!$img) $this->clientOutput(102, '请上传图片');
		if(!$this->chkSign($sign, $user_mac)) $this->clientOutput(105, '非法请求');
	
		$user = Gou_Service_ForumUser::getBy(array('md5_mac'=>md5($user_mac)));
		if(!$user) $this->clientOutput(107, '发帖失败');
		
		$ret = Common::upload("img", 'forum');
		if ($ret['code'] != 0) $this->clientOutput(102, $ret['msg']);
		$img_url = $ret['data'];
		
		$data = array(
				'forum_id'=>$forum_id,
				'img'=>$img_url,
				'sort'=>$sort,
		);
		Common::log($data, 'test.lgo');
		$forum_id = Gou_Service_ForumImg::addForumImg($data);
		if(!$forum_id) $this->clientOutput(107, '发帖失败');
		$this->clientOutput(0, '', array('forum_id'=>$forum_id));
	}
	
	/**
	 * 回帖
	 */
	public function forum_replyAction() {
		$forum_id = $this->getPost('forum_id');
		$content = $this->getPost('content');
		$sign = $this->getPost('sign');
		$user_mac = $this->getPost('user_mac');
		$client_version = $this->getPost('client_version');
		
		if(!$sign) $this->clientOutput(101, '非法请求');
		if(!$forum_id) $this->clientOutput(101, '非法请求');
		if(!$content) $this->clientOutput(103, '请填写帖子内容');
		if(!$this->chkSign($sign, $user_mac)) $this->clientOutput(105, '非法请求');
		if(!$user_mac) $this->clientOutput(106, '非法请求');
		
		$user = Gou_Service_ForumUser::getBy(array('md5_mac'=>md5($user_mac)));
		if(!$user) {
			$user_data = array(
					'user_mac'=>$user_mac,
					'md5_mac'=>md5($user_mac),
			);
			$uid = Gou_Service_ForumUser::addForumUser($user_data);
			if(!$uid) $this->clientOutput(107, '发帖失败');
			$user = Gou_Service_ForumUser::getForumUser($uid);
		}
		
		$forum = Gou_Service_Forum::getForum($forum_id);
		if(!$forum) $this->clientOutput(108, '帖子不存在');
		
		//过虑关键字
		$keywords_file = BASE_PATH .'docs/keywords.txt';
		$fil = new Util_Fillter($keywords_file);
		$content = $fil->fill($content);
		
		$data = array(
				'forum_id'=>$forum_id,
				'user_id'=>$user['id'],
				'username'=>$this->default_uid + $user['id'],
				'content'=>$content,
				'create_time'=>Common::getTime(),
		);
		$ret = Gou_Service_ForumReply::addForumReply($data);
		
		Gou_Service_Forum::updateForum(array('reply_count'=>$forum['reply_count'] + 1), $forum['id']);
		
		if(!$ret) $this->clientOutput(107, '发帖失败');
		$this->clientOutput(0, '回复成功');
	}
	
	
	/**
	 * 帖子列表
	 */
	public function forum_listAction() {
		$category_id = intval($this->getInput('category_id'));
		$sign = $this->getInput('sign');
		$client_version = $this->getInput('client_version');
		$page = $this->getInput('page');
		$perpage = $this->getInput('perpage');
		
		if ($page < 1) $page = 1;
		if(!$perpage) $perpage = 10;
		
		$categorys = Common::getConfig('typeConfig','forum_type');
		
		$search = array();
		$search['status'] = 2;
		if($category_id) $search['category_id'] = $category_id;
		list($total, $list) = Gou_Service_Forum::getList($page, $perpage, $search, array('is_top'=>'DESC', 'id'=>'DESC'));
		
		$data = array();
		if($list) {
			$list_forums = Common::resetKey($list, 'id');
			$ids = array_keys($list_forums);
			
			$forum_imgs = Gou_Service_ForumImg::getImagesByForumIds($ids);
			$forum_imgs = Common::resetKey($forum_imgs, 'forum_id');
			
			$webroot = Common::getWebRoot();
			foreach ($list as $key=>$val){
				$data[$key]['id'] = $val['id'];
				$data[$key]['title'] = $val['title'];
				$data[$key]['category'] = $categorys[$val['category_id']];
				$data[$key]['username'] = ($val['user_id'] == 0 && $val['category_id'] == 1) ? $val['user_name'] : '用户'.$val['user_name'];
				$data[$key]['reply_count'] = $val['reply_count'];
				$data[$key]['is_top'] = $val['is_top'] == 1 ? true : false;
				$data[$key]['has_img'] =  $forum_imgs[$val['id']] ? true : false;
				$data[$key]['create_time'] = date('m-d H:i', $val['create_time']);
				$data[$key]['detail_url'] =  $webroot.'/forum/detail?id='.$val['id'];
			}
		}
		
		$hasnext = (ceil((int) $total / $perpage) - ($page)) > 0 ? true : false;
		$this->clientOutput(0, '', array('list'=>$data, 'hasnext'=>$hasnext, 'curpage'=>$page));
		$this->clientOutput(0, 'success', array('data'=>$data));
	}
	
	
	/**
	 * 验证sign
	 * @param unknown_type $string
	 */
	private function chkSign($sign, $string) {
		$config = Common::getConfig('siteConfig');
		return $sign === md5($string.$config['secretKey']);
	}
	
	/**
	 * 客户端启动统计 
	 */
	
	public function clientTjAction(){
		$this->clientOutput(0, '', array('codUrl'=>html_entity_decode(Gou_Service_Config::getValue('cod_url'))));
	}
	
	/**
	 * 货到付款url
	 */
	
	public function codUrlAction(){
		$this->clientOutput(0, '', array('codUrl'=>html_entity_decode(Gou_Service_Config::getValue('cod_url'))));
	}
}
