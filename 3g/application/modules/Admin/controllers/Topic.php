<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * class TopicController
 * 专题管理
 */
class TopicController extends Admin_BaseController {

	public $actions = array(
		'listUrl'       => '/Admin/Topic/index',
		'addUrl'        => '/Admin/Topic/add',
		'addPostUrl'    => '/Admin/Topic/add_post',
		'editUrl'       => '/Admin/Topic/edit',
		'editPostUrl'   => '/Admin/Topic/edit_post',
		'deleteUrl'     => '/Admin/Topic/delete',
		'viewUrl'       => '/topic',
		'uploadUrl'     => '/Admin/Topic/upload',
		'uploadPostUrl' => '/Admin/Topic/upload_post',
		'uploadImgUrl'  => '/Admin/Topic/uploadImg',
	);

	public $perpage = 20;

	public $content = array('普通', '广告', '导流');

	public function indexAction() {
		$page    = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$param   = $this->getInput(array('title'));
		$search  = array();
		if ($param['title']) $search['title'] = array('LIKE', $param['title']);

		list($total, $topics) = Gionee_Service_Topic::getList($page, $perpage, $search, array('id' => 'DESC'));
		foreach ($topics as $k => $v) {
			$topics[$k]['url'] = Common::clickUrl($v['id'], 'TOPIC', Common::getCurHost() . '/topic/index?id=' . $v['id']);
		}
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('topics', $topics);
		$this->assign('param', $param);
		$this->assign('colors', Gionee_Service_Topic::$colors);
	}

	public function addAction() {
		$this->assign('colors', Gionee_Service_Topic::$colors);
		$vendors = Gionee_Service_Vendor::getAll();//渠道道
		array_unshift($vendors, array('name' => '普通', 'ch' => 'com'));
		$partners = Gionee_Service_Parter::getAll();
		array_unshift($partners, array('id' => 0, 'name' => '全部'));//合作商家
		$this->assign('channels', json_encode($vendors));
		$this->assign('parnters', json_encode($partners));
		$this->assign('contentType', json_encode($this->content));
		$this->assign('types', Gionee_Service_Topic::$types);
		$this->assign('topicId', 0);
	}

	public function createurlAction() {
		$topics  = Gionee_Service_Topic::getsBy(array('status' => 1), array('id' => 'DESC'));
		$vendors = Gionee_Service_Vendor::getAll();//渠道道
		array_unshift($vendors, array('name' => '普通', 'ch' => 'com'));
		$partners = Gionee_Service_Parter::getAll();
		array_unshift($partners, array('id' => 0, 'name' => '全部'));//合作商家
		$this->assign('vendors', $vendors);
		$this->assign('parnters', $partners);
		$this->assign('topics', $topics);

	}

	//专题页内容统计短链接构造
	public function createUrlPostAction() {
		$postData = $this->getInput(array('contentAttr', 'channel', 'parter', 'content', 'url', 'topic_id'));
		if (!strstr($postData['url'], 'http://')) {
			exit(json_encode(array('key' => 0, 'msg' => 'URL格式不正确')));
		}
		if (!$postData['topic_id']) {
			$this->output('-1', '请选择专题！');
		}
		$columnId = self::getColumnId();
		$pname    = '';
		if ($postData['parter']) {
			$parterInfo = Gionee_Service_Parter::get($postData['parter']);
			$pname      = $parterInfo['name'];
		}
		$data = array(
			'style'     => $postData['contentAttr'],
			'partner'   => $pname,
			'link'      => $postData['url'],
			'title'     => $postData['content'],
			'column_id' => $columnId
		);
		$res  = Gionee_Service_Ng::getIdByAdd($data);
		if (!$res) {
			exit(json_encode(array('key' => '0', 'msg' => '信息添加失败')));
		}
		$shortUrl = Common::clickUrl($res . "&" . $postData['topic_id'], 'TOPIC_CONTENT', $postData['url']);
		//构建渠道商URL
		if ($postData['channel'] != 'com') {
			$shortUrl .= '&ch=' . $postData['channel'];
		}
		echo(json_encode(array('key' => '1', 'msg' => $shortUrl)));
		exit;
	}

	public function editAction() {
		$id             = $this->getInput('id');
		$info           = Gionee_Service_Topic::get(intval($id));
		$content        = json_decode($info['content'], true);
		$content        = Gionee_Service_Topic::getView($content);
		$info['option'] = str_ireplace(",", "\n", $info['option']);
		$this->assign('info', $info);
		$this->assign('content', $content);
		$this->assign('isshow', array('0' => '否', '1' => '是'));
		$this->assign('colors', Gionee_Service_Topic::$colors);

		$vendors = Gionee_Service_Vendor::getAll();//渠道道
		array_unshift($vendors, array('name' => '普通', 'ch' => 'com'));
		$partners = Gionee_Service_Parter::getAll();
		array_unshift($partners, array('id' => 0, 'name' => '全部'));//合作商家
		$this->assign('channels', json_encode($vendors));
		$this->assign('parnters', json_encode($partners));
		$this->assign('contentType', json_encode($this->content));
		$this->assign('types', Gionee_Service_Topic::$types);
		$this->assign('topicId', $info['id']);
	}


	public function edit_postAction() {
		Admin_Service_Access::pass('edit');
		$info = $this->getPost(array(
			'id',
			'issuename',
			'title',
			'color',
			'content',
			'start_time',
			'end_time',
			'status',
			'like_num',
			'option',
			'sort',
			'interact',
			'init_like',
			'feedback_title',
			'type',
			'desc',
			'is_hot',
			'vote_limit'
		));
		
	
		$tmp  = array();
		$a    = explode("\r\n", trim($_POST['option']));
		foreach ($a as $v) {
			!empty($v) && $tmp[] = $v;
		}
		$info['option'] = implode(',', $tmp);

		$info = $this->_cookData($info);
		Admin_Service_Log::op($info);

		$imgInfo = Common::upload('img', 'topic');
		if (!empty($imgInfo['data'])) {
			$info['img'] = $imgInfo['data'];
		}
		$typeimgInfo = Common::upload('typeimg', 'topic');
		if (!empty($typeimgInfo['data'])) {
			$info['typeimg'] = $typeimgInfo['data'];
		}

		if (empty($info['id'])) {
			$ret = Gionee_Service_Topic::add($info);
		} else {
			$ret = Gionee_Service_Topic::update($info, intval($info['id']));
		}
		Gionee_Service_Topic::getInfo($info['id'], true);
		if (!$ret) $this->output(-1, '操作失败');
		$this->output(0, '操作成功.');
	}

	private function _cookData($info) {
		if (!$info['issuename']) $this->output(-1, '请添加专题期号名');
		if (!$info['title']) $this->output(-1, '专题标题不能为空.');
		//	if(!$info['option']) $this->output(-1, '用户反馈不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		if (!$info['end_time']) $this->output(-1, '结束时间不能为空.');
		$info['start_time'] = strtotime($info['start_time']);
		$info['end_time']   = strtotime($info['end_time']);
		if ($info['end_time'] <= $info['start_time']) $this->output(-1, '开始时间不能小于结束时间.');
		if (!$info['content']) $this->output(-1, '请添加专题内容.');
		//if(!$info['feedback_title']) $this->output(-1, '反馈标题不能为空..');

		$info['content'] = Common::jsonEncode($info['content']);
		return $info;
	}


	public function deleteAction() {
		Admin_Service_Access::pass('del');
		$id   = $this->getInput('id');
		$info = Gionee_Service_Topic::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
		Admin_Service_Log::op($id);
		$result = Gionee_Service_Topic::delete($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}

	public function uploadAction() {
		$imgId = $this->getInput('imgId');
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function upload_postAction() {
		$ret   = Common::upload('img', 'topic');
		$imgId = $this->getPost('imgId');
		$this->assign('code', $ret['data']);
		$this->assign('msg', $ret['msg']);
		$this->assign('data', $ret['data']);
		$this->assign('imgId', $imgId);
		$this->getView()->display('common/upload.phtml');
		exit;
	}

	public function uploadImgAction() {
		$ret        = Common::upload('imgFile', 'topic');
		if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
		exit(json_encode(array('error' => 0, 'url' => Common::getImgPath() . $ret['data'])));
	}


	//ajax生成短链接
	public function ajaxCreateShortUrlAction() {
		$postData = $this->getInput(array('topic_id', 'contentType', 'parnter_id', 'channel', 'url', 'title'));
		$topicId  = $postData['topic_id'];
		if (empty($topicId)) {//ID为空时，则认为是添加操作
			$max     = Gionee_Service_Topic::max();
			$topicId = $max + 1;
		}
		$contentAttr = $this->content[$postData['contentType']];//内容属性
		$columnId    = self::getColumnId();
		$parterName  = '';
		if ($postData['parnter_id']) {
			$parterInfo = Gionee_Service_Parter::get($postData['parnter_id']);
			$parterName = $parterInfo['name'];
		}
		$data = array(
			'style'     => $contentAttr,
			'partner'   => $parterName,
			'link'      => $postData['url'],
			'title'     => $postData['title'],
			'column_id' => $columnId
		);
		$res  = Gionee_Service_Ng::getIdByAdd($data);
		if (!$res) {
			exit(json_encode(array('key' => '0', 'msg' => '信息添加失败')));
		}
		$shortUrl = Common::clickUrl($res . "&" . $topicId, 'TOPIC_CONTENT', $postData['url']);
		//构建渠道商URL
		if ($postData['channel'] != 'com') {
			$shortUrl .= '&ch=' . $postData['channel'];
		}


		$this->output('0', '', array('shortUrl' => $shortUrl));
	}

	private function getWebRoot() {
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		if (isset($_SERVER['HTTP_HOST'])) {
			if (isset($_SERVER['SERVER_PROTOCOL'])) {
				$protocol = strtolower(explode('/', $_SERVER['SERVER_PROTOCOL'])[0]);
				$webroot  = "{$protocol}://{$_SERVER['HTTP_HOST']}";
			}
			return $webroot;
		}
	}

	//设置栏目ID
	private static function  getColumnId() {
		$host = Common::getCurHost();
		if (strpos($host, '3gtest')) {
			return '11039';
		}
		return '11017';
	}


}
