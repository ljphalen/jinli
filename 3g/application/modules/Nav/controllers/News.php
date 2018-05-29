<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 二级栏目-新闻
 */
class NewsController extends Front_BaseController {

    public function indexAction() {
        $t_bi     = $this->getSource();
        $postData = $this->getInput(array('type', 'column_id'));
        if (!empty($postData['type']) && $postData['type'] == 'back' && intval($postData['column_id'])) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, $postData['column_id'] . ':back_index');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, $postData['column_id'] . ':back_index', $t_bi);
        }
        if (!empty($postData['type']) && $postData['type'] == 'more') {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, 'more');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, 'more', $t_bi);
        }
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, 'index');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, 'index', $t_bi);

    }

    public function columnAction() {

        $ret = array(
            'list' => Nav_Service_NewsData::getColumnList('news'),
        );
        $this->output(0, '', $ret);
    }

    public function toAction() {
        $args = array(
            'column_id' => FILTER_VALIDATE_INT,
            'url'       => FILTER_SANITIZE_STRING,
            'crc'       => FILTER_VALIDATE_INT,
        );

        $params    = filter_input_array(INPUT_GET, $args);
        $column_id = $params['column_id'];
        $url       = urldecode($params['url']);

        if (crc32($column_id . $url) == $params['crc']) {
            $this->_statUrl($column_id);
            Common::redirect($url);
        }
        exit;
    }

    private function _statUrl($column_id) {
        $t_bi = $this->getSource();
        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, $column_id . ':to');
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, $column_id . ':to', $t_bi);
    }

    public function listAction() {
        //appId=123&limit=10&type=[recomm 默认|up 上拉历史数据|down 下拉刷新数据]&nextId
        $args = array(
            'appId'  => FILTER_VALIDATE_INT,
            'nextId' => FILTER_VALIDATE_INT,
            'limit'  => FILTER_VALIDATE_INT,
            'ver'    => FILTER_VALIDATE_INT,
        );

        $params   = filter_input_array(INPUT_GET, $args);
        $columnId = $params['appId'];
        $nextId   = intval($params['nextId']);
        $ver      = $params['ver'];
        if (empty($columnId)) {
            exit;
        }

        $this->_statList($columnId, $nextId);
        $page = max($nextId, 1);

        $key = "NAV_NEWS:{$columnId}_{$page}_{$ver}";
    
        $ret = Cache_APC::get($key);
        if (empty($ret)) {//主要目的 发push的时候  redis承受不住带宽压力  所以添加apc缓存60s
            $ret = Nav_Service_NewsData::getList($columnId, $page, $ver);
            Cache_APC::set($key, $ret);
        }


        $tmp         = $this->_adList($columnId, $page);
        $ret['list'] = array_merge($tmp, $ret['list']);

        $this->output(0, '', $ret);

    }

    private function _statList($columnId, $nextId) {
        $t_bi = $this->getSource();
        if ($nextId == -2) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, $columnId . ':column');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, $columnId . ':column', $t_bi);
        } else if ($nextId == -1) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, $columnId . ':column_last');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, $columnId . ':column_last', $t_bi);
        } else if ($nextId > 0) {
            Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, $columnId . ':column_page');
            Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, $columnId . ':column_page', $t_bi);
        }
    }

    private function _adList($columnId, $page) {
        $listads = Nav_Service_NewsAd::getListByPos('nav_news_list_txt_' . $columnId);
        $tmp     = array();
        if (!empty($listads) && $page == 1) {
            foreach ($listads as $v) {
                $tmp        = array();
                $formatTime = Common::formatDate($v['created_at']);
                $tmp[]      = array(
                    'cdnimgs'    => array($v['img']),
                    'appId'      => $columnId,
                    'detailId'   => $v['id'],
                    //'outId'      => $val['out_id'],
                    'title'      => $v['title'],
                    'postTime'   => $v['created_at'],
                    'formatTime' => $formatTime,
                    //'skip'       => false,
                    'url'        => $v['url'],
                );
            }
        }
        return $tmp;
    }

    public function detailAction() {
        
    	list($id, $act) = $this->getInputParam ();
        $info = $this->getNewsDetailInfoCacheData ( $id );
        if (empty($info['id'])) {
            exit;
        }

        $columnId = $info['column_id'];
        $this->_statDetail($columnId, $act);
        if (empty($info['content']) || $info['skip_status'] == 1 ) {
            Common::redirect($info['link']);
            exit;
        }
        
        $banner   = Nav_Service_NewsAd::getListByPos('nav_news_content_' . $columnId);
        $reclink  = Nav_Service_NewsAd::getListByPos('nav_news_reclink_' . $columnId);
        $cotentAd = Nav_Service_NewsAd::getListByPos('nav_new_content_ad_' . $columnId);
        //新闻tab列表
        $columnList = Nav_Service_NewsData::getColumnList('news');

        $this->assign('info', $info);
        $this->assign('banner', $banner);
        $this->assign('reclink', $reclink);
    }
	
	private function getNewsDetailInfoCacheData($id) {
		$key = $this->getNewInfoKey ($id);
		$info = Cache_APC::get($key);
        if (empty($info)) {//主要目的 发push的时候  redis承受不住带宽压力  所以添加apc缓存60s
            $info = Nav_Service_NewsData::getRecordInfo($id);
            Cache_APC::set($key, $info);
        }
        return $info;
	}

	
	
	private function getNewInfoKey($id) {
		$key  = "NAV_NEWS_INFO:{$id}";
		return $key;
	}

	
	
	private function getInputParam() {
		$args   = array(
            'id'  => FILTER_VALIDATE_INT,
            'act' => FILTER_SANITIZE_STRING,
        );
        $params = filter_input_array(INPUT_GET, $args);
        $id     = $params['id'];
        $act    = $params['act'];
		return array($id, $act);
	}


    private function _statDetail($columnId, $act) {
        $t_bi = $this->getSource();
        if ($act == 'rec') {
            $val = $columnId . ':detail_rec';
        } else if ($act == 'h5') {
            $val = $columnId . ':detail_h5';
        } else if ($act == 'card') {
            $val = $columnId . ':detail_card';
        } else {
            $val = $columnId . ':detail';
        }

        Gionee_Service_Log::incrBy(Gionee_Service_Log::TYPE_NAVNEWS_PV, $val);
        Gionee_Service_Log::toUVByCacheKey(Gionee_Service_Log::TYPE_NAVNEWS_UV, $val, $t_bi);
    }
}