<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_MygamesController extends Api_BaseController {
    public $perpage = 10;
    const FAVORITE_OPTION_ADD = 1;
    const FAVORITE_OPTION_REMOVE = 2;

    const FAVORITE_API_QUERY = 11;
    const FAVORITE_API_OPERATE = 12;
    
    const APPS_FILTER_EXPIRE = 86400;

    public function installedGamesAction() {
    	$appList = $this->extractAppList();
    	$gameInfoList = $this->extractGames($appList);
    	$installedGames = $this->constructInstalledGamesOutput($gameInfoList);
    
    	$this->localOutput(0, '', $installedGames);
    }
    
    public function extractGamesAction() {
        $appList = $this->extractAppList();
        $gameInfoList = $this->extractGames($appList);
        $myGames = $this->constructMyGamesOutput($gameInfoList);

        $this->localOutput(0, '', $myGames);
    }

    public function queryFavoriteStateAction() {
        $favoriteParam = $this->extractFavoriteInputed(self::FAVORITE_API_QUERY);
        $this->checkFavoriteParam($favoriteParam, self::FAVORITE_API_QUERY);
        $state = $this->queryFavoriteState($favoriteParam);
        $response['state'] = $state;

        $this->localOutput(0, '', $response);
    }

    public function operateFavoriteAction() {
        $favoriteParam = $this->extractFavoriteInputed(self::FAVORITE_API_OPERATE);
        $this->checkFavoriteParam($favoriteParam, self::FAVORITE_API_OPERATE);
        $result = $this->executeFavoriteOperate($favoriteParam);
        $code = $result ? 0 : -1;
        $this->localOutput($code);
    }

    public function getFavoriteAction() {
        list($page, $uuid) = $this->extractPageRequestParm();
        list($total, $favoriteGames) = $this->getFavoriteGames($page, $uuid);
        $gameList = $this->getGamesInfo($favoriteGames);
        //$gameList = $this->constructFavoritesOutput($gameList);

        $hasNext = ($page * $this->perpage >= $total) ? false : true;
        $pageData['hasnext'] = $hasNext;
        $pageData['curpage'] = $page;
        $pageData['list'] = $gameList;
        $pageData['totalCount'] = $total;

        $this->localOutput(0, '', $pageData);
    }

    private function constructFavoritesOutput($gamesInfo) {
        $favoriteGames = array();
        foreach($gamesInfo as $key => $value) {
            $item['gameId'] = $value['gameId'];
            $item['name'] = html_entity_decode($value['name'], ENT_QUOTES);
            $item['iconUrl'] = $value['iconUrl'];
            $item['packageName'] = $value['package'];
            $item['resume'] = html_entity_decode($value['resume'], ENT_QUOTES);
            $item['apkUrl'] = $value['link'];
            $item['size'] = $value['size'];
            $item['category'] = $value['category'];
            $item['hot'] = $value['hot'];
            $item['stars'] = $value['score'];
            $item['freeDownload'] = $value['freedl'];
            $item['attach'] = $value['attach'];
            $favoriteGames[] = $item;
        }

        return $favoriteGames;
    }

    private function getGamesInfo($favoriteGames) {
        $gamesInfo = array();
        foreach ($favoriteGames as $key => $value) {
            $gameId = $value['game_id'];
            $basicInfo = Resource_Service_GameData::getBasicInfo($gameId);
             $basicInfo['name'] = html_entity_decode($basicInfo['name'], ENT_QUOTES);
            if (empty($basicInfo)) {
                continue;
            }
            $extraInfo = Resource_Service_GameData::getExtraInfo($gameId);
            $item = array_merge($basicInfo, $extraInfo);
            $gamesInfo[] = $item;
        }

        return $gamesInfo;
    }

    private function getFavoriteGames($page, $uuid) {
        $search['uuid'] = $uuid;
        $sortBy = array('create_time'=>'DESC');

        return Client_Service_Attention::getList($page, $this->perpage, $search, $sortBy);
    }

    private function extractPageRequestParm() {
        $page = intval($this->getInput('page'));
        $uuid = $this->getInput('puuid');

        if (!$uuid || !$page) {
            $this->localOutput(-1, 'invalid parameters inputed');
        }

        return array($page, $uuid);
    }

    private function extractFavoriteInputed($whichApi) {
        $favoriteParam['gameId'] = intval($this->getInput('gameId'));
        $favoriteParam['uuid'] = $this->getInput('puuid');
        $favoriteParam['userName'] = $this->getInput('uname');
        if (self::FAVORITE_API_OPERATE == $whichApi) {
            $favoriteParam['option'] = intval($this->getInput('option'));
        }

        return $favoriteParam;
    }

    private function checkFavoriteParam($favoriteParam, $whichApi) {
        if (!$favoriteParam['gameId']) {
            $this->localOutput(-1, 'not found game id');
        }
        if (!$favoriteParam['uuid']) {
            $this->localOutput(-1, 'not found uuid');
        }

        if (self::FAVORITE_API_OPERATE == $whichApi) {
            if (self::FAVORITE_OPTION_ADD != $favoriteParam['option'] &&
                    self::FAVORITE_OPTION_REMOVE != $favoriteParam['option']) {
                $this->localOutput(-1, 'invalid option: ' . $favoriteParam['option']);
            }
        }

        $gameInfo = Resource_Service_GameData::getBasicInfo($favoriteParam['gameId']);

        if ($favoriteParam['gameId'] != $gameInfo['gameid']) {
            $this->localOutput(-1, 'we have no this game');
        }
    }

    private function executeFavoriteOperate($favoriteParam) {
        if (self::FAVORITE_OPTION_ADD == $favoriteParam['option']) {
            return $this->commitFavorite($favoriteParam);
        } else if (self::FAVORITE_OPTION_REMOVE == $favoriteParam['option']) {
            return $this->removeFavorite($favoriteParam);
        }

        return false;
    }

    private function queryFavoriteState($favoriteParam) {
        $search['uuid'] = $favoriteParam['uuid'];
        $search['game_id'] = $favoriteParam['gameId'];
        $log = Client_Service_Attention::getBy($search);

        if($log) {
            return true;
        } else {
            return false;
        }
    }

    private function commitFavorite($favoriteParam) {
        $search['uuid'] = $favoriteParam['uuid'];
        $search['game_id'] = $favoriteParam['gameId'];
        $oldLog = Client_Service_Attention::getBy($search);

        $favoriteItem['uuid'] = $favoriteParam['uuid'];
        $favoriteItem['uname'] = html_entity_decode($favoriteParam['userName'], ENT_QUOTES);
        $favoriteItem['game_id'] = $favoriteParam['gameId'];
        $favoriteItem['create_time'] = Common::getTime();

        if(!$oldLog) {
            return Client_Service_Attention::add($favoriteItem);
        } else {
            return Client_Service_Attention::update($favoriteItem, $oldLog['id']);
        }
    }

    private function removeFavorite($favoriteParam) {
        $search['uuid'] = $favoriteParam['uuid'];
        $search['game_id'] = $favoriteParam['gameId'];
        return Client_Service_Attention::deleteBy($search);
    }

    private function extractAppList() {
        $apps = $this->getInput('appList');
        if(!$apps) {
            $this->localOutput(-1, 'not found app list');
            return;
        }

        $appInfoList = explode('|', $apps);
        $appInfoList = Common::filterCommonApps($appInfoList);

        foreach($appInfoList as $key => $packageName) {
            $appInfoList[$packageName] = crc32($packageName);
        }

        if ((!$appInfoList) || count($appInfoList) <= 0) {
            $this->localOutput(-1, 'not found app list');
        }

        return $appInfoList;
    }

    private function extractGames($appList) {
        $packageCrcList = array_values($appList);

        $search = array();
        $search['status'] = 1;
        $search['packagecrc'] = array('IN', $packageCrcList);
        $orderBy = array();

        $gameInfoList = Resource_Service_Games::getsBy($search);

        $games = array();
        foreach ($gameInfoList as $packageCrc => $packageInfo) {
            if (array_key_exists($packageInfo['package'], $appList)) {
                $games[] = $packageInfo;
            }
        }

        return $games;
    }

    private function fillBasicInfo($gameInfo) {
        $item['name'] = html_entity_decode($gameInfo['name'], ENT_QUOTES);
        $item['packageName'] = $gameInfo['package'];
        $item['gameId'] = $gameInfo['id'];
        $item['iconUrl'] = $gameInfo['img'];
        $item['resume'] = html_entity_decode($gameInfo['resume'], ENT_QUOTES);

        return $item;
    }

    private function fillStars($gameId) {
        $gameInfo = Resource_Service_GameData::getExtraInfo($gameId);
        return $gameInfo['score'];
    }

    private function constructMyGamesOutput($gameInfoList) {
        $myGames = array();
        foreach($gameInfoList as $key => $value) {
            $item = $this->fillBasicInfo($value);
            $item['stars'] = $this->fillStars($value['id']);
            $myGames['gameList'][] = $item;
        }

        return $myGames;
    }
    
    private function constructInstalledGamesOutput($gameInfoList) {
    	$installedGames = array();
    	foreach($gameInfoList as $key => $value) {
    		$item = $this->fillBasicInfo($value);
    		$installedGames['list'][] = $item;
    	}
    
    	return $installedGames;
    }
  
    public function FilterAppsAction() {
        $data = array();
        $apps = $this->getFilterApps();
        $data['items'] = $apps;
        $this->localOutput(0, '', $data);
    }

    private function getFilterApps() {
        $filterList = Common::getConfig('appFilterConfig');
        $apps = '';
        if(!$filterList){
            return $apps;
        }
        
        $appList = array();
        foreach($filterList as $key => $value) {
            $appList[] = array( 
                     'packageName' => $key,
                    );
        }
        
        return $appList;
    }
}
