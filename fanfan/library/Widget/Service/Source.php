<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Source {
	/** 状态:正常 */
	const STATUS_OK = 1;
	/** 状态:关闭 */
	const STATUS_CLOSE = 0;
	/** 状态:需要下载图片 */
	const STATUS_DOWN_IMG = -1;
	/** 状态:下载图片完成 */
	const STATUS_DOWN_OK = -2;
	/** 状态:废弃资源 */
	const STATUS_DORP = -5;

	static $Status = array(
		self::STATUS_OK       => '已发布',
		self::STATUS_CLOSE    => '关闭',
		self::STATUS_DOWN_IMG => '下载图片',
		self::STATUS_DOWN_OK  => '图片完成',
		self::STATUS_DORP     => '废弃'
	);


	/**
	 * 概要长度
	 */
	const SUMMARY_LEN = 220;

	/** 2.0.1版本以前运营商 */
	static $CpVer201 = array(
		Widget_Service_Cp::CP_SHISHANG,
		Widget_Service_Cp::CP_SOHU_PHOTO,
		Widget_Service_Cp::CP_SOHU_NEWS,
		Widget_Service_Cp::CP_SOHU_RSS
	);

	/** 2.0.2版本以前运营商 */
	static $CpVer202 = array(
		Widget_Service_Cp::CP_SHISHANG,
		Widget_Service_Cp::CP_SOHU_PHOTO,
		Widget_Service_Cp::CP_SOHU_NEWS,
		Widget_Service_Cp::CP_SOHU_RSS,
		Widget_Service_Cp::CP_QQ,
	);

	/** 接口返回广告记录数量 */
	const RET_SORT_TOP_NUM = 30;
	/** 接口返回记录数量 */
	const RET_SORT_BASE_NUM = 90;


	/** 1.0接口每次返回记录数量*/
	const RET_INDEX_NUM = 3;
	/** 1.0接口总返回记录数量 */
	const RET_VERION_NUM = 90;

	/** 接口缓存过期时间 */
	const RC_EXPRIE_TLL = 300;

	const RC_DATA_TLL = 86400;

	/**
	 *
	 * @param int $page
	 * @param int $params
	 * @param array $page
	 * @param array $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		$params = self::_cookData($params);
		$start  = (max($page, 1) - 1) * $limit;
		$ret    = self::_getDao()->getList($start, $limit, $params, $orderBy);
		return $ret;
	}

	public static function getTotal($params = array()) {
		$total = self::_getDao()->count($params);
		return $total;
	}

	/**
	 * 1.0版本获取最新列表
	 * @version 1.0.x
	 * @param int $limit 记录数
	 * @param int $id 起始ID
	 * @param array $params
	 * @return array
	 */
	public static function getLastListV1($limit = 10, $id = 0) {
		//获取兼容版本的url_id,过滤
		$params = self::_filterUrlId();
		$id     = intval($id);
		if ($id > 0) {
			$params['id'] = array('<', $id);
		}
		$ret = self::_getDao()->getList(0, $limit, $params, array('id' => 'DESC'));
		return $ret;
	}

	/**
	 * 获取记录详细信息
	 * @author william.hu
	 * @param int $id
	 * @return array
	 */
	public static function getIdsInfoV1($id) {
		$id    = intval($id);
		$rcKey = Common::KN_WIDGET_V1_INFO . $id;
		$info  = Common::getCache()->get($rcKey);
		//输出结果
		if ($info) {
			return $info;
		}

		$result     = Widget_Service_Source::getLastListV1(self::RET_INDEX_NUM, $id);
		$prevId     = 0;
		$columnData = array();

		if ($result) {
			$cpUrls = Widget_Service_Cp::getAll();

			$arr = array(
				1 => '171x101',
				2 => '121x101',
				3 => '152x101',
			);

			$i = 1;
			foreach ($result as $key => $value) {
				$picUrl = Common::getAttachPath() . "/source/" . $value['img'] . '_400x357.' . pathinfo($value['img'], PATHINFO_EXTENSION);

				$tPic = $picUrl;
				if (isset($arr[$i])) {
					$tPic = Common::getAttachPath() . "/source/" . $value['img'] . '_' . $arr[$i] . '.' . pathinfo($value['img'], PATHINFO_EXTENSION);
				}

				$source = isset(Widget_Service_Cp::$CpCate[$value['source']][1]) ? Widget_Service_Cp::$CpCate[$value['source']][1] : $value['source'];
				$tmp    = array(
					'id'         => intval($value['id']),
					'source_id'  => $value['out_link'],
					'title'      => $value['title'],
					'subtitle'   => !empty($value['subtitle']) ? $value['subtitle'] : $cpUrls[$value['url_id']]['resume'],
					'summary'    => self::cutstrSummary($value['summary']),
					'color'      => $value['color'],
					'out_link'   => $value['out_link'],
					'source'     => $source,
					'type_id'    => 2,
					'img'        => $picUrl,
					'thumbnails' => $tPic,
				);

				$columnData[$key] = $tmp;

				$prevId = $value['id'];

				$i++;
			}
		}

		$id = intval($id);
		if (empty($id)) {
			$id = isset($result[0]['id']) ? $result[0]['id'] + 1 : 0;
		}

		$info['title'] = '';
		//赋值下次起始id
		$info['id']       = $id;
		$info['type_id']  = 2;
		$info['sub_time'] = isset($result[0]['create_time']) ? $result[0]['create_time'] + 1 : time();
		$info['column']   = $columnData;
		$info['prev_id']  = intval($prevId);

		Common::getCache()->set($rcKey, $info, Common::KT_IDS);

		return $info;
	}

	/**
	 * 获取版本id列表
	 * @author william.hu
	 * @version 1.0
	 * @return array
	 */
	public static function getIdsListV1() {
		$rcKey = Common::KN_WIDGET_V1_LIST;
		$tmpId = Common::getCache()->get($rcKey);
		if (!empty($tmpId)) {
			return $tmpId;
		}

		$result = Widget_Service_Source::getLastListV1(self::RET_VERION_NUM);

		$i     = 1;
		$tmpId = array();
		//初始第一个
		$tmpId[] = isset($result[0]['id']) ? $result[0]['id'] + 1 : 0;
		foreach ($result as $k => $v) {
			if ($i % 3 == 0) { //间隔3个 一个
				$tmpId[] = $v['id'];
			}
			$i++;
		}
		Common::getCache()->set($rcKey, $tmpId, Common::KT_IDS);

		return $tmpId;
	}

	/**
	 * 只获取需要数据源的记录
	 * 1.0版本数据源只有搜狐和i时尚
	 * @author william.hu
	 * @version 1.0.x
	 * @return array
	 */
	private static function _filterUrlId() {
		$params['status'] = Widget_Service_Source::STATUS_OK;
		$urlIdStr         = Widget_Service_Config::getValue('widget_urlid_filter');
		$urlIds           = explode(",", $urlIdStr);
		if (!empty($urlIds)) {
			sort($urlIds);
			$params['url_id'] = array('IN', $urlIds);
		}
		return $params;
	}


	public static function getLastIdsByUrlId($urlIds = array()) {
		if (empty($urlIds) || !is_array($urlIds)) {
			return array();
		}

		$tmp = array();
		foreach ($urlIds as $urlId) {
			$ids = self::getLimitIdsByUrlId($urlId);
			$tmp = array_merge($tmp, $ids);
		}
		rsort($tmp);
		$ret = array_slice($tmp, 0, Widget_Service_Source::RET_SORT_BASE_NUM);
		return $ret;

	}

	public static function getLimitIdsByUrlId($urlId) {
		if (empty($urlId)) {
			return array();
		}

		$limit                 = Widget_Service_Source::RET_SORT_BASE_NUM;
		$params['url_id']      = $urlId;
		$params['status']      = 1;
		$params['create_time'] = array('<=', time());

		$list = self::_getDao()->getIds(0, $limit, $params, array('create_time' => 'DESC'));
		$ret  = array();
		foreach ($list as $val) {
			$ret[] = $val['id'];
		}
		return $ret;
	}

	static public function calcNumByNowDay($urlId) {
		$params = array( //今天记录数
			'url_id'      => $urlId,
			'status'      => 1,
			'create_time' => array('>', mktime(0, 0, 0, date('m'), date('d') - 1)),
		);

		$ret = self::_getDao()->count($params);
		return $ret;
	}


	static public function getHistoryList($urlId, $limit, $d = 30) {
		$params = array( //获取1个月前的数据记录 作为循环基础
			'url_id'      => $urlId,
			'status'      => 1,
			'create_time' => array('<=', mktime(0, 0, 0, date('m'), date('d') - $d)),
		);

		$ret = self::_getDao()->getList(0, $limit, $params, array('create_time' => 'DESC'));
		return $ret;
	}

	/**
	 * 统计最后的记录时间和 提供商数据数量
	 * @author william.hu
	 * @param int $urlId 提供商ID
	 * @return array 总数,最后记录时间
	 */
	public static function getLastRow($urlId) {
		$params = array('url_id' => $urlId);
		$ret    = self::_getDao()->getBy($params, array('create_time' => 'DESC'));
		$total  = self::_getDao()->count($params);
		return array($total, !empty($ret['create_time']) ? $ret['create_time'] : 0);
	}

	public static function getDetail($id) {
		$id = intval($id);

		if (empty($id)) {
			return array();
		}

		$rcKey = Common::KN_WIDGET_SOURCE_DETAIL . $id;
		$row   = Common::getCache()->get($rcKey);
		if (!empty($row) && Common::TO_CACHE) {
			return $row;
		}

		$row = Widget_Service_Source::get($id);
		if (empty($row)) {
			return array();
		}

		if (empty($row['content']) || $row['content'] == '[]') {
			$content = $row['summary'];
		} else {
			$content = self::formatContent($row);
		}

		$cpUrls          = Widget_Service_Cp::getAll();
		$row['subtitle'] = !empty($row['subtitle']) ? $row['subtitle'] : $cpUrls[$row['url_id']]['resume'];
		$row['content']  = $content;

		Common::getCache()->set($rcKey, $row, Common::KT_SOURCE_INFO);

		return $row;

	}

	public static function formatContent($info) {
		$arr = Widget_Service_Cp::$jsonFormatCp;

		if (in_array($info['cp_id'], $arr)) {
			$contents = json_decode($info['content'], true);
			$content  = Widget_Service_Adapter::formatContentToView($contents);
		} else {
			$content = Widget_Service_Adapter::formatContentToText($info['content']);
		}
		return $content;
	}

	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		$ret = false;
		$id  = intval($id);
		if ($id) {
			$ret = self::_getDao()->get($id);
		}
		return $ret;
	}

	/**
	 *
	 * @param array $params
	 */
	public static function getsBy($params) {
		$ret = false;
		if (is_array($params)) {
			$ret = self::_getDao()->getsBy($params);
		}
		return $ret;
	}


	public static function getBy($params) {
		if (!is_array($params)) {
			return false;
		}
		$ret = self::_getDao()->getBy($params);
		return $ret;
	}

	/**
	 *
	 * Enter description here ...
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		$ret = false;
		$id  = intval($id);
		if (is_array($data) && $id > 0) {
			$data = self::_cookData($data);
			$ret  = self::_getDao()->update($data, $id);
		}
		return $ret;
	}


	/**
	 *
	 * @param array $ids
	 * @param array $data
	 * @return boolean|int
	 */
	public static function updates($ids, $data) {
		$ret = false;
		if (is_array($data) && is_array($ids)) {
			$data = self::_cookData($data);
			$ret  = self::_getDao()->updates("id", $ids, $data);
		}
		return $ret;
	}

	/**
	 * @param int $id
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	/**
	 *
	 * @param array $ids
	 * @return boolean
	 */
	public static function deletes($ids) {
		$ret = false;
		if (is_array($ids)) {
			$ret = self::_getDao()->deletes("id", $ids);
		}
		return $ret;
	}

	/**
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function deleteBy($params) {
		$ret = false;
		if (is_array($params)) {
			$ret = self::_getDao()->deleteBy($params);
		}
		return $ret;
	}

	/**
	 * @param array $data
	 */
	public static function add($data) {
		$ret = false;
		if (is_array($data)) {
			$data = self::_cookData($data);
			$bUp  = self::_getDao()->insert($data);
			if ($bUp) {
				$ret = self::_getDao()->getLastInsertId();
			}
		}
		return $ret;
	}

	public static function edit($data) {
		$ret = false;
		if (is_array($data)) {
			$data = self::_cookData($data);
			$ret  = self::_getDao()->update($data, $data['id']);
		}
		return $ret;
	}

	/**
	 *
	 * @param array $data
	 */
	public static function multiAdd($data) {
		$ret = false;
		if (is_array($data)) {
			$ret = self::_getDao()->mutiInsert($data);
		}
		return $ret;
	}

	/**
	 * @param array $data
	 */
	private static function _cookData($data) {
		$fields = array(
			'id', 'out_id', 'out_iid', 'cp_id', 'url_id', 'title', 'color',
			'subtitle', 'summary', 'content', 'img', 'source', 'out_link',
			'status', 'source_time', 'create_time', 'w3_color', 'url', 'mark'
		);
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	/**
	 * 置顶的广告记录
	 * @author william.hu
	 * @param int $limit
	 * @param array $urlIds 需要过滤的url id
	 * @return array
	 */
	public static function getTopIds($cpIds = array()) {
		$sTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$eTime = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));

		$limit  = Widget_Service_Source::RET_SORT_TOP_NUM;
		$params = array(
			'url_id'      => 1,
			'status'      => 1,
			'create_time' => array(
				array('>=', $sTime),
				array('<', $eTime)
			),
		);

		if (!empty($cpIds)) {
			$params['cp_id'] = array('IN', $cpIds);
		}

		$list = self::_getDao()->getList(0, intval($limit), $params, array('create_time' => 'DESC'));

		$ids = array();
		foreach ($list as $v) {
			$ids[] = $v['id'];
		}
		return $ids;
	}

	/**
	 * 通过标题查询记录
	 * @author william.hu
	 * @param string $title
	 * @return array
	 */
	static public function getByTitle($title) {
		$ret = self::_getDao()->getBy(array('title' => $title));
		return $ret;
	}


	static public function setCooike($arr) {
		if (!empty($arr)) {
			$arr['d'] = date('md');
			return urlencode(json_encode($arr));
		}
		return '';
	}


	/**
	 * 获取排序ID列表
	 *
	 * 广告设计: 获取今天的广告记录 在第一次加载时显示在顶部
	 * 并记录当前新闻记录的ID 做索引 传给客户端
	 * 客户端调用时 传回给服务器  获取广告记录 插入索引所在位置
	 * 广告的数据记录必须提前一天发布 当天发布的 如果在用户已经请求过数据 将不会显示在顶部
	 *
	 * @param array $urlIds [广告id列表, 新闻ID列表]
	 * @param string $ver 数量
	 * @param array $adPosArr 广告所在位置  (索引id|日期)
	 * @return string
	 */
	static public function filterAdIds($topResult, $ids, &$adPosArr) {
		$leftSortNum = self::RET_SORT_BASE_NUM;
		$tmpIds      = array();
		foreach ($adPosArr as $tKey => $tVal) {
			$tmpIds = array_merge($tmpIds, $tVal);
		}

		$tmp = array();
		$n   = 0;
		foreach ($ids as $id) {

			if ($n == 0) {
				//新广告位置
				$newIds = array();

				foreach ($topResult as $adId) {
					if (!in_array($adId, $tmpIds)) {
						$tmp[]    = $adId;
						$newIds[] = $adId;
						$leftSortNum--;
					}
				}


				if (!empty($newIds)) {
					$adPosArr[$id] = $newIds;
				}
			} else {
				if (isset($adPosArr[$id])) { //已获取过广告插入之前位置
					foreach ($adPosArr[$id] as $tmpId) {
						$tmp[] = $tmpId;
						$leftSortNum--;
					}
				}
			}

			$tmp[] = $id;

			if ($leftSortNum < 0) {
				break;
			}
			$leftSortNum--;
			$n++;
		}

		$data = implode(',', $tmp);
		return $data;
	}

	public static function getInfoV3($id) {
		if (empty($id)) {
			return array();
		}

		$id    = intval($id);
		$rcKey = Common::KN_W3_SOURCE_INFO . $id;
		$info  = Common::getCache()->get($rcKey);
		if (!empty($info) && Common::TO_CACHE) {
			return $info;
		}

		$row = Widget_Service_Source::get($id);
		if ($row['id']) {
			$cpUrls    = Widget_Service_Cp::getAll();
			$webroot   = Yaf_Application::app()->getConfig()->webroot;
			$jout      = array();
			$cpId      = intval($row['cp_id']);
			$outLink   = $row['out_link'];
			$detailUrl = $webroot . "/front/news/detail?id={$row['id']}";

			$cpSetting = W3_Service_Cp::all();
			$isWeb     = intval($cpSetting[$cpId]['is_web']);
			if ($cpId == Widget_Service_Cp::CP_GIONEE) {
				$isWeb = 1;
			}

			$className = '_formatOutLink_' . $cpId;
			if (method_exists(__CLASS__, $className)) {
				list($jout, $outLink) = self::$className($row);
			}

			$columnName = !empty($row['subtitle']) ? $row['subtitle'] : $cpUrls[$row['url_id']]['resume'];

			$img  = $row['img'];
			$info = array(
				'id'          => $row['id'],
				'title'       => $row['title'],
				'color'       => self::_upColor($row),
				'img'         => Common::getAttachPath() . "/source/" . $img,
				'column_name' => $columnName,
				'detail_url'  => $detailUrl,
				'is_web'      => 0,
				'cp_id'       => $cpId,
				'jout_link'   => $jout,
				'out_link'    => $outLink,
				'create_time' => date('Y-m-d H:i:s', $row['create_time']),
				'is_web'      => $isWeb,
				'pic_size'    => array('400x357')
			);
		}
		Common::getCache()->set($rcKey, $info, Common::KT_SOURCE_INFO);

		return $info;

	}

	private static function _upColor($row) {
		$color = $row['w3_color'];
		if (empty($row['w3_color'])) {
			$color = $row['color'];
		}
		return $color;
	}

	private static function _formatOutLink_114($row) {
		parse_str($row['out_link'], $tmpP);
		$pbName = isset($tmpP['publicationName']) ? $tmpP['publicationName'] : '';
		$jout   = array(
			array('k' => 'ACTIVITY', 'v' => 'push', 't' => 'string'),
			array('k' => 'publication_id', 'v' => $tmpP['pbID'], 't' => 'string'),
			array('k' => 'title', 'v' => $pbName, 't' => 'string'),
		);
		unset($tmpP['publicationName']);
		$outLink = http_build_query($tmpP);
		return array($jout, $outLink);
	}

	private static function _formatOutLink_111($row) {
		parse_str($row['out_link'], $tmpP);
		unset($tmpP['channelid']);
		$jout   = array();
		$jout[] = array('k' => 'channelid', 'v' => '1000120012', 't' => 'string');
		foreach ($tmpP as $k => $v) {
			$jout[] = array('k' => $k, 'v' => $v, 't' => 'string');
		}
		return array($jout, $row['url']);
	}

	private static function _formatOutLink_116($row) {
		return self::_formatOutLink_111($row);
	}

	private static function _formatOutLink_117($row) {
		parse_str($row['out_link'], $tmpP);
		$jout = array();
		foreach ($tmpP as $k => $v) {
			$jout[] = array('k' => $k, 'v' => $v, 't' => 'string');
		}
		$outLink = $tmpP['OTHER_URL'];
		return array($jout, $outLink);
	}

	private static function _formatOutLink_120($row) {
		$jout    = array();
		$outLink = $row['out_link'];
		if (!is_numeric($row['out_link'])) {
			preg_match('/content\/(\d*)\.html/i', $row['url'], $match);
			$outLink = $match[1];
		}
		return array($jout, $outLink);
	}

	/**
	 * 抓取源的详细信息
	 * @author william.hu
	 * @param int $id 信息ID
	 * @param string $ver 版本号
	 * @return array
	 */
	public static function getInfo($id) {
		if (empty($id)) {
			return array();
		}

		$id    = intval($id);
		$rcKey = Common::KN_WIDGET_SOURCE_INFO . $id;
		$info  = Common::getCache()->get($rcKey);
		if (!empty($info) && Common::TO_CACHE) {
			return $info;
		}

		$row = Widget_Service_Source::get($id);
		if (empty($row)) {
			return array();
		}

		$str      = Widget_Service_Config::getValue('cp_action');
		$cpAction = json_decode($str, true);

		$actionData = array();
		if (!empty($cpAction[$row['source']])) {
			//return array();
			$actionData = $cpAction[$row['source']];
		}

		$sourceTitle     = isset(Widget_Service_Cp::$CpCate[$row['source']][1]) ? Widget_Service_Cp::$CpCate[$row['source']][1] : $row['source'];
		$sourceStatsName = isset(Widget_Service_Cp::$CpCate[$row['source']][2]) ? Widget_Service_Cp::$CpCate[$row['source']][2] : '';
		$cpUrls          = Widget_Service_Cp::getAll();
		$webroot         = Yaf_Application::app()->getConfig()->webroot;
		$subTitle        = !empty($row['subtitle']) ? $row['subtitle'] : $cpUrls[$row['url_id']]['resume'];
		$cpInfo          = W3_Service_Cp::get($row['cp_id']);


		if ($row['cp_id'] == Widget_Service_Cp::CP_HAOKAN && !is_numeric($row['out_link'])) {
			preg_match('/content\/(\d*)\.html/i', $row['url'], $match);
			$row['out_link'] = $match[1];
		}

		//out_link 通过浏览器调用
		$outLink = $row['out_link'];
		if (!empty($cpInfo['to_url'])) {
			$outLink = $row['url'];
		}


		$newOutLink = $outLink;
		if ($row['cp_id'] == Widget_Service_Cp::CP_HAOKAN) {
			$newOutLink = $row['url'];
		}


		$titleLen = Widget_Service_Config::getValue('title_len') * 3;
		$info     = array(
			'id'          => $row['id'],
			'out_iid'     => $row['out_iid'],
			'title'       => mb_strcut($row['title'], 0, !empty($titleLen) ? $titleLen : 54, 'utf-8'),
			'resume'      => $subTitle, //v2新版本使用
			'color'       => $row['color'],
			'subtitle'    => $subTitle, //v1老板本使用
			'summary'     => self::cutstrSummary(strip_tags($row['summary'])),
			'img'         => Common::getAttachPath() . "/source/" . $row['img'],
			'source'      => $sourceTitle,
			'out_link'    => $outLink,//2.0.6+
			'create_time' => intval($row['create_time']),
			'cp_id'       => intval($row['source']),
			'action'      => $actionData['action'],
			'version'     => $actionData['version'],
			'type'        => intval($actionData['type']),
			'downurl'     => $webroot . '/front/res/info?cp_id=' . intval($row['cp_id']),
			'source_name' => $sourceStatsName,
			'pic_size'    => array('400x357')
		);

		//2.0.5
		$info['data'] = array(
			'key'     => '',
			'value'   => '',
			'dataurl' => '',
		);
		if ($actionData['type'] == 1) { //跳转客户端
			$info['data'] = array(
				'key'   => $actionData['extra_key'],
				'value' => $newOutLink,
			);
		} else if ($actionData['type'] == 2) { //跳转浏览器
			$info['data'] = array(
				'dataurl' => $newOutLink,
			);
		}
		Common::getCache()->set($rcKey, $info, Common::KT_SOURCE_INFO);

		return $info;

	}

	public static function cutstrSummary($str) {
		$enc        = 'utf-8';
		$summaryLen = Widget_Service_Config::getValue('summary_len');
		$len        = !empty($summaryLen) ? intval($summaryLen) * 3 : self::SUMMARY_LEN;
		$ret        = mb_strcut($str, 0, $len, $enc);
		if (mb_strlen($ret, $enc) < mb_strlen($str, $enc)) {
			$ret .= '...';
		}
		return $ret;
	}


	static public function filterIds($columnIdStr, $lt2 = false) {
		$columnIdArr = explode(',', $columnIdStr);
		sort($columnIdArr);
		$columnIdStr = implode(',', $columnIdArr);
		$k           = $lt2 ? 'lt2' : 'gt2';
		$rcKey       = Common::KN_WIDGET_IDS . $k . ':' . crc32($columnIdStr);
		$ret         = Common::getCache()->get($rcKey);
		if (!empty($ret) && Common::TO_CACHE) {
			return $ret;
		}

		$tmpUrlIds = $cpIds = array();
		if ($lt2) {
			$allUrlIds = Widget_Service_Cp::getIdsByCp();
			foreach (Widget_Service_Source::$CpVer201 as $cpId) {
				$tmpUrlIds = array_merge($tmpUrlIds, $allUrlIds[$cpId]);
			}
			$cpIds = Widget_Service_Source::$CpVer201;
		}

		$urlIds = $tmpIds = array();
		foreach ($columnIdArr as $columnId) {
			$row = Widget_Service_Column::getSourceIdsByColumnId($columnId);
			if (!empty($row['ids'])) {
				if (empty($tmpUrlIds) || in_array($row['url_id'], $tmpUrlIds)) {
					$tmpIds   = array_merge($tmpIds, $row['ids']);
					$urlIds[] = $row['url_id'];
				}
			}
		}
		rsort($tmpIds);
		$idsArr  = array_slice($tmpIds, 0, Widget_Service_Source::RET_SORT_BASE_NUM);
		$topList = Widget_Service_Source::getTopIds($cpIds);//置顶的人工数据
		$ret     = array($urlIds, $topList, $idsArr);
		Common::getCache()->set($rcKey, $ret, Common::KT_IDS);
		return $ret;
	}


	/**
	 *
	 * @return Widget_Dao_Source
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Source");
	}

}
