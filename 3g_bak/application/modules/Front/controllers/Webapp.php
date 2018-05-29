<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 轻应用
 * @author tiger
 */
class WebappController extends Front_BaseController {

	public $actions = array(
		'indexUrl' => '/Webapp/index',
		'listUrl'  => '/Webapp/list',
	);

	/*
	 * 首页
	 */
	public function indexAction() {
		$this->assign('cache', Gionee_Service_Config::getValue('gionee_cache'));
	}

	/*
	 * 搜索热词接口
	*/
	public function keywordsAction() {
		//热词
		$keywords = Gionee_Service_Ad::getCanUseAds(1, 20, array('ad_type' => 10));
		foreach ($keywords as $key => $value) {
			$data1[$key] = $value['title'];
		}
		if (!$data1) $data1 = '';
		//默认关键字
		$keyword = Gionee_Service_Ad::getCanUseAds(1, 10, array('ad_type' => 11));
		foreach ($keyword as $key => $value) {
			$data2[$key] = $value['title'];
		}
		if (!$data2) $data2 = '';
		$json_data = array('hotwords' => $data1, 'default' => $data2);
		$this->output(0, '', $json_data);
	}

	/*
	 * 搜索接口
	 */
	public function searchAction() {
		$like       = strtolower(trim($this->getInput('like')));
		$page       = 1;
		$webroot    = Common::getCurHost();
		$t_bi       = $this->getSource();
		$configs    = Gionee_Service_Config::getAllConfig();
		list(, $app_type) = Gionee_Service_WebAppType::getWebAppType();
		$app_type = Common::resetKey($app_type, 'id');
		$search   = array('status' => 1);
		if ($like) $search['tag'] = array('LIKE', $like);
		$num     = Gionee_Service_WebApp::count($search);
		$orderBy = array('sort' => 'DESC', 'id' => 'DESC');
		if ($num <= 10 && $num > 0) {
			list($total, $list) = Gionee_Service_WebApp::getList($page, $num, $search, $orderBy);
			foreach ($list as $key => $value) {
				$search_data[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext = '';
			list(, $usercomm) = Gionee_Service_WebApp::getList($page, $configs['user_num'], array('type_id' => $list[0]['type_id']), $orderBy);
			foreach ($usercomm as $key => $value) {
				$usercomm_data[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
		} elseif ($num > 10) {
			list($total, $list) = Gionee_Service_WebApp::getList($page, $configs['search_num'], $search, $orderBy);
			foreach ($list as $key => $value) {
				$search_data[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext = (ceil((int)$total / $configs['search_num']) - ($page)) > 0 ? true : false;
		}

		$json_data = array(
			'search'   => $search_data,
			'total'    => $total,
			'usercomm' => $usercomm_data,
			'hasnext'  => $hasnext
		);
		$this->output(0, '', $json_data);
	}

	/*
	 * 首页book接口
	 */
	public function bookAction() {
		$page       = 1;
		$webroot    = Common::getCurHost();
		$t_bi       = $this->getSource();
		$configs    = Gionee_Service_Config::getAllConfig();
		list(, $app_type) = Gionee_Service_WebAppType::getWebAppType();
		$app_type = Common::resetKey($app_type, 'id');
		$ad_data  = $must_data = $recommend_data = array();
		//广告
		$ad = Gionee_Service_Ad::getCanUseAds(1, 5, array('ad_type' => 9));
		foreach ($ad as $key => $value) {
			$ad_data[$key] = array(
				'id'   => $value['id'],
				'link' => Common::clickUrl($value['id'], 'APP_AD', $value['link'], $t_bi),
				'img'  => Common::getImgPath() . $value['img'],
			);
		}

		//必备
		$where   = array('is_must' => 1, 'status' => 1);
		$orderBy = array('sort' => 'DESC', 'id' => 'DESC');
		list($total_rec, $must) = Gionee_Service_WebApp::getList($page, $configs['must_num'], $where, $orderBy);
		foreach ($must as $key => $value) {
			$must_data[$key] = array(
				'id'      => $value['id'],
				'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
				'img'     => Common::getImgPath() . $value['img'],
				'name'    => $value['name'],
				'star'    => $value['star'],
				'descrip' => Util_String::substr($value['descrip'], 0, 12),
				'is_new'  => $value['is_new'],
				'appType' => $app_type[$value['type_id']]['name'],
				'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
			);
		}

		//推荐
		$where = array('is_recommend' => 1, 'status' => 1);
		list($total, $recommend) = Gionee_Service_WebApp::getList($page, $configs['recommend_num'], $where, $orderBy);
		foreach ($recommend as $key => $value) {
			$recommend_data[$key] = array(
				'id'      => $value['id'],
				'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
				'img'     => Common::getImgPath() . $value['img'],
				'name'    => $value['name'],
				'star'    => $value['star'],
				'descrip' => Util_String::substr($value['descrip'], 0, 12),
				'is_new'  => $value['is_new'],
				'appType' => $app_type[$value['type_id']]['name'],
				'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
			);
		}
		$hasnext    = (ceil((int)$total / $configs['recommend_num']) - ($page)) > 0 ? true : false;
		$json_data1 = array(
			'ads'       => $ad_data,
			'must'      => $must_data,
			'recommend' => $recommend_data,
			'hasnext'   => $hasnext
		);

		//排行
		$order = array('hits' => 'DESC', 'id' => 'DESC');
		list($total, $rank) = Gionee_Service_WebApp::getList($page, $configs['ranking_num'], array('status' => 1), $order);
		$rank_data = array();
		foreach ($rank as $key => $value) {
			$rank_data[$key] = array(
				'id'      => $value['id'],
				'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
				'img'     => Common::getImgPath() . $value['img'],
				'name'    => $value['name'],
				'star'    => $value['star'],
				'descrip' => Util_String::substr($value['descrip'], 0, 12),
				'is_new'  => $value['is_new'],
				'appType' => $app_type[$value['type_id']]['name'],
				'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
			);
		}
		$hasnext    = (ceil((int)$total / $configs['ranking_num']) - ($page)) > 0 ? true : false;
		$json_data2 = array('rank' => $rank_data, 'hasnext' => $hasnext);

		//分类
		$type_data = array();
		foreach ($app_type as $key => $value) {
			if ($value['name'] == '专题') {
				$link = '~loop:svt=topicList';
			} else {
				$link = '~loop:svt=cdetail/id/' . $value['id'];
			}
			$type_data[] = array(
				'id'      => $value['id'],
				'name'    => $value['name'],
				'descrip' => Util_String::substr($value['descrip'], 0, 12),
				'img'     => Common::getImgPath() . $value['img'],
				'link'    => $link,
			);
		}
		$json_data3 = array('category' => $type_data);

		//新品
		$order = array('is_new' => 'DESC', 'id' => 'DESC');
		list($total, $new) = Gionee_Service_WebApp::getList($page, $configs['new_num'], array('status' => 1), $order);
		$new_data = array();
		foreach ($new as $key => $value) {
			$new_data[$key] = array(
				'id'      => $value['id'],
				'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
				'img'     => Common::getImgPath() . $value['img'],
				'name'    => $value['name'],
				'star'    => $value['star'],
				'descrip' => Util_String::substr($value['descrip'], 0, 12),
				'is_new'  => $value['is_new'],
				'appType' => $app_type[$value['type_id']]['name'],
				'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
			);

			$hasnext    = (ceil((int)$total / $configs['new_num']) - ($page)) > 0 ? true : false;
			$json_data4 = array('news' => $new_data, 'hasnext' => $hasnext);
		}

		$json_data = array('t1' => $json_data1, 't2' => $json_data2, 't3' => $json_data3, 't4' => $json_data4);
		$this->output(0, '', $json_data);
	}

	/*
	 * 分类应用列表接口
	 */
	public function typelistAction() {
		$page       = 1;
		$type_id    = intval($this->getInput('type_id'));
		$configs    = Gionee_Service_Config::getAllConfig();
		$webroot    = Common::getCurHost();
		$t_bi       = $this->getSource();
		$type       = Gionee_Service_WebAppType::getAppType($type_id);
		$where      = array('type_id' => $type_id, 'status' => 1);
		$order      = array('sort' => 'DESC', 'id' => 'DESC');
		list($total, $app) = Gionee_Service_WebApp::getList($page, $configs['type_num'], $where, $order);
		$list = array();
		foreach ($app as $key => $value) {
			$list[$key] = array(
				'id'      => $value['id'],
				'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
				'img'     => Common::getImgPath() . $value['img'],
				'name'    => $value['name'],
				'star'    => $value['star'],
				'descrip' => Util_String::substr($value['descrip'], 0, 12),
				'is_new'  => $value['is_new'],
				'appType' => $type['name'],
				'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
			);
		}
		$hasnext   = (ceil((int)$total / $configs['type_num']) - ($page)) > 0 ? true : false;
		$json_data = array('cate' => $list, 'cateName' => $type['name'], 'hasnext' => $hasnext, 'page' => $page);
		$this->output(0, '', $json_data);
	}

	/*
	 * 专题列表接口
	*/
	public function themelistAction() {
		$webroot    = Common::getCurHost();
		$orderBy    = array('sort' => 'DESC', 'id' => 'DESC');
		$themes     = Gionee_Service_WebThemeType::getAll($orderBy);
		$list       = array();
		foreach ($themes as $key => $value) {
			$list[$key] = array(
				'id'   => $value['id'],
				'name' => $value['name'],
				'img'  => Common::getImgPath() . $value['icon'],
				'link' => $webroot . '/webapp/themeapps?type_id=' . $value['id'],
			);
		}
		$json_data = array('theme' => $list, 'cateName' => '专题');
		$this->output(0, '', $json_data);
	}

	/*
	 * 专题应用列表接口
	*/
	public function themeappsAction() {
		$page       = 1;
		$theme_id   = intval($this->getInput('type_id'));
		$configs    = Gionee_Service_Config::getAllConfig();
		$webroot    = Common::getCurHost();
		$t_bi       = $this->getSource();
		$theme      = Gionee_Service_WebThemeType::get($theme_id);
		$where      = array('theme_id' => $theme_id, 'status' => 1);
		$order      = array('sort' => 'DESC', 'id' => 'DESC');
		list($total, $app) = Gionee_Service_WebApp::getList($page, $configs['theme_num'], $where, $order);
		$list = array();
		foreach ($app as $key => $value) {
			$list[$key] = array(
				'id'      => $value['id'],
				'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
				'img'     => Common::getImgPath() . $value['img'],
				'name'    => $value['name'],
				'star'    => $value['star'],
				'descrip' => Util_String::substr($value['descrip'], 0, 12),
				'is_new'  => $value['is_new'],
				'appType' => $theme['name'],
				'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
			);
		}
		$hasnext   = (ceil((int)$total / $configs['theme_num']) - ($page)) > 0 ? true : false;
		$json_data = array(
			'theme'    => $list,
			'cateName' => $theme['name'],
			'cateImg'  => Common::getImgPath() . $theme['img'],
			'hasnext'  => $hasnext,
			'page'     => $page
		);
		$this->output(0, '', $json_data);

	}

	/*
	 * 加载更多接口
	 */
	public function moreAction() {
		$webroot    = Common::getCurHost();
		$t_bi       = $this->getSource();
		$configs    = Gionee_Service_Config::getAllConfig();
		list(, $app_type) = Gionee_Service_WebAppType::getWebAppType();
		$app_type = Common::resetKey($app_type, 'id');
		$type     = $this->getInput('type');
		$page     = intval($this->getInput('page'));
		if ($page < 2) $page = 2;

		if ($type == 'recommend') {
			//推荐
			$where = array('is_recommend' => 1, 'status' => 1);
			$order = array('sort' => 'DESC', 'id' => 'DESC');

			list($total, $recommend) = Gionee_Service_WebApp::getList($page, $configs['recommend_num'], $where, $order);
			$recommend_data = array();
			foreach ($recommend as $key => $value) {
				$recommend_data[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['recommend_num']) - ($page)) > 0 ? true : false;
			$json_data = array('recommend' => $recommend_data, 'hasnext' => $hasnext, 'page' => $page);
		} elseif ($type == 'rank') {
			//排行
			$where = array('status' => 1);
			$order = array('hits' => 'DESC', 'id' => 'DESC');
			list($total, $rank) = Gionee_Service_WebApp::getList($page, $configs['ranking_num'], $where, $order);
			$rank_data = array();
			foreach ($rank as $key => $value) {
				$rank_data[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['ranking_num']) - ($page)) > 0 ? true : false;
			$json_data = array('rank' => $rank_data, 'hasnext' => $hasnext, 'page' => $page);
		} elseif ($type == 'news') {
			//新品
			$where = array('status' => 1);
			$order = array('is_new' => 'DESC', 'id' => 'DESC');
			list($total, $new) = Gionee_Service_WebApp::getList($page, $configs['new_num'], $where, $order);
			$new_data = array();
			foreach ($new as $key => $value) {
				$new_data[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['new_num']) - ($page)) > 0 ? true : false;
			$json_data = array('news' => $new_data, 'hasnext' => $hasnext, 'page' => $page);
		} elseif ($type == 'cate') {
			//分类
			$type_id = $this->getInput('type_id');
			$where   = array('type_id' => $type_id, 'status' => 1);
			$order   = array('id' => 'DESC');
			list($total, $app_list) = Gionee_Service_WebApp::getList($page, $configs['type_num'], $where, $order);
			$list = array();
			foreach ($app_list as $key => $value) {
				$list[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['type_num']) - ($page)) > 0 ? true : false;
			$json_data = array('cate' => $list, 'hasnext' => $hasnext, 'page' => $page);
		} elseif ($type == 'theme') {
			//专题
			$theme_id = $this->getInput('type_id');
			$theme    = Gionee_Service_WebThemeType::get($theme_id);
			$where    = array('theme_id' => $theme_id, 'status' => 1);
			list($total, $app_list) = Gionee_Service_WebApp::getList($page, $configs['theme_num'], $where, array('id' => 'DESC'));
			$list = array();
			foreach ($app_list as $key => $value) {
				$list[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $theme['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['theme_num']) - ($page)) > 0 ? true : false;
			$json_data = array('theme' => $list, 'hasnext' => $hasnext, 'page' => $page);
		} elseif ($type == 'search') {
			//搜索
			$like   = strtolower(trim($this->getInput('like')));
			$search = array('status' => 1);
			if ($like) $search['tag'] = array('LIKE', $like);
			list($total, $searchlist) = Gionee_Service_WebApp::getList($page, $configs['search_num'], $search, array('id' => 'DESC'));
			$search_data = array();
			foreach ($searchlist as $key => $value) {
				$search_data[$key] = array(
					'id'      => $value['id'],
					'link'    => Common::clickUrl($value['id'], 'APP', $value['link'], $t_bi),
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['search_num']) - ($page)) > 0 ? true : false;
			$json_data = array('search' => $search_data, 'hasnext' => $hasnext, 'page' => $page);
		}

		$this->output(0, '', $json_data);
	}

	/*
	 * 首页book2接口
	 */
	public function book2Action($tid) {
		$page       = 1;
		$webroot    = Common::getCurHost();
		$configs    = Gionee_Service_Config::getAllConfig();
		list(, $app_type) = Gionee_Service_WebAppType::getWebAppType();
		$app_type = Common::resetKey($app_type, 'id');
		$tid      = $this->getInput('tid');
		if ($tid == 1) {//广告
			$ad_data = $must_data = $recommend_data = array();
			$ad      = Gionee_Service_Ad::getCanUseAds(1, 1, array('ad_type' => 9));
			foreach ($ad as $key => $value) {
				$ad_data[$key] = array(
					'id'   => $value['id'],
					'link' => $value['link'],
					'img'  => Common::getImgPath() . $value['img'],
				);
			}
			//必备
			$where = array('is_must' => 1, 'status' => 1);
			$order = array('sort' => 'DESC', 'id' => 'DESC');
			list($total_rec, $must) = Gionee_Service_WebApp::getList($page, $configs['must_num'], $where, $order);
			foreach ($must as $key => $value) {
				$must_data[$key] = array(
					'id'      => $value['id'],
					'link'    => $value['link'],
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			//推荐
			$where = array('is_recommend' => 1, 'status' => 1);
			list($total, $recommend) = Gionee_Service_WebApp::getList($page, $configs['recommend_num'], $where, $order);
			foreach ($recommend as $key => $value) {
				$recommend_data[$key] = array(
					'id'      => $value['id'],
					'link'    => $value['link'],
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['recommend_num']) - ($page)) > 0 ? true : false;
			$json_data = array(
				'ad'        => $ad_data,
				'must'      => $must_data,
				'recommend' => $recommend_data,
				'hasnext'   => $hasnext
			);
		} elseif ($tid == 2) {//排行
			$order = array('hits' => 'DESC', 'id' => 'DESC');
			list($total, $rank) = Gionee_Service_WebApp::getList($page, $configs['ranking_num'], array('status' => 1), $order);
			$rank_data = array();
			foreach ($rank as $key => $value) {
				$rank_data[$key] = array(
					'id'      => $value['id'],
					'link'    => $value['link'],
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['ranking_num']) - ($page)) > 0 ? true : false;
			$json_data = array('rank' => $rank_data, 'hasnext' => $hasnext);
		} elseif ($tid == 3) {//分类
			$type_data = array();
			list(, $app_type) = Gionee_Service_WebAppType::getAllAppType();
			foreach ($app_type as $key => $value) {
				$type_data[$key] = array(
					'id'      => $value['id'],
					'name'    => $value['name'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'img'     => Common::getImgPath() . $value['img'],
					'link'    => $webroot . '/app/typelist?type_id=' . $value['id'],
				);
			}
			$json_data = array('category' => $type_data);
		} elseif ($tid == 4) {//新品
			$where = array('is_new' => 1, 'status' => 1);
			list($total, $new) = Gionee_Service_WebApp::getList($page, $configs['new_num'], $where, array('id' => 'DESC'));
			$new_data = array();
			foreach ($new as $key => $value) {
				$new_data[$key] = array(
					'id'      => $value['id'],
					'link'    => $value['link'],
					'img'     => Common::getImgPath() . $value['img'],
					'name'    => $value['name'],
					'star'    => $value['star'],
					'descrip' => Util_String::substr($value['descrip'], 0, 12),
					'is_new'  => $value['is_new'],
					'appType' => $app_type[$value['type_id']]['name'],
					'addUrl'  => $webroot . '/api/app/book?id=' . $value['id']
				);
			}
			$hasnext   = (ceil((int)$total / $configs['new_num']) - ($page)) > 0 ? true : false;
			$json_data = array('news' => $new_data, 'hasnext' => $hasnext);
		}
		$this->output(0, '', $json_data);
	}

	/*
	 * 统计控制
	 */
	public function statsAction() {
		echo '1';
		exit;
	}
}