<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 */
class Widget_Service_Cp {
	/** 运营团队 */
	const CP_GIONEE = 1;
	/** 搜狐组图 */
	const CP_SOHU_PHOTO = 101;
	/** i时尚 */
	const CP_SHISHANG = 102;
	/** 腾讯 */
	const CP_QQ = 103;
	/** 海朴 */
	//const CP_HAIPU = 104;
	/** 搜狐新闻 */
	const CP_SOHU_NEWS = 105;
	/** 阅读 */
	const CP_IREADER = 106;
	/** 游戏大厅 */
	const CP_GAME = 107;
	/** 购物大厅 */
	const CP_SHOP = 108;
	/** 凤凰网 */
	const CP_IFENG = 109;
	/** 搜狐文摘 */
	const CP_SOHU_RSS = 110;
	/** 搜狐视频 */
	const CP_SOHU_VOD = 111;
	/** 豆瓣 */
	const CP_DOUBAN = 112;
	/** 新浪 */
	const CP_SINA = 113;
	/** 阅时尚 */
	const CP_YSS = 114;
	/** 搜狐组图web */
	const CP_SOHU_WAP = 115;
	/** 搜狐长视频 */
	const CP_SOHU_VOD_L = 116;
	/** 易用汇 */
	const CP_YYH = 117;
	/** 节操 */
	const CP_JIECAO = 118;
	/** 叽歪 */
	const CP_JIWAI = 119;
	/** 好看 */
	const CP_HAOKAN = 120;
	/** 扎客 */
	const CP_ZAKER = 121;

	/** 数据提供商 array(名称,公司, 统计标记, 参数模式(1单|2多))*/
	static $CpCate = array(
		self::CP_GIONEE     => array('翻翻', '翻翻', 'gionee', 1),
		self::CP_SOHU_PHOTO => array('搜狐组图', '搜狐新闻', 'souhu', 1),
		self::CP_SHISHANG   => array('i时尚', 'i时尚', 'iFashion', 1),
		self::CP_QQ         => array('腾讯新闻', '腾讯新闻', 'tencent', 1),
		//self::CP_HAIPU      => array('海朴', '海朴', 'haipu', 1),
		self::CP_SOHU_NEWS  => array('搜狐新闻', '搜狐新闻', 'souhu', 1),
		//self::CP_IREADER    => array('阅读', '阅读', 'reader', 1),
		self::CP_GAME       => array('游戏大厅', '游戏大厅', 'game', 1),
		self::CP_SHOP       => array('购物大厅', '购物大厅', 'shop', 1),
		self::CP_IFENG      => array('凤凰网', '凤凰网', 'ifeng', 1),
		self::CP_SOHU_RSS   => array('搜狐文摘', '搜狐新闻', 'souhu', 1),
		self::CP_SOHU_VOD   => array('搜狐视频', '搜狐视频', 'souhuvod', 2),
		self::CP_DOUBAN     => array('豆瓣', '豆瓣', 'douban', 1),
		self::CP_SINA       => array('新浪', '新浪', 'sina', 1),
		self::CP_YSS        => array('阅时尚', '阅时尚', 'yueshishang', 2),
		self::CP_SOHU_WAP   => array('搜狐wap', '搜狐新闻', 'souhu', 1),
		self::CP_SOHU_VOD_L => array('搜狐视频(长)', '搜狐视频', 'souhuvod', 2),
		self::CP_YYH        => array('易用汇', '易用汇', 'yyh', 2),
		self::CP_JIECAO     => array('节操', '节操', 'jiecao', 1),
		self::CP_JIWAI      => array('叽歪', '叽歪', 'jiwai', 1),
		self::CP_HAOKAN     => array('好看', '好看', 'haokan', 1),
		self::CP_ZAKER      => array('扎客', '扎客', 'zaker', 1),
	);

	/**
	 * 标准格式数据的CP
	 * @var array
	 */
	static $jsonFormatCp = array(
		Widget_Service_Cp::CP_SHISHANG,
		Widget_Service_Cp::CP_QQ,
		Widget_Service_Cp::CP_IFENG,
		Widget_Service_Cp::CP_DOUBAN,
		Widget_Service_Cp::CP_SINA,
		Widget_Service_Cp::CP_YSS,
		Widget_Service_Cp::CP_SOHU_NEWS,
		Widget_Service_Cp::CP_HAOKAN,
		Widget_Service_Cp::CP_ZAKER,
	);

	/**
	 * 统一相同的CP来源
	 * @param $cpId
	 * @return int
	 */
	public static function unifyCpId($cpId) {
		if ($cpId == Widget_Service_Cp::CP_SOHU_VOD_L) {
			$cpId = Widget_Service_Cp::CP_SOHU_VOD;
		} else if (in_array($cpId, array(Widget_Service_Cp::CP_SOHU_PHOTO, Widget_Service_Cp::CP_SOHU_RSS, Widget_Service_Cp::CP_SOHU_WAP))) {
			$cpId = Widget_Service_Cp::CP_SOHU_NEWS;
		}
		return $cpId;
	}

	/**
	 * 过滤相同的CP来源
	 * @return array
	 */
	public static function filterCpId() {
		$cps = Widget_Service_Cp::$CpCate;
		unset($cps[Widget_Service_Cp::CP_GIONEE]);
		unset($cps[Widget_Service_Cp::CP_SOHU_WAP]);
		unset($cps[Widget_Service_Cp::CP_SOHU_VOD_L]);
		unset($cps[Widget_Service_Cp::CP_SOHU_PHOTO]);
		unset($cps[Widget_Service_Cp::CP_SOHU_RSS]);
		return $cps;
	}

	public static function buildDownUrl($cpId, $param = array()) {
		$webroot = Yaf_Application::app()->getConfig()->webroot;
		$downUrl = $webroot . '/front/res/down?cp_id=' . Widget_Service_Cp::unifyCpId($cpId);
		!empty($param['app_ver']) && $downUrl .= '&ver=' . $param['app_ver'];
		!empty($param['model']) && $downUrl .= '&model=' . $param['model'];
		!empty($param['imei']) && $downUrl .= '&imei=' . $param['imei'];
		return $downUrl;
	}

	/**
	 * 获取cp列表
	 */
	public static function getAll($orderBy = array()) {
		static $list = null;
		if ($list == null) {
			$orderBy['cp_id'] = 'ASC';
			$tmpList          = self::_getDao()->getAll($orderBy);
			$list             = array();
			foreach ($tmpList as $val) {
				$list[$val['id']] = $val;
			}
		}

		return $list;
	}


	public static function getIdsByCp($sync = false) {
		$rcKey = Common::KN_WIDGET_CP_ALL_IDS;
		$ret   = Common::getCache()->get($rcKey);
		if ($sync || empty($ret)) {
			$ret     = array();
			$tmpList = self::_getDao()->getAll(array('cp_id' => 'ASC'));
			foreach ($tmpList as $val) {
				$ret[$val['cp_id']][] = $val['id'];
			}
			Common::getCache()->set($rcKey, $ret, Common::KT_CP_INFO);
		}

		return $ret;
	}

	/**
	 *
	 * @param int $page
	 * @param int $limit
	 * @param array $params
	 */
	public static function getList($page = 1, $limit = 10, $params = array()) {
		$params = self::_cookData($params);
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret   = self::_getDao()->getList($start, $limit, $params, array('cp_id' => 'ASC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * @param int $id
	 */
	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 * @return array
	 */
	public static function getsBy($params, $orderBy = array()) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret = self::_getDao()->getsBy($params, $orderBy);
		return $ret;
	}

	/**
	 *
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}

	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	public static function all() {
		return self::_getDao()->getAll();
	}

	private static function _cookData($data) {
		$fields = array('title','type','resume','cp_id','url','url_iid','last_time','md5_data');
		$tmp    = Common::cookData($data, $fields);
		return $tmp;
	}

	/**
	 * 2.0.2版本以前 只有 搜狐和i时尚 来源
	 * 非桌面版本, 桌面版本初始1.0.1 可已包含所有来源
	 * zv 桌面版本 | ov 第三方版本 | 空 锁屏版本
	 * @param string $ver
	 * @return boolean
	 */
	public static function isVer1($ver) {
		$ret = false;
		if (stristr($ver, 'zv') || stristr($ver, 'ov')) {
			$ret = false;
		} else if (self::compareVer($ver, '2.0.2')) {
			$ret = true;
		}
		return $ret;
	}

	public static function compareVer($ver, $verOther) {
		if (stristr($ver, 'zv')) {
			$ver = str_replace('zv', '', $ver);
		}

		$ret = '2.0.1'; //接口的最小版本号
		if (!empty($ver)) {
			$tmp  = explode('.', trim($ver));
			$ver0 = isset($tmp[0]) ? intval($tmp[0]) : 0;
			$ver1 = isset($tmp[1]) ? intval($tmp[1]) : 0;
			$ver2 = isset($tmp[2]) ? intval($tmp[2]) : 0;
			$ret  = "{$ver0}.{$ver1}.{$ver2}";
		}

		return (version_compare($ret, $verOther) < 0);
	}

	/**
	 *
	 * @return Widget_Dao_Cp
	 */
	private static function _getDao() {
		return Common::getDao("Widget_Dao_Cp");
	}
}
