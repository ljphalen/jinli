<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 首页推荐信息
 * Client_Ad_RecommendlistController
 * @author wupeng
 */
class Client_Ad_RecommendlistController extends Admin_BaseController {
	
	public $actions = array(
		'listUrl' => '/Admin/Client_Ad_Recommendlist/index',
		'editUrl' => '/Admin/Client_Ad_Recommendlist/edit',
		'editPostUrl' => '/Admin/Client_Ad_Recommendlist/editPost',
		'copyUrl' => '/Admin/Client_Ad_Recommendlist/copy',

	    'bannerAddUrl' => '/Admin/Client_Ad_Recommendlist/bannerAdd',
	    'bannerEditUrl' => '/Admin/Client_Ad_Recommendlist/bannerEdit',
	    'bannerDeleteUrl' => '/Admin/Client_Ad_Recommendlist/bannerDelete',
	    'bannerAddPostUrl' => '/Admin/Client_Ad_Recommendlist/bannerAddPost',
	    'bannerEditPostUrl' => '/Admin/Client_Ad_Recommendlist/bannerEditPost',
	    
	    'textAddUrl' => '/Admin/Client_Ad_Recommendlist/textAdd',
	    'textEditUrl' => '/Admin/Client_Ad_Recommendlist/textEdit',
	    'textDeleteUrl' => '/Admin/Client_Ad_Recommendlist/textDelete',
	    'textAddPostUrl' => '/Admin/Client_Ad_Recommendlist/textAddPost',
	    'textEditPostUrl' => '/Admin/Client_Ad_Recommendlist/textEditPost',
		'textSetUrl' => '/Admin/Client_Ad_Recommendlist/textSet',

	    'dailyAddUrl' => '/Admin/Client_Ad_Recommendlist/dailyAdd',
	    'dailyEditUrl' => '/Admin/Client_Ad_Recommendlist/dailyEdit',
	    'dailyDeleteUrl' => '/Admin/Client_Ad_Recommendlist/dailyDelete',
	    'dailyAddPostUrl' => '/Admin/Client_Ad_Recommendlist/dailyAddPost',
	    'dailyEditPostUrl' => '/Admin/Client_Ad_Recommendlist/dailyEditPost',
	    
	    'editlogUrl' => '/Admin/Client_Ad_Recommendlist/editlog',
	    'infoUrl' => '/Admin/Client_Ad_Recommendlist/info',

	    'recommendAddUrl' => '/Admin/Client_Ad_Recommendnew/recommendAdd',
	    'recommendEditUrl' => '/Admin/Client_Ad_Recommendnew/recommendEdit',
	    'recommendDeleteUrl' => '/Admin/Client_Ad_Recommendnew/recommendDelete',
	    
	    'uploadUrl' => '/Admin/Client_Ad_Recommendlist/upload',
	    'uploadPostUrl' => '/Admin/Client_Ad_Recommendlist/upload_post',

	    'sortPostUrl' => '/Admin/Client_Ad_Recommendnew/sortPost',
	);
	
	public $perpage = 20;
	
	/**
	 * 列表界面
	 */
	public function indexAction() {
		$perpage = $this->perpage;
		$date = $this->getInput('date');
		$sortParams = array('day_id' => 'DESC');
		
		$searchParams = array();
		if(! $date) $date = date("Y-m");
		$startTime = strtotime($date);
		$endTime = strtotime("+1 months", strtotime($date));

		$searchParams['day_id'][] = array(">=", $startTime);
		$searchParams['day_id'][] = array("<", $endTime);
		$list = Game_Service_RecommendList::getRecommendListsBy($searchParams, $sortParams);
		
		$list = Game_Manager_RecommendList::initMonthList($list, $startTime);

		$lastMonthDay = strtotime("-1 day", strtotime($date));
		$lastList = Game_Service_RecommendList::getRecommendList($lastMonthDay);
		$this->assign('date', $startTime);
		$this->assign('before', $lastList != 0);
		$this->assign('list', $list);
	}
	
	/**
	 * 编辑
	 */
	public function editAction() {
	    $from = 0;//链接来源::0:编辑,1:编辑回调
	    $keys = $this->getInput(array('day_id', 'dayId'));
	    if($keys['day_id']) {
	        $dayId = strtotime($keys['day_id']);
	    }elseif ($keys['dayId']) {
	        $dayId = $keys['dayId']; 
	        $from = 1;
	    }else{
	        exit();
	    }
	    $userId = $this->userInfo['uid'];
		if($from == 0) {
		    Game_Manager_RecommendList::loadRecommendList($dayId, $userId);
		}
		
		$bannerList = Game_Manager_RecommendList::getRecommendBanner($dayId, $userId);
		$text = Game_Manager_RecommendList::getRecommendText($dayId, $userId);
		$daily = Game_Manager_RecommendList::getRecommendDaily($dayId, $userId);
		$recommend = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
		
		$data = array('success' => true, 'msg' => "", 'data' => array());
		$data['data']['roll'] = Game_Manager_RecommendList::initBannerInfo($bannerList);
		$data['data']['notice'] = Game_Manager_RecommendList::initTextInfo($text);
		$data['data']['sameDay'] = Game_Manager_RecommendList::initDailyInfo($daily);
		$data['data']['recommend'] = Game_Manager_RecommendList::initRecommend($recommend);
		$this->assign('data', $data);
		
		$log = Game_Service_RecommendEditLog::getRecommendEditLogByDayId($dayId);
		if($log) {
		    $user = Admin_Service_User::getUser($log['uid']);
		    $log['username'] = $user['username'];
		}
		$this->assign('log', $log);

		$this->assign('day_id', $dayId);
		$this->assign('from', $from);
		$this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	/**
	 * 调整顺序
	 */
	public function sortPostAction() {
		$dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    if(! $userId) {
	        exit();
	    }
		$banner = html_entity_decode($this->getInput('banner'));
		$recommend = html_entity_decode($this->getInput('recommend'));
		$bannerSort = json_decode($banner);
		$recommendSort = json_decode($recommend);

		$bannerList = Game_Manager_RecommendList::getRecommendBanner($dayId, $userId);
		$recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
		
		$sort = count($bannerSort);
		foreach ($bannerSort as $index) {
		    $banner = $bannerList[$index];
		    $banner['sort'] = $sort--;
		    $bannerList[$index] = $banner;
		}
		$sort = count($recommendSort);
		foreach ($recommendSort as $index) {
		    $rec = $recommendList[$index][Game_Manager_RecommendList::RECOMMEND_INFO];
		    $rec['sort'] = $sort--;
		    $recommendList[$index][Game_Manager_RecommendList::RECOMMEND_INFO] = $rec;
		}
		Game_Manager_RecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
		Game_Manager_RecommendList::updateRecommendList($dayId, $userId, $recommendList);
		$this->output(0, '操作成功.');
	}
	
	/**
	 * 提交编辑内容
	 */
	public function editPostAction() {
		$dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    if(! $userId) {
	        exit();
	    }
		$banner = html_entity_decode($this->getInput('banner'));
		$recommend = html_entity_decode($this->getInput('recommend'));
		$bannerSort = json_decode($banner);
		$recommendSort = json_decode($recommend);

		$bannerList = Game_Manager_RecommendList::getRecommendBanner($dayId, $userId);
		$text = Game_Manager_RecommendList::getRecommendText($dayId, $userId);
		$daily = Game_Manager_RecommendList::getRecommendDaily($dayId, $userId);
		$recommendList = Game_Manager_RecommendList::getRecommendList($dayId, $userId);
		
		if(! $bannerList) {
		    $this->output(-1, '轮播图未添加');
		}
		if(! $recommendList) {
		    $this->output(-1, '推荐列表未添加');
		}
		$sort = count($bannerSort);
		foreach ($bannerSort as $index) {
		    $banner = $bannerList[$index];
		    $banner['sort'] = $sort--;
		    $bannerList[$index] = $banner;
		}
		$sort = count($recommendSort);
		foreach ($recommendSort as $index) {
		    $rec = $recommendList[$index][Game_Manager_RecommendList::RECOMMEND_INFO];
		    $rec['sort'] = $sort--;
		    $recommendList[$index][Game_Manager_RecommendList::RECOMMEND_INFO] = $rec;
		}
		
		$flg = Game_Service_RecommendList::saveRecommend($dayId, $bannerList, $text, $daily, $recommendList);
        if(! $flg) {
            $this->output(-1, '保存失败.');
        }

		$editInfo = Game_Service_RecommendList::getRecommendList($dayId);
		if (! $editInfo) {
		    $recList = array();
		    $recList['day_id'] = $dayId;
		    $recList['create_time'] = Common::getTime();
		    Game_Service_RecommendList::addRecommendList($recList);
		}
		
		if(strtotime(date("Y-m-d")) == $dayId) {
		    Async_Task::execute('Async_Task_Adapter_UpdateRecommendCache', 'update');
		}

		Game_Manager_RecommendList::deleteRecommendBanner($dayId, $userId);
	    Game_Manager_RecommendList::deleteRecommendText($dayId, $userId);
	    Game_Manager_RecommendList::deleteRecommendDaily($dayId, $userId);
	    Game_Manager_RecommendList::deleteRecommendList($dayId, $userId);
		
		Game_Manager_RecommendList::addLog($dayId, $userId);
		$this->output(0, '操作成功.');
	}
	
	public function copyAction() {
		$dayId = $this->getInput('day_id');
		$type = $this->getInput('type');
		if($type == 1) {
		    /**复制前一天内容*/
		    $to_day_id = strtotime($dayId);
		    $from_day_id = strtotime("-1 day", $to_day_id);
		    Game_Service_RecommendList::copyRecommendListByDayId($from_day_id, $to_day_id);

		    Game_Manager_RecommendList::addLog($to_day_id, $this->userInfo['uid']);
		}
	    $this->redirect("edit?day_id=".$dayId);
	}
	
	public function editlogAction() {
	    $perpage = $this->perpage;
	    $page = intval($this->getInput('page'));
	    $dayId = intval($this->getInput('day_id'));
	    if ($page < 1) $page = 1;
	    $searchParams = array('day_id' => $dayId);
	    $sortParams = array('id' => 'DESC');
	    list($total, $list) = Game_Service_RecommendEditLog::getPageList($page, $this->perpage, $searchParams, $sortParams);
	    foreach ($list as $key => $value) {
	        $user = Admin_Service_User::getUser($value['uid']);
	        $list[$key]['username'] = $user['username'];
	    }
	    $requestData=array();
	    $this->assign('day_id', $dayId);
	    $this->assign('list', $list);
	    $this->assign('total', $total);
	    $url = $this->actions['listUrl'].'/?' . http_build_query($requestData) . '&';
	    $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
	}
	
	public function infoAction() {}
	
	public function bannerAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('day_id', $dayId);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function bannerAddPostAction() {
	    $requestData = $this->getInput(array('title', 'link_type', 'link', 'img1', 'img2', 'img3', 'day_id', 'status'));
	    if(! ($requestData['day_id'])) $this->output(-1, '生效日期不能为空.');
	    $postData = $this->checkBannerData($requestData);
	    $dayId = $requestData['day_id'];
	    $userId = $this->userInfo['uid'];
		$bannerList = Game_Manager_RecommendList::getRecommendBanner($dayId, $userId);
	    
	    if(count($bannerList) >=4) {
	        $this->output(-1, 'Banner图不能多于4个.');
	    }
	    $postData['sort'] = 0;
	    $bannerList[] = $postData;
	    
	    Game_Manager_RecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
	    
	    $this->output(0, '操作成功');
	}
	
	public function bannerEditAction() {
	    $dayId = $this->getInput('day_id');
	    $id = $this->getInput('id');
	    $userId = $this->userInfo['uid'];

	    $bannerList = Game_Manager_RecommendList::getRecommendBanner($dayId, $userId);
	    $info = $bannerList[$id];

	    $this->assign('id', $id);
	    $this->assign('day_id', $dayId);
	    $this->assign('info', $info);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function bannerEditPostAction() {
	    $dayId = $this->getInput('day_id');
	    $index = $this->getInput('id');
	    $requestData = $this->getInput(array('title', 'link_type', 'link', 'img1', 'img2', 'img3', 'status'));
	    if(! $dayId) $this->output(-1, '生效日期不能为空.');
	    $postData = $this->checkBannerData($requestData);
	    $userId = $this->userInfo['uid'];

	    $bannerList = Game_Manager_RecommendList::getRecommendBanner($dayId, $userId);
	    $editInfo = $bannerList[$index];
	    if (! $editInfo) $this->output(-1, '编辑的内容不存在');
	    
	    $editInfo = Game_Manager_RecommendList::getNewData($postData, $editInfo);
	    $bannerList[$index] = $editInfo;
	    Game_Manager_RecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功.');
	}
	
	public function bannerDeleteAction() {
	    $dayId = $this->getInput('day_id');
	    $index = $this->getInput('id');
	    $userId = $this->userInfo['uid'];

	    $bannerList = Game_Manager_RecommendList::getRecommendBanner($dayId, $userId);
	    array_splice($bannerList, $index, 1);
	    Game_Manager_RecommendList::updateRecommendBanner($dayId, $userId, $bannerList);
	    $this->output(0, '操作成功', array('href'=>$this->actions['editUrl'].'?dayId='.$dayId));
	}

	public function textAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('day_id', $dayId);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function textAddPostAction() {
	    $requestData = $this->getInput(array('title', 'link_type', 'link', 'day_id', 'status'));
	    if(! isset($requestData['day_id'])) $this->output(-1, '生效日期不能为空.');
	    $postData = $this->checkTextData($requestData);

	    $dayId = $requestData['day_id'];
	    $userId = $this->userInfo['uid'];
	    
	    Game_Manager_RecommendList::updateRecommendText($dayId, $userId, $postData);
	    $this->output(0, '操作成功');
	}
	
	public function textEditAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];

	    $info = Game_Manager_RecommendList::getRecommendText($dayId, $userId);
	    $this->assign('info', $info);
	    $this->assign('day_id', $dayId);
	    $this->assign('linkType', Game_Service_Util_Link::$linkType);
	}
	
	public function textEditPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $requestData = $this->getInput(array('title', 'link_type', 'link', 'status'));
	    $postData = $this->checkTextData($requestData);

	    $editInfo = Game_Manager_RecommendList::getRecommendText($dayId, $userId);
	    if (! $editInfo) $this->output(-1, '编辑的内容不存在');

	    $editInfo = Game_Manager_RecommendList::getNewData($postData, $editInfo);
	    Game_Manager_RecommendList::updateRecommendText($dayId, $userId, $editInfo);
	    $this->output(0, '操作成功.');
	}
	
	public function textDeleteAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    Game_Manager_RecommendList::deleteRecommendText($dayId, $userId);
	    $this->output(0, '操作成功', array('href'=>$this->actions['editUrl'].'?dayId='.$dayId));
	}
	
	public function dailyAddAction() {
	    $dayId = $this->getInput('day_id');
	    $this->assign('day_id', $dayId);
	}
	
	public function dailyAddPostAction() {
	    $requestData = $this->getInput(array('day_id', 'title', 'game_id', 'content', 'status'));
	    if(! isset($requestData['day_id'])) $this->output(-1, '生效日期不能为空.');
	    $daily = $this->cookDailyData($requestData);

	    $dayId = $requestData['day_id'];
	    $userId = $this->userInfo['uid'];
	    Game_Manager_RecommendList::updateRecommendDaily($dayId, $userId, $daily);
	    $this->output(0, '操作成功');
	}
	
	public function dailyEditAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    
	    $info = Game_Manager_RecommendList::getRecommendDaily($dayId, $userId);
	    $game = Resource_Service_GameData::getGameAllInfo($info['game_id']);
	    $info['game_name'] = $game['name'];
	    $this->assign('day_id', $dayId);
	    $this->assign('info', $info);
	}
	
	public function dailyEditPostAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    $requestData = $this->getInput(array('title', 'game_id', 'content', 'status'));
	    $postData = $this->cookDailyData($requestData);

	    $editInfo = Game_Manager_RecommendList::getRecommendDaily($dayId, $userId);
	    if (! $editInfo) $this->output(-1, '编辑的内容不存在');
	    
	    $daily = Game_Manager_RecommendList::getNewData($postData, $editInfo);
	    Game_Manager_RecommendList::updateRecommendDaily($dayId, $userId, $daily);
	    $this->output(0, '操作成功.');
	}
	
	public function dailyDeleteAction() {
	    $dayId = $this->getInput('day_id');
	    $userId = $this->userInfo['uid'];
	    Game_Manager_RecommendList::deleteRecommendDaily($dayId, $userId);
	    $this->output(0, '操作成功', array('href'=>$this->actions['editUrl'].'?dayId='.$dayId));
	}
	
	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function upload_postAction() {
	    $ret = Common::upload('img', 'ad');
	    $imgId = $this->getPost('imgId');
	    $this->assign('code' , $ret['data']);
	    $this->assign('msg' , $ret['msg']);
	    $this->assign('data', $ret['data']);
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	public function textSetAction() {
	    $settingKey = 'WELFARE_TASK_CONFIG';
	    if($_SERVER['REQUEST_METHOD'] == 'POST') {
	        $input = $this->getInput('switch');
	        $res = Game_Service_Config::setValue($settingKey, $input);
	        if($res) {
	            $this->output(0,'设置成功');
	        }
	        $this->output(-1,'设置失败');
	    }
	    $config = Game_Service_Config::getValue($settingKey);
	    $this->assign('config', $config);
	}
	
	private function checkBannerData($requestData) {
	    if(! ($requestData['title'])) $this->output(-1, '标题不能为空.');
	    if(! isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
	    if(! isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
	    $result = Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link']);
	    if($result) {
	        $this->output(-1, $result);
	    }
	    if(! ($requestData['img1'])) $this->output(-1, '1.5.5版本尺寸不能为空.');
	    if(! ($requestData['img2'])) $this->output(-1, '1.5.6版本尺寸不能为空.');
	    if(! ($requestData['img3'])) $this->output(-1, '1.5.7版本尺寸不能为空.');
	    return $requestData;
	}
	
	private function checkTextData($requestData) {
	    if(!isset($requestData['title'])) $this->output(-1, '标题不能为空.');
	    if(Util_String::strlen($requestData['title']) > 15) {
	        $this->output(-1, '标题不能超过15个字.');
	    }
	    if(!isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
	    if(!isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
	    $result = Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link']);
	    if($result) {
	        $this->output(-1, $result);
	    }
	    if(!isset($requestData['status'])) $this->output(-1, '0:无效,1:生效不能为空.');
	    return $requestData;
	}
	
	private function cookDailyData($info) {
	    if(!$info['title']) $this->output(-1, '标题不能为空.');
	    if (Util_String::strlen($info['title']) > 15) {
	        $this->output(- 1, '标题不能超过15个字.');
	    }
	    if(! $info['content']) $this->output(-1, '描述不能为空.');
	    if (Util_String::strlen($info['content']) > 35) {
	        $this->output(- 1, '描述不能超过35个字.');
	    }
	    if (! $info['game_id']) {
	        $this->output(- 1, '游戏ID不能为空.');
	    }
	    
	    $game = Resource_Service_Games::getResourceByGames($info['game_id']);
	    if (! $game) {
	        $this->output(- 1, '游戏不存在.');
	    }
	    if (! isset($info['status']))
	        $this->output(- 1, '状态不能为空.');
	    return $info;
	}
	
	private function cookRecommendData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($info['title']) > 15) {
            $this->output(- 1, '标题不能超过15个字.');
        }
		if(!isset($info['pgroup'])) $this->output(-1, '机组不能为空.');
		return $info;
	}
	
	private function cookRecommendImgData($requestData) {
		if(!isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
		if(!isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');
		$result = Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link']);
		if($result) {
		    $this->output(-1, $result);
		}
		if(!$requestData['img']) $this->output(-1, '图片不能为空.');
		return $requestData;
	}
	
	private function assignGroups() {
        list(, $groups) = Resource_Service_Pgroup::getAllPgroup();
        $groups = Common::resetKey($groups, 'id');
        $all[0] = array("id" => 0, "title" => "全部");
        $groups = array_merge($all, $groups);
        $this->assign('groups', $groups);
	}
	
	
	public function testAction() {
	    for ($i = 0; $i < 4; $i++) {
	        if($i == 0) {
	           $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_SLIDE_AD);
	        }elseif($i == 1) {
	           $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_TEXT_AD);
	        }elseif($i == 2) {
	           $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_DAILY_RECOMMEND);
	        }else {
	           $api = Util_CacheKey::getApi(Util_CacheKey::HOME, Util_CacheKey::HOME_RECOMMEND_LIST);
	        }
	        $keys = Util_Api_Cache::getValidKeys($api);
	        var_dump($keys);
	        foreach ($keys as $key => $params) {
	            $args = $params['args'];
	            var_dump($args);
	        }
	        echo "----------" . "<BR>";
	    }
	    exit();
	}
	
	public function cacheAction() {
	    $key = $this->getInput('key');
	    $cache = Cache_Factory::getCache();
	    var_dump($cache->hGetAll($key));
	    exit();
	}
	
	
}
