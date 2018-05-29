<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 资源下载
 * @author rainkid
 */
class ResourceController extends Front_BaseController {

	public $actions = array(
		'indexUrl'  => '/resource/index',
		'detailUrl' => '/resource/detail',
		'morelUrl'  => '/resource/more'
	);

	public $perpage = 10;
	public $current_model_id;

	public function init() {
		parent::init();
		//机型
		$current_model_name     = Yaf_Registry::get("current_model_name");
		$model                  = Gionee_Service_Models::getModelsByName($current_model_name);
		$this->current_model_id = $model ? $model['id'] : 0;
	}

	/**
	 * 首页
	 */
	public function indexAction() {
		$t_bi    = $this->getSource();
		$page    = 1;
		$orderBy = array('sort' => 'DESC', 'id' => 'ASC');

		$rcKey = 'RESOURCE_ASSIGN:' . $this->current_model_id . ':' . $page;
		$ret   = Common::getCache()->get($rcKey);
		if (empty($ret)) {
			list($total, $resourceassign) = Gionee_Service_ResourceAssign::getList($page, $this->perpage, array('model_id' => $this->current_model_id), $orderBy);
			$rids = array();
			foreach ($resourceassign as $key => $value) {
				$rids[] = $value['resource_id'];
			}
			//资源
			if ($rids) {
				$resource = Gionee_Service_Resource::getListByIds($rids);
				$resource = Common::resetKey($resource, 'id');
			}
			$ret = array($resourceassign, $resource);
			Common::getCache()->set($rcKey, $ret, 600);
		}

		list($resourceassign, $resource) = $ret;

		$webroot    = Common::getCurHost();
		$list       = array();
		foreach ($resourceassign as $key => $value) {
			if ($resource[$value['resource_id']]['status'] == 1) {
				$list[$key]['href']    = $webroot . "/resource/detail?rid=" . $value['resource_id'] . "&t_bi=" . $t_bi;
				$list[$key]['img']     = Common::getImgPath() . $resource[$value['resource_id']]['icon'];
				$list[$key]['title']   = $resource[$value['resource_id']]['name'];
				$list[$key]['star']    = $resource[$value['resource_id']]['star'];
				$list[$key]['summary'] = Util_String::substr($resource[$value['resource_id']]['summary'], 0, 12);
				//$list[$key]['description'] = Util_String::substr($description, 0, 30);
				$value['down_url']      = html_entity_decode($resource[$value['resource_id']]['down_url']);
				$list[$key]['down_url'] = Common::clickUrl($value['id'], 'DOWN', $value['down_url'], $t_bi);
			}
		}

		$this->assign('resourceassign', $list);
		$this->assign('resource', $resource);
		$this->assign('pageTitle', '资源下载');
		$this->assign('nav', 'resource');

		//轮播广告
		$rcKey = 'RESOURCE_INDEX_AD';
		$ads   = Common::getCache()->get($rcKey);
		if ($ads === false) {
			$ads = Gionee_Service_Ad::getCanUseAds(1, 1, array('ad_type' => 2));
			Common::getCache()->set($rcKey, $ads, 600);
		}


		$hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? 'true' : 'false';
		$this->assign('hasnext', $hasnext);
		$this->assign('ads', $ads[0]);
	}

	/**
	 * 加载更多
	 */
	public function moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 2) $page = 2;
		$t_bi    = $this->getSource();
		$orderBy = array('sort' => 'DESC', 'id' => 'ASC');

		$rcKey = 'RESOURCE_ASSIGN:' . $this->current_model_id . ':' . $page;
		$ret   = Common::getCache()->get($rcKey);
		if (empty($ret)) {
			list($total, $resourceassign) = Gionee_Service_ResourceAssign::getList($page, $this->perpage, array('model_id' => $this->current_model_id), $orderBy);

			$rids = array();
			foreach ($resourceassign as $key => $value) {
				$rids[] = $value['resource_id'];
			}
			//资源
			$resource = Gionee_Service_Resource::getListByIds($rids);
			$resource = Common::resetKey($resource, 'id');

			$ret = array($resourceassign, $resource);
			Common::getCache()->set($rcKey, $ret, 600);
		}

		list($resourceassign, $resource) = $ret;

		$webroot    = Common::getCurHost();
		$list       = array();
		foreach ($resourceassign as $key => $value) {
			if ($resource[$value['resource_id']]['status'] == 1) {
				$list[$key]['href']    = $webroot . "/resource/detail?rid=" . $value['resource_id'] . "&t_bi=" . $t_bi;
				$list[$key]['img']     = Common::getImgPath() . $resource[$value['resource_id']]['icon'];
				$list[$key]['title']   = $resource[$value['resource_id']]['name'];
				$list[$key]['star']    = $resource[$value['resource_id']]['star'];
				$list[$key]['summary'] = Util_String::substr($resource[$value['resource_id']]['summary'], 0, 12);
				//$list[$key]['description'] = Util_String::substr($description, 0, 30);
				$value['down_url']      = html_entity_decode($resource[$value['resource_id']]['down_url']);
				$list[$key]['down_url'] = Common::clickUrl($value['id'], 'DOWN', $value['down_url'], $t_bi);
			}
		}
		$hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'curpage' => $page));
	}

	/**
	 * 详细页
	 */
	public function detailAction() {
		$rid = intval($this->getInput('rid'));

		$rcKey = 'RESOURCE_DETAIL:' . $rid;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret === false) {
			$resource = Gionee_Service_Resource::getResource($rid);
			list(, $images) = Gionee_Service_ResourceImg::getList(1, 10, array('rid' => $rid));
			$ret = array($resource, $images);
			Common::getCache()->set($rcKey, $ret, 600);
		}

		list($resource, $images) = $ret;

		$webroot = Common::getCurHost();
		if (!$rid || !$resource || $resource['id'] != $rid || $resource['status'] == 0) {
			Common::redirect($webroot . $this->actions['indexUrl']);
		}

		$model = $this->getInput('model'); //fanfan version
		$ver   = $this->getInput('ver'); //fanfan version
		$from  = $this->getInput('from'); //fanfan version

		$extraUrl = '';
		if (!empty($ver)) {
			$extraUrl .= '&ver=' . $ver;
		}
		if (!empty($model)) {
			$extraUrl .= '&model=' . $model;
		}
		if (!empty($from)) {
			$extraUrl .= '&from=' . $from;
		}

		$this->assign('extraUrl', $extraUrl);

		$this->assign('info', $resource);
		$this->assign('images', $images);
		$this->assign('pageTitle', '资源下载');
	}
}
