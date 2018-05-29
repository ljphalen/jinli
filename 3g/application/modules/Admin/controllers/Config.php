<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 系统设置
 * @author rainkid
 */
class ConfigController extends Admin_BaseController {

	public $actions = array(
		'editUrl'        => '/admin/config/index',
		'editPostUrl'    => '/admin/config/edit_post',
		'delFileUrl'     => '/admin/config/delResFile',
		'extractFileUrl' => '/admin/config/extractResFile',
		'loglist'        => '/Admin/config/loglist',
		'incomeUrl'      => '/Admin/config/income',
		'errLogDelUrl'   => '/Admin/config/errLogDel',
		'errLogUrl'      => '/Admin/config/errlog',
		'cronLogUrl'     => '/Admin/config/cronlog',
		'uconfigUrl'     => '/Admin/config/uconfig',
		'editMsgTplUrl'  => '/Admin/config/editMsgTpl',
		'channelUrl'     => '/Admin/Config/channel',
		'hotwordsUrl'    => '/Admin/Config/hotwords',
		'searchlistUrl'  => '/Admin/Config/searchlist',
		'searcheditUrl'  => '/Admin/Config/searchedit',
		'searchdelUrl'   => '/Admin/Config/searchdel',
	);


	public function indexAction() {
		$configs = Gionee_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	public function cleanverAction() {
		$vers = $this->getInput(array('nav_index', 'nav_type_id', 'news_index', 'nav_comic'));
		if (!empty($vers['nav_index'])) {
			Gionee_Service_Ng::updataVersion();
		} elseif (!empty($vers['nav_type_id'])) {
			$rcKey = Gionee_Service_Ng::cleanNgTypeData($vers['nav_type_id']);
			Common::getCache()->delete($rcKey);
		} elseif (!empty($vers['news_index'])) {
			Gionee_Service_Config::setValue('APPC_Front_News_index', Common::getTime());
		} elseif (!empty($vers['nav_comic'])) {
			$comicData = Gionee_Service_LocalNavList::getComicOutData(true);
			echo Common::jsonEncode($comicData);
		}
		Admin_Service_Log::op();
	}

	public function baidukeywordsAction() {
		$vers     = $this->getInput(array('update'));
		if (!empty($vers['update'])) {
			$ret = Gionee_Service_Baidu::updateKeywords();
		}
		$keywords = Common::getCache()->get("baidu_keywords");
		$hotwords = Common::getCache()->get("baidu_hotwords");
		$hotTitles = array();
		foreach ($hotwords as $v) {
			$hotTitles[] = $v['text'];
		}
		//首页导航信息
		$words  = Gionee_Service_Baidu::getNavIndexWrods();
		$hotNav = array();
		foreach ($words as $v) {
			$hotNav[] = $v['text'];
		}

			 $items = Gionee_Service_Baidu::takeKeywords();
			$t     = array();
				foreach ($items as $k => $v) {
					$t[] = $v['word'];
				} 
		/*  $t     = array();
		$items = Gionee_Service_Baidu::getSmHotwords();
		foreach ($items as $m => $n) {
			$t[] = $n['title'];
		}  */
		//把后台手动添加的有效数据放入相应的位置
		$apikeys = Gionee_Service_Baidu::apiKeys();
		foreach ($apikeys as $v) {
			$apiValues[] = $v['text'];
		}
		$this->assign('items', $t);
		$this->assign('keywords', $keywords);
		$this->assign('hotwords', $hotTitles);
		$this->assign('navindexwords', $hotNav);
		$this->assign('apikeys', $apiValues);
	}

	public function edit_postAction() {
		$it                          = array(
			'styles_version',
			'gionee_cache',
			'activity_1111',
			'topic_top_func_status',
			'nav_bottom',
			'localnav_to_html',
			'baidu_stat_no',
			'localnav_to_appcache',
            'card_list_manage_title'
		);
		$config                      = $this->getInput($it);
		$config['sohu_redirect_url'] = $_POST['sohu_redirect_url'];
        $tmp = array();
        foreach(explode("\n",$_POST['filter_label_imeis']) as $v) {
            $tmp[] = trim($v);
        }
        $config['filter_label_imeis'] = json_encode($tmp);

		foreach ($config as $key => $value) {
			Gionee_Service_Config::setValue($key, $value);
		}
		Admin_Service_Log::op($config);
		$this->output(0, '操作成功.');
	}

	public function uploadResAction() {
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$savePath   = sprintf('%s/%s/%s', $attachPath, 'res_file', '');
		if (!empty($_FILES['res_file'])) {
			$fileInfo = $_FILES['res_file'];
			$filename = sprintf('%s/%s.zip', $savePath, date('YmdHis'));
			if ($fileInfo['tmp_name']) {
				move_uploaded_file($fileInfo['tmp_name'], $filename);
			}
			$zip = new ZipArchive;
			$zip->open($filename);
			$zip->extractTo($savePath . '/sync');
			$zip->close();
		}
		$files = array_diff(scandir($savePath), array('..', '.'));
		$this->assign('files', $files);
	}

	public function delResFileAction() {

	}

	public function extractResFileAction() {
		$attachPath = Common::getConfig('siteConfig', 'attachPath');
		$savePath   = sprintf('%s/%s/%s', $attachPath, 'res_file', '');
		$name       = $this->getInput('name');
		$filename   = sprintf('%s%s', $savePath, $name);
		$zip        = new ZipArchive;
		$zip->open($filename);
		$zip->extractTo($savePath . '/sync');
		$zip->close();
		echo "更新成功";
		exit;
	}

	/**
	 * 检测URL是否有404现象
	 */
	public function checkUrlAction() {
		$params['status']     = 1;
		$params['start_time'] = array('<=', time());
		$params['end_time']   = array('>=', time());
		$total                = Gionee_Service_Ng::count($params);
		$this->assign('total', $total);
		$pageSize = $this->getInput('pageSize');
		$pageSize = $pageSize ? $pageSize : 30;
		$this->assign('pageSize', $pageSize);
	}

	/**
	 *AJAX 动态获取数据
	 */
	public function ajaxgetCheckResultAction() {
		$page                 = intval($this->getInput('page'));
		$page                 = max(1, $page);
		$pageSize             = intval($this->getInput('pageSize'));
		$pageSize             = max(30, $pageSize);
		$params               = array();
		$params['status']     = 1;
		$params['start_time'] = array('<=', time());
		$params['end_time']   = array('>=', time());
		$dataList             = Gionee_Service_Ng::getList($page, $pageSize, $params);
		$j                    = 0;
		$data                 = array();
		foreach ($dataList[1] as $k => $v) {
			if (empty($v['link'])) {
				$data[$j] = array(
					'url'   => $v['link'],
					'desc'  => 'Bad URL!',
					'title' => $v['title'],
					'id'    => $v['id'],
				);
				$j++;
			} else {
				$urls[] = $v['link'];
				$info[] = array('title' => $v['title'], 'id' => $v['id']);
			}
		}
		$out = $this->exec($urls);
		foreach ($out as $k => $v) {
			$err = '';
			if ($v['code'] == 404) {
				$err = '404 Error!';
			} elseif (empty($v['code'])) {
				$err = 'Bad URL!';
			}
			if ($err) {
				$data[$j] = array(
					'url'   => $v['url'],
					'desc'  => $err,
					'title' => $info[$k]['title'],
					'id'    => $info[$k]['id'],
				);
				$j++;
			}

		}
		exit (json_encode($data));
	}

	public function exec($urls) {
		$curl_setopt = array(
			CURLOPT_RETURNTRANSFER => 1, //结果返回给变量
			CURLOPT_HEADER         => 0, //要HTTP头不
			CURLOPT_NOBODY         => 1, //不要内容
			CURLOPT_TIMEOUT        => 60,//超时时间
		);
		$mh          = curl_multi_init();
		foreach ($urls as $i => $url) {
			$conn[$i] = curl_init($url);
			foreach ($curl_setopt as $key => $val) {
				curl_setopt($conn[$i], $key, $val);
			}
			curl_multi_add_handle($mh, $conn[$i]);
		}
		$active = false;

		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active and $mrc == CURLM_OK) {
			if (curl_multi_select($mh) != -1) {
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		$res = array();
		foreach ($urls as $i => $url) {
			$res[$i]['url']  = $url;
			$res[$i]['code'] = curl_getinfo($conn[$i], CURLINFO_HTTP_CODE);
			curl_close($conn[$i]);
			curl_multi_remove_handle($mh, $conn[$i]); //释放资源
		}
		curl_multi_close($mh);
		return $res;
	}


	//上线日志记录

	public function addLogAction() {
	}

	public function logPostAction() {
		$date    = $this->getInput('online_date');
		$title   = $this->getInput('title');
		$content = trim($_POST['content']);
		if (empty($date) || empty($title)) {
			$this->output('-1', '日期和内容不能为空');
		}
		$res = Gionee_Service_OnlineLog::add(array(
			'online_date' => $date,
			'title'       => $title,
			'content'     => $content,
			'admin_id'    => $this->userInfo['uid'],
			'add_time'    => time()
		));
		if ($res) {
			$this->output('0', '添加成功');
		} else {
			$this->output('-1', '添加失败');
		}
	}


	/**
	 * 日志列表
	 */
	public function logListAction() {
		$page = $this->getInput('page');
		list($total, $dataList) = Gionee_Service_OnlineLog::getList(array(), array('add_time' => "DESC"), $page);
		foreach ($dataList as $k => $v) {
			$userinfo                   = Admin_Service_User::getUser($v['admin_id']);
			$dataList[$k]['admin_name'] = $userinfo['username'];
			$modifier                   = Admin_Service_User::getUser($v['edit_userid']);
			$dataList[$k]['modifier']   = $modifier ? $modifier['username'] : '';
		}
		$this->assign('userinfo', $this->userInfo);
		$this->assign('list', $dataList);
		$this->assign('pager', Common::getPages($total, $page, 20, $this->actions['loglist'] . "/?"));
	}

	public function editLogAction() {
		$id   = $this->getInput('id');
		$info = Gionee_Service_OnlineLog::get($id);
		$this->assign('info', $info);
		$this->assign('id', $id);
	}

	public function editlogPostAction() {
		$id      = $this->getInput('id');
		$date    = $this->getInput('online_date');
		$title   = $this->getInput('title');
		$content = trim($_POST['content']);
		$uid     = $this->userInfo['uid'];
		$updata  = array(
			'online_date' => $date,
			'title'       => $title,
			'content'     => $content,
			'edit_userid' => $uid,
			'edit_time'   => time()
		);
		$res     = Gionee_Service_OnlineLog::update($id, $updata);
		Admin_Service_Log::op(array($id, $updata));
		if ($res) {
			$this->output('0', '修改成功！');
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	public function logDeleteAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_OnlineLog::delete($id);
		if ($res) {
			$this->output('0', '修改成功！');
		} else {
			$this->output('-1', '操作失败！');
		}
	}

	/**
	 * type nav_news  导航新闻
	 * type out_news  聚合新闻
	 */
	public function cronrunAction() {
		$type = $this->getInput('type');
		if ($type == 'nav_news') {
			$out = Gionee_Service_Jhnews::run();
		} else if ($type == 'out_news') {
			$out = Gionee_Service_OutNews::run();
		} else if ($type == 'localnav_up_page') {
			$out1 = Gionee_Service_LocalNavList::run();
			$out2 = Gionee_Service_LocalNavList::make_appcache_file();
			$out3 = Gionee_Service_LocalNavList::card_data_2_html();
			$out  = $out1 . $out2 . $out3;
        } else if ($type == 'localnav_news_up_list') {
            $tmp = Nav_Service_NewsData::makeList();
            $out = '';
            foreach ($tmp as $k => $s) {
                $out .= date('m/d H:i:s') . ':[' . $k . ']' . $s . "\n";
            }
		} else if ($type == 'localnav_news_get_rss') {
			$tmp = Nav_Service_NewsParse::run();
			$out = '';
			foreach ($tmp as $sourceId => $ids) {
				$out .= date('m/d H:i:s') . ':[' . $sourceId . ']' . Common::jsonEncode($ids) . "\n";
			}
		}
		echo str_replace("\n", '<hr>', $out);
		exit;
	}

	/**
	 * 任务日志
	 */
	public function cronlogAction() {
		$d    = $this->getInput('date');
		$t    = $this->getInput('type');
		$page = $this->getInput('page');
		$page = max($page, 1);
		if (empty($d)) {
			$d = date('Y-m-d');
		}
		$sDate = strtotime("{$d} 00:00:00");
		$eDate = strtotime("{$d} 23:59:59");


		$where = array(
			'created_at' => array(array('>=', $sDate), array('<=', $eDate)),
		);

		if (!empty($t)) {
			$where['type'] = $t;
		}

		$orderBy = array('created_at' => 'desc');
		list($total, $list) = Gionee_Service_CronLog::getList($page, 50, $where, $orderBy);
		$pages = Common::getPages($total, $page, 50, $this->actions['cronLogUrl'] . "?date={$d}&");
		$this->assign('list', $list);
		$this->assign('date', $d);
		$this->assign('types', Gionee_Service_CronLog::$types);
		$this->assign('type', $t);
		$this->assign('pager', $pages);
	}

	/**
	 * 错误日志
	 */
	public function errlogAction() {
		$d    = $this->getInput('date');
		$t    = $this->getInput('type');
		$page = $this->getInput('page');
		$page = max($page, 1);
		if (empty($d)) {
			$d = date('Y-m-d');
		}
		$sDate = strtotime("{$d} 00:00:00");
		$eDate = strtotime("{$d} 23:59:59");


		$where = array(
			'created_at' => array(array('>=', $sDate), array('<=', $eDate)),
		);

		if (!empty($t)) {
			$where['type'] = $t;
		}

		$orderBy = array('created_at' => 'desc');
		list($total, $list) = Gionee_Service_ErrLog::getList($page, 50, $where, $orderBy);
		$pages = Common::getPages($total, $page, 20, $this->actions['errLogUrl'] . "?date={$d}&");
		$this->assign('list', $list);
		$this->assign('date', $d);
		$this->assign('types', Gionee_Service_ErrLog::$types);
		$this->assign('type', $t);
		$this->assign('page', $page);
		$this->assign('pager', $pages);

	}

	public function errLogDelAction() {
		$id  = $this->getInput('id');
		$res = Gionee_Service_ErrLog::delete($id);
		if ($res) {
			$this->output('0', '修改成功！');
		} else {
			$this->output('-1', '操作失败！');
		}
	}


	public function channelAction() {
		$config = $this->_channelConfig();
		$keys   = array();
		foreach ($config as $k => $v) {
			$keys[]   = $v['key'];
			$postData = $this->getInput($v['key']);
			if (!empty($postData)) {
				$res = Gionee_Service_Config::setValue($v['key'], json_encode($postData));
				Admin_Service_Log::op($postData);
			}
		}
		$result = array();
		$data   = Gionee_Service_Config::getsBy(array('3g_key' => array('IN', $keys)));
		foreach ($data as $key => $val) {
			$result[$val['3g_key']] = json_decode($val['3g_value'], true);
		}
		$this->assign('result', $result);
		$this->assign('config', $config);
	}

	private function _channelConfig() {
		$config = array(
			/* array('name' => '浏览器地址栏', 'key' => 'broswer_address'),
			array('name' => '右屛书签位', 'key' => 'right_side'),
			array('name' => '首页搜索框', 'key' => 'index_search'),
			array('name' => '首页百度热词', 'key' => 'index_hotword'),
			array('name' => '新闻页搜索框', 'key' => 'news_search'),
			array('name' => '桌面搜索', 'key' => 'destop_search'),
			array('name' => '新闻页百度关键词', 'key' => 'baidu_hotwords_news'), */
			array('name' => '首页热词', 'key' => 'baidu_hotwords_index'),
		);
		return $config;
	}


	public function changyanAction() {
		$data     = array(
			'changyan_app_id',
			'changyan_app_key',
			'changyan_comment_val',
		);
		$postData = $this->getPost($data);
		if (!empty($postData['changyan_app_id'])) {
			Admin_Service_Log::op($postData);
			foreach ($postData as $key => $val) {
				Gionee_Service_Config::setValue($key, trim($val));
			}
			$this->output('0', '操作成功!');
		}

		$configs = Gionee_Service_Config::getAllConfig();
		$this->assign('configs', $configs);

	}

	public function geetestAction() {
		$data     = array(
			'geetest_captcha_id',
			'geetest_private_key',
		);
		$postData = $this->getPost($data);
		if (!empty($postData['geetest_captcha_id'])) {
			Admin_Service_Log::op($postData);
			foreach ($postData as $key => $val) {
				Gionee_Service_Config::setValue($key, trim($val));
			}
			$this->output('0', '操作成功!');
		}

		$configs = Gionee_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}

	public function hotwordsAction() {
		$key      = '3g_hotwords_tpl';
		$postData = trim($_POST[$key]);
		if (!empty($postData)) {
			Gionee_Service_Config::setValue($key, $postData);
			$this->output('0', '操作成功!');
		}
		$data = Gionee_Service_Config::getValue($key);
		$this->assign('data', $data);

	}

	public function wankaAction() {
		$data     = array(
			"wk_main_switch",
			"wk_searchEngines_switch",
			"wk_hotKeyword_switch",
			"wk_suggested_switch",
		);
		$postData = $this->getPost($data);
		if (!empty($_POST)) {
			Admin_Service_Log::op($postData);
			Gionee_Service_Config::setValue('wanka_conf', Common::jsonEncode($postData));
			$this->output('0', '操作成功!');
		}

		$str = Gionee_Service_Config::getValue('wanka_conf');
		$this->assign('configs', json_decode($str, true));
	}

	public function browserbaseAction() {

		$data                            = array(
			'browser_online_time',
			'browser_up_ver_coin',
		);
		$postData                        = $this->getPost($data);
		$postData['browser_online_time'] = $_POST['browser_online_time'];
		if (!empty($_POST)) {
			Admin_Service_Log::op($postData);
			foreach ($postData as $key => $value) {
				Gionee_Service_Config::setValue($key, $value);
			}
			$this->output('0', '操作成功!');
		}

		$configs = Gionee_Service_Config::getAllConfig();
		$this->assign('configs', $configs);
	}


	public function searchlistAction() {
		Admin_Service_Access::pass('searchlist');
		$get    = $this->getInput(array('togrid', 'page', 'rows', 'sort', 'order'));
		$togrid = !empty($get['togrid']) ? true : false;
		if ($togrid) {

			$page           = max(intval($get['page']), 1);
			$offset         = !empty($get['rows']) ? $get['rows'] : $this->pageSize;
			$sort           = !empty($get['sort']) ? $get['sort'] : 'id';
			$order          = !empty($get['order']) ? $get['order'] : 'desc';
			$orderBy[$sort] = $order;

			$where = array();
			foreach ($_POST['filter'] as $k => $v) {
				if (strlen($v) != 0) {
					$where[$k] = $v;
				}
			}

			$start = ($page - 1) * $offset;
			$total = Gionee_Service_SearchKeyword::getDao()->count($where);
			$list  = Gionee_Service_SearchKeyword::getDao()->getList($start, $offset, $where, $orderBy);
			foreach ($list as $k => $v) {
				$list[$k]['created_at'] = date('y/m/d H:i', $v['created_at']);
				$list[$k]['updated_at'] = date('y/m/d H:i', $v['updated_at']);
				$list[$k]['link']       = Common::getPrevSearchUrl() . '?keyword={searchTerms}&from=' . $v['from'];
			}
			$ret = array(
				'total' => $total,
				'rows'  => $list,
			);
			echo json_encode($ret);
			exit;
		}
	}

	public function searcheditAction() {
		Admin_Service_Access::pass('searchedit');
		$id       = $this->getInput('id');
		$postData = $this->getPost(array('id', 'name', 'from', 'url',));
		$now      = time();
		if (!empty($postData['name'])) {
			$postData['updated_at'] = $now;
			if (empty($postData['id'])) {
				$postData['created_at'] = $now;
				$ret                    = Gionee_Service_SearchKeyword::getDao()->insert($postData);
			} else {
				$ret = Gionee_Service_SearchKeyword::getDao()->update($postData, $postData['id']);
			}
			Gionee_Service_SearchKeyword::all(true);
			Admin_Service_Log::op($postData);
			if ($ret) {
				$this->output(0, '操作成功');
			} else {
				$this->output(-1, '操作失败');
			}
		}

		$info = Gionee_Service_SearchKeyword::getDao()->get($id);
		$this->assign('info', $info);
	}

	public function searchdelAction() {
		Admin_Service_Access::pass('del');
		$idArr = (array)$this->getInput('id');
		$i     = 0;
		$succ  = array();
		foreach ($idArr as $id) {
			$ret = Gionee_Service_SearchKeyword::getDao()->delete($id);
			if ($ret) {
				$i++;
				$succ[] = $id;
			}
		}

		Admin_Service_Log::op($succ);
		if ($i == count($succ)) {
			$this->output(0, '操作成功');
		} else {
			$this->output(-1, '操作失败', $succ);
		}
	}
}
	

