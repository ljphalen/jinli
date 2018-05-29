<?php

if (!defined('BASE_PATH'))
    exit('Access Denied!');

class IndexController extends Game_BaseController {

    public $actions = array(
        'listUrl' => '/index/index',
        'detailUrl' => '/index/detail/',
        'giftUrl' => '/index/gift/',
        'tjUrl' => '/index/tj',
        'strategyUrl' => '/index/strategy'
    );
    public $perpage = 10;
    const TMP_EXPIRE = 600;

    /**
     * 
     * index page view
     */
    public function indexAction() {
        $t_bi = $this->getInput('t_bi');
        if (!$t_bi)
            $this->setSource();
        $configs = Game_Service_Config::getAllConfig();
        unset($configs['game_react']);
        $this->assign('configs', $configs);
        $indexKey = $this->getCacheKey();
        $cache = $this->getCache();
        $data = $cache->get($indexKey);
        if (!$data || $data['timetmp'] !== strtotime(date('Y-m-d'))) {
            $bannerList = Game_Service_H5HomeDataHandle::getRecommendBannersBy(array('day_id' => strtotime(date('Y-m-d'))));
            $data['imgUrl'] = $this->initBannerInfo($bannerList);
            $recommendList = Game_Service_H5HomeDataHandle::getRecommendListGetsBy(array('day_id' => strtotime(date('Y-m-d')), 'status' => Game_Service_H5RecommendNew::GAME_OPEN_STATUS));
            $data['list'] = $this->initRecommend($recommendList);
            $data['timetmp'] = strtotime(date('Y-m-d'));
            if ($data['list']) {
                $cache->set($indexKey, $data, self::TMP_EXPIRE);
            }
        }
        $data['indexAd'] = Game_Service_H5IndexAd::getLast();
        $this->assign('dataInfoList', json_encode($data['list']));
        $this->assign('indexAd', $data['indexAd']);
        $this->assign('bi', $this->getSource());
        $this->assign('dataBannerList', json_encode($data['imgUrl']));
    }
    
    public function closeTooltipAction() {
        exit;
    }

    private function getCache() {
        return Cache_Factory::getCache();
    }

    private function getCacheKey() {
        return Util_CacheKey::HOME_H5_INDEX;
    }

    private function initBannerInfo($bannerList) {
        $bannerInfo = array();
        $sort = 4;
        foreach ($bannerList as $key => $banner) {
            if ($this->initCheckInfo($banner['link_type'], $banner['link']) == true) {
                $tmp = array();
                $tmp['sort'] = $sort--;
                $tmp['id'] = $banner['id'];
                $tmp['name'] = $banner['title'];
                $tmp['imgUrl'] = Common::getAttachPath() . $banner['img'];
                $tmp['href'] = $this->initBannerHrefSet($banner['link_type'], $banner['link'], $key+1, $tmp['id']);
                $bannerInfo[] = $tmp;
            }
        }
        return array_values($bannerInfo);
    }

    private function initCheckInfo($linkType, $linkInfo) {
        switch ($linkType) {
            case Game_Service_H5RecommendBanner::BANNER_TYPE_GAME:
                return $this->gameInfo($linkInfo) !== NULL ? true : false;
            case Game_Service_H5RecommendBanner::BANNER_TYPE_CATEGORY:
                return true;
            case Game_Service_H5RecommendBanner::BANNER_TYPE_SUBJECT:
                $info = Client_Service_Subject::getSubject($linkInfo);
                if ($info['status'] == 1 && $info['start_time'] <= Common::getTime() && $info['end_time'] >= Common::getTime()) {
                    return true;
                } else {
                    return false;
                }
            case Game_Service_H5RecommendBanner::BANNER_TYPE_URL:
                return true;
            case Game_Service_H5RecommendBanner::BANNER_TYPE_ACTIVITY:
                $info = Client_Service_Hd::getHd($linkInfo);
                if ($info['status'] == 1 && $info['start_time'] <= Common::getTime() && $info['end_time'] >= Common::getTime()) {
                    return true;
                } else {
                    return false;
                }
        }
    }

    private function initBannerHrefSet($linkType, $linkInfo, $key, $id, $type = 'bannerList', $keyWithRec = '') {
        if ($type == 'bannerList') {
            $statistSrc = 'home_ad' . $id . 'I' . $key;
        } else {
            $statistSrc = 'home'.$keyWithRec.'_img' . $id;
        }
        switch ($linkType) {
                case Game_Service_H5RecommendBanner::BANNER_TYPE_GAME:
                    return Util_Statist::getGameDetailUrl($linkInfo, $statistSrc);
                case Game_Service_H5RecommendBanner::BANNER_TYPE_CATEGORY:
                    return Util_Statist::getCategoryDetailUrl(0, $linkInfo, $statistSrc);
                case Game_Service_H5RecommendBanner::BANNER_TYPE_SUBJECT:
                    return Util_Statist::getSubjectDetailUrl($linkInfo, $statistSrc);
                case Game_Service_H5RecommendBanner::BANNER_TYPE_URL:
                    return Util_Statist::getOutLinkUrl($linkInfo, $statistSrc);
                case Game_Service_H5RecommendBanner::BANNER_TYPE_ACTIVITY:
                    return Util_Statist::getActivityDetailUrl($linkInfo, $statistSrc);
        }
        return NULL;
    }

    /*     * 设置游戏名称 */

    private function initRecommend($recommendList) {
        $recommendData = array();
        $this->mRankType = Game_Service_Rank::getH5OpenRankType();
        foreach ($recommendList as $key => $recommend) {
            $h5HomeListInfo = array(
                'id' => $recommend['id'],
                'name' => $recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_ACTIVE ? '' : $recommend['title'],
                'type' => Game_Service_H5RecommendNew::$m_h5_index_type[$recommend['rec_type']],
            );
            if ($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_LIST) {
                $h5HomeListInfo['list'] = array();
                $gamesInfo = Game_Service_H5HomeDataHandle::getGames($recommend["id"]);
                $h5HomeListInfo = $this->gameIndexList($gamesInfo, $h5HomeListInfo, $recommend["id"], $key+1);
            } elseif ($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_RANK) {
                $rankInfo = Game_Service_H5HomeDataHandle::getRankByRecommendId($recommend['id']);
                $h5HomeListInfo = $this->rankIndexList($rankInfo, $h5HomeListInfo, $key+1);
            } elseif ($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_NEW) {
                $newsInfo = Game_Service_H5HomeDataHandle::getNewsByRecommendId($recommend['id']);
                $h5HomeListInfo = $this->newsIndexList($newsInfo, $h5HomeListInfo, $key+1);
            } elseif ($recommend['rec_type'] == Game_Service_H5RecommendNew::REC_TYPE_ACTIVE) {
                $newsInfo = Game_Service_H5HomeDataHandle::getNewsByRecommendId($recommend['id']);
                $h5HomeListInfo = $this->activeIndexList($newsInfo, $h5HomeListInfo, $key+1);
            } else {
                if ($recommend['status'] == '1') {
                    $imgInfo = Game_Service_H5HomeDataHandle::getRecommendImgsBy(array('recommend_id' => $recommend["id"]));
                    $h5HomeListInfo = $imgInfo ? $this->imgIndexList($imgInfo, $h5HomeListInfo, $key+1) : NULL;
                } else {
                    $h5HomeListInfo = NULL;
                }
            }
            if ($h5HomeListInfo) {
                $recommendData[] = $h5HomeListInfo;
            }
        }
        return $recommendData;
    }

    private function imgIndexList($imgInfo, $h5HomeListInfo, $h5key) {
        if ($this->initCheckInfo($imgInfo['link_type'], $imgInfo['link'])) {
            $h5HomeListInfo['imgUrl'] = Common::getAttachPath() . $imgInfo['img'];
            $h5HomeListInfo['href'] = $this->initBannerHrefSet($imgInfo['link_type'], $imgInfo['link'], 1, $h5HomeListInfo['id'], 'imgList', $h5key);
            $h5HomeListInfo['typeName'] = $h5HomeListInfo['typeName'] . '-' . Game_Service_Util_Link::$linkType[$imgInfo['link_type']];
            return $h5HomeListInfo;
        } else {
            return null;
        }
    }

    private function gameIndexList($gamesInfo, $h5HomeListInfo, $recommendId, $h5key) {
        $index = 0;
        foreach ($gamesInfo as $key => $game) {
            if ($index < Game_Service_H5RecommendNew::SHOW_NUM) {
                $gameListInfo = self::gameInfo($game['game_id'], $key+1, $recommendId ,$h5key);
                if ($gameListInfo) {
                    $h5HomeListInfo['data'][] = $gameListInfo;
                    $index++;
                }
            }
        }
        $h5HomeListInfo['LoadMoreHref'] = Util_Statist::getRecommendGameList($recommendId, 'home'.$h5key.'_RCMDlist');
        return $h5HomeListInfo;
    }

    private function activeIndexList($recommend, $h5HomeListInfo, $h5key) {
        $ids = common::resetKey($recommend, 'list_id');
        if (!$ids)
            return '';
        $statis = 'home'.$h5key;
        $params = array('status' => 1, 'id' => array('IN', array_keys($ids)));
        $params['start_time'] = array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
        $params['end_time'] = array('>=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
        $newList = Client_Service_Hd::getHdByIds($params);
        foreach ($newList as $key => $newInfo) {
            $newNameList[] = $newInfo['title'];
            $h5HomeListInfo['activityName'] = $newInfo['title'];
            $h5HomeListInfo['info'] = $recommend[0]['list_resume'];
            $h5HomeListInfo['date'] = date('Y-m-d', $newInfo['start_time']).'至'.date('Y-m-d', $newInfo['end_time']);
            $h5HomeListInfo['imgUrl'] = Common::getAttachPath() . $newInfo['img'];
            $h5HomeListInfo['href'] = Util_Statist::getActivityDetailUrl($newInfo['id'], $statis);
        }
        $h5HomeListInfo['LoadMoreHref'] = Util_Statist::getActivityListUrl($statis);
        $h5HomeListInfo['names'] = implode(',', $newNameList);
        return $newNameList ? $h5HomeListInfo : NULL;
    }

    private function newsIndexList($recommend, $h5HomeListInfo, $h5key) {
        $ids = common::resetKey($recommend, 'list_id');
        if (!$ids)
            return '';
        $statis = 'home'.$h5key;
        $newList = Client_Service_News::getNewsByIds(array('id' => array('IN', array_keys($ids))));
        foreach ($newList as $key => $newInfo) {
            $newData = array(
                'name' => mb_strlen($newInfo['title'], 'utf-8') > 6 ? mb_substr($newInfo['title'], 0, 6, 'utf-8') : $newInfo['title'],
                'imgUrl' => Common::getAttachPath() . $newInfo['thumb_img'],
                'href' => Util_Statist::getZXDetailUrl($newInfo['id'], $statis),
                'info' => $newInfo['resume'],
                'link' => $newInfo['link']
            );
            $newNameList[] = $newInfo['title'];
            $h5HomeListInfo['list'][] = $newData;
        }
        $h5HomeListInfo['LoadMoreHref'] = Util_Statist::getZXListUrl($statis);
        $h5HomeListInfo['names'] = implode(',', $newNameList);
        return $h5HomeListInfo;
    }

    private function rankIndexList($recommend, $h5HomeListInfo, $h5key) {
        $webroot = Common::getWebRoot();
        list($total, $gameList) = Game_Service_Rank::getGameListByType($recommend['rank_type'], 1, Game_Service_H5RecommendRank::INDEX_RANK_SHOWNUM);
        foreach ($gameList as $key => $gameInfo) {
            $gameData = array(
                'name' => mb_strlen($gameInfo['name'], 'utf-8') > 6 ? mb_substr($gameInfo['name'], 0, 6, 'utf-8') : $gameInfo['name'],
                'size' => $gameInfo['size'] . 'M',
                'href' => Util_Statist::getGameDetailUrl($gameInfo['id'], 'home'.$h5key.'_'.$recommend['rank_type'].'I'.($key+1)),
                'info' => $gameInfo['resume'],
                'stars' => $gameInfo['web_star'],
                'typeName' => $gameInfo['category_title'],
                'imgUrl' => $gameInfo['img'],
                'download' => Util_Statist::getDownloadUrl($gameInfo['id'], $gameInfo['link'], $key+1, 'home'.$h5key.'_'.$recommend['rank_type'].$h5HomeListInfo['id'].'I'.($key+1))
            );
            $h5HomeListInfo['data'][] = $gameData;
        }
        $h5HomeListInfo['LoadMoreHref'] = Util_Statist::getRankDetailUrl($recommend['rank_type'], 'home'.$h5key);
        $h5HomeListInfo['name'] = $this->mRankType[$recommend['rank_type']];
        return $h5HomeListInfo;
    }

    private function gameInfo($gameId, $key, $recommendId, $h5key) {
        $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
        if ($gameInfo['status'] < 1)
            return null;
        $webroot = Common::getWebRoot();
        $gameData = array(
            'name' => mb_strlen($gameInfo['name'], 'utf-8') > 6 ? mb_substr($gameInfo['name'], 0, 6, 'utf-8') : $gameInfo['name'],
            'stars' => $gameInfo['web_star'],
            'size' => $gameInfo['size'] . 'M',
            'info' => $gameInfo['resume'],
            'typeName' => $gameInfo['category_title'],
            'href' => Util_Statist::getGameDetailUrl($gameInfo['id'], 'home'.$h5key.'_RCMD'.$recommendId.'I'.$key),
            'imgUrl' => $gameInfo['img'],
            'download' => Util_Statist::getDownloadUrl($gameInfo['id'], $gameInfo['link'], $key, 'home'.$h5key.'_RCMD'.$recommendId)
        );
        return $gameData;
    }

    public function linkRedirectAction() {
        $t_bi = $this->getInput('t_bi');
        if (!$t_bi)
            $this->setSource();
        $url = html_entity_decode($this->getInput('url'));
        $this->redirect($url);
        exit();
    }
    
    public function tjAction() {
        $id = intval($this->getInput('id'));
        $type = $this->getInput('type');
        $url = html_entity_decode(html_entity_decode($this->getInput('_url')));
        if ($id) {
            switch ($type) {
                case GAME:
                    Game_Service_Game::updateGameTJ($id);
                    break;
                case AD:
                    Game_Service_Ad::updateAdTJ($id);
                    break;
                case SUBJECT:
                    Game_Service_Subject::updateSubjectTJ($id);
                    break;
                default:
            }
        }
        if (strpos($url, "gionee.com")) {
            if (strpos($url, '?') === false) {
                $url = $url . '?t_bi=' . $this->getSource();
            } else {
                $url = $url . '&t_bi=' . $this->getSource();
            }
        }
        $this->redirect($url);
    }

    /**
     * 
     * get game detail info
     */
    public function detailAction() {
        $gameId = intval($this->getInput('id'));
        $t_bi = $this->getInput('t_bi');
        if (!$t_bi)
            $this->setSource();
        
        $from = $this->getInput('from') ? $this->getInput('from') : 'gionee';
        if (!$t_bi)
            $this->setSource();
        if ($from == 'baidu') {
            $this->baiduGameInfo($gameId, $from);
        } else {
            $this->gioneeGameInfo($gameId);
        }
        list($hasPage, $list, $giftNum) = $this->giftList($gameId, 1);
        list($hasPage, $list, $strategyNum) = $this->strategyList($gameId, 1);
        $this->assign('activities', $this->fillActivities($gameId));
        $this->assign('hasGift', $giftNum > 0 ? 'true' : 'false');
        $this->assign('hasStrategy', $strategyNum > 0 ? 'true' : 'false');
        $this->assign('ajaxUrl', Common::getWebRoot() . '/index/');
        $this->assign('from', $from);
        $this->assign('tjUrl', $this->actions['tjUrl']);
        $this->assign('hasSearch', true);
        $this->assign('gameId', $gameId);
        $this->assign('intersrc', $this->getInput('intersrc'));
        $this->assign('title', '游戏详情');
    }

    private function baiduGameInfo($gameId) {
        $baiduApi = new Api_Baidu_Game();
        $info = $baiduApi->getInfo($gameId, 'baidu');
        foreach ($info['simgs'] as $key => $value) {
            $viewPicList[] = "'" . $value . "'";
        }
        $viewPicListJs = implode(',', $viewPicList);
        $this->assign('info', $info);
        $this->assign('viewPicListJs', $viewPicListJs);
    }

    private function gioneeGameInfo($gameId) {
        $info = Resource_Service_GameData::getGameAllInfo($gameId);
        foreach ($info['gimgs'] as $key => $value) {
            $viewPicList[] = "'" . $value . "'";
        }
        $viewPicListJs = implode(',', $viewPicList);
        $clientVersion = $this->getInput('clientVersion');
        $uuid = $this->getInput('uuid');
        $this->shareSendAtIcket($gameId, $uuid, $clientVersion);
        $this->assign('info', $info);
        $this->assign('viewPicListJs', $viewPicListJs);
        $this->whichIsYourLikePlayList($gameId);
        list(, $news) = Client_Service_News::getGameNews(1, 10, array('hot' => '1', 'game_id' => $gameId, 'status' => 1));
        $this->getClientDownloadUrl();
        $this->assign('newList', json_encode($news));
    }

    private function getClientDownloadUrl() {
        if (ENV == 'product') {
            $game = Resource_Service_GameData::getGameAllInfo(117);
        } else {
            $game = Resource_Service_GameData::getGameAllInfo(66);
        }
        $this->assign('gameClient', $game);
    }

    public function giftAction() {
        $t_bi = $this->getInput('t_bi');
        if (!$t_bi)
            $this->setSource();
        $giftId = intval($this->getInput('giftid'));
        $giftInfo = Client_Service_Gift::getGift($giftId);
        $gameInfo = Resource_Service_GameData::getGameAllInfo($giftInfo['game_id']);
        //剩下的激活码数量
        $giftRemainNum = Client_Service_Gift::getGiftRemainNum($giftId);
        //总激活码数量
        $giftTotal = Client_Service_Gift::getGiftTotal($giftId);
        $this->getClientDownloadUrl();
        $this->assign('giftTotal', $giftTotal);
        $this->assign('giftRemainNum', $giftRemainNum);
        $this->assign('giftInfo', $giftInfo);
        $this->assign('gameInfo', $gameInfo);
        $this->assign('hasSearch', true);
        $this->assign('title', '礼包详情');
    }

    public function strategyAction() {
        $t_bi = $this->getInput('t_bi');
        if (!$t_bi)
            $this->setSource();
        $strategyid = intval($this->getInput('strategyid'));
        $strategyInfo = Client_Service_News::getNews($strategyid);
        $this->assign('strategyInfo', $strategyInfo);
        $this->assign('hasSearch', true);
        $this->assign('title', '功略详情');
    }

    public function strategyListAction() {
        $t_bi = $this->getInput('t_bi');
        if (!$t_bi)
            $this->setSource();
        $gameId = intval($this->getInput('gameid'));
        $page = intval($this->getInput('page'));
        $page = $page > 0 ? $page : 1;
        list($hasNext, $strategyList) = $this->strategyList($gameId, $page);
        $this->output(0, '', array('hasNext' => $hasNext, 'page' => $page, 'list' => $strategyList));
    }

    public function giftListAction() {
        $t_bi = $this->getInput('t_bi');
        if (!$t_bi)
            $this->setSource();
        $gameId = intval($this->getInput('gameid'));
        $page = intval($this->getInput('page'));
        $page = $page > 0 ? $page : 1;
        list($hasNext, $giftList) = $this->giftList($gameId, $page);
        $this->output(0, '', array('hasNext' => $hasNext, 'page' => $page, 'list' => $giftList));
    }

    private function strategyList($gameId, $page) {
        $search = array('ntype' => Client_Service_News::ARTICLE_TYPE_STRATEGY, 'game_id' => $gameId, 
            'create_time' => array('>=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR)), 
            'create_time' => array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR))
            );
        list($total, $result) = Client_Service_News::getList($page, $this->perpage, $search, array('sort' => 'DESC', 'create_time' => 'DESC', 'id' => 'DESC'));
        $strategyList = array();
        foreach ($result as $key => $value) {
            $info['name'] = $value['name'];
            $info['href'] = $this->actions['strategyUrl'] . '/?strategyid=' . $value['id'] . '&intersrc=gamedetail'.$gameId.'_strategy'.$value['id'].'&t_bi=' . $this->getSource();
            $info['info'] = $value['resume'];
            $info['date'] = date('Y-m-d', $value['create_time']);
            $strategyList[] = $info;
        }
        return array(ceil($total / $this->perpage) > $page ? true : false, $strategyList, $total);
    }

    private function giftList($gameId, $page) {
        $orderBy = array('sort' => 'DESC', 'effect_start_time' => 'DESC', 'id' => 'DESC');
        $params['game_id'] = $gameId;
        $params['status'] = 1;
        $params['game_status'] = 1;
        $params['effect_start_time'] = array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
        $params['effect_end_time'] = array('>=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
        $gameInfo = Resource_Service_GameData::getGameAllInfo($gameId);
        list($total, $result) = Client_Service_Gift::getList($page, $this->perpage, $params, $orderBy);
        $giftList = array();
        foreach ($result as $key => $value) {
            $info['residual'] = Client_Service_Giftlog::getGiftlogByStatus(0, $value['id']);
            $info['total'] = Client_Service_Gift::getGiftTotal($value['id']);
            $info['name'] = $value['name'];
            $info['href'] = $this->actions['giftUrl'] . '?giftid=' . $value['id'] . '&intersrc=gamedetail'.$gameId.'_giftdetail'.$value['id'].'&t_bi=' . $this->getSource();
            $info['imgUrl'] = $gameInfo['big_img'] ? $gameInfo['big_img'] : ($gameInfo['mid_img'] ? $gameInfo['mid_img'] : $gameInfo['img']);
            $giftList[] = $info;
        }
        return array(ceil($total / $this->perpage) > $page ? true : false, $giftList, $total);
    }

    private function whichIsYourLikePlayList($gameId) {
        if (!$gameId)
            return '';
        $game_ids = Client_Service_Recommend::getRecommendGames(array('GAMEC_RESOURCE_ID' => $gameId));
        if ($game_ids) {
            foreach ($game_ids as $key => $value) {
                $info = array();
                $info = Resource_Service_Games::getGameAllInfo(array('id' => $value['GAMEC_RECOMEND_ID']));
                if ($info['status']) {
                    $playgames_list[] = array('name' => $info['name'], 'imgUrl' => $info['img'], 'href' => Util_Statist::getGameDetailUrl($info['id'], 'gamedetail'.$gameId.'_like'));
                }
            }
        }
        $this->assign('playgames', $playgames_list ? json_encode($playgames_list) : '[]');
    }

    private function shareSendAtIcket($id, $uuid, $clientVersion) {
        // 1.5.5分享的赠送
        if (strnatcmp($clientVersion, '1.5.5') >= 0) {
            if ($uuid) {
                $uuid = Common::encrypt(rawurldecode($uuid), 'DECODE');
                $configArr = array('uuid' => $uuid,
                    'content_type' => Util_Activity_Context::CONTENT_TYPE_SHARE_GAME,
                    'game_id' => $id,
                    'type' => Util_Activity_Context::TASK_TYPE_DAILY_TASK,
                    'task_id' => Util_Activity_Context::DAILY_TASK_SHARE_TASK_ID);
                $shareObject = new Util_Activity_Context(new Util_Activity_Share($configArr));
                $shareObject->sendTictket();
            }
        }
    }

    private function fillActivities($gameId) {
        $query['game_id'] = $gameId;
        $query['start_time'] = array('<=', Util_TimeConvert::floor(time(), Util_TimeConvert::RADIX_HOUR));
        $query['end_time'] = array('>=', Util_TimeConvert::floor(strtotime('-7 day'), Util_TimeConvert::RADIX_HOUR));
        $query['status'] = 1;
        $orderBy = array('sort' => 'DESC', 'start_time' => 'DESC', 'id' => 'DESC');
        $page = 1;
        $limit = 3;
        list($count, $activities) = Client_Service_Hd::getList($page, $limit, $query, $orderBy);
        if ($count == 0) {
            return;
        }
        $activityArr = array();
        $format = 'Y年n月j';
        foreach ($activities as $key => $value) {
            $activityArr[] = array(
                'name' => html_entity_decode($value['title'], ENT_QUOTES),
                'date' => date($format, $value['start_time']) . '-' . date($format, $value['end_time']),
                'rewardRule' => $value['award'] ? html_entity_decode($value['award'], ENT_QUOTES) : '',
                'href' => '/activity/detail?id=' . $value['id'] . '&intersrc=gamedetail'.$gameId.'_eventdetail'.$value['id'].'&t_bi=' . $this->getSource(),
            );
        }
        return $activityArr ? json_encode($activityArr) : json_encode(array());
    }

}

?>