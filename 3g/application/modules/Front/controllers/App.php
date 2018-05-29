<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 轻应用
 * @author tiansh
 *
 */
class AppController extends Front_BaseController {

	public $actions = array(
		'indexUrl'         => '/App/index',
		'recommendMoreUrl' => '/App/recommend_more',
		'newMoreUrl'       => '/App/new_more',
		'listMoreUrl'      => '/App/list_more',
		'listUrl'          => '/App/list',
		'rankUrl'          => '/App/rank'
	);
	public $perpage = 8;

	/**
	 * 应用首页
	 */
	public function indexAction() {
		$page = 1;
		//推荐
		list($total_rec, $recommend) = Gionee_Service_App::getList($page, $this->perpage, array('is_recommend' => 1, 'status' => 1), array('sort' => 'DESC', 'id' => 'DESC'));

		//分类
		list(, $app_type) = Gionee_Service_AppType::getAllAppType();
		$app_type = Common::resetKey($app_type, 'id');

		//排行
		list(, $rank) = Gionee_Service_App::getList($page, 10, array('status' => 1), array('hits' => 'DESC', 'id' => 'DESC'));

		//新品
		list($total_new, $new) = Gionee_Service_App::getList($page, $this->perpage, array('status' => 1), array('id' => 'DESC'));

		$rec_hasnext    = (ceil((int)$total_rec / $this->perpage) - ($page)) > 0 ? true : false;
		$new_hasnext    = (ceil((int)$total_new / $this->perpage) - ($page)) > 0 ? true : false;
		$webroot        = Common::getCurHost();
		$recommend_data = array();
		foreach ($recommend as $key => $value) {
			$recommend_data[$key]['id']      = $value['id'];
			$recommend_data[$key]['link']    = $value['link'];
			$recommend_data[$key]['img']     = Common::getImgPath() . $value['img'];
			$recommend_data[$key]['title']   = $value['name'];
			$recommend_data[$key]['star']    = $value['star'];
			$recommend_data[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$recommend_data[$key]['appType'] = $app_type[$value['type_id']]['name'];
			$recommend_data[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}

		$rank_data = array();
		foreach ($rank as $key => $value) {
			$rank_data[$key]['id']      = $value['id'];
			$rank_data[$key]['link']    = $value['link'];
			$rank_data[$key]['img']     = Common::getImgPath() . $value['img'];
			$rank_data[$key]['title']   = $value['name'];
			$rank_data[$key]['star']    = $value['star'];
			$rank_data[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$rank_data[$key]['appType'] = $app_type[$value['type_id']]['name'];
			$rank_data[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}

		$new_data = array();
		foreach ($new as $key => $value) {
			$new_data[$key]['id']      = $value['id'];
			$new_data[$key]['link']    = $value['link'];
			$new_data[$key]['img']     = Common::getImgPath() . $value['img'];
			$new_data[$key]['title']   = $value['name'];
			$new_data[$key]['star']    = $value['star'];
			$new_data[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$new_data[$key]['appType'] = $app_type[$value['type_id']]['name'];
			$new_data[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}

		$rec_hasnext = (ceil((int)$total_rec / $this->perpage) - ($page)) > 0 ? true : false;
		$new_hasnext = (ceil((int)$total_new / $this->perpage) - ($page)) > 0 ? true : false;

		$json_data = array('recommend' => array('data' => array('list' => $recommend_data, 'hasnext' => $rec_hasnext, 'curpage' => 1)), 'rank' => array('data' => array('list' => $rank_data, 'hasnext' => false, 'curpage' => 1)), 'news' => array('data' => array('list' => $new_data, 'hasnext' => $new_hasnext, 'curpage' => 1)));
		$this->assign('json_data', json_encode($json_data, true));
		$this->assign('app_type', Common::resetKey($app_type, 'id'));
		$this->assign('pageTitle', '在线应用');
	}

	/**
	 * 加载更多
	 */
	public function recommend_moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 2) $page = 2;

		list($total, $recommend) = Gionee_Service_App::getList($page, $this->perpage, array('is_recommend' => 1, 'status' => 1), array('sort' => 'DESC', 'id' => 'DESC'));

		//分类
		list(, $app_type) = Gionee_Service_AppType::getAllAppType();
		$app_type   = Common::resetKey($app_type, 'id');
		$webroot    = Common::getCurHost();
		$list       = array();
		foreach ($recommend as $key => $value) {
			$list[$key]['id']      = $value['id'];
			$list[$key]['link']    = $value['link'];
			$list[$key]['img']     = Common::getImgPath() . $value['img'];
			$list[$key]['title']   = $value['name'];
			$list[$key]['star']    = $value['star'];
			$list[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$list[$key]['appType'] = $app_type[$value['type_id']]['name'];
			$list[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}
		$hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'curpage' => $page));
	}

	/**
	 * 排行
	 */
	public function rankAction() {

		list(, $rank) = Gionee_Service_App::getList(1, $this->perpage, array('status' => 1), array('sort' => 'DESC', 'id' => 'DESC'));

		//分类
		list(, $app_type) = Gionee_Service_AppType::getAllAppType();
		$app_type   = Common::resetKey($app_type, 'id');
		$webroot    = Common::getCurHost();
		$list       = array();
		foreach ($rank as $key => $value) {
			$list[$key]['id']      = $value['id'];
			$list[$key]['link']    = $value['link'];
			$list[$key]['img']     = Common::getImgPath() . $value['img'];
			$list[$key]['title']   = $value['name'];
			$list[$key]['star']    = $value['star'];
			$list[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$list[$key]['appType'] = $app_type[$value['type_id']]['name'];
			$list[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}
		$this->output(0, '', array('list' => $list));
	}

	/**
	 * 加载更多新品
	 */
	public function news_moreAction() {
		$page = intval($this->getInput('page'));
		if ($page < 2) $page = 2;

		list($total, $recommend) = Gionee_Service_App::getList($page, $this->perpage, array('status' => 1), array('id' => 'DESC'));

		//分类
		list(, $app_type) = Gionee_Service_AppType::getAllAppType();
		$app_type   = Common::resetKey($app_type, 'id');
		$webroot    = Common::getCurHost();
		$list       = array();
		foreach ($recommend as $key => $value) {
			$list[$key]['id']      = $value['id'];
			$list[$key]['link']    = $value['link'];
			$list[$key]['img']     = Common::getImgPath() . $value['img'];
			$list[$key]['title']   = $value['name'];
			$list[$key]['star']    = $value['star'];
			$list[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$list[$key]['appType'] = $app_type[$value['type_id']]['name'];
			$list[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}
		$hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list' => $list, 'hasnext' => $hasnext, 'curpage' => $page));
	}

	/**
	 * 列表页
	 */
	public function listAction() {
		$page    = 1;
		$type_id = intval($this->getInput('type_id'));

		$type = Gionee_Service_AppType::getAppType($type_id);

		$webroot = Common::getCurHost();

		if (!$type_id || !$type || $type['id'] != $type_id) Common::redirect($webroot . $this->actions['indexUrl']);

		list($total, $app) = Gionee_Service_App::getList($page, $this->perpage, array('type_id' => $type_id, 'status' => 1), array('sort' => 'DESC', 'id' => 'DESC'));
		$list       = array();
		foreach ($app as $key => $value) {
			$list[$key]['id']      = $value['id'];
			$list[$key]['link']    = $value['link'];
			$list[$key]['img']     = Common::getImgPath() . $value['img'];
			$list[$key]['title']   = $value['name'];
			$list[$key]['star']    = $value['star'];
			$list[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$list[$key]['appType'] = $type['name'];
			$list[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}
		$hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;

		$this->assign('json_data', json_encode(array('app-reco' => array('data' => $list, 'hasnext' => $hasnext, 'curpage' => 1))));
		$this->assign('pageTitle', $type['name'] . ' - 在线应用');
		$this->assign('type', $type);
	}

	/**
	 * 更多
	 */
	public function list_moreAction() {
		$page    = intval($this->getInput('page'));
		$type_id = intval($this->getInput('type_id'));

		$type      = Gionee_Service_AppType::getAppType($type_id);
		$type_name = $type['name'];
		list($total, $apps) = Gionee_Service_App::getList($page, $this->perpage, array('type_id' => $type_id, 'status' => 1), array('sort' => 'DESC', 'id' => 'DESC'));

		//分类
		list(, $app_type) = Gionee_Service_AppType::getAllAppType();
		$app_type   = Common::resetKey($app_type, 'id');
		$webroot    = Common::getCurHost();
		$list       = array();
		foreach ($apps as $key => $value) {
			$list[$key]['id']      = $value['id'];
			$list[$key]['link']    = $value['link'];
			$list[$key]['img']     = Common::getImgPath() . $value['img'];
			$list[$key]['title']   = $value['name'];
			$list[$key]['star']    = $value['star'];
			$list[$key]['appInfo'] = Util_String::substr($value['descrip'], 0, 10);
			$list[$key]['appType'] = $app_type[$value['type_id']]['name'];
			$list[$key]['addUrl']  = $webroot . '/api/app/index?id=' . $value['id'];
		}
		$hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
		$this->output(0, '', array('list' => $list, 'cateName' => $type_name, 'hasnext' => $hasnext, 'curpage' => $page));
	}
}
