<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 网址大全分类
 */
class Gionee_Service_SiteContent {

	public static function add($params) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->insert($params);
	}

	public static function update($id, $params = array()) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->update($params, $id);
	}

	public static function get($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get($id);
	}

	public static function getAll() {
		return self::_getDao()->getAll();
	}

	public static function getBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		return self::_getDao()->getBy($params, $orderBy);
	}

	public static function getsBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}

	public static function getList($page, $pageSize, $params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		$params = self::_checkData($params);
		$count  = self::_getDao()->count($params);
		$data   = self::_getDao()->getList($pageSize * ($page - 1), $pageSize, $params, $orderBy);
		return array($count, $data);
	}

	public static function delete($id) {
		if (!intval($id)) return false;
		return self::_getDao()->delete($id);
	}


	public static function getSitesData($sync = false) {
		$data = $hotData = $temp = $column = $subColumn = array();
		$rs   = Common::getCache();
		$key  = 'GIONEE:SITE:MAP';
		$data = $rs->get($key);
		if (empty($data) || !$sync) {
			$cates = Gionee_Service_SiteCategory::getCategoryData();//第一级分类
			foreach ($cates as $k => $v) {
				$column[$v['id']] = $v;
				$subData          = Gionee_Service_SiteCategory::getCategoryData($v['id']);
				foreach ($subData as $m => $n) {
					if ($n['is_show'] == 1) {
						$subColumn[$v['id']][$n['id']] = $n['name'];
					}
					$options               = array();
					$options['cat_id']     = $n['id'];
					$options['status']     = 1;
					$options['start_time'] = array('<=', time());
					$options['end_time']   = array('>=', time());
					$contentData           = Gionee_Service_SiteContent::getsBy($options, array(
						'sort' => 'ASC',
						'id'   => 'DESC'
					));
					//-----start switch ------//
					switch ($n['style']) {
						case 'word5': {
							if ($contentData) {
								$tmpData = array();
								foreach ($contentData as $i => $j) {
									$j['link'] = Common::clickUrl($j['id'], 'SITE', $j['link']);
									if ($j['is_special']) {
										$tmpData['show'][] = $j;
									} else {
										$tmpData['hide'][] = $j;
									}
								}
								$temp[$v['id']][$n['style']][$n['id']] = $tmpData;
							}
							break;
						}
						default: {
							foreach ($contentData as $s => $t) {
								$contentData[$s]['link'] = Common::clickUrl($t['id'], 'SITE', $t['link']);
							}
							if ($n['style'] != 'ads') {
								if ($n['style'] == 'hotnav') {
									$hotData[$n['style']] = $contentData;//导航热词
								} else {
									$temp[$v['id']][$n['style']] = $contentData;
								}
							}
							break;
						}
					}
					//------end switch------//
				}
			}
			$data = array(
				'column'       => $column,
				'scolumn'      => $subColumn,
				'hotContents'  => $hotData,
				'planContents' => $temp,
			);
			$rs->set($key, $data, Common::T_ONE_DAY);
		}
		return $data;
	}


	private static function _checkData($data = array()) {
		$tmp = array();
		if (isset($data['id'])) $tmp['id'] = $data['id'];
		if (isset($data['name'])) $tmp['name'] = $data['name'];
		if (isset($data['cat_id'])) $tmp['cat_id'] = $data['cat_id'];
		if (isset($data['status'])) $tmp['status'] = $data['status'];
		if (isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if (isset($data['start_time'])) $tmp['start_time'] = strtotime($data['start_time']);
		if (isset($data['end_time'])) $tmp['end_time'] = strtotime($data['end_time']);
		if (isset($data['add_time'])) $tmp['add_time'] = $data['add_time'];
		if (isset($data['link'])) $tmp['link'] = $data['link'];
		if (isset($data['image'])) $tmp['image'] = $data['image'];
		if (isset($data['is_special'])) $tmp['is_special'] = $data['is_special'];
		if(isset($data['cp_id']))			$tmp['cp_id'] = $data['cp_id'];
		return $tmp;
	}

	private static function _getDao() {
		return Common::getDao("Gionee_Dao_SiteContent");
	}
}

