<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 账号管理 
 * @author fanch
 *
*/
class Account_UserController extends Admin_BaseController {
	public $actions = array(
			'listUrl' => '/Admin/Account_User/index',
			'infoUrl' => '/Admin/Account_User/info',
			'giftUrl' => '/Admin/Account_User/gift',
			'giftConfigUrl' => '/Admin/Account_User/giftConfig',
			'giftConfigPostUrl' => '/Admin/Account_User/giftConfigPost',
	);
	public $perpage = 20;
	public $mode = array('1'=> '客户端', '2'=>'web');
	public $giftType = array('1'=> 'A券', '2'=>'积分');
	
	public function indexAction() {
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$search = $params = array();
		$search = $this->getInput(array('uname', 'start_time', 'end_time'));
		if($search['uname'])
			$params['uname'] = array('LIKE',$search['uname']);
		if($search['start_time'])
			$params['reg_time'] = array('>=', strtotime($search['start_time']));
		if($search['end_time'])
			$params['reg_time'] = array('<=', strtotime($search['end_time']));
		if($search['end_time'] && $search['end_time'])
			$params['reg_time'] = array(array('>=', strtotime($search['start_time'])), array('<=', strtotime($search['end_time'])));
		
		list($total, $result) = Account_Service_User::getUserList($page, $perpage, $params);
		$data = array();
		if($result){
			foreach ($result as $value){
				$uinfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
				$data[]=array(
					'id'=>$value['id'],
					'avatar'=> $uinfo['avatar'] ? $uinfo['avatar'] : '',
					'uuid'=>$value['uuid'],
					'uname'=>$value['uname'],
					'nickname' => $uinfo['nickname'] ? $uinfo['nickname'] : '',
					'regTime'=>$value['reg_time'],
					'lastLoginTime'	=>$value['last_login_time']
				);
			}
		}
		$this->assign('result', $data);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$url = $this->actions['listUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	public function infoAction(){
		$id = intval($this->getInput('id'));
		$user = Account_Service_User::getUser(array('id'=>$id));
		$userInfo  = Account_Service_User::getUserInfo(array('uname'=>$user['uname']));
		$uuidParam = array('uuid'=>$user['uuid']);
		$userInfo['favorType'] = $this->getFavorTypeStr($uuidParam);
		$userInfo['attentionGame'] = $this->getAttentionGameStr($uuidParam);
		$userInfo['gainPoints'] = $this->getGainPointsTotal($uuidParam);
		$userInfo['consumePoints'] = $this->getConsumePointsTotal($uuidParam);
		$regLog = Account_Service_User::getUserLog(array('act'=>'1', 'uuid'=>$user['uuid']), array('create_time'=>'asc'));
		$llLog = Account_Service_User::getUserLog(array('act'=>'2', 'uuid'=>$user['uuid']), array('create_time'=>'desc'));
		
		$this->assign('user', $user);
		$this->assign('userInfo', $userInfo);
		$this->assign('reglog', $regLog);
		$this->assign('lllog', $llLog);
		$this->assign('mode', $this->mode);
	}
	
	public function giftAction(){
		$giftConfig = Game_Service_Config::getvalue('Game_User_Gift_Config');
		$giftConfig = $giftConfig ? json_decode($giftConfig, true) : array();
		$this->assign('giftConfig', $giftConfig);
		$this->assign('giftType', $this->giftType);
		$page = intval($this->getInput('page'));
		$perpage = $this->perpage;
		$search = $params = array();
		$search = $this->getInput(array('uname', 'start_time', 'end_time'));
		$params = $this->getSearchCondition($search);
		
		list($total, $result) = Account_Service_UserGift::getList($page, $perpage, $params);

		$data = array();
		if($result){
			foreach ($result as $value){
				$uinfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
				$data[]=array(
					'id'=>$value['id'],
					'uuid'=>$value['uuid'],
					'uname'=>$uinfo['uname'],
					'birthday' => ($uinfo['birthday']!='0000-00-00') ? $uinfo['birthday'] : '',
					'giftType' => $value['type'],
					'giftCost' => $value['cost'],
					'startTime'=>$value['effect_start_time'],
					'endTime'=>$value['effect_end_time'],
					'createTime'=>$value['create_time'],
				);
			}
		}
		$this->assign('result', $data);
		$this->assign('total', $total);
		$this->assign('search', $search);
		$url = $this->actions['giftUrl'] . '/?' . http_build_query($search) . '&';
		$this->assign('pager', Common::getPages($total, $page, $perpage, $url));
	}
	
	public function giftConfigAction(){
		$giftConfig = Game_Service_Config::getvalue('Game_User_Gift_Config');
		$giftConfig = $giftConfig ? json_decode($giftConfig, true) : array();
		$this->assign('giftConfig', $giftConfig);
	}
	
	public function giftConfigPostAction(){
		$config = $this->getInput(array('acoupon', 'day', 'point', 'status'));
		$data = array(
			'acoupon'=>array(
				'cost' => $config['acoupon'],
				'day' => $config['day'],		
			),
			'point' =>array(
				'cost'=> $config['point'],
			),
			'status' => $config['status']	
		);
		$data = json_encode($data);
		$result = Game_Service_Config::setValue('Game_User_Gift_Config', $data);
		if (!$result) $this->output(- 1, '操作失败');
        $this->output(0, '操作成功');
	}
	
	private function getSearchCondition($search){
		$startTimeFlag = $search['start_time'] ? '1' : '0';
		$endTimeFlag = $search['end_time'] ? '1' : '0';
		$unameFlag = $search['uname'] ? '1' : '0';
	
		$flag = strval($startTimeFlag. $endTimeFlag. $unameFlag);
		switch ($flag){
			case '100':
				//100-仅选择开始时间
				return array('create_time'=>array('>=', $search['start_time']));
				break;
			case '010':
				//010-仅选择结束时间
				return array('create_time'=>array('<', $search['end_time']));
				break;
			case '001':
				//001-仅选择名称
				$uuidItems = $this->searchUsers(array('name'=>array('LIKE', trim($search['uname']))));
				return array('uuid'=>array('IN', $uuidItems));
				break;
			case '110':
				//110-选择开始时间+结束时间
				return array('create_time'=>array(array('>=', $search['start_time']), array('<', $search['end_time'])));
				break;
			case '101':
				//101-选择开始时间+用户名
				$params = array();
				$params['create_time'] = array('>=', $search['start_time']);
				$uuidItems = $this->searchUsers(array('name'=>array('LIKE', trim($search['uname']))));
				$params['uuid']=array('IN', $uuidItems);
				return $params;
				break;
			case '011':
				//011-选择结束时间+用户名
				$params = array();
				$params['create_time'] = array('<', $search['end_time']);
				$uuidItems = $this->searchUsers(array('name'=>array('LIKE', trim($search['uname']))));
				$params['uuid']=array('IN', $uuidItems);
				return $params;
				break;
			case '111':
				//111-选择开始时间+结束时间+用户名
				$params = array();
				$params['create_time'] = array(array('>=', $search['start_time']), array('<', $search['end_time']));
				$uuidItems = $this->searchUsers(array('name'=>array('LIKE', trim($search['uname']))));
				$params['uuid']=array('IN', $uuidItems);
				return $params;
				break;
			default:
				//000-搜索全部的
				return array();
		}
		
	}
	
	private function searchUsers($params){
		$uuidData = array();
		$userData = Account_Service_User::getsByUser($params);
		if($userData){
			$userData = Common::resetKey($userData, 'uuid');
			$uuidData = array_keys($userData);
		}else{
			$uuidData = array(0);
		}
		return $uuidData;
	}
	
	
	private function getFavorTypeStr($uuidParam) {
	    $userFavorType = Account_Service_User::getFavorCategory($uuidParam);
	    $favorTypeStr = "";
	    if($userFavorType){
	        $favorTypeArr = array();
	        foreach ($userFavorType as $value){
	            $category = Resource_Service_Attribute::getBy(array('id' => $value['category_id']));
	            array_push($favorTypeArr, $category['title']);
	        }
	        $favorTypeStr = implode(',', $favorTypeArr);
	    }
	    return $favorTypeStr;
	}
	
	/**
	 * 关注游戏
	 * @author yinjiayan
	 * @param unknown $uuidParam
	 * @return string
	 */
	private function getAttentionGameStr($uuidParam) {
	    $attentionGames = Client_Service_Attention::getsBy($uuidParam);
	    if (!$attentionGames) {
	    	return '';
	    }
	    $gameNameAtt = array();
	    foreach ($attentionGames as $value) {
	        $gameInfo = Resource_Service_Games::getBy(array('id' => $value['game_id']));
	        array_push($gameNameAtt, '《'.$gameInfo['name'].'》');
	    }
	    return implode('', $gameNameAtt);
	}
	
	private function getGainPointsTotal($uuidParam) {
	    $gainPoints = Point_Service_Gain::getsBy($uuidParam);
	    if (! $gainPoints) {
	    	return 0;
	    }
	    $total = 0;
	    foreach ($gainPoints as $value) {
	        $total += $value['points'];
	    }
	    return $total;
	}
	
	private function getConsumePointsTotal($uuidParam) {
	    $consumePoints = Point_Service_Consume::getsBy($uuidParam);
	    if (! $consumePoints) {
	    	return 0;
	    }
	    $total = 0;
	    foreach ($consumePoints as $value) {
	        $total += $value['points'];
	    }
	    return $total;
	}
}