<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Comment extends Common_Service_Base{
    const STATE_WAIT_FOR_REVIEW = 1;
    const STATE_REVIEW_NOT_PASS = 2;
    const STATE_REVIEW_PASS = 3;
    const ILLEGAL_IMEI = 'FD34645D0CF3A18C9FC4E2C49F11C510';

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllComment() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public static function getAllSortComment() {
		return self::_getDao()->getAllSortComment();
	}
	
	/**
	 * 
	 * Enter description here ...
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
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getSearchList($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$sqlWhere = self::_getDao()->_cookParams($params);
		$ret = self::_getDao()->searchBy($start, $limit, $sqlWhere, $orderBy);
		$total = self::_getDao()->searchCount($sqlWhere);
		return array($total, $ret);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $params 二维数组，一级数组各元素之间使用OR关系, 二级数组各元素之间使用AND关系
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 */
	public static function getListExt($page = 1, $limit = 10, $params = array(), $orderBy = array('id'=>'DESC')) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		return self::_getDao()->getListExt($start, $limit, $params, $orderBy);
	}
	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return Ambigous <boolean, mixed, multitype:>
	 */
	public static function getsByComment($params = array(),$orderBy = array('id'=>'DESC')) {
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 * @param unknown_type $orderBy
	 * @return Ambigous <boolean, mixed>
	 */
	public static function getByComment($params = array(),$orderBy = array('create_time'=>'DESC')) {
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getComment($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateComment($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function updateCommentStatus($data,$statu,$comment_name) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			
			$params = $search = $tmp = array();
					
			
			$cmm_info = Client_Service_Comment::getComment($value);
			//插入一条审核记录日志
			$info = array(
					'id'=>'',
					'comment_id'=>$cmm_info['id'],
					'check_name'=>$comment_name,
					'check_time'=>Common::getTime(),
					'title'=>$cmm_info['title'],
					'badwords'=>$cmm_info['badwords'],
					'uname'=>$cmm_info['uname'],
					'nickname'=>$cmm_info['nickname'],
					'imei'=>$cmm_info['imei'],
					'imcrc'=>$cmm_info['imcrc'],
					'game_id'=>$cmm_info['game_id'],
					'create_time'=>$cmm_info['create_time'],
					'is_sensitive'=>$cmm_info['is_sensitive'],
					'is_filter'=>$cmm_info['is_filter'],
					'model'=>$cmm_info['model'],
					'version'=>$cmm_info['version'],
					'sys_version'=>$cmm_info['sys_version'],
					'is_top'=>$cmm_info['is_top'],
					'top_time'=>$cmm_info['top_time'],
					'utype'=>$cmm_info['utype'],
					'status'=>$statu,
					'is_del'=>$cmm_info['is_del'],
					'is_blacklist'=>$cmm_info['is_blacklist'],
					'client_pkg'=>$cmm_info['client_pkg'],
			);
			$log_id = Client_Service_CommentLog::add($info);
			
			//更新评论
			$ret = self::_getDao()->update(array('status'=>$statu,'check_time'=>Common::getTime()), $value);
			//插入一条审核操作日志
			$tmp = array(
					'id'=>'',
					'comment_id' => $value,
					'comment_log_id' => $log_id,//($info_log ? $info_log['id'] : $log_id),
					'check_name'=> $comment_name,
					'check_time'=> Common::getTime(),
					'status'=> $statu,
					'operate'=> 0
					);
			$rets = Client_Service_CommentOperatLog::add($tmp);
		}
		return $ret;
	}
	
	/**
	 * 批量删除评论记录
	 * @param unknown_type $data
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteByComments($data,$comment_name) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$ret = self::updateComment(array('is_del'=>1),$value);
			$ret = Client_Service_CommentLog::updateLog(array('is_del'=>1),array('comment_id'=>$value));
			//插入一条删除操作日志
			$log = Client_Service_CommentLog::getBy(array('comment_id'=>$value));
			$tmp = array(
					'id'=>'',
					'comment_id' => $value,
					'comment_log_id' => $log['id'],
					'check_name'=> $comment_name,
					'check_time'=> Common::getTime(),
					'status'=> $log['status'],
					'operate'=> 1
			);
			$ret = Client_Service_CommentOperatLog::add($tmp);
		}
		return $ret;
	}
	
	public static function topComments($data,$top_statu) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			$top_time = 0;
			if($top_statu) $top_time = Common::getTime() + 7 * 24 *3600;//默认的有效的置顶时间为一周
			//更新评论置顶时间
			$ret = self::_getDao()->update(array('is_top'=>$top_statu,'top_time'=>$top_time), $value);
			//更新评论日志置顶时间
			$ret = Client_Service_CommentLog::updateLog(array('is_top'=>$top_statu,'top_time'=>$top_time), array('comment_id'=>$value));
		}
		return $ret;
	}
	
	public static function topUpdateComments($data,$top_statu) {
		if (!is_array($data)) return false;
		foreach($data as $key=>$value){
			 $top_time = Common::getTime() + 7 * 24 *3600;//默认的有效的置顶时间为一周
			//将原来置顶的取消
			$info = self::getComment($value);
			$ret = self::updateByComment(array('is_top'=>0,'top_time'=>''), array('game_id'=>$info['game_id'],'status'=>3,'is_del'=>0,'is_top'=>1));
			//更新评论置顶时间
			$ret = self::_getDao()->update(array('is_top'=>$top_statu,'top_time'=>$top_time), $value);
			//更新评论日志置顶时间
			$ret = Client_Service_CommentLog::updateLog(array('is_top'=>$top_statu,'top_time'=>$top_time), array('comment_id'=>$value));
		}
		return $ret;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteComment($id) {
		if (!$id) return false;
		return self::_getDao()->delete(intval($id));
	}
	
	/**
	 *
	 * @param unknown_type $status
	 * @param unknown_type $game_id
	 * @return boolean|Ambigous <boolean, number>
	 */
	public static function deleteByComment($params) {
		return self::_getDao()->deleteBy($params);
	}
	
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function updateByComment($data, $params) {
		return self::_getDao()->updateBy($data, $params);
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function add($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	
	public static function addComment($data, $score, $game_id, $uname, $imei, $imeicrc, $isLogin, $scorelog) {
		if (!is_array($scorelog)) return false;
		  //开始事务
		  $trans = parent::beginTransaction();
	      try {
	      	$params = $score_info = $search = array();
	      	$params['game_id'] = $game_id;
	      	$params['is_del'] = 0;
	      	$search['game_id'] = $game_id;
	      	if($isLogin == 'true') {
	      		$params['uname'] = $uname;
	      		$search['user'] = $uname;
	      	} else {
	      		$params['imcrc'] = $imeicrc;
	      		$search['imei'] = $imei;
	      	}
	      	if($data){
	      		//没有的话，找一条最新的评论修改
	      		$comment = self::getByComment($params);
	      		//如果最新的是已经审核通过的或者没有，新增
	      		if(($comment && $comment['status'] == self::STATE_REVIEW_PASS) || !$comment){  
	      			$ret = self::add($data);
	      			if (!$ret) throw new Exception('Add Comment fail.', -203);
	      		} else if($comment){   
	      			//如果最新的不是已经审核通过的，则替换
	      			unset($data['id']);
	      			$ret = self::updateByComment($data, array('id'=>$comment['id']));
	      			if (!$ret) throw new Exception('Update Comment fail.', -204);
	      		}
	      	}
	      	
		     $total_info = Resource_Service_Score::getByScore(array('game_id'=>$game_id));
		     $log = Resource_Service_Score::getByLog($search);
		     
		     //计算该游戏的平均分
		     if($total_info) {
		     	//该游戏有评分记录就更新
		     	$number = $total_info['number'] + ($log ? 0 : 1);
		     	//查找该用户有没有评分日志,如果之前该用户有评分，总分要减掉之前的评分 总分计算
		     	$total_score = ($total_info['total'] - ($log ? $log['score'] : 0)) + $score;
		     	$avg_score = Resource_Service_Score::avgScore($total_score, $number);
		     	$ret = Resource_Service_Score::updateGameScore(array('score'=>$avg_score,'total' => $total_score, 'number'=>$number, 'update_time'=>Common::getTime()),array('game_id'=>$game_id));
		     	if (!$ret) throw new Exception('Update Score fail.', -203);
		     } else {
		     	//该游戏没有记录就添加该评分
		     	$score_info = array(
		     			'id'=>'',
		     			'game_id'=>$game_id,
		     			'score'=>Resource_Service_Score::avgScore($score, 1),
		     			'total'=>$score,
		     			'number'=>1,
		     			'update_time'=>Common::getTime(),
		     	);
		     	$ret = Resource_Service_Score::add($score_info);
		     	if (!$ret) throw new Exception('Add Score fail.', -204);
		     }
		     
		     //查找该用户有没有评分日志.有，更新原来的评分；无，添加一条记录	      
		     if(!$log) {    //添加评分日志
		     	$ret = Resource_Service_Score::addLog($scorelog);
		     	if (!$ret) throw new Exception('Add ScoreLog fail.', -204);
		     } else {       //更新原来的评分
		     	$ret = Resource_Service_Score::updateGameScoreLog(array('score'=>$score),$search);
		     	if (!$ret) throw new Exception('Update ScoreLog fail.', -205);
		     }
		    
		     //事务提交
		     if($trans)  parent::commit();
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
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['badwords'])) $tmp['badwords'] = $data['badwords'];
		if(isset($data['uname'])) $tmp['uname'] = $data['uname'];
		if(isset($data['uuid'])) $tmp['uuid'] = $data['uuid'];
		if(isset($data['nickname'])) $tmp['nickname'] = $data['nickname'];
		if(isset($data['imei'])) $tmp['imei'] = $data['imei'];
		if(isset($data['imcrc'])) $tmp['imcrc'] = intval($data['imcrc']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		if(isset($data['check_time'])) $tmp['check_time'] = $data['check_time'];
		if(isset($data['is_sensitive'])) $tmp['is_sensitive'] = $data['is_sensitive'];
		if(isset($data['is_filter'])) $tmp['is_filter'] = $data['is_filter'];
		if(isset($data['model'])) $tmp['model'] = $data['model'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['sys_version'])) $tmp['sys_version'] = $data['sys_version'];
		if(isset($data['is_top'])) $tmp['is_top'] = $data['is_top'];
		if(isset($data['top_time'])) $tmp['top_time'] = $data['top_time'];
		if(isset($data['utype'])) $tmp['utype'] = $data['utype'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['is_del'])) $tmp['is_del'] = $data['is_del'];
		if(isset($data['is_blacklist'])) $tmp['is_blacklist'] = $data['is_blacklist'];
		if(isset($data['client_pkg'])) $tmp['client_pkg'] = $data['client_pkg'];
		return $tmp;
	}

	/**
	 * 
	 * @return Client_Dao_Comment
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Comment");
	}
	
	
	/**
	 *
	 * @return Client_Dao_CommentLog
	 */
	private static function _getCommentlogDao() {
		return Common::getDao("Client_Dao_CommentLog");
	}
	
	/**
	 *
	 * @return Client_Dao_CommentLog
	 */
	private static function _getCommentOperatlogDao() {
		return Common::getDao("Client_Dao_CommentOperatLog");
	}
}
