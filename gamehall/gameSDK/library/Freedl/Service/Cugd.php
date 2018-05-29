<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Freedl_Service_Cugd
 * @author fanch
 *
 */
class Freedl_Service_Cugd extends Common_Service_Base{

	/**
	 * 按条件检索单条记录
	 * @param array $params
	 * @param array $orderBy
	 * @return 
	 */
	public static function getBy($params, $orderBy = array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getBy($params, $orderBy);
	}
	
	/**
	 * 按条件检索单条记录
	 * @param array $params
	 * @param array $orderBy
	 * @return
	 */
	public static function getsBy($params, $orderBy = array()){
		if (!is_array($params)) return false;
		return self::_getDao()->getsBy($params, $orderBy);
	}
	
	/**
	 * 检查游戏id是否是指定活动中专区免流量类型的游戏
	 * @param int $gameid
	 * @return bool
	 */
	public static function checkFreedlGame($gameid){
		//联通内容库上线同时游戏内容库也是上线的
		$ret =  self::_getDao()->getBy(array('cu_status' => array('IN', array(2, 4)) , 'game_status'=>1 , 'game_id'=>$gameid));
		return $ret['id'] ? true : false;
	}
	
	/**
	 * 获取列表数据
	 * Enter description here ...
	 * @param array $params
	 * @param int $page
	 * @param int $limit
	 * @param array $orderBy
	 * @return array
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
	 * 更新联通免流量游戏内容库
	 * @param array $data
	 * @param array $params
	 */
	public static function updateCugd($data, $params) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->updateBy($data, $params);
	}
	
	/**
	 *
	 * 添加联通免流量游戏内容库资源
	 * @param array $data
	 */
	public static function addCugd($data) {
		if (!is_array($data)) return false;
		$data = self::_cookData($data);
		return self::_getDao()->insert($data);
	}
	
	/**
	 *
	 * 删除联通免流量游戏内容库数据
	 * @param array $data
	 */
	public static function deleteCugd($data) {
		if (!is_array($data)) return false;
		return self::_getDao()->deleteBy($data);
	}
	
	/**
	 * 填充资源数据
	 * @param unknown $game
	 * @return array
	 *
	 * 	1073	掌上网游
	 *	1074	其他
	 *	1075	休闲游戏
	 *	1076	益智游戏
	 *	1077	棋牌游戏
	 *	1078	体育运动
	 *	1079	动作射击
	 */
	public static function fillData($game){
		$default = array('1074','其他');
        $game_cutype = array();
        if (Util_Environment::isOnline()) {
            //正式站类型对接配置
             $game_cutype = array(
                     '1'=> array('1075','休闲游戏'), //'休闲益智'
                     '3'=> array('1079','动作射击'), //'动作冒险'
                     '4'=> array('1077','棋牌游戏'), //'扑克棋牌'
                     '5'=> array('1076','益智游戏'), //'塔防游戏'
                     '8'=> array('1075','休闲游戏'), //'模拟经营'
                     '13'=> array('1073','掌上网游'), //'角色扮演'
                     '32'=> array('1073','掌上网游'), //'手机网游'
                     '35'=> array('1079','动作射击'), //'飞行射击'
                     '36'=> array('1078','体育运动'), //'跑酷竞速'
                     '151'=> array('1077','棋牌游戏'), //'卡牌游戏'
                     '152'=> array('1074','其他'), //'免费游戏'
                     '154'=> array('1078','体育运动'), //'体育竞技'
                     '155'=> array('1076','益智游戏'), //'消除游戏'
                     '156'=> array('1075','休闲游戏'), //'音乐游戏'
                     '157'=> array('1074','其他'), //'破解游戏'
             );
        } else {
            //测试站类型对接配置
            $game_cutype = array(
                    '15'=> array('1077','棋牌游戏'), //'扑克棋牌'
                    '16'=> array('1076','益智游戏'), //'塔防游戏'
                    '18'=> array('1073','掌上网游'), //'大型网游',
                    '19'=> array('1075','休闲游戏'), //'经营养成',
                    '20'=> array('1078','体育运动'), //'竞速游戏',
                    '21'=> array('1075','休闲游戏'), //'休闲益智',
                    '30'=> array('1079','动作射击'), //'动作冒险',
                    '43'=> array('1079','动作射击'), //'飞行射击',
                    '44'=> array('1073','掌上网游'),
            );
        }
		
		//提交游戏数据整理
		$tmp = $data =  array();
		if($game['gimgs']) {
			foreach ($game['gimgs'] as $value){
				$tmp[] = array('url'=> $value, 'scope'=>'all', 'createTime' => date('Y-m-d\TH:i:s'));
			}
		}
		$data = array(
				'contentName' => html_entity_decode($game['name']),
				'description' => preg_replace('/\<br(\s*)?\/?\>/i', "\n", html_entity_decode($game['descrip'])),
				'typeId' => isset($game_cutype[$game['category_id']]) ? intval($game_cutype[$game['category_id']][0]) : intval($default[0]),
				'typeName' => isset($game_cutype[$game['category_id']]) ? $game_cutype[$game['category_id']][1] : $default[1],
				'providerId'=>'108', //提供商Id
				'providerName'=>'金立', //提供商名称
				'providerPid'=> $game['id'], //提供商资源Id
				'createTime'=> $game['create_time'] ? date('Y-m-d\TH:i:s', $game['create_time']): date('Y-m-d\TH:i:s'),
				'updateTime'=> $game['online_time']? date('Y-m-d\TH:i:s', $game['online_time']): date('Y-m-d\TH:i:s'),
				'price'=> 0,
				'logos'=>array(
						array('url'=> $game['img'], 'scope'=>'all', 'createTime' => date('Y-m-d\TH:i:s'))
				),
				'screenshots'=> $tmp,
				'files'=> array(
						array('url'=>$game['link'], 'platform'=>'android', 'format' => '.apk', 'size'=> strval(ceil($game['size']*1024)), 'version'=> $game['version'], 'updateTime'=> date('Y-m-d\TH:i:s'))
				),
				'platform'=>'android',
				'ext'=> array(
						array('name' => 'language','value' => $game['language']),
						array('name' => 'developers','value' => $game['developer'] ? $game['developer'] : '互联网'),
				),
				'parentTypeId'=> '2',
				'parentTypeName'=> '游戏',
				'free'=> '免费'
		);
		return $data;
	}
	
	/**
	 *
	 * Enter description here ...
	 * @param array $data
	 */
	private static function _cookData($data) {
		$tmp = array();
		if(isset($data['id'])) $tmp['id'] = intval($data['id']);
		if(isset($data['game_id'])) $tmp['game_id'] = $data['game_id'];
		if(isset($data['app_id'])) $tmp['app_id'] = $data['app_id'];
		if(isset($data['version'])) $tmp['version'] = $data['version'];
		if(isset($data['version_code'])) $tmp['version_code'] = $data['version_code'];
		if(isset($data['content_id'])) $tmp['content_id'] = $data['content_id'];
		if(isset($data['cu_status'])) $tmp['cu_status'] = $data['cu_status'];
		if(isset($data['online_flag'])) $tmp['online_flag'] = $data['online_flag'];
		if(isset($data['cu_online_time'])) $tmp['cu_online_time'] = $data['cu_online_time'];
		if(isset($data['game_status'])) $tmp['game_status'] = $data['game_status'];
		if(isset($data['create_time'])) $tmp['create_time'] = $data['create_time'];
		return $tmp;
	}
	
	/**
	 *
	 * @return Freedl_Dao_Cugd
	 */
	private static function _getDao() {
		return Common::getDao("Freedl_Dao_Cugd");
	}
}
