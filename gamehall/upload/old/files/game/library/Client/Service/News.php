<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_News{
    const ARTICLE_TYPE_NEWS = 1;
    const ARTICLE_TYPE_EVALUATION = 2;
    const ARTICLE_TYPE_ACTIVITY = 3;
    const ARTICLE_TYPE_STRATEGY = 4;
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getList($page = 1, $limit = 10, $params = array(), $orderBy = array('sort'=>'DESC','create_time'=>'DESC','id' =>'ASC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	/**
	 * 
	 * @param unknown $params
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getNewsByIds($params=array()){
		return self::_getDao()->getsBy($params);
	}
	/**
	 *
	 * @param int $type
	 * @return boolean|Ambigous <multitype:, multitype:>
	 */
	public static function getAllNewsOutId($type) {
		if (!intval($type)) return false;
		return self::_getDao()->getsBy(array('ntype'=>intval($type)));
	}
	
	/**
	 * 获取所有评测
	 * @return array
	 */
	public static function getAllEvaluation(){
		return array(self::_getDao()->count(array('ntype' => 2)), self::_getDao()->getsBy(array('ntype' => 2)));
	}
	
	/**
	 * 
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @param unknown $orderBy
	 * @return array:
	 */
	public static function getAdEvaluation($page = 1, $limit = 10, $params = array(), $orderBy = array()){
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$Where = Db_Adapter_Pdo::sqlWhere($params);
		$ret = self::_getDao()->searchBy($start, $limit, $Where, $orderBy);
		$total = self::_getDao()->searchCount($Where);
		return array($total, $ret);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNews($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}

	/**
	 * 根据时间搜索
	 * @param unknown_type $params
	 * @param unknown_type $start_time
	 * @param unknown_type $end_time
	 * @return multitype:unknown multitype:
	 */
	public function getUseNews($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$params['create_time'] = array('<=', Common::getTime());
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC' , 'create_time' => 'DESC' , 'id' => 'ASC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 * 获取游戏详情关联News
	 * @param number $page
	 * @param number $limit
	 * @param unknown $params
	 * @return multitype:unknown multitype:
	 */
	public function getGameNews($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$params['create_time'] = array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
		$ret = self::_getDao()->getList($start, $limit, $params, array('create_time'=>'DESC','id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	/**
	 *
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getUseAdNews($page = 1, $limit = 10, $params = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$params['create_time'] = array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
		$ret = self::_getDao()->getList($start, $limit, $params, array('sort'=>'DESC' , 'create_time' => 'DESC' , 'id' => 'ASC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}

	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateApiNewsSort($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateApiNewsStatus($data,$statu) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$info = Client_Service_News::getNews($value);
			$tj_info = Client_Service_Tuijian::getTuijiansByNId($info['id']);
			if($tj_info){
				Client_Service_Tuijian::updateTuijianStatus($tj_info['id'],$statu);
			}
		}
		//UPDATE %s SET status = %s WHERE id IN %s
		return self::_getDao()->updateBy(array('status'=>$statu),array('id'=>array('IN', $data)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addNews($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateNews($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteNews($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 * 
	 * cron news use
	 * Enter description here ...
	 */
	public static function getAllNews() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}

	/**
	 * cron news use
	 * @return multitype:
	 */
	public static function getGameClientNewsAll() {
		return 	self::_getIdxGameClientNewsDao()->getAll();
	}
	
	/**
	 * cron news use
	 * @return multitype:
	 */
	public static function updateGameClientNewsByOutId($ntype,$id,$out_id) {
		if (!intval($out_id)) return false;
		return 	self::_getIdxGameClientNewsDao()->updateBy(array('ntype'=>$ntype, 'n_id'=>$id), array('out_id'=>$out_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 */
	public static function adsearch($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere = self::_getDao()->_cookParams($params);
		$sort = array('sort' => 'DESC',	'id' => 'DESC');
		if (count($params['aid'])) $sort = array('FIELD' => self::_getDao()->quoteAdInArray($params['aid']));
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
	public static function search($page = 1, $limit = 10, $params = array(), $orderBy = array()) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere  = self::_getDao()->_cookParams($params);
		$sort = array('sort'=>'DESC','id'=>'DESC');
		if (count($params['ids'])) $sort = array('FIELD'=>self::_getDao()->quoteInArray($params['ids']));
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $sort);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}

		
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getNewsByOutId($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getsBy(array('out_id'=>intval($id)));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateNewsTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteBatchNews($data) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value) {
			self::_getDao()->deleteBy(array('id'=>$value));
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateBatchNews($ids,$status) {
		if (!is_array($ids)) return false;
		return self::_getDao()->updateBatchNews($ids, $status);
	}
	
	/**
	 * 
	 * @param unknown_type $out_id
	 * @return boolean|Ambigous <boolean, mixed>
	 */
	public static function getGameClientNewsId($out_id) {
		if (!intval($out_id)) return false;
		return 	self::_getIdxGameClientNewsDao()->getBy(array('out_id'=>$out_id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addApiNews($data) {
		//注意字段跟数据库结构顺序
		$tmp = array();
		$time = Common::getTime();
		foreach($data as $key=>$value) {
			$tmp[] = array(
					'id'=>'',
					'sort'=> 0,
					'out_id'=>$value['id'],
					'game_id'=>'',
					'title'=>$value['title'],
					'resume'=>$value['resume'],
					'thumb_img'=>$value['thumb_img'],
					'name'=>'',
					'ntype'=>$value['ntype'],
					'ctype'=>$value['ctype'],
					'collect'=>0,
					'status'=>1,
					'start_time'=>$time,
					'end_time'=>$time,
					'content'=>$value['content'],
					'fromto'=>$value['from'],
					'create_time'=>$value['time'],
					'hot'=>0
			);
		}
	
		return self::_getDao()->mutiInsert($tmp);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['sort'])) $tmp['sort'] = intval($data['sort']);
		if(isset($data['out_id'])) $tmp['out_id'] = intval($data['out_id']);
		if(isset($data['game_id'])) $tmp['game_id'] = intval($data['game_id']);
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['resume'])) $tmp['resume'] = $data['resume'];
		if(isset($data['thumb_img'])) $tmp['thumb_img'] = $data['thumb_img'];
		if(isset($data['name'])) $tmp['name'] = $data['name'];
		if(isset($data['ntype'])) $tmp['ntype'] = $data['ntype'];
		if(isset($data['ctype'])) $tmp['ctype'] = intval($data['ctype']);
		if(isset($data['collect'])) $tmp['collect'] = intval($data['collect']);
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['start_time'])) $tmp['start_time'] = intval($data['start_time']);
		if(isset($data['end_time'])) $tmp['end_time'] = intval($data['end_time']);
		if(isset($data['content'])) $tmp['content'] = $data['content'];
		if(isset($data['fromto'])) $tmp['fromto'] = $data['fromto'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['hot'])) $tmp['hot'] = intval($data['hot']);
		if(isset($data['ids'])) $tmp['ids'] = $data['ids'];
		if(isset($data['aid'])) $tmp['aid'] = $data['aid'];
		if(isset($data['st'])) $tmp['st'] = $data['st'];
		return $tmp;
	}
	
    public static function getCount($params) {
		return self::_getDao()->count($params);
	}
	
    public static function getBy($params = array() , $orderBy = array('id'=>'ASC')){
		$ret = self::_getDao()->getBy($params ,$orderBy);
		if(!$ret) return false;
		return $ret;

	}
	
	/**
	 * 
	 * @return Client_Dao_News
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_News");
	}
	
	/**
	 *
	 * @return Client_Dao_IdxGameClientNews
	 */
	private static function _getIdxGameClientNewsDao() {
		return Common::getDao("Client_Dao_IdxGameClientNews");
	}
}
