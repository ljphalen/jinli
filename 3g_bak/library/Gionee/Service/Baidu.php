<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 百度合作商 处理类
 *
 */
class Gionee_Service_Baidu {
	const WIDGET = 10; //默认的百度热词排序号

	static public function getNavIndexWrods() {
		//百度默认关键字
		$hotwords = Common::getCache()->get("baidu_hotwords");
		$max_l    = 18;
		$words    = array();
		$words[]  = array_shift($hotwords);
		foreach ($hotwords as $val) {
			$l = mb_strlen($val['text'], 'utf8');
			if ($l < $max_l) {
				$max_l -= $l;
				$words[] = $val;
			}
			if ($max_l < 0 || count($words) == 2) {
				break;
			}
		}
		if (empty($words)) {
			$words[] = array('text' => $hotwords['text'], 'url' => '');
		}
		return $words;
	}

	/**
	 * 抓取百度热词
	 * @return array
	 */
	static public function takeKeywords() {
		$baidu   = 'http://top.baidu.com/gen_json?b=1';
		$content = file_get_contents($baidu);
		$rss     = json_decode($content, true);
		$items   = $rss['data'];
		return $items;
	}

	public static function formatBaibuHotWords(){
		$items = Gionee_Service_Baidu::takeKeywords();
		//搜狐新闻页百度关键字
		$keywords = array();
		foreach ($items as $k => $v) {
			if ($k == 1 || mb_strlen($v['word'], 'utf8') < 6) {
				$keywords[] = $v['word'];
			}
		}
		
		//导航页百度热词
		$hotwords = array();
		foreach ($items as $k => $v) {
			$hotwords[] = $v['word'];
		}
		
		$tmpWords = array($keywords[0], $keywords[1], $keywords[2], $keywords[3]);
		$hotwords = array_diff($hotwords, $tmpWords);
		return array($hotwords,$keywords);
	}
	/**
	 * 抓取神马热词
	 * @return  array
	 */
	public static function getSmHotwords() {
		$data    = array();
		$smHot   = 'http://m.sm.cn/api/rest?method=tools.hot&size=0';
		$content = file_get_contents($smHot);
		return json_decode($content, true);
	}

	public static function formatSmHotWords(){
		
		$keywords =$hotwords = array();
		$items = self::getSmHotwords();
		foreach($items as $m=>$n){
			$hotwords[] = $n['title'];
			if (mb_strlen($n['title'], 'utf8') <= 7) {
				$keywords[] = $n['title'];
			}
		}
		return array($hotwords,$keywords);
	}
	/**
	 * 过滤热词
	 *
	 */
	static public function updateKeywords() {
        $items = array();
		list($hotwords,$keywords) = self::formatBaibuHotWords();
		array_shift($hotwords);//删除第一个热词
		//手动添加的热词内容
		$prev        = $next = array();
		$t_bi        = Util_Cookie::get('3G-SOURCE', true);
		$manualWords = Common::getCache()->get("manual_words");
		if (empty($manualWords['prev']) && empty($manualWords['next'])) {
			$where    = array(
				'column_id'  => self::getColumnID(),
				'status'     => 1,
				'start_time' => array('<=', time()),
				'end_time'   => array('>=', time())
			);
			$dataList = Gionee_Service_Ng::getsBy($where, array('sort' => 'ASC', 'id' => 'DESC'));
			foreach ($dataList as $v) {
				$temp = array('text' => $v['title'], 'url' => Common::clickUrl($v['id'], 'NAV', $v['link'], $t_bi));
				if ($v['sort'] <= self::WIDGET) { //由于按升序排列，故sort值越小越在前面
					$prev[] = $temp;
				} else {
					$next[] = $temp;
				}
			} 
			$manualWords = array('prev' => $prev, 'next' => $next);
			Common::getCache()->set('manual_words', $manualWords, 60);
		}
		//加入手动词后
		$finalHotWords = array();
		foreach ($hotwords as $v) {
			$bdurl           = Common::clickUrl(-100, 'BAIDU_HOT', self::getSearchUrl(urlencode($v)), $t_bi);
			$finalHotWords[] = array('text' => $v, 'url' => $bdurl);
		}
		$finalHotWords = array_merge(array_merge($manualWords['prev'], $finalHotWords), $manualWords['next']);

		Common::getCache()->set("baidu_keywords", $keywords, Common::T_ONE_DAY);
		Common::getCache()->set("baidu_hotwords", $finalHotWords, Common::T_ONE_DAY);

		return array('items' => $items, 'keywords' => $keywords, 'hotwords' => $finalHotWords);
	}

	static public function apiKeys() {
		$res = Common::getCache()->get("baidu_hotwords");
		if (!$res) {
			$dataList = self::updateKeywords();
			$res      = $dataList['hotwords'] ? $dataList['hotwords'] : '';
		}
		return $res;
	}

	static public function getFromNo() {
		//百度渠道号
		if (stristr($_SERVER['HTTP_HOST'], 'mkox')) {
			$baidu_num = '1000472e';
		} else {
			$config = Gionee_Service_Config::getValue('baidu_hotwords_index');
			$data = json_decode($config,true);
			$baidu_num = $data['local_nav'];
		}
		return $baidu_num;
	}

	/**
	 * 获得热词栏目ID
	 */
	public static function getColumnID() {
		$host = Common::getCurHost();
		if (strpos($host, '3gtest')) {
			$columd_id = 11020; //测试站
		} else {
			$columd_id = 12035; //线上
		}
		return $columd_id;
	}


	//获取渠道搜索内容
	private static function _getChanel($type, $val) {
		$urlString = '';
		if (!in_array($type, array('baidu', 'sm'))) return $urlString;
		if ($type == 'baidu') {
			$urlString = 'http://m.baidu.com/s?from=' . self::getFromNo() . '&word=';
		} elseif ($type == 'sm') {
			$urlString = 'http://m.yz.sm.cn/s?from=100080&q=';
		}
		return $urlString . $val;
	}
	
	
	/**
	 * 得到搜索渠道号信息
	 */
	public  static function getSearchUrl($val){
		$config = Gionee_Service_Config::getValue('baidu_hotwords_index');
		$configData = json_decode($config,true);
		if(!empty($configData['link'])){
			$url  = sprintf($configData['link'],$configData['local_nav'],$val);
		}else{
			$url =  'http://m.baidu.com/s?from=' . self::getFromNo() . '&word='.$val;
		}
		return $url;
	}
}

