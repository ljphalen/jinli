<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author lichanghua
 *
 */
class Client_Service_Ad{
	//------------------广告类型----------------------//
	const AD_TYPE_TURN = 1;           //置顶广告
	const AD_TYPE_RECOMMEND = 2;      //广告位1
	const AD_TYPE_SUBJECT = 3;        //首页推荐列表
	const AD_TYPE_START = 6;          //启动广告
	const AD_TYPE_CHANNEL = 7;        //频道管理
	const AD_TYPE_ACTIVITY = 8;       //文字公告
	const AD_TYPE_PICTURE = 9;        //图片广告
	const AD_TYPE_RECPIC = 10;        //推荐图片列表
	const AD_TYPE_MAGNETICON = 11;    //磁铁图标
    const AD_TYPE_BGIMG = 12;         //背景图
    
    //------------------广告连接类型----------------------//
    const ADLINK_TYPE_GAMEID = 1;    //游戏内容
    const ADLINK_TYPE_CATEGOTY = 2;  //分类
    const ADLINK_TYPE_SUBJECT = 3;   //专题
    const ADLINK_TYPE_LINK = 4;      //外链
    const ADLINK_TYPE_ACTIVITY = 5;  //活动
    const ADLINK_TYPE_GIFT = 6;      //礼包
    
    
    const AD_STATUS_OPEN = 1;         //广告开启状态
    const AD_STATUS_CLOSE = 0;        //广告关闭状态
    
    const AD_POSITION_HOME = 1;        //首页广告位置
    const AD_TYPE_POSITION_WEB = 2;    //网游广告位置
    const AD_TYPE_POSITION_SINGLE = 3; //单机广告位置

	/**
	 * 
	 * Enter description here ...
	 */
	public static function getAllAd() {
		return array(self::_getDao()->count(), self::_getDao()->getAll());
	}
	
	/**
	 * 8.20-改造
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public static function getCanUseAds($page, $limit, $params = array(), $orderBy = array('sort'=>'DESC','id'=>'DESC')) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, $orderBy);
		$total = self::_getDao()->count($params);
		return array($total, $ret); 
	}
	
	public static function getAdCount($params) {
	    return self::_getDao()->count($params);
	}
	
	/**
	 * 8.20 改造
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseNormalAds($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$time = strtotime(date('Y-m-d H:i'));
		$params['status']=1;
		$params['start_time']=array('<', $time);
		$params['end_time']=array('>', $time);
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort'=>'DESC','id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	
	/**
	 * 8.20 号改造
	 * @param unknown_type $page
	 * @param unknown_type $limit
	 * @param unknown_type $params
	 * @return multitype:unknown
	 */
	public function getCanUseApiAds($page, $limit, $params = array()) {
		if(intval($page) < 1) $page = 1;
		$start = ($page - 1) * $limit;
		$time = strtotime(date('Y-m-d H:i'));
		$params['status']=1;
		$params['start_time']=array('<', $time);
		$params['end_time']=array('>', $time);
		if($params['not_ids']){
			$params['link'] = array('NOT IN', $params['not_ids']);
		}
		unset($params['not_ids']);
		$ret = self::_getDao()->getList(intval($start), intval($limit), $params, array('sort'=>'DESC','id'=>'DESC'));
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
    /**
     * 
     * @param unknown_type $page
     * @param unknown_type $limit
     * @param unknown_type $params
     * @return multitype:unknown multitype:
     */
	public static function getList($page = 1, $limit = 10, $params = array(),$order =array('sort'=>"DESC")) {
		if ($page < 1) $page = 1; 
		$start = ($page - 1) * $limit;
		$ret = self::_getDao()->getList($start, $limit, $params, $order);
		$total = self::_getDao()->count($params);
		return array($total, $ret);
	}
	
	public static function getListByOffset($offset,$limit=10,$params=array(),$order=array()){
		if($offset<0 ) $offset = 0;
		$ret = $ret = self::_getDao()->getList($offset, $limit, $params, $order);
		$total = self::_getDao()->count($params);
		return array($total,$ret);
	}
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function updateAdTJ($id) {
		if (!$id) return false;
		return self::_getDao()->increment('hits', array('id'=>intval($id)));
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getAd($id) {
		if (!intval($id)) return false;
		return self::_getDao()->get(intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function getByLinkAd($id) {
		if (!intval($id)) return false;
		return self::_getDao()->getsBy(array('link'=>$id));
	}
	
	/**
	 *
	 * @param unknown_type $id
	 * @return Ambigous <boolean, number>
	 */
	public static function getAdGames(array $params = array()) {
		$result = self::_getDao()->getsBy($params, array('sort'=>'DESC','id'=>'DESC'));
		$count = self::_getDao()->count($params);
		return array($count, $result);
	}
	
	/**
	 * 
	 * @param unknown_type $params
	 */
	public static function getsBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	public static function getsByStartTime($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		if($params['start_time']){
			$where .= "((start_time <='".$params['start_time']."' AND end_time >='".$params['start_time']."') OR start_time >='".$params['start_time']."') AND";
			unset($params['start_time']);
		}
		$where = $where.Db_Adapter_Pdo::sqlWhere($params);

		$sort = Db_Adapter_Pdo::sqlSort($orderBy);
		$sql = sprintf('SELECT * FROM %s WHERE %s %s', self::_getDao()->getTableName(), $where, $sort);
		return Db_Adapter_Pdo::fetchAll($sql);
	}
	
	/**
	 *
	 * @param unknown_type $params
	 */
	public static function getBy($params = array(), $orderBy = array()) {
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateAd($data, $id) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->update($data, intval($id));
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $data
	 * @param unknown_type $id
	 */
	public static function updateByAd($data, $params) {
		if (!is_array($data)) return false;
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $cid
	 * @return Ambigous <boolean, number>
	 */
	public static function addGameAd($data, $ad_type) {
		$tmp = array();
		$time = Common::getTime();
		
		foreach($data as $key=>$value) {
			$info = self::getBy(array('ad_type'=>3,'link'=>$value));
			if(!$info){
				$tmp[] = array(
						'id'=>'',
						'sort'=>0,
						'title'=>'',
						'head'=>'',
						'ad_type'=> $ad_type,
						'ad_ptype'=>1,
						'link'=>$value,
						'img'=>'',
						'icon'=>'',
						'start_time'=>$time,
						'end_time'=>'',
						'status'=>1,
						'hits'=>0,
				);
			}
		}
		$ret = true;
		if($tmp) $ret =  self::_getDao()->mutiInsert($tmp);
		return $ret;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortGameAd($data, $sorts) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->update(array('sort'=>$sorts[$v[1]]), $v[0]);
		}
		return true;
	}
	
	
	/**
	 *
	 * @param unknown_type $data
	 * @param unknown_type $sorts
	 * @return boolean
	 */
	public static function sortAd($sorts) {
		foreach($sorts as $key=>$value) {
			self::_getDao()->update(array('sort'=>$value), $key);
		}
		return true;
	}
	
	/**
	 *
	 * @param unknown_type $data
	 * @return boolean
	 */
	public static function deleteGameAd($data) {
		foreach($data as $key=>$value) {
			$v = explode('|', $value);
			self::_getDao()->deleteBy(array('id'=>$v[0]));
		}
		return true;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 */
	public static function deleteAd($id) {
		return self::_getDao()->delete(intval($id));
	}
	
	public static function getDataAd($type,$title,$id,$img,$intersrc='',$t_bi='') {
		if(!$type && !$id) return false;
		$dataInfo = array();
		$webroot = Common::getWebRoot();
		$model = "client";
		//外发包处理
		if(stristr($webroot,'ala'))  $model = "channel";
		if(stristr($webroot,'kingstone'))  $model = "kingstone";
		
		if($type == 1){  //内容
			$info = Resource_Service_Games::getGameAllInfo(array('id'=>intval($id)), '', Common::getAttachPath());
			if(!$info['status']) return false;
			$dataInfo['data-Info'] = sprintf("%s,%s,%s,%s,%s,%s,%s,%s", $info['name'], $webroot."/".$model."/index/detail/?id=".$id."&pc=1&intersrc=".$intersrc."&t_bi=".$t_bi, $id, $info['link'], $info['package'], $info['size'],'Android'.$info['min_sys_version_title'], $info['min_resolution']."-".$info['max_resolution']);
			$dataInfo['img'] = $img;
			//1.4.8版本之后加入
			$dataInfo['type'] = $type;
			$dataInfo['data-Info-v2'] = sprintf("%s,%s,%s",'游戏详情', $webroot."/".$model."/index/detail/?id=".$id."&pc=1&intersrc=".$intersrc."&t_bi=".$t_bi, $id);
			
			//1.5.1版本之后加入
			$dataInfo['viewType'] = 'GameDetailView';
			$dataInfo['title'] = $title;
			$dataInfo['imageUrl'] = Common::getAttachPath().$img;
			$dataInfo['param'] = array(
									'url'=>'',
									'contentId'=>$info['id'],
									'gameId'=>$info['id'],
					                'title'=>html_entity_decode($title),
					             );
			
			
			//加入评测链接
			$evaluationId = Client_Service_IndexAdI::getEvaluationByGame($id);
			$evaluationUrl = '';
			if ($evaluationId) {
				$evaluationUrl = ',评测,' . $webroot . '/'.$model.'/evaluation/detail/?id=' . $evaluationId.'&pc=3&intersrc='. $intersrc . '&t_bi=' .$t_bi;
				$dataInfo['data-Info-v2'] .= $evaluationUrl;
			}
			$dataInfo['data-type'] = '1';
			
		} else if($type == 2){ //分类
			$info = Resource_Service_Attribute::getBy(array('id'=>$id));
			if(!$info['status']) return false;
			$dataInfo['data-Info'] = sprintf("%s,%s", $title, $webroot ."/".$model."/category/detail/?id=".$id."&intersrc=".$intersrc."&t_bi=".$t_bi);
			$dataInfo['img'] = $img;
			//1.4.8版本之后加入
			$dataInfo['type'] = $type;
			$dataInfo['data-type'] = '0';
			
			//1.5.1版本之后加入
			$dataInfo['viewType'] = 'CategoryDetailView';
			$dataInfo['title'] = $title;
			$dataInfo['imageUrl'] = Common::getAttachPath().$img;
			$dataInfo['param'] = array(
									'url'=>'',
									'contentId'=>$info['id'],
									'gameId'=>'',
					                'title'=>html_entity_decode($title),
					             );
			
		} else if($type == 3){ //专题
			$info = Client_Service_Subject::getSubject($id);
			if(!$info['status']) return false;
			$dataInfo['data-Info'] = sprintf("%s,%s", $title, $webroot ."/".$model."/subject/detail/?id=".$id."&intersrc=".$intersrc."&t_bi=".$t_bi);
			$dataInfo['img'] = $img;
			//1.4.8版本之后加入
			$dataInfo['type'] = $type;
			$dataInfo['data-type'] = '0';

			//1.5.1版本之后加入
			$dataInfo['viewType'] = 'TopicDetailView';
			$dataInfo['title'] = $title;
			$dataInfo['imageUrl'] = Common::getAttachPath().$img;
			$dataInfo['param'] = array(
									'url'=>'',
									'contentId'=>$info['id'],
									'gameId'=>'',
					                'title'=>html_entity_decode($title),
					             );
		} else if($type == 4){ //外链
			$info = self::getAd(intval($id));
			if (strpos(html_entity_decode($id), "?")) {
				$anchor = $id.'&intersrc='.$intersrc.'&t_bi='.$t_bi;
			} else {
				$anchor = $id.'?intersrc='.$intersrc.'&t_bi='.$t_bi;
			}
			$dataInfo['data-Info'] = sprintf("%s,%s", $title, $anchor);
			$dataInfo['img'] = $img;
			//1.4.8版本之后加入
			$dataInfo['type'] = $type;
			$dataInfo['data-type'] = '3';
			
			//1.5.1版本之后加入
			$dataInfo['viewType'] = 'Link';
			$dataInfo['title'] = $title;
			$dataInfo['imageUrl'] = Common::getAttachPath().$img;
			$dataInfo['param'] = array(
									'url' => html_entity_decode($anchor),
									'contentId'=>'',
									'gameId'=>'',
					                'title'=>html_entity_decode($title),
					             );;
		} else if($type == 5){ //活动
			//1.5.1版本之后加入
			$params['start_time'] = array('<=',Common::getTime());
			$params['status'] = 1;
			$params['id'] = $id;
			$orderBy = array('sort'=>'DESC','start_time'=>'DESC');
			list(, $hd) = Client_Service_Hd::getList(1, 1, $params, $orderBy);
			$hd = $hd[0];
			
			if ($hd ['status'] && $hd) {
				$dataInfo ['title'] = $title;
				$dataInfo ['imageUrl'] = Common::getAttachPath() . $img;
				$dataInfo ['viewType'] = 'ActivityDetailView';
				$dataInfo ['param'] = array(
						'url'=>'',
						'contentId'=>$id,
						'gameId'=>$hd['game_id'],
						'title'=>html_entity_decode($title),
				);
			}
		}
		return $dataInfo;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function addAd($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = $data['id'];
		if(isset($data['sort'])) $tmp['sort'] = $data['sort'];
		if(isset($data['ad_type'])) $tmp['ad_type'] = $data['ad_type'];
		if(isset($data['ad_ptype'])) $tmp['ad_ptype'] = $data['ad_ptype'];
		if(isset($data['title'])) $tmp['title'] = $data['title'];
		if(isset($data['head'])) $tmp['head'] = $data['head'];
		if(isset($data['link'])) $tmp['link'] = $data['link'];
		if(isset($data['img'])) $tmp['img'] = $data['img'];
		if(isset($data['icon'])) $tmp['icon'] = $data['icon'];
		if(isset($data['start_time'])) $tmp['start_time'] = $data['start_time'];
		if(isset($data['end_time'])) $tmp['end_time'] = $data['end_time'];
		if(isset($data['status'])) $tmp['status'] = $data['status'];
		if(isset($data['not_ids'])) $tmp['not_ids'] = $data['not_ids'];
		if(isset($data['hits'])) $tmp['hits'] = $data['hits'];
		return $tmp;
	}
	
	/**
	 * 
	 * @return Client_Dao_Ad
	 */
	private static function _getDao() {
		return Common::getDao("Client_Dao_Ad");
	}
}
