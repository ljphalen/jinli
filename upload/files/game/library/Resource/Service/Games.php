<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Resource_Service_Games extends Common_Service_Base{
	const STATE_ONLINE = 1;
	const STATE_OFFLINE = 0;
	const MAX_LOAD = 50;
	const COMBINE_GAME = 1; //联运游戏
	const COMMON_GAME = 2;  //普通游戏

	/**
	 * 根据游戏唯一标识查询游戏信息
	 * @param unknown_type $params 
	 * @param boolean $preview 预览标识
	 * @param boolean $cflag 缓存使用标识
	 * @return boolean
	 */
	public static function getGameAllInfo($params, $preview = false, $cflag = true) {
		$game = array();
		// 内容
		if(!$preview) $params['status']=1;
		$game = Resource_Service_Games::getBy ( $params );
		if (!$game) return false;
		
		if(!$preview){
			if($cflag) {
				$cache = Cache_Factory::getCache();
				$ckey = "-gid-" . $game['id'];
				$cgame = $cache->get($ckey);
				if ($cgame){
					//处理webp图片-v1.5.6
					$cgame = self::processGameImg($cgame);
					return $cgame;
				}
			}
		}
		
		$game_version = Resource_Service_Games::getGameVersionInfo ( $game['id'] );

		if(!$preview){
			if (!$game_version) return false;
		}

		//游戏主分类游戏数据
		$mainCategoryData =  Resource_Service_GameCategory::getMainCategory($game['id']);
		//游戏次分类数据
		$lessCategoryData =  Resource_Service_GameCategory::getLessCategory($game['id']);
				
		//游戏属性信息【最新、最热、首发】
		$hot = Resource_Service_Attribute::getBy(array('id'=>$game['hot']));
		//价格
		$price = Resource_Service_Attribute::getBy(array('id'=>$game['price']));
		//支持外设
		$devices = Resource_Service_Games::getIdxGameResourceDeviceBy(array('game_id' => $game['id']));
		//游戏分数
		$game_score = Resource_Service_Score::getByScore(array('game_id' => $game['id']));
		
		$game['size'] = $game_version['size'];
		$game['version'] = $game_version['version'];
		$game['md5_code'] = $game_version['md5_code'];
		$game['link'] = $game_version['link'];
		$game['package'] = $game['package'];
		$game['min_sys_version'] = $game_version['min_sys_version'];
		$game['min_resolution'] = $game_version['min_resolution'];
		$game['max_resolution'] = $game_version['max_resolution'];
		$game['version_code'] = $game_version['version_code'];
		$game['status'] = $game_version['status'];
		$game['vcreate_time'] = $game_version['create_time'];
		$game['update_time'] = $game_version['update_time'];
		$game['changes'] = $game_version['changes'];
		$game['client_star'] = $game_score['score'] ? $game_score['score']/2 : 0;
		$game['web_star'] = $game_score['score'] ? $game_score['score'] : 0;
		
		$game['version_id'] = $game_version['id'];
		//主分类：父分类数据
		$game['category_title'] = $mainCategoryData['parent']['title'];
		$game['category_id'] = $mainCategoryData['parent']['id'];
		
		$game['min_resolution_title'] = '240*320'; //$min_resolution['title'];
		$game['max_resolution_title'] = '1080*1920'; //$max_resolution['title'];
		$game['min_sys_version_title'] = '1.6'; //$min_sys_version['title'];
		$game['hot_title'] = $hot['title'];
		$game['hot_id'] = $game ['hot'];
		$game['price_title'] = $price['title'];
		$game['device'] = empty($devices) ? 0 : 1;
		//v1.5.6增加主分类
		$game['mainCategory'] = array(
				'parent' =>	array(
						'id'=> $mainCategoryData['parent']['id'],
						'title'=> $mainCategoryData['parent']['title']
						),
				'sub'=> array(
						'id'=> $mainCategoryData['sub']['id'],
						'title'=> $mainCategoryData['sub']['title']
						)
		);
		//v1.5.6增加次分类
		if($lessCategoryData){
			$game['lessCategory'] = array(
				'parent' =>	array(
						'id'=> $lessCategoryData['parent']['id'],
						'title'=> $lessCategoryData['parent']['title']
				),
				'sub'=> array(
						'id'=> $lessCategoryData['sub']['id'],
						'title'=> $lessCategoryData['sub']['title']
				)
			);
		}
		//v1.5.3增加免流量识别 
		//获取该游戏参与的活动今天跟明天的免流量[运营商_地区_活动id]标识符号。
		$game['freedl'] = self::_getFreedlFlag($game['id']);
		//1.5.4包数字签名
		$game['signature_md5'] = $game_version['fingerprint'] ? self::_formartStr($game_version['fingerprint']) : '';
		
		$game['infpage']  = sprintf("%s,%s,%s,%s,Android%s,%s-%s", 
				$game['id'],
				$game['link'],
				$game['package'],
				$game['size'], 
				$game['min_sys_version_title'],
				$game['min_resolution_title'],
				$game['max_resolution_title']);
		//游戏详情图片
		list(, $gimgs) = Resource_Service_Img::getList(1, 10, array('game_id'=>$game['id']));
		foreach($gimgs as $key=>$value) {
			$file = array();
			//大图
			$game['gimgs'][] = $value['img'];
			$file = explode(".", basename($value['img']));
			//小图
			$game['simgs'][] = $value['img'] . '_240x400.' . $file[1];
		}
		
		//icon 特殊处理必须放到末尾
		$game['img'] = $game['big_img'] ? $game['big_img']: ($game['mid_img'] ? $game['mid_img'] : $game['img']);
		
		if(!$preview){
			if($cflag) $cache->set($ckey, $game, 60);
		}
		
		//处理输出的webp图片-v1.5.6
		$game = self::processGameImg($game);
		return $game;
	}

	/**
	 * webp特殊处理
	 * @param string $img
	 * @param int flag
	 * @return string
	 */
	public static function webpProcess($img, $flag=0){
		$androidVersion = self::checkAndroidVersion();
		$webpSwitch = self::getWebpSwitch();
		if($webpSwitch && $flag && $androidVersion){
			$img = $img.'.webp';
		}
		return $img;
	}
	
	private static function checkAndroidVersion($androidVersion){
		$androidVer = Yaf_Registry::get("androidVersion");
		if(!$androidVer){
			return false;
		}
		//android系统版本低于Android4.2.0 的手机,不支持webp处理
		if (strnatcmp($androidVer, 'Android4.2.0') < 0){
			return false;
		}
		return true;
	}
	
	private static function getWebpSwitch(){
		return 1;
		/*
		$ckey = ":game_webp_switch";
		$cache = Cache_Factory::getCache();
		$data = $cache->get($ckey);
		if($data === false){
			$data = Game_Service_Config::getValue("game_webp_switch");
			$data = $data ? $data : 0 ;
			$cache->set($ckey, $data, 5*60);
		}
		return $data;
		*/
	}
	
	/**
	 * 处理游戏输出的图片信息
	 * @param array $data
	 * @return Ambigous <string, string>
	 */
	private static function processGameImg($data){
		$result = $data;
		if($data['webp']){
			$gimgs = $simgs = array();
			//大截图处理
			foreach($data['gimgs'] as $value) {
				$gimgs[] = self::webpProcess($value, $data['webp']);
			}
			$result['gimgs'] = $gimgs;
			//截图缩略图
			foreach($data['simgs'] as $value) {
				$file = array();
				//大图
				$simgs[] = self::webpProcess($value, $data['webp']);
			}
			$result['simgs'] = $simgs;
			$result['img'] = self::webpProcess($data['img'], $data['webp']);
		}
		return $result;
	}
	
	/**
	 * 包中数字签名字符串格式化处理,低于1.5.4的不处理
	 * @param string $str
	 */
	private static function _formartStr($strContent){
		$apkVer = Yaf_Registry::get("apkVersion");
		//版本低于1.5.3免流量不支持直接返回
		if (strnatcmp($apkVer, '1.5.4') < 0) return '';
		
		$flag = stristr($strContent, ',');
		$strMd5 = "";
		if($flag == false){
			//单个签名
			$strMd5=md5($strContent);
		}else{
			//多个签名
			$tmpArr=explode(',',$strContent);
			sort($tmpArr, SORT_STRING);//值按字符串升序排列
			$strMd5 = md5(implode('', $tmpArr));
		}
		return $strMd5;
	}
	
	/**
	 * 获取游戏的免流量标识
	 * @param int $gameId
	 */
	private static function _getFreedlFlag($gameId){
		$apkVer = Yaf_Registry::get("apkVersion");
		//版本低于1.5.3免流量不支持直接返回
		if (strnatcmp($apkVer, '1.5.3') < 0) return '';
		$freedlStr = '';
		$tmp = array();
		//当前进行的免流量活动增加5分钟缓存
		 $ckey='-freedl-now-hd';
		 $cache = Cache_Factory::getCache();
		 $has = $cache->exists($ckey);
		 if(!$has){
			//当前时间戳
			$todayTime = Common::getTime();
			//获取今天当前进行中的活动
			$data = Freedl_Service_Hd::getsByFreedl(array('status' => 1, 'start_time' => array('<=', $todayTime), 'end_time' => array('>=', $todayTime)), array('id' => 'ASC'));
			$cache->set($ckey, $data, 5*60);
		 }else{
		 	$data = $cache->get($ckey);
		 }
		//没有活动直接返回
		if(!$data) return "";

		foreach ($data as $value){
			switch($value['htype']){
				case 1 ://广东移动专区免流量
					$flag = Freedl_Service_Hd::checkFreedlGame($value['id'], $gameId);
					if($flag) $tmp[] = 'cmcc19_' . $value['id'];
					break;
				case 2 ://广东联通全站免流量
					$flag = Freedl_Service_Cugd::checkFreedlGame($gameId);
					if($flag) $tmp[] = 'cu19_' . $value['id'];
					break;
			}
		}
		$freedlStr = implode('|', $tmp);
		return $freedlStr;
	}

	/**
	 * 根据游戏唯一标识查询游戏简洁数据
	 * Api v2 版本使用
	 * @param unknown_type $params
	 * @return boolean
	 */
	public static function getGameSimpleInfo($params) {
		$game = self::getGameAllInfo($params);
		$tmp = array(
			'id' => $game['id'],
			'name' => $game['name'],
			'resume' => $game['resume'],
			'img' => $game['img'],
			'size' => $game['size'],
			'package' => $game['package'],
			'link' => $game['link'],
			'category_title' => $game['category_title'],
			'hot_title' => $game['hot_title'],
			'hot_id' => $game ['hot_id'],
			'device' => $game['device']	
	     );
		return $tmp;
	}
	
	/**
	 * 客户端本地化游戏列表输出数组
	 * @param $data 游戏列表 
	 * @param $intersrc BI统计参数 
	 * @param $checkVer 客户端版本判断 
	 * @param $type 数组中id值的类型.1为game_id,0为id
	 * @return array
	 */
	public static function getClientGameData($data, $intersrc, $checkVer, $type) {
		if(!$data) return '';
		foreach($data as $key=>$value) {
			$id = $type ? $value['game_id'] : ($value['id'] ? $value['id']:$value);
			$info = Resource_Service_GameData::getGameAllInfo($id);

			//附加属性处理,1：礼包
			$attach = array();
			if ($info['gift']) array_push($attach, '1');
				
			$temp = array(
					'img'=>urldecode($info['img']),
					'name'=>html_entity_decode($info['name']),
					'resume'=>html_entity_decode($info['resume']),
					'package'=>$info['package'],
					'link'=>$info['link'],
					'gameid'=>$info['id'],
					'size'=>$info['size'].'M',
					'category'=>$info['category_title'],
					'attach' => ($attach) ? implode(',', $attach) : '',
					'hot' => Resource_Service_Games::getSubscript($info['hot']),
					'viewType' => 'GameDetailView',
					'score' => $info['client_star'],
					'freedl' => $info['freedl'],
					'reward' => $info['reward']
			);
			$tmp[] = $temp;
		}
		return $tmp;
	}
	
	/**
	 * 游戏角标
	 * @param unknown_type $id
	 * @return intval
	 */
	public static function getSubscript($id) {
		if(!$id) return '';
		$subscript = 0;
        if (Util_Environment::isOnline()) {
            switch ($id) {
                case 29:        //最新
                    $subscript = '1';
                    break;
                case 30:        //最热
                    $subscript = '2';
                    break;
                case 31:        //首发
                    $subscript = '3';
                    break;
                case 102:        //活动
                    $subscript = '4';
                    break;
                default:
            }
        } else {
            switch ($id) {
                //测试
                case 104:        //最新    
                    $subscript = '1';
                    break;
                case 105:        //最热
                    $subscript = '2';
                    break;
                case 106:        //首发
                    $subscript = '3';
                    break;
                case 116:        //活动
                    $subscript = '4';
                    break;
                default:
            }
		}

		return $subscript;
	}
	
	/**
	 * 按条件检索游戏资源表
	 * @param unknown_type $params
	 * @return boolean
	 */
	public static function getBy($params) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 * 检索游戏资源表所有数据
	 * Enter description here ...
	 */
	public static function getAllResourceGames() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 分页检索游戏资源表数据
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * 后台分页检索游戏资源表数据
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function adminSearch($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 * 根据参数查询游戏分类
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getCategoryGames(array $params = array()) {
		$result = self::_getIdxGameResourceCategoryDao()->getsBy($params, array('sort'=>'DESC','game_id'=>'DESC'));
		$count = self::_getIdxGameResourceCategoryDao()->count($params);
		return array($count, $result);
	}
	
	/**
	 * 根据游戏id集合检索游戏资源表
	 * @param array $ids
	 * @return boolean|Ambigous <boolean, mixed, multitype:>
	 */
	public static function getByIds(array $ids) {
		if (!is_array($ids)) return false;
		return self::_getDao()->getsBy(array('id'=>array("IN", $ids)));
	}
	
	/**	
	 * 根据条件获取多个游戏
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getsBy($params, $orderBy = array('id'=>'DESC')) {
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 *
	 * 游戏资源表特殊条件搜索
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere = Db_Adapter_Pdo::sqlWhere($params);
		$sort = array('sort'=>'DESC','id'=>'DESC');
		if (count($params['id'])) $sort = array('FIELD '=>self::quoteInArray($params['id'][1]));
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $sort);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 *
	 * 游戏资源表特殊条件搜索
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function search2($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * where id条件特殊拼组
	 * @param unknown_type $variable
	 * @return string
	 */
	public function quoteInArray($variable) {
		if (empty($variable) || !is_array($variable)) return '';
		$_returns = array();
		foreach ($variable as $value) {
			$_returns[] = Db_Adapter_Pdo::quote($value);
		}
		return '(' .'`id`'.','. implode(', ', $_returns) . ')';
	}
	
	/**
	 *
	 * 游戏资源表特殊搜索 
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function addSearch($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere = Db_Adapter_Pdo::sqlWhere($params);
		$sort = array('id'=>'DESC');
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $sort);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params 
	 * @param int $page
	 * @param int $limit
	 */
	public static function installSearch($params) {
		return self::_getDao()->getsBy($params, array('id'=>'DESC'));
	}
	
	/**
	 * 
	 * Enter description here ... 
	 * @param unknown_type $id
	 */
	public static function getResourceGames($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getResourceByGames($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getBy(array('id'=>$id,'status'=>1));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getGameInfoByResourceId($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getBy(array('id'=>intval($id)), array('id'=>'DESC'));
	}
	
	/**
	 *
	 * Enter description here ... 
	 * @param unknown_type $id
	 */
	public static function getGameInfoByName($params) {
		if (!is_array($params)) return false;
		$params['status'] = 1;
		return self::_getDao()->getsBy($params);
	}
	
	/**
	 * 组装游戏数组
	 * @游戏数组 $data
	 */
	public static function getGameList($data,$webroot) {
		if (!is_array($data)) return false;
		
		$i=1;
		$tmp = array();
		foreach($data as $key=>$value){
			$info = array();
			$info = self::getGameAllInfo(array('id'=>$value['id']));
			$tmp[$i]['id'] = $info['id'];
			$tmp[$i]['from'] = 'gn';
			$tmp[$i]['name'] = $info['name'];
			$tmp[$i]['link'] = $info['link'];
			$tmp[$i]['package'] = $info['package'];
			$tmp[$i]['resume'] = $info['resume'];
			$tmp[$i]['language'] = $info['language'];
			$tmp[$i]['img'] = urldecode($info['img']);
			$tmp[$i]['size'] = $info['size'];
			$tmp[$i]['category'] = $info['category_title'];
			$tmp[$i]['version'] = $info['min_sys_version_title'];
			$tmp[$i]['min_resolution'] = $info['min_resolution_title'];
			$tmp[$i]['max_resolution'] = $info['max_resolution_title'];
			$tmp[$i]['updatetime'] = date('Y-m-d',$value['create_time']);
			$tmp[$i]['descrip'] = $value['descrip'];
			$tmp[$i]['company'] = $value['company'];
			$tmp[$i]['device'] = $info['device'];
			$i++;
		}
		return  $tmp;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateResourceGames($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	
	
	public static function getGamesByGameNames(array $params = array()) {
		return self::_getDao()->getsBy($params, array('id'=>'DESC'));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateResourceGamesStatus($status, $id) {
		if (!$id) return false;
		return self::_getDao()->update(array('status'=>$status), intval($id));
	}
	
	/**
	 * 更新游戏下载量
	 * @param unknown_type $downloads
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateResourceGamesDownload($downloads, $id) {
		if (!$id) return false;
		$result = self::_getDao()->update(array('downloads'=>$downloads), intval($id));
		return $result;
	}
	
	/**
	 * 更新游戏月下载量
	 * @param unknown_type $month_downloads
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateResourceGamesMonthDownload($month_downloads, $id) {
	    if (!$id) return false;
	    $result = self::_getDao()->update(array('month_downloads'=>$month_downloads), intval($id));
	    return $result;
	}
	
	
	public static function updateResourceGamesCertificate($certificate, $appid) {
		if (!$appid) return false;
		return self::_getDao()->updateBy(array('certificate'=>$certificate), array('appid'=>intval($appid)));
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxImgByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameImgDao()->getBy(array('game_id'=>$game_id));
	}
	
	/**
	 * 
	 * @param unknown_type $data
	 * @return multitype:unknown Ambigous <boolean, mixed, multitype:>
	 */
	public static function getGames($params = array()) {
		$ret = self::_getIdxGameResourceCategoryDao()->getsBy($params, array('sort'=>'DESC', 'game_id'=>'DESC'));
		$total = self::_getIdxGameResourceCategoryDao()->count($params);
		return array($total, $ret);
	}
	
		
	public static function getIdxGamesByCategoryId($page = 1, $limit = 10, $params = array(), $orderBy = array('sort'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getIdxGameResourceCategoryDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getIdxGameResourceCategoryDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxCategoryByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameResourceCategoryDao()->getsBy(array('game_id'=>$game_id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateIdxCategoryStatus($category_id, $status) {
		if (!$category_id) return false;
		return self::_getIdxGameResourceCategoryDao()->updateBy(array('status'=>intval($status)), array('category_id'=>intval($category_id)));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateIdxGameCategoryStatus($game_id, $status) {
		if (!$game_id) return false;
		return self::_getIdxGameResourceCategoryDao()->updateBy(array('game_status'=>intval($status)), array('game_id'=>intval($game_id)));
	}
	
	/**
	 * 更新索引表游戏的上线时间和下载量
	 * @param unknown_type $online_time
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function updateIdxGameCategoryOntime($online_time, $downloads, $game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameResourceCategoryDao()->updateBy(array('online_time'=>intval($online_time),'downloads'=>$downloads), array('game_id'=>intval($game_id),'game_status'=>1));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxModelByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameResourceModelDao()->getBy(array('game_id'=>$game_id));
	}

	
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxGameResourceCategorys() {
		return self::_getIdxGameResourceCategoryDao()->getAll();
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxResourceCategorys() {
		return self::_getIdxGameResourceCategoryDao()->getsBy(array('status'=>1));
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxResourceCategoryGames($params) {
		return self::_getIdxGameResourceCategoryDao()->getsBy($params);
	}
	
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGameResourceByCategoryId($id) {
		return 	self::_getIdxGameResourceCategoryDao()->getsBy(array('category_id'=>intval($id)));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGameResourceByCategoryIdStatus($id,$orderBy) {
		return 	self::_getIdxGameResourceCategoryDao()->getsBy(array('category_id'=>intval($id),'game_status'=>1),$orderBy);
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGameResourceByCategoryGameId($id) {
		return 	self::_getIdxGameResourceCategoryDao()->getBy(array('game_id'=>intval($id),'status'=>1));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getGameResourceByCategoryByGameId($id) {
		return 	self::_getIdxGameResourceCategoryDao()->getsBy(array("game_id"=>intval($id)));
	}
	
	/**
	 *
	 * @param unknown_type $resource_game_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameResourceCategoryBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameResourceCategoryDao()->getsBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameResourceVersionDao()->getsBy(array('game_id'=>$game_id),array('id'=>'DESC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public static function deleteIdxVersionByGameId($params) {
		if (!is_array($params)) return false;
		return self::_getIdxGameResourceVersionDao()->deleteBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionByVersionId($id,$game_id) {
		if (!$id) return false;
		return self::_getIdxGameResourceVersionDao()->getBy(array('id'=>$id,'game_id'=>$game_id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxGameVersionByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameResourceVersionDao()->getsBy(array('game_id'=>$game_id));
	}
	
	/**
	 * 
	 * @param unknown_type $gameid
	 */
	public static function getGameVersionInfo($gameid) {
		return self::_getIdxGameResourceVersionDao()->getBy(array('game_id'=>$gameid, 'status'=>1));
	}
	
	
	/**
	 * 
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return multitype:unknown multitype:
	 */
	public static function getVersionList($page = 1, $limit = 10, $params = array(), $orderBy = array('create_time'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getIdxGameResourceVersionDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getIdxGameResourceVersionDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionByVersionStatus($status) {
		return self::_getIdxGameResourceVersionDao()->getsBy(array('status'=>intval($status)));
	}
	
	/**
	 * 
	 * @param unknown $params
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getsByIdxVersions($params, $orderBy=array()) {
		return self::_getIdxGameResourceVersionDao()->getsBy($params, $orderBy);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateVersionMd5($id,$md5_code) {
		if (!$id) return false;
		return self::_getIdxGameResourceVersionDao()->updateVersionMd5(intval($id),$md5_code);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionByResourceGameId($params) {
		return self::_getIdxGameResourceVersionDao()->getsBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionGetBy($params) {
		return self::_getIdxGameResourceVersionDao()->getBy($params);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionByNewVersion($params) {
		return self::_getIdxGameResourceVersionDao()->getIdxVersionByNewVersion($params);
	}
	
	/**
	 *
	 * @param unknown_type $ids Resource_Service_Games::updateByGameVesion
	 * @return boolean
	 */
	public static function updateByGameVesion($data,$params) {
		return self::_getIdxGameResourceVersionDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getVersionGames($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getIdxGameResourceVersionDao()->getList($start, $limit, $params, array('game_id'=>'DESC'));
		$total = self::_getIdxGameResourceVersionDao()->count($params);
		
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getVersionSortGames($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getIdxGameResourceVersionDao()->getVersionSortGames(intval($start), intval($limit), $params);
		$total = self::_getIdxGameResourceVersionDao()->getVersionGamescount($params);
		return array($total, $ret);
	}
	
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getCountResourceVersion($params) {
		return self::_getIdxGameResourceVersionDao()->getCountResourceVersion($params);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addIdxGameResourceVersion($data) {
		if (!is_array($data)) return false;
		$id = self::addResourceVersion($data);
		$versions = array();
		if($data['status']){
			$versions = self::getIdxVersionByGameId($data['game_id']);
			foreach($versions as $key=>$val){
				if($val['id'] == $id){
					$status = 1;
				} else {
					$status = 0;
				}
				self::_getIdxGameResourceVersionDao()->update(array('status'=>$status), $val['id']);
				self::updateResourceGamesStatus($data['status'],$data['game_id']);
				//更新分类索引
				$categorys = Resource_Service_Games::getIdxCategoryByGameId($data['game_id']);
				if($categorys){
					foreach($categorys as $key=>$value){
						Resource_Service_Games::updateIdxGameCategoryStatus($data['game_id'],$data['status']);
					}
				
				}
			}
		}
		return true;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateIdxGameResourceVersion($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookVersionData($data);
		$versions = $tmp = array();
		if($data['status']){
			$versions = self::getIdxVersionByGameId($data['game_id']);
			foreach($versions as $key=>$val){
				if($val['id'] == $id){
					$status = 1;
				} else {
					$status = 0;
				}
				self::_getIdxGameResourceVersionDao()->update(array('status'=>$status), $val['id']);
			}
			self::_getIdxGameResourceVersionDao()->update($data, intval($id));
			self::updateResourceGamesStatus($data['status'],$data['game_id']);
			//更新分类索引
			$categorys = Resource_Service_Games::getIdxCategoryByGameId($data['game_id']);
			if($categorys){
				Resource_Service_Games::updateIdxGameCategoryStatus($data['game_id'],$data['status']);			
			}
			//更新专题索引
			Client_Service_SubjectGames::updateGamesStatus($data['game_id'],$data['status']);
			//更新装机必备
			$installes = Client_Service_Installe::getIdxInstalleByGameId($data['game_id']);
			if($installes){
				Client_Service_Installe::updateIdxInstalleStatus($data['game_id'],$data['status']);
			}
			//更新单机
			$channels = Client_Service_Channel::getChannelByGameId($data['game_id'],1);
			if($channels){
				Client_Service_Channel::updateChannelStatus($data['game_id'],$data['status'],1);
			}
			//更新网游
			$webs = Client_Service_Channel::getChannelByGameId($data['game_id'],2);
			if($webs){
				Client_Service_Channel::updateChannelStatus($data['game_id'],$data['status'],2);
			}
			//更新标签
			$labels = Resource_Service_Games::getIdxLabelByGameIdStatus($data['game_id']);
			if($labels){
				Resource_Service_Games::updateIdxLabelStatus($data['game_id'],$data['status']);
			}
			//更新礼包
			$gifts = Client_Service_Gift::getGiftByGameId($data['game_id']);
			if($gifts){
				Client_Service_Gift::updateGiftGameId($data['status'], $data['game_id']);
				Client_Service_GiftHot::updateBy(array('game_status'=>$data['status']), array('game_id'=>$data['game_id']));
			}
			//更新猜你喜欢
			$guess = Client_Service_Game::getGuessByGameId($data['game_id']);
			if($guess){
				Client_Service_Game::updateGuessGameId($data['status'],$data['game_id']);
			}
			//更新月榜默认数据
			$months = Client_Service_Game::getMonthRankByGameId($data['game_id']);
			if($months){
				Client_Service_Game::updateMonthRankGameId($data['status'],$data['game_id']);
			}
			//大家都在玩
			$webs = Web_Service_Ad::getWebAdByGameId($data['game_id']);
			if($webs){
				Web_Service_Ad::updateWebAdStatus($data['status'],$data['game_id']);
			}
			//新游尝鲜
			$tastes = Client_Service_Taste::getTasteGames(array('game_id'=>$data['game_id']));
			if($tastes){
				Client_Service_Taste::updateByTasteStatus($data['status'],$data['game_id']);
			}
			//免流量专区游戏
			$freedls = Freedl_Service_Hd::getGames(array('game_id'=>$data['game_id']));
			if($freedls){
				Freedl_Service_Hd::updateByGames(array('status'=>$data['status']), array('game_id'=>$data['game_id']));
				Freedl_Service_Hd::updateByTmpGames(array('status'=>$data['status']), array('game_id'=>$data['game_id']));
			}
			//更新首页推荐中游戏的状态字段
			Game_Service_RecommendList::updateGameStatus($data['game_id'], $data['status']);
			//更新赠送礼包活动
			$giftActivity = Client_Service_GiftActivity::getBy(array('game_id'=>$data['game_id']));
			if($giftActivity){
			    Client_Service_GiftActivity::updateGiftActivityByGameStatus($data['status'], $data['game_id']);
			}
			return true;
		} else {
			$versions = self::getIdxVersionByGameId($data['game_id']);
			foreach($versions as $k=>$v){
				if($v['id'] != $id){
					$tmp[] = $v['status'];
				}
			}
			if(count($versions) == 1 || (in_array($data['status'],$tmp) && !in_array(1,$tmp))){
				self::updateResourceGamesStatus($data['status'],$data['game_id']);
				//更新分类索引
				$categorys = Resource_Service_Games::getIdxCategoryByGameId($data['game_id']);
				if($categorys){
					Resource_Service_Games::updateIdxGameCategoryStatus($data['game_id'],$data['status']);
				}
				//更新专题索引
			    Client_Service_SubjectGames::updateGamesStatus($data['game_id'], $data['status']);
				//更新广告3
				$ads = Client_Service_Ad::getByLinkAd($data['game_id']);
				if($ads){
					foreach($ads as $key=>$value){
						Client_Service_Ad::deleteAd($value['id']);
					}
				
				}
				//更新精品推荐索引
				$besttjs = Client_Service_Besttj::getIdxBesttjByGameId($data['game_id']);
				if($besttjs){
					Client_Service_Besttj::updateIdxBesttjStatus($data['game_id'],$data['status']);
				}
				//更新装机必备
				$installes = Client_Service_Installe::getIdxInstalleByGameId($data['game_id']);
				if($installes){
					Client_Service_Installe::updateIdxInstalleStatus($data['game_id'],$data['status']);
				}
				//更新单机
				$channels = Client_Service_Channel::getChannelByGameId($data['game_id'],1);
				if($channels){
					Client_Service_Channel::updateChannelStatus($data['game_id'],$data['status'],1);
				}
				//更新网游
				$webs = Client_Service_Channel::getChannelByGameId($data['game_id'],2);
				if($webs){
					Client_Service_Channel::updateChannelStatus($data['game_id'],$data['status'],2);
				}
				//更新标签
				$labels = Resource_Service_Games::getIdxLabelByGameIdStatus($data['game_id']);
				if($labels){
					Resource_Service_Games::updateIdxLabelStatus($data['game_id'],$data['status']);
				}
				//更新礼包
				$gifts = Client_Service_Gift::getGiftByGameId($data['game_id']);
				if($gifts){
					Client_Service_Gift::updateGiftGameId($data['status'],$data['game_id']);
					Client_Service_GiftHot::updateBy(array('game_status'=>$data['status']), array('game_id'=>$data['game_id']));
				}
				//更新猜你喜欢
				$guess = Client_Service_Game::getGuessByGameId($data['game_id']);
				if($guess){
					Client_Service_Game::updateGuessGameId($data['status'],$data['game_id']);
				}
				//更新月榜默认数据
				$months = Client_Service_Game::getMonthRankByGameId($data['game_id']);
				if($months){
					Client_Service_Game::updateMonthRankGameId($data['status'],$data['game_id']);
				}
				//大家都在玩
				$webs = Web_Service_Ad::getWebAdByGameId($data['game_id']);
				if($webs){
					Web_Service_Ad::updateWebAdStatus($data['status'],$data['game_id']);
				}
				//新游尝鲜
				$tastes = Client_Service_Taste::getTasteGames(array('game_id'=>$data['game_id']));
				if($tastes){
					Client_Service_Taste::updateByTasteStatus($data['status'],$data['game_id']);
				}
				//免流量专区游戏
				$freedls = Freedl_Service_Hd::getGames(array('game_id'=>$data['game_id']));
				if($freedls){
					Freedl_Service_Hd::updateByGames(array('status'=>$data['status']), array('game_id'=>$data['game_id']));
					Freedl_Service_Hd::updateByTmpGames(array('status'=>$data['status']), array('game_id'=>$data['game_id']));
				}
				//过滤游戏
				$filterGame = Resource_Service_FilterGame::getBy(array('game_id'=>$data['game_id']));
				if($filterGame){
					Resource_Service_FilterGame::deleteBy(array('game_id'=>$data['game_id']));
				}
				//更新首页推荐中游戏的状态字段
				Game_Service_RecommendList::updateGameStatus($data['game_id'], $data['status']);
				//更新赠送礼包活动
				$giftActivity = Client_Service_GiftActivity::getBy(array('game_id'=>$data['game_id']));
				if($giftActivity){
				    Client_Service_GiftActivity::updateGiftActivityByGameStatus($data['status'], $data['game_id']);
				}
				//更新活动里面的状态
				Client_Service_Hd::updateByGameStatus($data['game_id'], $data['status']);
			}
			return self::_getIdxGameResourceVersionDao()->update($data, intval($id));
		}
	}
	
	public static function updateIdxGameResourceVersionUpdateType($update_type, $id) {
		if (!$id) return false;
		
		return self::_getIdxGameResourceVersionDao()->update(array('update_type'=>$update_type), intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionByVersionInfo($game_id) {
		return self::_getIdxGameResourceVersionDao()->getIdxVersionByVersionInfo(intval($game_id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addResourceVersion($data) {
		if (!is_array($data)) return false;
		$data = self::_cookVersionData($data);
		$ret = self::_getIdxGameResourceVersionDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getIdxGameResourceVersionDao()->getLastInsertId(); 
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteIdxGameResourceVersion($id) {
		return self::_getIdxGameResourceVersionDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxGameResourceVersion($id) {
		return self::_getIdxGameResourceVersionDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxGameResourceVersion2() {
		return self::_getIdxGameResourceVersion2Dao()->getAll();
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxGameResourceVersionByMd5($md5) {
		return self::_getIdxGameResourceVersionDao()->getBy(array('md5_code'=>$md5));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteIdxGameResourceDiff($id) {
		return self::_getIdxGameResourcePackageDao()->delete(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 */
	public static function deleteIdxGameResourceDiffByGameId($params) {
		if (!is_array($params)) return false;
		return self::_getIdxGameResourcePackageDao()->deleteBy($params);
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxGameResourceAllDiff() {
		return self::_getIdxGameResourcePackageDao()->getAll();
	}
	
	/**
	 *
	 * @param unknown_type $params
	 * @return multitype:
	 */
	public static function getIdxDiffByVersionId($version_id, $obiect_id) {
		return self::_getIdxGameResourcePackageDao()->getBy(array('version_id'=>$version_id,'object_id'=>$obiect_id));
	}
	
	/**
	 * 
	 * @param array $params
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getsByIdxDiff($params){
		return self::_getIdxGameResourcePackageDao()->getsBy($params);
	}
	
	/**
	 *
	 * @param array $params
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getByIdxDiff($params){
		return self::_getIdxGameResourcePackageDao()->getBy($params);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxGameResourceDiff() {
		return self::_getIdxGameResourcePackageDao()->getCountResourceDiff();
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxGamePackageByGameId($game_id) {
		if (!$game_id) return false;
		return self::_getIdxGameResourcePackageDao()->getsBy(array('game_id'=>$game_id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxVersionByDiffId($id,$game_id) {
		if (!$id) return false;
		return self::_getIdxGameResourcePackageDao()->getBy(array('id'=>$id,'game_id'=>$game_id));
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addIdxGameResourcePackage($data) {
		if (!is_array($data)) return false;
		$data = self::_cookPackageData($data);
		return self::_getIdxGameResourcePackageDao()->insert($data);
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function getIdxPackageByPackageId($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameResourcePackageDao()->getsBy($params,array('update_time'=>'DESC'));
	}
	
	/**
	 *
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxGameResourceModels() {
		return self::_getIdxGameResourceModelDao()->getAll();
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function deleteIdxGameResourceModels($id) {
		return 	self::_getIdxGameResourceModelDao()->deleteBy(array('game_id'=>$id));
	}
	
	/**
	 *
	 * @param unknown_type $resource_model_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameResourceModelBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameResourceModelDao()->getsBy($params);
	}

	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateIdxCategorySort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getIdxGameResourceCategoryDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function getIdxLabelByGameId($game_id) {
		return self::_getIdxGameResourceLabelDao()->getsBy(array('game_id'=>$game_id,'status'=>1));
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @param unknown_type $btype
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getIdxLabelLevelByGameId($game_id,$btype) {
		return self::_getIdxGameResourceLabelDao()->getBy(array('btype'=>$btype,'game_id'=>$game_id,'status'=>1));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @param unknown_type $btype
	 * @return Ambigous <boolean, mixed>
	 */
	public static function updateIdxLabelLevelByGameId($game_id,$btype,$level) {
		$tmp[] = array(
				'id'=>'',
				'btype'=>$btype,
				'label_id'=>$level,
				'game_id'=>$game_id,
				'status'=>1,
				'game_status'=>1,
		);
		$game_level = self::getIdxLabelLevelByGameId($game_id,$btype);
		if($game_level) {
			//如果评级存在，且有值，则更新
			if($level) return self::_getIdxGameResourceLabelDao()->updateBy(array('label_id'=>$level),array('btype'=>$btype,'game_id'=>$game_id));
			//如果评级存在，且无值，则删除
			if(!$level) return self::_getIdxGameResourceLabelDao()->deleteBy(array('btype'=>$btype,'game_id'=>$game_id));
		}
		//如果评级不存在，且有值,则添加
		if($level) return self::_getIdxGameResourceLabelDao()->mutiInsert($tmp);
		
		
	}
	
	/**
	 * 
	 * @param unknown_type $game_id
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxLabelByGameIdStatus($game_id) {
		return self::_getIdxGameResourceLabelDao()->getsBy(array('game_id'=>$game_id));
	}
	
	/**
	 * 
	 * @param unknown_type $id
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getIdxLabelByLabelId($id) {
		return self::_getIdxGameResourceLabelDao()->getsBy(array('label_id'=>$id));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function updateIdxLabelByLabelIdStatus($label_id,$status) {
		return self::_getIdxGameResourceLabelDao()->updateBy(array('status'=>$status),array('label_id'=>$label_id));
	}
	
	/**
	 *
	 * @param unknown_type $game_id
	 * @return boolean
	 */
	public static function updateIdxLabelStatus($game_id,$status) {
		if (!$game_id) return false;
		return self::_getIdxGameResourceLabelDao()->updateBy(array('status'=>$status),array('game_id'=>$game_id));
	}
	
	/**
	 *
	 * @param unknown_type $resource_game_id
	 * @return boolean|mixed
	 */
	public static function getIdxGameResourceDeviceBy($params = array()) {
		if (!is_array($params)) return false;
		return self::_getIdxGameResourceDeviceDao()->getsBy($params);
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getIdxGameResourceResolutionByGameId($params) {
		if (!is_array($params)) return false;
		return self::_getIdxGameResourceResolutionDao()->getsBy($params,array('attribute_id'=>'ASC'));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addIdxGameResourceCategory($data) {
		if (!is_array($data)) return false;
		return self::_getIdxGameResourceCategoryDao()->insert($data);
	}
	
	public static function updateResourceGamesModel($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
								
		    //添加包名索引
			if($model){
				$models = self::_cookIdxData($data, $id, 1, 'PACKAGE');
				$ret = self::_getIdxGameResourceModelDao()->mutiInsert($models);
				if (!$ret) throw new Exception('Add Model fail.', -205);
			} else {
				$model_ids = Resource_Service_Games::getIdxGameResourceModelBy(array('game_id'=>intval($id)));
				if($model_ids){
					$ret = self::deleteIdxGameResourceModels(intval($id));
					if (!$ret) throw new Exception('Delete Model fail.', -205);
				}
			}
		
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	
	/**
	 *
	 * @param array $data
	 * @param unknown_type $type
	 * @return boolean|multitype:unknown
	 */
	private static function _gameData(array $data ,$type) {
		$tmp = array();
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$tmp[] = $value[$type];
		}
		return $tmp;
	}
	
	public static function updateResourceGamesByModel($data, $id ,$model) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
	
			//删除机型索引
			$ret = self::_getIdxGameResourceModelDao()->deleteBy(array('game_id'=>$id));
			if (!$ret) throw new Exception('Delete Model fail.', -205);
	
			//添加机型索引
			if($model){
				$models = self::_cookIdxData($model, $id, $data['status'], 'MODEL');
				$ret = self::_getIdxGameResourceModelDao()->mutiInsert($models);
				if (!$ret) throw new Exception('Update Model fail.', -205);
			}
	
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateResourceGamesIdx($data, $upimg, $img, $id ,$category ,$labels, $resolution, $devlop) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		//开始事务
		$trans = parent::beginTransaction();
		try {
		    
			//更新游戏-包含游戏分类
			$data = self::filterData($data);
			$ret = self::updateResourceGames($data, $id);
			if (!$ret) throw new Exception("Update Game fail.", -202);
						
			//修改的图片
			if($upimg){
				foreach($upimg as $key=>$value) {
					if ($key && $value) {
						Resource_Service_Img::updateGameImg(array('img'=>$value), $key);
					}
				}
			}
			//新增加的图片
			if ($img[0] != null && !$devlop) {
				$gimgs = array();
				foreach($img as $key=>$value) {
					if ($value != '') {
						$gimgs[] = array('game_id'=>$id, 'img'=>$value);
					}
				}
				$ret = Resource_Service_Img::addGameImg($gimgs);
				if (!$ret) throw new Exception('add GameImg fail.', -203);
			}
			
			//兼容开发者平台
			if($devlop){
				if($img){
					//删除图片
					$ret = Resource_Service_Img::deleteGameImgByGameId(array('game_id'=>$id));
					//添加图片
					foreach($img as $key=>$value) {
						if ($value != '') {
							$gimgs[] = array('game_id'=>$id, 'img'=>$value);
						}
				   }
				   $ret = Resource_Service_Img::addGameImg($gimgs);
				}
			}
			
			//删除分类索引
			$idx_ret = self::_getIdxGameResourceCategoryDao()->deleteBy(array('game_id'=>$id));
			
			//添加分类索引[新版本索引表加上线时间(online_time)，下载量(downloads)2个字段]
		    if($category){
		    	//获取该游戏当前的下载量
		    	$downloads = Client_Service_WeekRank::getRankGameId($id);
		    	$down = ($downloads) ? $downloads['DL_TIMES'] : 0;
				$categorys = self::_cookIdxCategoryData($category, $id, $data['status'], $data['online_time'],$down, 'UPCATEGORY');
				foreach ($categorys as $value){
					$ret = self::_getIdxGameResourceCategoryDao()->insert($value);
					if (!$ret) throw new Exception('Add Category fail.', -205);
				}
			}
			
			//添加标签索引
			if($labels){
				$tmp = array();
				$idx_ret = self::_getIdxGameResourceLabelDao()->deleteBy(array('game_id'=>$id));
				foreach($labels as $key=>$value){
					$lab = explode('|',$value);
					$tmp[] = array(
							'id'=>'',
							'btype'=>$lab[0],
							'label_id'=>$lab[1],
							'game_id'=>$id,
							'status'=>1,
							'game_status'=>$data['status'],
					);
				}
				$ret = self::_getIdxGameResourceLabelDao()->mutiInsert($tmp);
				if (!$ret) throw new Exception('Update Label fail.', -205);
			}
			
			
			//添加游戏分辨率索引
			if($resolution){
				//删除游戏分辨率索引
				$tmp = array();
				$idx_ret = self::_getIdxGameResourceResolutionDao()->deleteBy(array('game_id'=>$id));
				$resolutions = explode('-',$resolution);
				foreach($resolutions as $key=>$value){
					$tmp[] = array(
							'id'=>'',
							'attribute_id'=>$value,
							'game_id'=>$id,
							'status'=>1,
					);
				}
				$ret = self::_getIdxGameResourceResolutionDao()->mutiInsert($tmp);
				if (!$ret) throw new Exception('Update Resolution fail.', -206);
			}
							
			//事务提交
			if($trans) {
				parent::commit();
				return true;
			}
		} catch (Exception $e) {
			parent::rollBack();
			print_r($e->getMessage());
			return false;
		}
	}
	
	
	/**
	 * 更新游戏可编辑的属性
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateBaseResourceGames($data,$id ,$category ,$labels, $device, $version_id) {
		if (!is_array($data)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新游戏
			$ret = self::updateResourceGames($data, $id);
			if (!$ret) throw new Exception("Update Game fail.", -202);
			//删除分类索引
			$idx_ret = self::_getIdxGameResourceCategoryDao()->deleteBy(array('game_id'=>$id));
				
			$info = self::getResourceGames($id);
			//添加分类索引
			if($category){
				$categorys = self::_cookIdxCategoryData($category, $id, $info['status'],$info['online_time'],$info['downloads'], 'UPCATEGORY');
				foreach ($categorys as $value){
					$ret = self::_getIdxGameResourceCategoryDao()->insert($value);
					if (!$ret) throw new Exception('Add Category fail.', -205);
				}
			}
				
			//添加标签索引
			if($labels){
				$tmp = array();
				$idx_ret = self::_getIdxGameResourceLabelDao()->deleteBy(array('game_id'=>$id));
				
				foreach($labels as $key=>$value){
					$lab = explode('|',$value);
					$tmp[] = array(
							'id'=>'',
							'btype'=>$lab[0],
							'label_id'=>$lab[1],
							'game_id'=>$id,
							'status'=>1,
							'game_status'=>$info['status'],
					);
				}
				$ret = self::_getIdxGameResourceLabelDao()->mutiInsert($tmp);
				if (!$ret) throw new Exception('Update Label fail.', -205);
			}
				
			//删除设备索引
			$idxds_ret = self::_getIdxGameResourceDeviceDao()->deleteBy(array('game_id'=>$id));
			//添加设备索引
			if($device){
				$devices = self::_cookIdxData($device, $id, $info['status'], 'DEVICE');
				$ret = self::_getIdxGameResourceDeviceDao()->mutiInsert($devices);
				if (!$ret) throw new Exception('Add Device fail.', -205);
			}
			
			//查找当前线上版本并且更新最后编辑时间
			$online_version = self::getIdxGameResourceVersion($version_id);
			if($online_version){
				$verData = array('update_time'=>Common::getTime());
				if($data['changes']) $verData['changes'] = $data['changes'];  
				$ret = self::_getIdxGameResourceVersionDao()->update($verData, intval($version_id));
				
				if (!$ret) throw new Exception('Update Version fail.', -206);
			}
			
				
			//事务提交
			if($trans) {
				parent::commit();
				return true;
			}
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteResourceGames($id) {
		return self::_getDao()->delete(intval($id));
	}
	
   /**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
    public static function deleteResourceGamesIdx($id) {
    if (!$id) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
		    
			//删除游戏
			$info = Resource_Service_Games::getResourceGames($id);
			Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
			
			$ret = self::deleteResourceGames($id);
			if (!$ret) throw new Exception("Delete Game fail.", -202);
			
			//删除游戏预览图片
			$ret = self::_getIdxGameImgDao()->deleteBy(array('game_id'=>$id));
			if (!$ret) throw new Exception('Delete GameImg fail.', -205);
			
			
			//删除分类索引
			$category_ids = self::getIdxCategoryByGameId($id);
			if($category_ids){
				$ret = self::_getIdxGameResourceCategoryDao()->deleteBy(array('game_id'=>$id));
				if (!$ret) throw new Exception('Delete Category fail.', -205);
			}
			
			//删除版本索引
			$version_ids = self::getIdxGameVersionByGameId($id);
			if($version_ids){
				$ret = self::_getIdxGameResourceVersionDao()->deleteBy(array('game_id'=>$id));
				if (!$ret) throw new Exception('Delete Version fail.', -205);
			}
			
			//删除差分包
			$packge_ids = self::getIdxGamePackageByGameId($id);
			if($packge_ids){
				$ret = self::_getIdxGameResourcePackageDao()->deleteBy(array('game_id'=>$id));
				if (!$ret) throw new Exception('Delete Package fail.', -205);
			}
			
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addResourceGames($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		$ret = self::_getDao()->insert($data);
		if (!$ret) return $ret;
		return self::_getDao()->getLastInsertId(); 
	}
	
	public static function getKeyword() {
		list(, $keywords) = Resource_Service_Keyword::getCanUseResourceKeywords(0,1,array('ktype'=>2));
		return $keywords;
	}
	
	public static function getHots() {
		list(, $keywords) = Resource_Service_Keyword::getCanUseResourceKeywords(0, 10 ,array('ktype'=>1));
		$tmp = array();
		foreach($keywords as $key=>$value) {
			$tmp[] = $value['name'];
		}
		return $tmp;;
	}
	
	/**
	 * 
	 * @param array $data
	 * @param array $img
	 * @param array $label
	 * @param array $subject
	 * @throws Exception
	 * @return boolean
	 */
	public static function addResourceGamesIdx($data, $img, $category,$labels,$resolution) {
		if (!is_array($data)) return false;
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//添加游戏
			$game_id = self::addResourceGames($data);
			$data['status'] = 1;
			if (!$game_id) throw new Exception("Add Game fail.", -202);
				
			//添加游戏图片
			if($img){
				$imgs = self::_cookIdxData($img, $game_id, $data['status'], 'IMG');
				$ret= Resource_Service_Img::addGameImg($imgs);
				if (!$ret) throw new Exception('Add GameImg fail.', -203);
			}
			
			//添加分类索引
			if($category){
				$categorys = self::_cookIdxCategoryData($category, $game_id, $data['status'],$data['online_time'], 0, 'CATEGORY');
				foreach ($categorys as $value){
					$ret = self::_getIdxGameResourceCategoryDao()->insert($value);
					if (!$ret) throw new Exception('Add Category fail.', -205);
				}
			}		
			//添加标签索引
			if($labels){
				$tmp = array();
				foreach($labels as $key=>$value){
					$lab = explode('|',$value);
					$tmp[] = array(
							'id'=>'',
							'btype'=>$lab[0],
							'label_id'=>$lab[1],
							'game_id'=>$game_id,
							'status'=>1,
							'game_status'=>0,
			        );
				}
				$ret = self::_getIdxGameResourceLabelDao()->mutiInsert($tmp);
				if (!$ret) throw new Exception('Add Label fail.', -205);
			}
			//添加游戏分辨率索引
			if($resolution){
				//删除游戏分辨率索引
				$tmp = array();
				$resolutions = explode('-',$resolution);
				foreach($resolutions as $key=>$value){
					$tmp[] = array(
							'id'=>'',
							'attribute_id'=>$value,
							'game_id'=>$game_id,
							'status'=>1,
					);
				}
				$ret = self::_getIdxGameResourceResolutionDao()->mutiInsert($tmp);
				if (!$ret) throw new Exception('Update Resolution fail.', -206);
			}
		//事务提交
			if($trans) {
				parent::commit();
				return $game_id;
			}
			
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}

	/**
	 * 数据变更缓存变更通知
	 */
	public static function notifyCache($gameId, $status=1){
		$subjct = new Common_Service_Subject(array('gameId' => $gameId, 'status' => $status));
		//游戏缓存观察者
		$cacheObserver = new Resource_Service_GameCacheObserver();
		$subjct->attach($cacheObserver);
		$subjct->notify();
	}
	
	
	private static function _cookIdxData($data, $game_id, $status,  $type) {
		$tmp = array();
		foreach($data as $key=>$value) {
			if ($value != '') {
				if ($type == 'IMG') {
					$tmp[] = array('id'=>'', 'game_id'=>$game_id, 'img'=>$value);
				}else if ($type == 'MODEL') {
					$tmp[] = array('id'=>'', 'status'=>$status, 'model_id'=>$value, 'game_id'=>$game_id);
				}else if ($type == 'PROPERTY') {
					$tmp[] = array('id'=>'', 'status'=>$status, 'property_id'=>$value, 'game_id'=>$game_id);
				}else if ($type == 'DEVICE') {
					$tmp[] = array('id'=>'', 'status'=>$status, 'device_id'=>$value, 'game_id'=>$game_id);
				}
			}
		}
		return $tmp;
	}

	private static function _cookIdxCategoryData($data, $game_id, $status, $online_time, $downloads, $type){
		$tmp = array();
		//主分类 一级分类数据
		$tmp[0]=array(
				'id'=>'',
				'status' => 1,
				'category_id' =>$data['mainSub'],
				'parent_id' => $data['mainParent'],
				'level' => 1,
				'game_id' => $game_id,
				'sort' => 0,
				'game_status' => $status,
				'online_time' => $online_time,
				'downloads' => $downloads
		);
		//次分类数据
		if($data['lessParent'] && $data['lessSub']){
			if(($data['lessParent'] == $data['mainParent'])&&($data['lessSub'] == $data['mainSub'])){
				return $tmp;
			}else{
				$tmp[1]=array(
						'id'=>'',
						'status' => 1,
						'category_id' => $data['lessSub'],
						'parent_id' => $data['lessParent'],
						'level' => 2,
						'game_id' => $game_id,
						'sort' => 0,
						'game_status' => $status,
						'online_time' => $online_time,
						'downloads' => $downloads
				);
			}
		}
		
		return $tmp;
	}
	
	/**
	 * 运营取消
	 * 一句话介绍(简述)，热词，小编八卦，游戏介绍
	 * 开发者平台做平台变更时内容的覆盖
	 * @param array $data
	 * @return array
	 */
	private static function filterData($data){
		//取消一句话（简述）
		unset($data['resume']);
		//热词
		unset($data['label']);
		//小编八卦
		unset($data['tgcontent']);
		//游戏介绍
		unset($data['descrip']);
		return $data;
	}
	
	public static  function calcOrderScore($gameList, $keyword){
		if (!is_array($gameList)){
			return false;
		}
		$keywordLen = strlen($keyword);
		foreach ($gameList as $key=>$val){
			$gameList[$key]['weight'] = 0;
			$pos = stripos($val['name'], $keyword);
			if($pos !== false) {
				$gameList[$key]['weight'] =
				round($keywordLen/strlen($val['name']), 2)*1000 - $pos;
			}
		}
		return $gameList;
	}
	
	public static function compare($a, $b){
		if ($a['weight'] > $b['weight']){
			return -1;
		} elseif ($a['weight'] == $b['weight']){
			if($a['online_time']>$b['online_time']){
				return -1;
			}else{
				return 1;
			}
		} else {
			return 1;
		}
	}
	
	public static function getSearchList($gameList, $page, $limit, $keyword){
		$gameList  = self::calcOrderScore($gameList, $keyword);
		uasort($gameList, array('self','compare'));
		$gameList = array_slice($gameList, $page*$limit, $limit, true);
		return $gameList;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['label'])) $tmp['label'] = $data['label'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['mid_img'])) $tmp['mid_img'] = $data['mid_img'];
		if(isset($data['big_img'])) $tmp['big_img'] = $data['big_img'];
		if(isset($data['language'])) $tmp['language'] = $data['language'];
		if(isset($data['price'])) $tmp['price'] = $data['price'];
		if(isset($data['package'])) $tmp['package'] = $data['package'];
		if(isset($data['packagecrc'])) $tmp['packagecrc'] = $data['packagecrc'];
		if(isset($data['company'])) $tmp['company'] = $data['company'];
		if(isset($data['descrip'])) $tmp['descrip'] = $data['descrip'];
		if(isset($data['tgcontent'])) $tmp['tgcontent'] = $data['tgcontent'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['ids'])) $tmp['ids'] = $data['ids'];
		if(isset($data['hot'])) $tmp['hot'] = $data['hot'];
		if(isset($data['st'])) $tmp['st'] = $data['st'];
		if(isset($data['category_id'])) $tmp['category_id'] = $data['category_id'];
		if(isset($data['cooperate'])) $tmp['cooperate'] = $data['cooperate'];
		if(isset($data['developer'])) $tmp['developer'] = $data['developer'];
		if(isset($data['certificate'])) $tmp['certificate'] = $data['certificate'];
		if(isset($data['appid'])) $tmp['appid'] = intval($data['appid']);
		if(isset($data['online_time'])) $tmp['online_time'] =$data['online_time'];
		if(isset($data['secret_key'])) $tmp['secret_key'] = $data['secret_key'];
		if(isset($data['api_key'])) $tmp['api_key'] = $data['api_key'];
		if(isset($data['agent'])) $tmp['agent'] = $data['agent'];
		if(isset($data['level'])) $tmp['level'] = $data['level'];
		if(isset($data['downloads'])) $tmp['downloads'] = intval($data['downloads']);
		if(isset($data['month_downloads'])) $tmp['month_downloads'] = intval($data['month_downloads']);
		if(isset($data['webp'])) $tmp['webp'] = intval($data['webp']);
		return $tmp;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookVersionData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['changes'])) $tmp['changes'] = $data['changes'];
		if(isset($data['version_code'])) $tmp['version_code'] = $data['version_code'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['min_sys_version'])) $tmp['min_sys_version'] = $data['min_sys_version'];
		if(isset($data['min_resolution'])) $tmp['min_resolution'] = $data['min_resolution'];
		if(isset($data['max_resolution'])) $tmp['max_resolution'] = $data['max_resolution'];
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['md5_code'])) $tmp['md5_code'] = $data['md5_code'];
		if(isset($data['fingerprint'])) $tmp['fingerprint'] = $data['fingerprint'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = intval($data['create_time']);
		if(isset($data['update_time'])) $tmp['update_time'] = intval($data['update_time']);
		if(isset($data['upgrade_type'])) $tmp['update_type'] = $data['upgrade_type'];
		return $tmp;
	}
	
	
	/**
	 *
	 * Enter description here ...
	 */
	private static function _cookPackageData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['version_id'])) $tmp['version_id'] = intval($data['version_id']);
		if(isset($data['object_id'])) $tmp['object_id'] = intval($data['object_id']);
		if(isset($data['diff_name'])) $tmp['diff_name'] = $data['diff_name'];
		if(isset($data['new_version'])) $tmp['new_version'] = $data['new_version'];
		if(isset($data['old_version'])) $tmp['old_version'] = $data['old_version'];
		if(isset($data['size'])) $tmp['size'] = $data['size'];
		if(isset($data['create_user'])) $tmp['create_user'] = $data['create_user'];
		if(isset($data['modify_user'])) $tmp['modify_user'] = $data['modify_user'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['update_time'])) $tmp['update_time'] = $data['update_time'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Resource_Dao_Games
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Games");
	}
	
	
	/**
	 *
	 * @return Resource_Dao_Img
	 */
	private static function _getIdxGameImgDao() {
		return Common::getDao("Resource_Dao_Img");
	}
	
	/**
	 *
	 * @return Resource_Dao_IdxGameResourceCategory
	 */
	private static function _getIdxGameResourceCategoryDao() {
		return Common::getDao("Resource_Dao_IdxGameResourceCategory");
	}
	
	/**
	 *
	 * @return Resource_Dao_IdxGameResourceType
	 */
	private static function _getIdxGameResourceModelDao() {
		return Common::getDao("Resource_Dao_IdxGameResourceType");
	}

	
	/**
	 *
	 * @return Resource_Dao_IdxGameResourceVersion
	 */
	private static function _getIdxGameResourceVersionDao() {
		return Common::getDao("Resource_Dao_IdxGameResourceVersion");
	}
	
	/**
	 *
	 * @return Resource_Dao_IdxGameResourcePackage
	 */
	private static function _getIdxGameResourcePackageDao() {
		return Common::getDao("Resource_Dao_IdxGameResourcePackage");
	}
	
	
	/**
	 *
	 * @return Resource_Dao_IdxGameResourceLabel
	 */
	private static function _getIdxGameResourceLabelDao() {
		return Common::getDao("Resource_Dao_IdxGameResourceLabel");
	}
	
	/**
	 *
	 * @return Resource_Dao_IdxGameResourceDevice
	 */
	private static function _getIdxGameResourceDeviceDao() {
		return Common::getDao("Resource_Dao_IdxGameResourceDevice");
	}
	
	/**
	 *
	 * @return Resource_Dao_IdxGameResourceResolution
	 */
	private static function _getIdxGameResourceResolutionDao() {
		return Common::getDao("Resource_Dao_IdxGameResourceResolution");
	}
	
	/**
	 *
	 * @return Resource_Dao_Keyword
	 */
	private static function _getResourceKeywordDao() {
		return Common::getDao("Resource_Dao_Keyword");
	}
}
