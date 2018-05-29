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
	);
	public $perpage = 20;
	public $mode = array('1'=> '客户端', '2'=>'web');
	
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
		$this->assign('pager', Common::getPages($total, $page, $perpage, $this->actions['listUrl'].'?'));
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