<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 *
 * 二级栏目-新闻 数据
 * @author huwei
 *
 */
class Nav_Service_NewsData {
	
	static private  $DEAFAULT_COLUMN = array('appId'   => 0,
    								         'appName' => '全部',
    					                     'status'  => 1,
    					                     'locked'  => 1,
			                         );

    static public function getColumnList($cate, $sync = false) {
        $rcKey = 'Nav_Service_NewsData:ColumnList:' . $cate;
        $list  = Common::getCache()->get($rcKey);
        if (empty($list) || $sync) {
            $where      = array('status' => 1, 'group' => $cate);
            $columnList = Nav_Service_NewsDB::getColumnDao()->getsBy($where, array('sort' => 'asc'));
            $list       = array();
            foreach ($columnList as $val) {
                $list[] = array(
                    'appId'   => $val['id'],
                    'appName' => $val['title'],
                    'status'  => $val['is_selected'],
                    'locked'  => $val['is_locked'],
                );
            }
            Common::getCache()->set($rcKey, $list, 600);
        }
        return $list;
    }
    
    // 美图的栏目列表
    static public function getMettleCloumnList($source = 'pic', $sync = false){
    	$rcKey = 'Nav_Service_NewsData:ColumnList:' . $source;
    	$list  = Common::getCache()->get($rcKey);
    	if (empty($list) || $sync) {
    		$where      = array('status' => 1, 'group' => $source, 'pid'=>0);
    		$columnList = Nav_Service_NewsDB::getColumnDao()->getsBy($where, array('sort' => 'asc'));
    		$list       = array();
    		foreach ($columnList as $val) {
    			$subList = self::getSubMettleCloumnList($source, $val['id']);
    			$list[] = array(
    					'appId'   => $val['id'],
    					'appName' => $val['title'],
    					'status'  => $val['is_selected'],
    					'locked'  => $val['is_locked'],
    					'subList' => $subList
    			);
    		}
    		Common::getCache()->set($rcKey, $list, 600);
    	}
    	return $list;
    }
    
    static public function getSubMettleCloumnList($source = 'pic', $pid){
    	$where      = array('status' => 1, 'group' => $source, 'pid'=>$pid);
    	$columnList = Nav_Service_NewsDB::getColumnDao()->getsBy($where, array('sort' => 'asc'));
    	
    	$list       = array();
    	foreach ($columnList as $val) {
    		$list[] = array(
    				'appId'   => $val['id'],
    				'appName' => $val['title'],
    				'status'  => $val['is_selected'],
    				'locked'  => $val['is_locked'],
    		);
    	}
    	return $list;
    }
    

    static public function getRecordInfo($id, $sync = false) {
        $id = intval($id);
        if (empty($id)) {
            return array();
        }
        $rcKey = 'Nav_Service_NewsData:getRecordInfo:' . $id;
        $ret   = Common::getCache()->get($rcKey);
        if ($ret === false || $sync) {
            $ret  = array();
            $info = Nav_Service_NewsDB::getRecordDao()->get($id);
            if (!empty($info['id'])) {

                $sourceInfo = Nav_Service_NewsDB::getSourceDao()->get($info['source_id']);

                $sourceIds = self::getSourceIds($sourceInfo['column_id']);
                $lastList  = self::_nextData($info, $sourceIds);
                $prevList  = self::_prevData($info, $sourceIds);

                $formatTime = Common::formatDate($info['out_created_at']);

                $crc = crc32($sourceInfo['column_id'] . $info['link']);
                self::_fixpic_wh($sourceInfo['column_id'], $info);

                $img = $info['img'];
                if (!empty($img) && substr($img, 0, 4) != 'http') {
                    $img = Common::getImgPath() . $img;
                }

                $ret = array(
                    'id'         => $info['id'],
                    'detailId'   => $info['id'],
                    'title'      => $info['title'],
                    'img'        => $img,
                    'content'    => json_decode($info['content'], true),
                    'from'       => $sourceInfo['title'],
                    'source_id'  => $sourceInfo['id'],
                    'column_id'  => $sourceInfo['column_id'],
					'skip_status'=> $sourceInfo['skip_status'],
                    'formatTime' => $formatTime, //格式化日期
                    'link'       => Common::getCurHost() . '/nav/' . $info['group'] . '/to?crc=' . $crc . '&column_id=' . $sourceInfo['column_id'] . '&url=' . urlencode($info['link']),
                    'lastList'   => $lastList,
                    'prevList'   => $prevList,
                );
            }
            Common::getCache()->set($rcKey, $ret, Common::T_TEN_MIN);
        }

        return $ret;
    }

    /**
     * 图片栏目宽高获取数据校正
     *
     * @param $columnId
     * @param $info
     */
    static private function _fixpic_wh($columnId, $info) {
        if ($columnId == 32 && empty($info['img_wh']) && !empty($info['img'])) {
            $imgInfo = getimagesize(Common::getImgPath() . $info['img']);
            if (!empty($imgInfo[0]) && !empty($imgInfo[1])) {
                $upData = array('img_wh' => $imgInfo[0] . 'x' . $imgInfo[1]);
                Nav_Service_NewsDB::getRecordDao()->update($upData, $info['id']);
            }
        }
    }

    static public function getSourceIds($columnId) {
    	if(is_array($columnId) && count($columnId)){
    		$where      = array('column_id' => array('IN', $columnId), 'status' => 1);
    	}else{
    		$where      = array('column_id' => $columnId, 'status' => 1);
    	}
        $sourceList = Nav_Service_NewsDB::getSourceDao()->getsBy($where);
        $sourceIds  = array();
        foreach ($sourceList as $val) {
            $sourceIds[] = $val['id'];
        }
        return $sourceIds;
    }


    static private function _nextData($info, $sourceIds) {
        $where   = array(
            'status'         => 1,
            'source_id'      => array('in', $sourceIds),
            'out_created_at' => array('<', $info['out_created_at'])
        );
        $orderBy = array('out_created_at' => 'desc');

        if ($info['group'] == 'pic' || $info['group'] == 'fun') {
            unset($where['out_created_at']);
            $where['id'] = array('<', $info['id']);
            $orderBy     = array('id' => 'desc');
        }
        $tmpList  = Nav_Service_NewsDB::getRecordDao()->getList(0, 3, $where, $orderBy);
        $lastList = array();
        foreach ($tmpList as $val) {
            $lastList[] = array(
                'id'    => $val['id'],
                'title' => $val['title'],
                'link'  => Common::getCurHost() . '/nav/' . $info['group'] . '/detail?act=rec&id=' . $val['id'],
            );
        }
        return $lastList;
    }

    static private function _prevData($info, $sourceIds) {
        $where   = array(
            'status'         => 1,
            'source_id'      => array('in', $sourceIds),
            'out_created_at' => array('>', $info['out_created_at'])
        );
        $orderBy = array('out_created_at' => 'asc');
        if ($info['group'] == 'pic' || $info['group'] == 'fun') {
            unset($where['out_created_at']);
            $where['id'] = array('>', $info['id']);
            $orderBy     = array('id' => 'asc');
        }
        $tmpList  = Nav_Service_NewsDB::getRecordDao()->getList(0, 1, $where, $orderBy);
        $prevList = array();
        foreach ($tmpList as $val) {
            $prevList[] = array(
                'id'    => $val['id'],
                'title' => $val['title'],
                'link'  => Common::getCurHost() . '/nav/' . $info['group'] . '/detail?act=rec&id=' . $val['id'],
            );
        }
        return $prevList;
    }


    static public function getWeatherRecordInfo($id, $sync = false) {
        $id = intval($id);
        if (empty($id)) {
            return array();
        }
        $rcKey = 'Nav_Service_NewsData:getRecordInfo:' . $id;
        $ret   = Common::getCache()->get($rcKey);
        if ($ret === false || $sync) {
            $ret  = array();
            $info = Nav_Service_NewsDB::getRecordDao()->get($id);
            if (!empty($info['id'])) {
                $sourceInfo = Nav_Service_NewsDB::getSourceDao()->get($info['source_id']);
                $where      = array(
                    'status'         => 1,
                    'source_id'      => $info['source_id'],
                    'out_created_at' => array('<', $info['out_created_at'])
                );
                $orderBy    = array('out_created_at' => 'desc');
                $tmpList    = Nav_Service_NewsDB::getRecordDao()->getList(0, 3, $where, $orderBy);

                foreach ($tmpList as $val) {
                    $lastList[] = array(
                        'id'    => $val['id'],
                        'title' => $val['title'],
                        'link'  => Common::getCurHost() . '/partner/weather/detail?act=rec&id=' . $val['id'],
                    );
                }

                $formatTime = Common::formatDate($info['out_created_at']);
                $crc        = crc32($sourceInfo['column_id'] . $info['link']);

                $ret = array(
                    'id'         => $info['id'],
                    'detailId'   => $info['id'],
                    'title'      => $info['title'],
                    'content'    => json_decode($info['content'], true),
                    'from'       => $sourceInfo['title'],
                    'source_id'  => $sourceInfo['id'],
                    'column_id'  => $sourceInfo['column_id'],
                    'formatTime' => $formatTime, //格式化日期
                    'link'       => Common::getCurHost() . '/nav/news/to?crc=' . $crc . '&column_id=' . $sourceInfo['column_id'] . '&url=' . urlencode($info['link']),
                    'lastList'   => $lastList,
                );
            }
            Common::getCache()->set($rcKey, $ret, Common::T_TEN_MIN);
        }

        return $ret;
    }

    static private function _buildListData($columnId, $maxPage = 10) {
        $limit      = 20 + ($maxPage - 1) * 10;
        $tmp        = Nav_Service_NewsDB::getColumnDao()->get($columnId);
        $sourceIds  = Nav_Service_NewsData::getSourceIds($columnId);
        $recordList = array();
        if ($sourceIds) {
            $where   = array('source_id' => array('in', $sourceIds), 'status' => 1);
            $orderBy = array('out_created_at' => 'desc');
            if ($tmp['group'] == 'pic' || $tmp['group'] == 'fun') {
                $orderBy = array('id' => 'desc');
            }

            $recordList = Nav_Service_NewsDB::getRecordDao()->getList(0, $limit, $where, $orderBy);
        }
        return $recordList;
    }

    static private function _splitListData($recordList) {
        $ret    = array();
        $ret[1] = array_slice($recordList, 0, 20);
        $arr    = array_slice($recordList, 20, -1);
        $tmpArr = array_chunk($arr, 10);
        foreach ($tmpArr as $vList) {
            $tmp = array();
            foreach ($vList as $v) {
                if ($v['group'] != 'fun') {
                    unset($v['content']);
                }
                $tmp[$v['id']] = $v;
            }
            $ret[] = $tmp;
        }

        return $ret;
    }


    static public function getWeatherList($page, $ver, $sync = false) {
        $columnId = 41;
        $page     = max($page, 0);
        if ($page == 1) {
            $ver = 0;
        }
        $rcKey = sprintf('Nav_Service_NewsData:getList:%s_%s:%s', intval($columnId), intval($page), $ver);
        $ret   = Common::getCache()->get($rcKey);
        if ($sync) {
            self::_toWeatherCache($columnId);
        }
        return $ret;
    }

    static private function _toWeatherCache($columnId, $ver = '') {
        $ver       = empty($ver) ? time() : $ver;
        $ret       = self::_buildListData($columnId);
        $splitList = self::_splitListData($ret);
        $maxNum    = count($splitList);
        $out       = '';

        foreach ($splitList as $page => $recordList) {
            $banner = array();
            if ($page == 1) {
                $banner = Nav_Service_NewsAd::getListByPos('partner_weather_list', true);
                //$ver    = 0;
            }

            $list = self::_formatWeatherListData($recordList, $columnId);
            $ret  = array(
                'banner' => $banner,
                'nextId' => $page == $maxNum ? 0 : $page + 1, //是否有下一页
                'list'   => $list,
                'ver'    => $ver,
            );

            $rcKey = sprintf('Nav_Service_NewsData:getList:%s_%s:%s', intval($columnId), intval($page), ($page == 1) ? 0 : $ver);
            Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY * 7);
            $out .= $columnId . "\t" . $page . "\t" . count($list) . "\n";
        }
        return $out;
    }

    static public function getList($columnId, $page, $ver = 0, $sync = false) {
        $page = max($page, 0);
        if ($page == 1) {
            $ver = 0;
        }
        $rcKey = sprintf('Nav_Service_NewsData:getList:%s_%s:%s', intval($columnId), intval($page), $ver);
        $ret   = Common::getCache()->get($rcKey);
      
        if ($sync) {
            self::_toCache($columnId);
        }
        return $ret;
    }
    
    static public function getMettleList($columnId, $pId, $page, $ver = 0, $sync = false){
    	$page = max($page, 0);
    	if ($page == 1) {
    		$ver = 0;
    	}
    	$rcKey = sprintf('Nav_Service_NewsData:getList:%s_%s_%s:%s', intval($columnId), intval($pId), intval($page), $ver);
    	$ret   = Common::getCache()->get($rcKey);
    	if ($sync) {
    		self::mettleListToCache($columnId, $pId);
    	}
    	return $ret;
    
    }
    
    static private function mettleListToCache($columnId, $pId = '', $ver = ''){
    	$ver       = empty($ver) ? time() : $ver;
    	$ret       = self::_buildMettleListData($columnId, $pId);
    
    	$splitList = self::_splitListData($ret);
    	$maxNum    = count($splitList);
    	$out       = '';
    	foreach ($splitList as $page => $recordList) {
    		$banner = array();
    		if ($page == 1) {
    			$banner = Nav_Service_NewsAd::getListByPos('nav_news_list_' . $columnId, true);
    		}    	
    		$list = self::_formatListData($recordList, $columnId);
    		$ret  = array(
    				'nextId' => $page == $maxNum ? 0 : $page + 1, //是否有下一页
    				'appId'  => $columnId, //栏目ID
    				'pId'=>$pId,
    				'banner' => $banner,   	
    				'list'   => $list,
    				'ver'    => $ver,
    		);
    	
    		$rcKey = sprintf('Nav_Service_NewsData:getList:%s_%s:%s', intval($columnId), intval($page), ($page == 1) ? 0 : $ver);
    	
    		Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
    		if($recordList['group']== 'pic'){
    			$out .= $pId . "\t" .$columnId . "\t" . $page . "\t" . count($list) . "\n";
    		}else {
    			$out .= $columnId . "\t" . $page . "\t" . count($list) . "\n";
    		}
    		
    	}
    	return $out;
    	
    }
    
    static private function _buildMettleListData($columnId, $pId, $maxPage = 10) {
    	$limit      = 20 + ($maxPage - 1) * 10;
    	
    	if($columnId == 0){
    		$ret = Nav_Service_NewsDB::getColumnDao()->getsBy(array('pId'=>$pId));
    		$columnId = Common::resetKey($ret, 'id');
    		$columnId  = array_keys($columnId);
    	}
    
    	$sourceIds  = Nav_Service_NewsData::getSourceIds($columnId);
    	$recordList = array();
    	if ($sourceIds) {
    		$where   = array('source_id' => array('IN', $sourceIds), 'status' => 1);
    		$orderBy = array('sort'=>'DESC', 'id' => 'desc');    
    		$recordList = Nav_Service_NewsDB::getRecordDao()->getList(0, $limit, $where, $orderBy);
    	}
    	return $recordList;
    }
     

    static private function _toCache($columnId, $ver = '') {
        $ver       = empty($ver) ? time() : $ver;
        $ret       = self::_buildListData($columnId);
        $splitList = self::_splitListData($ret);
        $maxNum    = count($splitList);
        $out       = '';
        foreach ($splitList as $page => $recordList) {
            $banner = array();
            if ($page == 1) {
                $banner = Nav_Service_NewsAd::getListByPos('nav_news_list_' . $columnId, true);
                //$ver    = 0;
            }
            $list = self::_formatListData($recordList, $columnId);
            $ret  = array(
                'nextId' => $page == $maxNum ? 0 : $page + 1, //是否有下一页
                'appId'  => $columnId, //栏目ID
                'banner' => $banner,
                'list'   => $list,
                'ver'    => $ver,
            );

            $rcKey = sprintf('Nav_Service_NewsData:getList:%s_%s:%s', intval($columnId), intval($page), ($page == 1) ? 0 : $ver);


            Common::getCache()->set($rcKey, $ret, Common::T_ONE_DAY);
            $out .= $columnId . "\t" . $page . "\t" . count($list) . "\n";
        }
        return $out;
    }

    static public function makeList() {
        $ret = array();
        foreach (array('news', 'fun') as $v) {
            $columnList = self::getColumnList($v, true);
            foreach ($columnList as $val) {
                $columnId       = $val['appId'];
                $ret[$columnId] = self::_toCache($columnId);
            }
        }
        
        //美图数据生成
        $columnList = self::getMettleCloumnList('pic', true);   
        foreach ($columnList as $val) {
        	$pid            = $val['appId'];
        	if(count($val['subList'])){
        		//美图的全部
        		$ret[$columnId] = self::mettleListToCache(0, $pid);
        	}
        	foreach ($val['subList'] as $va){
        			$columnId       = $va['appId'];
        			$ret[$columnId] = self::mettleListToCache($columnId, $pid);
        	}
        }  
        return $ret;
    }

    static private function _formatListData($recordList, $columnId) {
        $list = array();
        foreach ($recordList as $val) {
            $img = $val['img'];
            if (!empty($img) && substr($img, 0, 4) != 'http') {
                if ($val['group'] == 'fun' || $val['group'] == 'pic') {
                    $img = Common::getImgPath() . $img;
                } else {
                    $imgtype = pathinfo($val['img'], PATHINFO_EXTENSION);
                    $img     = Common::getImgPath() . $img . '_180x120.' . $imgtype;
                }
            }

            $formatTime = Common::formatDate($val['out_created_at']);

            $content = '';
            if ($val['group'] == 'fun') {
                $tmp = json_decode($val['content'], true);
                foreach ($tmp as $v) {
                    if ($v['type'] == 1) {
                        $content .= $v['value'];
                    }
                }
            }

            $title = mb_substr($val['title'], 0, 28, 'utf-8') . (mb_strlen($val['title'], 'utf-8') > 28 ? '…' : '');
            $_tmp  = array(
                'cdnimgs'    => array($img),
                'appId'      => intval($columnId),
                'detailId'   => intval($val['id']),
                'title'      => $title,
                'postTime'   => intval($val['out_created_at']),
                'formatTime' => $formatTime,
                'url'        => Common::getCurHost() . '/nav/' . $val['group'] . '/detail?act=list&id=' . $val['id'],
                'content'    => $content,
            );

            $rcKey = 'NAV_FUN_OP:' . intval($val['id']);
            Common::getCache()->hSet($rcKey, 'op_1', intval($val['op_1']));
            Common::getCache()->hSet($rcKey, 'op_2', intval($val['op_2']));
            Common::getCache()->hSet($rcKey, 'c_num', intval($val['c_num']));

            if ($val['group'] == 'fun') {
                $sourceInfo   = Nav_Service_NewsDB::getSourceDao()->get($val['source_id']);
                $_tmp['from'] = $sourceInfo['title'];

                if (!empty($val['img_wh'])) {
                    list($w, $h) = explode('x', $val['img_wh']);
                    $_tmp['img_w'] = $w;
                    $_tmp['img_h'] = $h;
                }
            }

            if ($val['group'] == 'pic' && !empty($val['img'])) {
                $attachPath      = realpath(Common::getConfig("siteConfig", "attachPath"));
                $imgInfo         = getimagesize($attachPath . $val['img']);
                $thu_img_w       = 165;
                $thu_img_h       = floor($imgInfo[1] / ($imgInfo[0] / $thu_img_w));
                $_tmp['img_w']   = $thu_img_w;
                $_tmp['img_h']   = $thu_img_h;
                $_thuimg         = Common::genThumbImg($val['img'], $thu_img_w, $thu_img_h);
                $thu_img         = !empty($_thuimg) ? Common::getImgPath() . $_thuimg : '';
                $_tmp['big_img'] = $img;
                $_tmp['img']     = $thu_img;
            }

            $list[] = $_tmp;
        }
        return $list;
    }

    static private function _formatWeatherListData($recordList, $columnId) {
        $list = array();
        foreach ($recordList as $val) {
            if (substr($val['img'], 0, 4) != 'http') {
                $imgtype    = pathinfo($val['img'], PATHINFO_EXTENSION);
                $val['img'] = Common::getImgPath() . $val['img'] . '_180x120.' . $imgtype;
            }

            $formatTime = Common::formatDate($val['out_created_at']);
            $list[]     = array(
                'cdnimgs'    => array($val['img']),
                'appId'      => $columnId,
                'detailId'   => $val['id'],
                //'outId'      => $val['out_id'],
                'title'      => $val['title'],
                'postTime'   => $val['out_created_at'],
                'formatTime' => $formatTime,
                //'skip'       => false,
                'url'        => Common::getCurHost() . '/partner/weather/detail?id=' . $val['id'],
                //'out_link'   => $val['link'],
            );
        }
        return $list;
    }


}