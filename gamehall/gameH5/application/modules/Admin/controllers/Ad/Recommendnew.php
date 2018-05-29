<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Enter description here ...
 * @author wupeng
 *
 */
class Ad_RecommendnewController extends Admin_BaseController {
	
	public $actions = array(
		'listEditUrl' => '/Admin/Ad_Recommendlist/edit',
	    'rankRetrunPage' => 'Admin/Ad_Recommendlist/edit?day_id=',
		'addUrl' => '/Admin/Ad_Recommendnew/add',
		'editUrl' => '/Admin/Ad_Recommendnew/edit',
		'addPostUrl' => '/Admin/Ad_Recommendnew/addPost',
		'editPostUrl' => '/Admin/Ad_Recommendnew/editPost',
	    'postRankUrl' => '/Admin/Ad_Recommendnew/postRank',
	    'otherPostUrl' => '/Admin/Ad_Recommendnew/otherPost',
	    
	    'checkTitleUrl' => '/Admin/Ad_Recommendnew/ajaxHandle',
	    
	    'gamesUrl' => '/Admin/Ad_Recommendnew/games',
	    'gamesPostUrl' => '/Admin/Ad_Recommendnew/gamesPost',

	    'gamesSortUrl' => '/Admin/Ad_Recommendnew/gamesSort',
		'gamesSortPostUrl' => '/Admin/Ad_Recommendnew/gamesSortPost',
		'deleteUrl' => '/Admin/Ad_Recommendnew/delete',

		'queryGameUrl' => Game_Service_H5RecommendNew::QUERYGAME,

	    'imgAddPostUrl' => '/Admin/Ad_Recommendnew/imgAddPost',
	    'imgEditPostUrl' => '/Admin/Ad_Recommendnew/imgEditPost',
	    
	    'uploadUrl' => '/Admin/Ad_Recommendnew/upload',
	    'uploadPostUrl' => '/Admin/Ad_Recommendnew/upload_post',
	    
	);
	
	public $mHdAndNewType = array('1' => 'new', '2' => 'hd');
	
	/**
	 * 添加
	 */
	public function addAction() {
	    $day_id = $this->getInput('day_id');
	    
	    $this->assign('rec_type', Game_Service_H5RecommendNew::$rec_type);
	    $this->assign('day_id', $day_id);
	}
	
	/**
	 * 添加内容提交
	 */
	public function addPostAction() {
        $data = $this->getInput(array('title', 'content', 'rec_type', 'day_id'));
        $info = $this->cookData($data);
		if(!isset($info['rec_type'])) $this->output(-1, '推荐方式不能为空.');
        $info['create_time'] = Common::getTime();
        $info['status'] = Game_Service_H5RecommendNew::RECOMMEND_INVALID_STATUS;
        $result = Game_Service_H5RecommendNew::addRecommendnew($info);
        if (! $result)
            $this->output(- 1, '操作失败');
        $this->output(0, '操作成功', $result);
	}
	/**
	 * 编辑
	 */
	public function editAction() {
        $id = $this->getInput('id');
        $info = Game_Service_H5RecommendNew::getRecommendnew(intval($id));
        $this->assign('info', $info);
	    $this->assign('rec_type', Game_Service_H5RecommendNew::$rec_type);
	}
	
	/**
	 * 编辑内容提交
	 */
	public function editPostAction() {
		$id = intval($this->getInput('id'));
		$info = $this->getInput(array('title', 'content'));
		$postData = $this->cookData($info);
		$editInfo = Game_Service_H5RecommendNew::getRecommendnew($id);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');
		
		$updateParams = $this->getUpdateParams($postData, $editInfo);
		if (count($updateParams) >0) {
            $ret = Game_Service_H5RecommendNew::updateRecommendnew($updateParams, $id);
            if (! $ret) $this->output(-1, '操作失败');
        }
        $this->output(0, '操作成功', $id);
	}
	
	public function postRankAction() {
	    $data = $this->getInput(array('id', 'rank_type', 'day_id'));
	    if(!isset($data['rank_type'])) $this->output(-1, '排行榜不能为空.');
	    if($data['id'] < 1) {
	        $info['title'] = 'rank_'.$info['day_id'].'_'.$data['rank_type'];
	        $info['day_id'] = $data['day_id'];
	        $info['create_time'] = Common::getTime();
	        $info['status'] = Game_Service_H5RecommendNew::RECOMMEND_OPEN_STATUS;
	        $info['rec_type'] =Game_Service_H5RecommendNew::REC_TYPE_RANK;
	        $recommend_id = Game_Service_H5RecommendNew::addRecommendnew($info);
	        $result = Game_Service_H5RecommendRank::addRecommendRank(array('rank_type' => $data['rank_type'], 'recommend_id' => $recommend_id));
	    } else {
	        $result = Game_Service_H5RecommendRank::updateRecommendRank(array('rank_type' => $data['rank_type']), $data['id']);
	    }
	    if (!$result)
	        $this->output(- 1, '操作失败');
	    $this->output(0, '操作成功', date('Y-m-d', $data['day_id']));
	}
	
	public function rankAddAction() {
	    $this->assign('rank_type', Game_Service_Rank::getH5OpenRankType());
	    $this->assign('day_id', $this->getInput('day_id'));
	}
	
	public function rankEditAction() {
	    $id = $this->getInput('id');
	    $info = Game_Service_H5RecommendRank::getRankByRecommendId(intval($id));
	    $this->assign('info', $info);
	    $this->assign('day_id', Game_Service_H5HomeAutoHandle::getID());
	    $this->assign('rank_type', Game_Service_Rank::getH5OpenRankType());
	}
	
	public function otherAddAction() {
	    Yaf_Dispatcher::getInstance()->disableView();
	    $this->assign('day_id', $this->getInput('day_id'));
	    $this->assign('type', $this->getInput('type'));
	    $this->assign('loop_num', $this->getInput('type') == 'hd' ? 1 : '5');
	    $this->getView()->display("ad/recommendnew/addother.phtml");
	}
	
	public function otherEditAction() {
	    Yaf_Dispatcher::getInstance()->disableView();
	    $id = $this->getInput('id');
	    $mainInfo = Game_Service_H5RecommendNew::getRecommendnew($id);
	    $info = Game_Service_H5RecommendNewHd::getNewHdByRecommendId(intval($id));
	    $info = $this->handleOtherInfoWithSort($info);
	    $this->assign('info', $info);
	    $this->assign('mainInfo', $mainInfo);
	    $this->assign('loop_num', $info[1]['list_type'] == 'hd' ? 1 : '5');
	    $this->getView()->display("ad/recommendnew/editother.phtml");
	}
	
	public function otherPostAction() {
	    $data = $this->getInput(array('recommend_id', 'type', 'day_id', 'list_id', 'list_title', 'title'));
            Common_Service_Base::beginTransaction();
	    if($data['recommend_id']) {
	        $status = $this->handleOtherPostWithRecommend($data, $data['recommend_id'], 'update');
	    } else {
	        $info['title'] = $data['title'];
	        $info['day_id'] = $data['day_id'];
	        $info['create_time'] = Common::getTime();
	        $info['status'] = Game_Service_H5RecommendNew::RECOMMEND_OPEN_STATUS;
	        if($data['type'] == 'hd') {
	            $info['rec_type'] =Game_Service_H5RecommendNew::REC_TYPE_ACTIVE;
	        } else {
	            $info['rec_type'] =Game_Service_H5RecommendNew::REC_TYPE_NEW;
	        }
	        $recommend_id = Game_Service_H5RecommendNew::addRecommendnew($info);
	        $status = $this->handleOtherPostWithRecommend($data, $recommend_id, 'add');
	    }
	    if($status == false) {
                Common_Service_Base::rollBack();
	    	$this->output(-1, '存在无效的条目.');
	    } else {
                Common_Service_Base::commit();
	    	$this->output(0, '操作成功', date('Y-m-d', $data['day_id']));
	    }
	}
	
	public function ajaxHandleAction() {
	    $data = $this->getInput(array('infoId', 'type'));
	    if($data['type'] == 'new') {
	          $info = Client_Service_News::getNews($data['infoId']);
	    } else {
	          $info = Client_Service_Hd::getHd($data['infoId']);
	    }
	    if($info['status'] == '1') {
	        $this->output(0, 'ID正确', array('title' => html_entity_decode($info['title'])));
	    } else {
	        $this->output(0, '不正确的ID或没开启', array('title' => NULL));
	    }
	}
	
	private function handleOtherPostWithRecommend($info, $recommend_id, $type) {
	    foreach($info['list_id'] as $key => $value) {
	        $list[] = array('list_type' => $info['type'], 'list_id' => $value, 'list_sort' => $key, 'list_title' => $info['list_title'][$key], 'recommend_id' => $recommend_id, 'tmp_id' => Game_Service_H5HomeAutoHandle::getID());
	    }
	    if($type == 'add') {
	        return Game_Service_H5RecommendNewHd::addMutiRecommendInfo($list);
	    } else {
	        return Game_Service_H5RecommendNewHd::updateMutiRecommendInfo($list);
	    }
	}
	
	private function handleOtherInfoWithSort($info) {
	    foreach($info as $key => $value) {
	        $infoList[$value['list_sort']] = $value;
	    }
	    return $infoList;
	}
	
	/**取需要更新的数据*/
	private function getUpdateParams($postData, $dbData) {
	    $updateParams = array();
	    foreach ($postData as $key => $value) {
	        if ($value == $dbData[$key])
	            continue;
	        $updateParams[$key] = $value;
	    }
	    return $updateParams;
	}
	
	/**
	 * 编辑
	 */
	public function gamesAction() {
	    $id = intval($this->getInput('id'));
	    $info = Game_Service_H5RecommendNew::getRecommendnew(intval($id));
	    if (! $info) $this->output(-1, '编辑的内容不存在');
	    if($info['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_LIST) {
	        $games = Game_Service_H5RecommendGames::getGames($id);
	        foreach ($games as $key => $value) {
	            $game = $this->getGameInfo($value['game_id']);
	            $games[$key] = array_merge($game, $value);
	        }
	        $this->assign('hiddens', array('id' => $id));
	        $this->assign('games', $games);
	        $this->assign('postUrl', $this->actions['gamesPostUrl']);
	        $this->assign('nextStepUrl', $this->actions['gamesSortUrl'] . "?id=" . $id);
	        $this->assign('preStepUrl', $this->actions['listEditUrl'] . "?day_id=" . date('Y-m-d', $info['day_id']));
	        Yaf_Dispatcher::getInstance()->disableView();
	        $this->getView()->display("common/games.phtml");
	    }else if($info['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_IMAGE) {
	        if($info['status'] == Game_Service_H5RecommendNew::RECOMMEND_INVALID_STATUS) {
	            $info['status'] = Game_Service_H5RecommendNew::RECOMMEND_OPEN_STATUS;
	        }
	        $searchParams = array('recommend_id' => $id);
	        $img = Game_Service_H5RecommendImgs::getRecommendImgsBy($searchParams);
	        $this->assign('img', $img);
	        $this->assign('info', $info);
	        $this->assign('linkType', Game_Service_Util_Link::$linkType);
	        Yaf_Dispatcher::getInstance()->disableView();
	        if($img) {
	            $this->getView()->display("ad/recommendnew/imgedit.phtml");
	        }else{
	            $this->assign('recommend_id', $id);
	            $this->getView()->display("ad/recommendnew/imgadd.phtml");
	        }
	    }
	}
	
	/**游戏相关信息*/
	private function getGameInfo($gameId) {
	    $info = array();
	    $game = Resource_Service_GameData::getGameAllInfo($gameId);
	    $info['gameName'] = $game['name'];
	    $info['gameCategory'] = $game['category_title'];
	    $info['gameIcon'] = $game['img'];
	    $info['gameSize'] = $game['size'];
	    $info['gameVersion'] = $game['version'];
	    return $info;
	}

    /**
     * 编辑
     */
    public function gamesPostAction () {
        $id = intval($this->getInput('id'));
        $data = $this->getInput('games');
        if(!$data) {
            $this->output('-1', '推荐游戏为空，不可以提交');
        }
        $games = array_unique($data);
        $gameSize = count($games);
        if ($gameSize != count($data)) {
            $this->output('-1', '有重复游戏，请删除后提交');
        }
        if ($gameSize <Game_Service_H5RecommendNew::SHOW_NUM) {
            $this->output('-1', '列表至少添加'.Game_Service_H5RecommendNew::SHOW_NUM.'个游戏');
        }
        $info = Game_Service_H5RecommendNew::getRecommendnew($id);
        if (! $info)
            $this->output(- 1, '编辑的内容不存在');
        $ret = Game_Service_H5RecommendGames::updateItemsList($id, $games);
        if (! $ret)
            $this->output('-1', '操作失败.');
        $this->output('0', '操作成功.');
    }
	
	/**
	 * 推荐游戏排序
	 */
	public function gamesSortAction() {
	    $id = intval($this->getInput('id'));
	    $list = Game_Service_H5RecommendGames::getGames($id);
	    foreach($list as $key=>$value){
            $game = $this->getGameInfo($value['game_id']);
            $list[$key] = array_merge($game, $value);
	    }
	    $info = Game_Service_H5RecommendNew::getRecommendnew($id);
	    if($info['status'] == Game_Service_H5RecommendNew::RECOMMEND_INVALID_STATUS) {
	        $info['status'] = Game_Service_H5RecommendNew::RECOMMEND_OPEN_STATUS;
	    }
	    $this->assign('info', $info);
	    $this->assign('id', $id);
	    $this->assign('list', $list);
	}
	
	/**
	 * 推荐游戏排序保存
	 */
	public function gamesSortPostAction() {
	    $id = $this->getInput('id');
	    $sort = $this->getInput('sort');
	    $status = $this->getInput('status');
	    
	    $info = Game_Service_H5RecommendNew::getRecommendnew(intval($id));
	    if(!$info) {
	        $this->output('-1', '操作失败.');
	    }
	    $ret = Game_Service_H5RecommendGames::updateItemsSort($id, $sort);
	    if (!$ret) $this->output('-1', '操作失败.');
	    
	    if($status != $info['status']) {
	        $data['status'] = $status;
	        Game_Service_H5RecommendNew::updateRecommendnew($data, $id);
	    }
	    
	    $this->output('0', '操作成功.');
	}
	
	/**
	 * 保存推荐图片
	 */
	public function imgAddPostAction() {
	    $status = $this->getInput('status');
	    $data = $this->getInput(array('link_type', 'link', 'img', 'recommend_id'));
	    if(!$data['recommend_id']) $this->output(-1, '推荐ID不能为空.');
	    $info = $this->cookImgData($data);
	    $result = Game_Service_H5RecommendImgs::addRecommendImgs($info);
	    if (!$result) $this->output(-1, '操作失败');
	    
        $recommend['status'] = $status;
        Game_Service_H5RecommendNew::updateRecommendnew($recommend, $data['recommend_id']);
	
	    $this->output('0', '操作成功.');
	}
	
	/**
	 * 保存推荐图片
	 */
	public function imgEditPostAction() {
	    $id = $this->getInput('id');
	    $status = $this->getInput('status');
	    $requestData = $this->getInput(array('link_type', 'link', 'img', 'recommend_id'));
		$postData = $this->cookImgData($requestData);
	    
	    $editInfo = Game_Service_H5RecommendImgs::getRecommendImgs($id);
		if (!$editInfo) $this->output(-1, '编辑的内容不存在');	

	    $updateParams = $this->getUpdateParams($postData, $editInfo);
	    if (count($updateParams) >0) {
	        $ret = Game_Service_H5RecommendImgs::updateRecommendImgs($updateParams, $id);
	        if (!$ret) $this->output(-1, '操作失败');
	    }
	    $data['status'] = $status;
	    Game_Service_H5RecommendNew::updateRecommendnew($data, $requestData['recommend_id']);
	    $this->output('0', '操作成功.');
	}
	
	/**
	 * 删除
	 */
	public function deleteAction() {
		$id = $this->getInput('id');
		$info = Game_Service_H5RecommendNew::getRecommendnew($id);
		if (!$info) $this->output(-1, '无法删除');
		$result = Game_Service_H5RecommendNew::deleteRecommendnew($id);
		if (!$result) $this->output(-1, '操作失败');
		$this->output(0, '操作成功');
	}
	
	public function rankDeleteAction() {
	    $id = $this->getInput('id');
	    $info = Game_Service_H5RecommendNew::getRecommendnew($id);
	    if (!$info) $this->output(-1, '无法删除');
	    $result = Game_Service_H5RecommendNew::deleteRecommendnew($id);
	    if (!$result) $this->output(-1, '操作失败');
	    $this->output(0, '操作成功');
	}
	
	public function otherDeleteAction() {
	    $id = $this->getInput('id');
	    $info = Game_Service_H5RecommendNew::getRecommendnew($id);
	    if (!$info) $this->output(-1, '无法删除');
	    $result = Game_Service_H5RecommendNew::deleteRecommendnew($id);
	    if (!$result) $this->output(-1, '操作失败');
	    $this->output(0, '操作成功');
	}

	/**
	 * 查询游戏
	 */
	public function queryGameAction() {
	    $name = $this->getInput('name');
	    $selected = $this->getInput('selected');
	    
	    //游戏名称检索
	    $gameParams['status'] = 1;
	    $gameParams['name'] = array('LIKE', $name);
	    $gameData = Resource_Service_Games::getsBy($gameParams);
        
	    $list = array();
	    foreach($gameData as $value){
	        if($selected && in_array($value['id'], $selected)) continue;
	        $game = $this->getGameInfo($value['id']);
	        $game['id'] = $value['id'];
	        $list[] = $game;
	    }
	    $this->output('0', '查询成功.', $list);
	}
	
	/**
	 *
	 * Enter description here ...
	 */
	public function uploadAction() {
	    $imgId = $this->getInput('imgId');
	    $this->assign('imgId', $imgId);
	    $this->getView()->display('common/upload.phtml');
	    exit;
	}
	
	/**
	 *
	 * Enter description here ...
	 */
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
	
	/**
	 * 检查数据有效性
	 */
	private function cookData($info) {
		if(!$info['title']) $this->output(-1, '标题不能为空.');
        if (Util_String::strlen($info['title']) > 15) {
            $this->output(- 1, '标题不能超过15个字.');
        }
		return $info;
	}
	
	/**
	 * 检查数据有效性
	 */
	private function cookImgData($requestData) {
		if(!isset($requestData['link_type'])) $this->output(-1, '链接类型不能为空.');
		if(!isset($requestData['link'])) $this->output(-1, '链接参数不能为空.');

		if(! Game_Service_Util_Link::checkLinkValue($requestData['link_type'], $requestData['link'])) {
		    $this->output(-1, '链接参数错误.');
		}
		if(!$requestData['img']) $this->output(-1, '图片不能为空.');
		return $requestData;
	}

}
