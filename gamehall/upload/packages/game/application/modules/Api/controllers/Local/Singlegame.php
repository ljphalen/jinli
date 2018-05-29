<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 单机频道
 * @author wupeng
 */
class Local_SinglegameController extends Api_BaseController {

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
        $dataList = Game_Api_SingleRecommendBanner::getClientBanner($clientVersion);
    	
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

        $dataList = Game_Api_SingleRecommendList::getClientRecommend($page, $device, $systemVersion);
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
        $pageData = Game_Api_SingleRecommendList::getClientRecommendGamesList($recommendId, $page);
        $data = array();
        $data['sign'] = 'GioneeGameHall';
        $data['msg'] = '';
        $data['success'] = true;
        $data['data'] = $pageData;
        $this->clientOutput($data);
    }

    /**
     * 礼包列表列表
     */
    public function giftListAction() {
        $sp = $this->getInput('sp');
    	$page = intval($this->getInput('page'));
    	$recommendId = intval($this->getInput('id'));
        if(! $sp) {
            return false;
        }
        if($page <= 0) {
            $page = 1;
        }
        $userName = $this->getInput('uname');
        $imei = end(explode('_',$sp));
        $onLine = false;
        if($userName) {
            $onLine = Account_Service_User::checkOnline($userName, $imei);
        }
        $pageData = Game_Api_SingleRecommendList::getClientRecommendGiftsList($recommendId, $page, $onLine, $userName);
        $data = array();
        $data['sign'] = 'GioneeGameHall';
        $data['msg'] = '';
        $data['success'] = true;
        $data['data'] = $pageData;
        $this->clientOutput($data);
    }

}
