<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 聚合新闻管理
 * @author huwei
 *
 */
class Gionee_Service_Jhnews {

	public static $columns = array(
		'product' => array('1' => 101, '3' => 11016, '4' => 11014, '9' => 11015),
		'test'    => array('1' => 101, '3' => 104, '4' => 102, '9' => 103),
	);

	/**
	 *
	 * @param int $id
	 */

	public static function get($id) {
		return self::_getDao()->get(intval($id));
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getListByType($type) {
		if (!$type) return false;
		$ret   = self::_getDao()->getListByType(intval($type));
		$total = self::_getDao()->count(array('type_id' => intval($type)));
		return array($total, $ret);
	}

	/**
	 *
	 * @param unknown_type $type
	 *
	 * @return boolean|multitype:unknown
	 */
	public static function getCanUseNews() {
		$ret   = self::_getDao()->getCanUseNews();
		$total = self::_getDao()->count(array('status' => '1'));
		return array($total, $ret);
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getTopNews($type) {
		if (!type) return false;
		$ret   = self::_getDao()->getBy(array('istop' => 1, 'type_id' => intval($type)));
		$total = self::_getDao()->count(array('istop' => 1, 'type_id' => intval($type)));
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $id
	 */
	public static function getNewsList($types, $limit, $params = array()) {
		if (!is_array($types)) return false;
		$params = self::_cookData($params);
		return self::_getDao()->getNewsList($types, $limit, $params);

	}


	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 */
	public static function getList($page = 1, $limit = 20, $params = array()) {
		if ($page < 1) $page = 1;
		$start  = ($page - 1) * $limit;
		$params = self::_cookData($params);
		$ret    = self::_getDao()->getList(intval($start), intval($limit), $params);
		$total  = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * get by
	 */
	public static function getBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public static function getsBy($params, $orderBy) {
		if (!is_array($params) || !is_array($orderBy)) return false;
		$ret   = self::_getDao()->getsBy($params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */
	public static function addNews($data) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}


	/**
	 *
	 * 批量插入
	 *
	 * @param array $data
	 */
	public static function batchAddNews($data) {
		if (!is_array($data)) return false;
		self::_getDao()->mutiInsert($data);
		return true;
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */

	public static function updateNews($data, $id) {
		if (empty($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}

	/**
	 * 批量修改显示状态
	 *
	 * @param array $ids
	 *
	 * @return boolean
	 */
	public static function updateStatusByIds($ids, $status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateStatusByIds($ids, $status);
	}

	/**
	 * 批量修改置顶状态
	 *
	 * @param array $ids
	 *
	 * @return boolean
	 */
	public static function updateTopById($id, $status) {
		if (!intval($id)) return false;
		return self::_getDao()->updateTopById($id, $status);
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $uid
	 */
	public static function deleteNews($id) {
		return self::_getDao()->delete(intval($id));
	}


	/**
	 *
	 * Enter description here ...
	 */
	public static function deleteByType($type) {
		if (!$type) return false;
		return self::_getDao()->deleteByType(intval($type));
	}

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $data
	 */
	public static function _cookData($data) {
		$tmp = array();
		if (isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if (isset($data['title'])) $tmp['title'] = $data['title'];
		if (isset($data['color'])) $tmp['color'] = $data['color'];
		if (isset($data['url'])) $tmp['url'] = $data['url'];
		if (isset($data['ontime'])) $tmp['ontime'] = strtotime($data['ontime']);
		if (isset($data['start_time'])) $tmp['start_time'] = strtotime($data['start_time']);
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['is_ad'])) $tmp['is_ad'] = $data['is_ad'];
		if (isset($data['istop'])) $tmp['istop'] = $data['istop'];
		if (isset($data['type_id'])) $tmp['type_id'] = $data['type_id'];
		if( isset($data['cp_id']))		$tmp['cp_id']  = $data['cp_id'];
		return $tmp;
	}

	/**
	 *
	 * get news time
	 *
	 * @param int $time
	 */
	public static function newsTime($time) {
		if (!$time) return false;
		$now_time = Common::getTime();
		$poor     = $now_time - $time; //时间差
		$min      = floor($poor / 60);
		$hour     = floor($poor / 3600);
		if ($poor <= 3600) return $min . "分钟前";
		if ($poor > 3600 and $poor <= 3600 * 24) return $hour . "小时前";
		if ($poor > 86400) return date('m月d日', $time);

	}

	//计划任务中的数据处理
	public static function getData($url, $columnId) {
		$content = file_get_contents($url);
		if (empty($content)) {
			return false;
		}

		$rss      = json_decode($content, true);
		$newslist = $rss['news'];

		$where   = array('is_interface' => 1, 'type_id' => 2, 'column_id' => $columnId);
		$orderBy = array('sort' => 'ASC', 'id' => 'ASC');
		$list    = Gionee_Service_Ng::getsBy($where, $orderBy); //获取记录

		if ($newslist[0]['title'] != $list[0]['title']) {
			$i = 0;

			foreach ($list as $value) { //存在的数据记录
				$v              = $newslist[$i]; //抓取的新闻数据记录
				$where['title'] = $v['title'];
				$tmpRow         = Gionee_Service_Ng::getBy($where);//是否存在标题相同
				if (!empty($tmpRow['id'])) {
					continue;
				}

				$i++;
				if ($v && mb_strlen($v['title'], 'utf8') >= 5) { //新闻标题字数在5个以下的抓取下一条新闻
					$data['title']       = $v['title'];
					$data['link']        = $v['url'];
					$data['img']         = '';
					$data['create_time'] = $v['timestamp'];
					$data['start_time']  = time();
					$data['end_time']    = strtotime("+1 week");

					/**
					 * $data['img']         = $v['thumbnails_qqnews']['qqnews_thu_big'];
					 * $newImg = self::downNewsImage($v['thumbnails_qqnews']['qqnews_thu_big']);
					 * if ($newImg) {
					 * $data['img'] = $newImg;
					 * }
					 */

					//rss数据 前面第i条
					Gionee_Service_Ng::update($data, $value['id']);
				}
			}
			return true;
		}

		return false;
	}

	function downNewsImage($url) {
		if (empty($url)) {
			return false;
		}

		$dir = Common::getConfig('siteConfig', 'attachPath') . '/news/' . date('Ymd') . '/';
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		//文件保存路径
		$ch      = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$img = curl_exec($ch);
		curl_close($ch);

		$tmp_file = "/tmp/" . crc32($url);
		file_put_contents($tmp_file, $img);
		if (file_exists($tmp_file)) {
			$ext = end(explode("/", mime_content_type($tmp_file)));

			if ($ext == 'jpeg') {
				$ext = 'jpg';
			}
			$filename  = md5($url) . "." . $ext;
			$localFile = $dir . "/" . $filename;
			@copy($tmp_file, $localFile);
			@unlink($tmp_file);
			file_put_contents($tmp_file, $localFile);
			return '/news/' . date('Ymd') . '/' . $filename;
		}
		return false;
	}

	/**
	 * 导航新闻内容抓取
	 */
	static public function run() {
		$out         = '';
		$sources1    = Common::getConfig('outnewsConfig', 'news');
		$top_news    = $sources1['qq'];
		$topNewsFlag = 0;
		foreach (Gionee_Service_Jhnews::$columns[ENV] as $k => $columnId) {
			$url = $top_news[$k]['url'];
			$ret = Gionee_Service_Jhnews::getData($url, $columnId);
			if ($ret) {
				$topNewsFlag++;
			}
			$out .= date('Y-m-d H:i:s') . ":{$columnId}:{$url}:{$ret}\n";
		}

		if ($topNewsFlag > 0) { //清除缓存数据
			$rcKey = 'NG:2';
			Common::getCache()->delete($rcKey);
			Gionee_Service_Config::setValue('APPC_Front_Nav_index', Common::getTime());
		}
		return $out;
	}

	/**
	 * 金立内部接口
	 * @return string
	 */
	static public function runGrabGionee() {
		$jh_gionee = Common::getConfig('apiConfig', 'jh_gionee');
		$out       = array();
		foreach ($jh_gionee as $key => $value) {
			if ($value['url']) {
				$content = file_get_contents($value['url']);
				if ($content) {
					$rss   = json_decode($content, true);
					$items = $rss['data'];
					if (is_array($items)) {
						$news_titles = array();

						$data = array();
						foreach ($items as $k => $v) {
							$unique = crc32($v['title']);
							if (!in_array($unique, $news_titles)) {
								$data[$k]['id']         = '';
								$data[$k]['sort']       = $v['sort'] ? $v['sort'] : 0;
								$data[$k]['type_id']    = $key;
								$data[$k]['title']      = $v['title'];
								$data[$k]['color']      = '';
								$data[$k]['url']        = $v['link'];
								$data[$k]['ontime']     = strtotime($v['pubDate']);
								$data[$k]['status']     = 0;
								$data[$k]['is_ad']      = 0;
								$data[$k]['istop']      = 0;
								$data[$k]['start_time'] = Common::getTime();
							}
							$out[] = $v['title'];
							array_push($news_titles, $unique);
						}

						//清空原数据
						Gionee_Service_Jhnews::deleteByType($key);

						//插入数据
						Gionee_Service_Jhnews::batchAddNews($data);

						//更新新闻显示状态 随机显示6条
						list($total, $news) = Gionee_Service_Jhnews::getListByType($key);
						$news = Common::resetKey($news, 'id');
						$ids  = array_slice(array_keys($news), 0, 6);
						Gionee_Service_Jhnews::updateStatusByIds($ids, 1);
					}
				}
			}
		}
		return implode(',', $out);
	}

	/**
	 *
	 * @return Gionee_Dao_Jhnews
	 */
	private static function _getDao() {
		return Common::getDao("Gionee_Dao_Jhnews");
	}
}
