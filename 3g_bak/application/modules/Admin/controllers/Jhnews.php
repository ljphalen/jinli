<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 聚合新闻管理
 * @author rainkid
 */
class JhnewsController extends Admin_BaseController {

	public $actions = array(
		'listUrl'           => '/Admin/Jhnews/index',
		'addUrl'            => '/Admin/Jhnews/add',
		'addPostUrl'        => '/Admin/Jhnews/add_post',
		'editUrl'           => '/Admin/Jhnews/edit',
		'editPostUrl'       => '/Admin/Jhnews/edit_post',
		'editStatusPostUrl' => '/Admin/Jhnews/edit_status_post',
		'deleteUrl'         => '/Admin/Jhnews/delete',
	);

	public $appCacheName = 'APPC_Front_News_index';

	public $perpage = 20;
	public $newstype;

	public function init() {
		parent::init();
		$this->ifeng_news  = Common::getConfig('apiConfig', 'jh_ifeng');
		$this->sohu_news   = Common::getConfig('apiConfig', 'jh_sohu');
		$this->gionee_news = Common::getConfig('apiConfig', 'jh_gionee');
		$this->newstype    = $this->ifeng_news + $this->sohu_news + $this->gionee_news;
	}

	public function indexAction() {
		$page = intval($this->getInput('page'));

		$type  = $this->getInput('type_id');
		$param = array();
		if ($type) {
			$param['type_id'] = $type;
		}

		$perpage = $this->perpage;
		list($total, $news) = Gionee_Service_Jhnews::getList($page, $perpage, $param);
		$this->assign('news', $news);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($param) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
		$this->assign('type', $type);
		$this->assign('param', $param);

		$this->assign('newstype', $this->newstype);

	}

	public function addAction() {
		//合作商信息
		$cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
		$this->assign('cooperators', $cooperators);
		$this->assign('newstype', $this->newstype);
	}

	public function add_postAction() {
		$info        = $this->getPost(array(
			'sort',
			'title',
			'color',
			'url',
			'ontime',
			'status',
			'is_ad',
			'istop',
			'start_time',
			'type_id',
			'cp_id',
			'parter_id',
			'bid'
		));
		$info['url'] = Gionee_Service_ParterUrl::getLink($info['parter_id'], $info['cp_id'], $info['bid'], $info['url']);
		if (!$info['type_id']) $this->output(-1, '分类不能为空.');
		if (!$info['title']) $this->output(-1, '标题不能为空.');
		if (!$info['url']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['url'], 'http://') === false || !strpos($info['url'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if (!$info['ontime']) $this->output(-1, '发布时间不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		$ret = Gionee_Service_Jhnews::addNews($info);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function editAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Jhnews::get(intval($id));
		if (empty($info)) $this->output('-1', '内容不存在!');
		$urlInfo = Gionee_Service_ParterUrl::get($info['cp_id']);
		$this->assign('urlInfo', $urlInfo);
		$blist = Gionee_Service_Business::getsBy(array('parter_id' => $urlInfo['pid']));
		$this->assign('blist', $blist);
		$urlList = Gionee_Service_ParterUrl::getsBy(array('bid' => $urlInfo['bid']));
		$this->assign('urlList', $urlList);
		$cooperators = Gionee_Service_Parter::getsBy(array('status' => 1), array('id' => 'DESC'));
		array_unshift($cooperators, array('id' => '0', 'name' => '普通'));
		$this->assign('info', $info);
		$this->assign('cooperators', $cooperators);
		$this->assign('newstype', $this->newstype);
	}

	public function edit_postAction() {
		$info        = $this->getPost(array(
			'id',
			'type_id',
			'sort',
			'title',
			'color',
			'url',
			'ontime',
			'status',
			'is_ad',
			'istop',
			'start_time',
			'cp_id',
			'bid',
			'parter_id'
		));
		$info['url'] = $info['url'] = Gionee_Service_ParterUrl::getLink($info['parter_id'], $info['cp_id'], $info['bid'], $info['url']);
		if (!$info['type_id']) $this->output(-1, '分类不能为空.');
		if (!$info['title']) $this->output(-1, '标题不能为空.');
		if (!$info['url']) $this->output(-1, '链接地址不能为空.');
		if (strpos($info['url'], 'http://') === false || !strpos($info['url'], 'https://') === false) {
			$this->output(-1, '链接地址不规范.');
		}
		if (!$info['ontime']) $this->output(-1, '发布时间不能为空.');
		if (!$info['start_time']) $this->output(-1, '开始时间不能为空.');
		$ret = Gionee_Service_Jhnews::updateNews($info, $info['id']);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	/**
	 * 批量修改状态
	 */
	public function edit_status_postAction() {
		$ids = $this->getPost(array('ids', 'oids'));
		if (!$ids['ids']) $this->output(-1, '请选择要显示的新闻.');
		//设置原选中的状态为0
		Gionee_Service_Jhnews::updateStatusByIds($ids['oids'], 0);

		$ret = Gionee_Service_Jhnews::updateStatusByIds($ids['ids'], 1);
		if (!$ret) $this->output(-1, '操作失败.');
		$this->updataVersion();
		$this->output(0, '操作成功.');
	}

	public function deleteAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_Jhnews::get($id);
		if ($info && $info['id'] == 0) $this->output(-1, '信息不存在无法删除');
		$ret = Gionee_Service_Jhnews::deleteNews($id);
		if (!$ret) $this->output(-1, '操作失败');
		$this->updataVersion();
		$this->output(0, '操作成功');
	}

	/**
	 * update_version
	 */
	private function updataVersion() {
		Gionee_Service_Config::setValue('nav_version', Common::getTime());
	}

}
