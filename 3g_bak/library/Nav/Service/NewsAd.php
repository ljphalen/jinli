<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 *二级栏目-新闻 广告
 * @author huwei
 *
 */
class Nav_Service_NewsAd {

	static public function getNewsPos() {
		$where      = array('group' => 'news');
        $ret = array();
		$columnList = Nav_Service_NewsDB::getColumnDao()->getsBy($where, array('sort' => 'asc'));
		foreach ($columnList as $val) {
			$key1       = 'nav_news_list_' . $val['id'];
			$key2       = 'nav_news_list_txt_' . $val['id'];
			$key3       = 'nav_news_content_' . $val['id'];
			$key4       = 'nav_news_reclink_' . $val['id'];
			$key5       = 'nav_new_content_ad_'.$val['id'];
			$ret[$key1] = $val['title'] . '_列表banner';
			$ret[$key2] = $val['title'] . '_列表文字链';
			$ret[$key3] = $val['title'] . '_内容banner';
			$ret[$key4] = $val['title'] . '_推荐文字链';
			$ret[$key5] = $val['title'] . '_内容广告';
		}
		return $ret;
	}


    static public function getFunPos() {
        $where      = array('group' => 'fun');
        $ret = array();
        $columnList = Nav_Service_NewsDB::getColumnDao()->getsBy($where, array('sort' => 'asc'));
        foreach ($columnList as $val) {
            $key1       = 'nav_fun_list_' . $val['id'];
            $key2       = 'nav_fun_list_txt_' . $val['id'];
            $key3       = 'nav_fun_content_' . $val['id'];
            //$ret[$key1] = $val['title'] . '_列表banner';
            //$ret[$key2] = $val['title'] . '_列表文字链';
            $ret[$key3] = $val['title'] . '_内容banner';
        }
        return $ret;
    }

    static public function getPicPos() {
        $where      = array('group' => 'pic', 'pid'=>0);
        $ret = array();
        $columnList = Nav_Service_NewsDB::getColumnDao()->getsBy($where, array('sort' => 'asc'));
        foreach ($columnList as $val) {
            $key1       = 'nav_fun_list_' . $val['id'];
            $key2       = 'nav_fun_list_txt_' . $val['id'];
            $key3       = 'nav_fun_content_' . $val['id'];
            //$ret[$key1] = $val['title'] . '_列表banner';
            //$ret[$key2] = $val['title'] . '_列表文字链';
            $ret[$key3] = $val['title'] . '_内容banner';
        }
        return $ret;
    }


    static public function getInfo($id,$sync=false) {
        $rcKey = 'Nav_Service_NewsAd:INFO:' . $id;
        $ret   = Common::getCache()->get($rcKey);
        if ($ret === false || $sync) {
            $ret = Nav_Service_AdDB::getListDao()->get($id);
            Common::getCache()->set($rcKey, $ret, 600);
        }
        return $ret;
    }

	static public function getListByPos($pos, $sync = false) {
		$rcKey = 'Nav_Service_NewsAd:getListByPos:' . $pos;
		$ret   = Common::getCache()->get($rcKey);
		if ($ret === false || $sync) {
			$now    = time();
			$where  = array(
				'pos'        => $pos,
				'status'     => 1,
				'start_time' => array('<=', $now),
				'end_time'   => array('>=', $now)
			);
			$tmpAds = Nav_Service_AdDB::getListDao()->getsBy($where, array('sort' => 'asc'));
			$ret    = array();
			foreach ($tmpAds as $val) {
				$ret[] = array(
					'id'    => $val['id'],
					'title' => $val['title'],
					'img'   => Common::getImgPath() . $val['img'],
					'url'   => Common::clickUrl($val['id'], 'NAVAD', $val['link'], ''),
					'created_at' => $val['created_at'],
				);
			}
			Common::getCache()->set($rcKey, $ret, 600);
		}
		return $ret;

	}

}