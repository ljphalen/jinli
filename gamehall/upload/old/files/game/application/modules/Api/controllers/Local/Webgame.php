<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 网游频道
 * @author wupeng
 */
class Local_WebgameController extends Api_BaseController {
    
    /**
     * 按钮导航
     */
    public function navigationAction() {
    	$sp = $this->getInput('sp');
    	if(! $sp) {
    		return false;
    	}
    	$buttonList = Game_Api_WebGameButton::getButtonConfig();
    	$data = array();
    	$data['sign'] = 'GioneeGameHall';
    	$data['msg'] = '';
    	$data['success'] = true;
    	$data['data'] = $buttonList;
    	$this->clientOutput($data);
    }
    
    /**
     * 轮播图
     */
    public function bannerAction() {
        $sp = $this->getInput('sp');
        if(! $sp) {
            return false;
        }
    	$spArr = Common::parseSp($sp);
    	$clientVersion = Common::getClientVersion($spArr['game_ver']);
        $dataList = Game_Api_WebRecommendBanner::getClientBanner($clientVersion);
    	
    	$data = array();
    	$data['sign'] = 'GioneeGameHall';
    	$data['msg'] = '';
    	$data['success'] = true;
    	$data['data'] = $dataList;
    	$this->clientOutput($data);
    }

    /**
     * 推荐列表
     */
    public function listAction() {
        $sp = $this->getInput('sp');
    	$page = intval($this->getInput('page'));
        if(! $sp) {
            return false;
        }
        if($page <= 0) {
            $page = 1;
        }
    	$spArr = Common::parseSp($sp);
    	$device  = $spArr['device'];
    	$systemVersion = Common::getSystemVersion($spArr['android_ver']);

        $dataList = Game_Api_WebRecommendList::getClientRecommend($page, $device, $systemVersion);
        $data = array();
        $data['sign'] = 'GioneeGameHall';
        $data['msg'] = '';
        $data['success'] = true;
        $data['data'] = $dataList;
    	$this->clientOutput($data);
    }
    
    /**
     * 游戏列表
     */
    public function gameListAction() {
        $sp = $this->getInput('sp');
    	$page = intval($this->getInput('page'));
    	$recommendId = intval($this->getInput('id'));
        if(! $sp) {
            return false;
        }
        if($page <= 0) {
            $page = 1;
        }
        $pageData = Game_Api_WebRecommendList::getClientRecommendGamesList($recommendId, $page);
        $data = array();
        $data['sign'] = 'GioneeGameHall';
        $data['msg'] = '';
        $data['success'] = true;
        $data['data'] = $pageData;
        $this->clientOutput($data);
    }
    
    /**
     * 开服列表
     */
    public function openListAction() {
        $sp = $this->getInput('sp');
        $page = intval($this->getInput('page'));
        if(! $sp) {
            return false;
        }
        if ($page < 1) $page = 1;
        $pageData = Game_Api_WebRecommendOpen::getOpenListByPage($page);
        $data = array();
        $data['sign'] = 'GioneeGameHall';
        $data['msg'] = '';
        $data['success'] = true;
        $data['data'] = $pageData;
        
        $this->clientOutput($data);
    }
    
}
