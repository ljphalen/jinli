<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * 游戏属性表修改
 * @author fanch
 *
 */
class Resource_Service_Attribute extends Common_Service_Base{
	
	/**
	 * Resource_Service_Attribute::getList
	 * 
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 * @param array $orderBy
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'ASC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * Resource_Service_Attribute::getApiList
	 * 
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 * @param int $game_caini
	 * @param array $orderBy
	 */
	public static function getApiList($page = 1, $limit = 10, $game_caini, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		if($game_caini && $page > 1) $start = ($page - 1) * $limit - 1;
		if($game_caini && $page <= 1) $limit = $limit -1;
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	
	/**
	 * Resource_Service_Attribute::updateAttributeStatus
	 * 分类状态更新
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public static function updateAttributeStatus($data, $id) {
		if (!is_array($data)) return false;
		
		//开始事务
		$trans = parent::beginTransaction();
		try {
			//更新属性
			self::update($data, $id);
			//更新分类索引
			Resource_Service_Games::updateIdxCategoryStatus($id, $data['status']);
			//事务提交
			if($trans) return parent::commit();
			return true;
		} catch (Exception $e) {
			parent::rollBack();
			return false;
		}
	}
	
	
	/**
	 * Resource_Service_Attribute::updateCategorySort
	 * 分类排序修改
	 * @param array $sorts
	 * @return boolean
	 */
	public static function updateCategorySort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 * Resource_Service_Attribute::getBy
	 * 按条件检索属性值
	 * @param array $params
	 */
	public static function getBy($params){
		 if(!is_array($params)) return false;
		return self::_getDao()->getBy($params);
	}

	/**
	 * Resource_Service_Attribute::getsBy
	 * 
	 * 按条件检索属性值
	 * @param array $params
	 * @param array $orderBy
	 */
	public static function getsBy($params, $orderBy = array()){
		if(!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}

	/**
	 * Resource_Service_Attribute::add
	 * 添加属性数据
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * Resource_Service_Attribute::update
	 * 更新属性
	 * @param array $data
	 * @param int $id
	 */
	public static function update($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 * Resource_Service_Attribute::delete
	 * 删除属性数据
	 * @param int $id
	 *
	 */
	public static function delete($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['at_type'])) $tmp['at_type'] = $data['at_type'];
		if(isset($data['parent_id'])) $tmp['parent_id'] = $data['parent_id'];
		if(isset($data['status'])) $tmp['status'] = intval($data['status']);
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['img2'])) $tmp['img2'] = $data['img2'];
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['editable'])) $tmp['editable'] = intval($data['editable']);
		return $tmp;
	}
	
	/**
	 * Resource_Service_Attribute::getJsonData
	 * 通过api获取分类属性的json
	 * @param unknown_type $categorys
	 * @param unknown_type $checkVer
	 * @param unknown_type $game_caini
	 * @param unknown_type $sp
	 * @param unknown_type $tjUrl
	 * @param unknown_type $detailUrl
	 * @param unknown_type $page
	 * @param unknown_type $hasnext
	 * @param unknown_type $intersrc
	 * @param unknown_type $source
	 */
	public static function  getJsonData($categorys, $checkVer, $game_caini, $sp, $tjUrl, $detailUrl, $page, $hasnext ,$intersrc, $source, $filter = array()) {
		$webroot = Common::getWebRoot();
		$temp = $tmp = $imgs = array();
		$sps = explode('_', $sp);
		$version = $sps[1];
		//判断是否为外发包
		$model = "client";
		if(stristr($webroot,'ala'))  $model = "channel";
		if(stristr($webroot,'kingstone'))  $model = "kingstone";
		//猜你喜欢
		if($game_caini && ($model == "client" || $model == "kingstone") && strnatcmp($version, '1.5.1') < 0){
			$game_img =  Game_Service_Config::getValue('game_img');
			$href = $webroot.'/'.$model.'/guess/index?intersrc=glike&t_bi='.$source;
			$tmp['id'] = 'caini';
			$tmp['title'] = '猜你喜欢';
			$tmp['link'] = Common::tjurl($webroot.$tjUrl, 'caini', 'guess', $href);
			$tmp['img'] = urldecode(Common::getAttachPath().$game_img);
			$tmp['data-infpage'] = $tmp['title'].','.urldecode($href);
			$tmp['game_title'] = self::getGuessGameData($sp,$filter);
			$tmp['viewType'] = 'CategoryDetailView';
			if(($model == "client" || $model == "kingstone") && $checkVer >= 2) $tmp['data-type'] = 0;
			$cn_img = $game_img;
		}
	
		foreach($categorys as $key=>$value) {
			$intersrc = 'CATEGORY'.$value['id'];
			$href = urldecode($webroot.$detailUrl. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$source);
			$temp[$key]['id'] = $value['id'];
			$temp[$key]['title'] = $value['title'];
			$temp[$key]['link'] = Common::tjurl($webroot.$tjUrl, $value['id'], $intersrc, $href);
			$temp[$key]['img'] = urldecode(Common::getAttachPath().$value['img']);
			$temp[$key]['data-infpage'] = $value['title'].','.urldecode($webroot.$detailUrl. '?id=' . $value['id'].'&intersrc='.$intersrc.'&t_bi='.$source);
			$temp[$key]['game_title'] = self::getGameData($value['id'],$sp, $filter);
			$temp[$key]['viewType'] = 'CategoryDetailView';
			if($checkVer >= 2 && ($model == "client" || $model == "kingstone")){
				$temp[$key]['data-type'] = 0;
			}
			$imgs[] = $value['img'];
		}
		if($game_caini && $page < 2 && ($model == "client" || $model == "kingstone") && strnatcmp($version, '1.5.1') < 0 ) array_unshift($temp, $tmp);
		if($game_caini && ($model == "client" || $model == "kingstone") && strnatcmp($version, '1.5.1') < 0 )  array_unshift($imgs, $cn_img);
		return array($temp, $imgs);
		 
	
	}
	
	/**
	 * Resource_Service_Attribute::getGameData
	 * 
	 * 获取前3个游戏的名称
	 * @param unknown_type $category_id
	 * @return string
	 */
	public static function getGameData($category_id,$sp,$filter = array()) {
		if (!$category_id) $this->output(-1, "");
		
		$sp = explode('_', $sp);
		$version = $sp[1];
		
		$cache = Common::getCache();
		if(strnatcmp($version, '1.5.1') < 0){
			$ckey = "-g-cid-" . $category_id;
		} else {
			$ckey = "-g-cid-v2" . $category_id;
		}
		$cgame = $cache->get($ckey);
		if ($cgame) return $cgame;
		
		//get games list
		$game_title = array();
		
		if($category_id == '100'){              //全部游戏
			$params = array('status'=>1);
			//过滤
			if($filter){
				$params['id'] = array('NOT IN', $filter);
			}
			if(strnatcmp($version, '1.5.1') < 0){
				$orderBy = array('id'=>'DESC');
			} else {
				$orderBy = array('online_time'=>'DESC');
			}
			list($total, $games) = Resource_Service_Games::getList(1, 3, $params, $orderBy);
		} else if($category_id == '101'){      //最新游戏
			$params = array('status'=>1);
			//过滤
			if($filter){
				$params['id'] = array('NOT IN', $filter);
			}
			$orderBy = array('online_time'=>'DESC');
			list($total, $games) = Resource_Service_Games::getList(1, 3, $params, $orderBy);
		} else {
			if($filter){
				$params['game_id'] = array('NOT IN', $filter);
			}
			if(strnatcmp($version, '1.5.1') < 0){
				$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
			} else {
				$orderBy = array('online_time'=>'DESC');
			}
			$params['parent_id'] = $category_id;;
			$params['game_status'] = 1;
			$params['status'] = 1;
			list($total, $games) = Resource_Service_GameCategory::getListByMainCategory(1, 3, $params, $orderBy);
		}
		 
		foreach($games as $key=>$value) {
			if ($value['game_id']) {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['game_id']));
			} else {
				$info = Resource_Service_Games::getGameAllInfo(array("id"=>$value['id']));
			}
			if ($info) {
				$game_title[] = $info['name'];
			}
		}
		$game_name = implode('、',$game_title);
		$cache->set($ckey, $game_name, 300);
		
		return $game_name;
	}
	
	/**
	 * Resource_Service_Attribute::getGuessGameData
	 * @param unknown $sp
	 * @param unknown $filter
	 * @return string
	 */
	public static function getGuessGameData($sp, $filter = array()) {
		if($sp) {
			$imei = end(explode('_',$sp));
			$imei = crc32(trim($imei));
			$sp = explode('_', $sp);
			$version = $sp[1];
		}
		
		$gues = Client_Service_Guess::getGamesByImCrc( $imei );
		$ids = explode(',',$gues['game_ids']);
	
		if($gues){
			$orderBy = array();
			if($ids) $gues_params['id'] = array('IN',$ids);
			if($ids) $orderBy = array('FIELD'=>array('id', $ids));
			$gues_params['status'] = 1;
			list(, $games) = Resource_Service_Games::search2(1, 8, $gues_params, $orderBy);
	
		}
		//如果猜你喜欢没有数据家用默认的代替
	 	if(!$gues || !$sp){
			$orderBy = array('sort'=>'DESC','game_id'=>'DESC');
			$params['game_status'] = 1;
			$params['status'] = 1;
			list(, $games) = Client_Service_Game::geGuesstList(1, 8, $params,$orderBy);
		} 
		
		$i = 0;
		$game_title = array();
		foreach($games as $key=>$value) {
			$info = Resource_Service_Games::getGameAllInfo(array("id"=>($gues?$value['id'] : $value['game_id'])));
			if ($info && $i < 3) {
				$game_title[] = $info['name'];
				$i++;
			}
		}
		$game_name = implode('、',$game_title);
		return $game_name;
	}
	
	/**
	 * 数据变更缓存变更通知
	 */
	public static function notifyCache($type){
		$subjct = new Common_Service_Subject(array('type'=>$type));
		//属性缓存观察者
		$cacheObserver = new Resource_Service_AttributeCacheObserver();
		$subjct->attach($cacheObserver);
		$subjct->notify();
	}
	
	
	/**
	 * 
	 * @return Resource_Dao_Attribute
	 */
	private static function _getDao() {
		return Common::getDao("Resource_Dao_Attribute");
	}
}
