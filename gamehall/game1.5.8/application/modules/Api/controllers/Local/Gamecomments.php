<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class Local_GamecommentsController extends Api_BaseController {
    public $perpage = 10;

    private $mPage;
    private $mScore;
    private $mComment;
    private $mGameId;
    private $mImei;
    private $mUUID;
    private $mUserName;
    private $mClient;
    private $mTerminalModel;
    private $mSdkVersion;
    private $mAndroidVersion;
    private $mSpInfo;
    private $mClientId;

    private $mScoreSummary = array();
    private $mCommentsData = array();

    public function getPageDataAction() {
        $this->extractInputElement();

        if(!$this->mPage) {
            $this->mPage = 1;
        }

        if (1 == $this->mPage) {
            $this->getScoreSummary();
        }

        $this->getItemList();

        $this->localOutput(0, '', $this->mCommentsData);
    }

    public function commitStarAction() {
        $this->extractInputElement();

        if ($this->mScore < 0) {
            $this->localOutput(-1, 'no star input');
        }
        
        if ($this->mImei && $this->mImei == Client_Service_Comment::ILLEGAL_IMEI) {
            $this->localOutput(-1, 'illegal imei');
        }

        $oldScoreLog = $this->getScoreByCurrentUser();

        $trans = Common_Service_Base::beginTransaction();

        try {
            $result = $this->updateAverageScore($oldScoreLog);
            if (!$result) {
                throw new Exception('updateAverageScore fail.', -205);
            }

            $result = $this->updatePersonalScore($oldScoreLog);
            if (!$result) {
                throw new Exception('updatePersonalScore fail.', -205);
            }

            if($trans) {
                Common_Service_Base::commit();
            }

            $this->localOutput(0, '', $this->mScoreSummary);
        } catch (Exception $e) {
            Common_Service_Base::rollBack();
            $this->localOutput(-1, 'commit transaction failed');
        }
    }

    public function commitCommentAction() {
        $this->extractInputElement();

        if(!$this->mComment) {
            $this->localOutput(-1, 'invalid comment inputed');
        }
        
        if ($this->mImei && $this->mImei == Client_Service_Comment::ILLEGAL_IMEI) {
            $this->localOutput(-1, 'illegal imei');
        }

        $result = $this->addNewComment();
        if (!$result) {
            $this->localOutput(-1, 'add new comment failed');
        }

        $this->tryToGetReward();
        $this->localOutput(0);
    }

    private function extractInputElement() {
        $this->parseInput();
        //$this->dumpInput();
        $this->checkCommonInput();
    }

    private function parseInput() {
        $onLine = false;

        $this->mPage = intval($this->getInput('page'));

        $imei = $this->getInput('imei');
        $uuid = $this->getInput('puuid');

        if ($uuid) {
            $onLine = Account_Service_User::checkOnline($uuid, $imei, 'uuid');
        }
        if ($onLine) {
            $this->mUUID= $uuid;
            $this->mUserName = $this->getInput('uname');
            $this->mImei = '';
        } else {
            $this->mUUID= '';
            $this->mUserName = '';
            $this->mImei = $imei;
        }

        $this->mComment = $this->getInput('comment');
        $this->mGameId = intval($this->getInput('gameId'));
        $this->mClientId =  $this->getInput('clientId');

        $star = intval($this->getInput('star'));
        if ($star) {
            $this->mScore = $star * 2;
        } else {
            $this->mScore = -1;
        }

        $clientPackage = $this->getInput('clientPackage');
        if($clientPackage == "com.android.amigame") {
            $this->mClient = 1;
        } else {
            $this->mClient = 2;
        }

        $this->mSpInfo = $this->getInput('sp');
        $spElement = explode("_", $this->mSpInfo);
        $this->mTerminalModel = $spElement[0];
        $this->mSdkVersion = $spElement[1];
        $this->mAndroidVersion = substr($spElement[3],7);
    }

    private function checkCommonInput() {
        if (!$this->mGameId) {
            $this->localOutput(-1, 'not found gameId');
        }
        if ((!$this->mUUID) && (!$this->mImei)) {
            $this->localOutput(-1, 'uuid and imei are empty');
        }
        if (($this->mUUID) && (!$this->mUserName)) {
            $this->localOutput(-1, 'user name must not empty when user has login');
        }
    }

    private function dumpInput() {
        echo 'mPage: ' . $this->mPage . '<br/>';
        echo 'mImei: ' . $this->mImei . '<br/>';
        echo 'mUUID: ' . $this->mUUID. '<br/>';
        echo 'mUserName: ' . $this->mUserName. '<br/>';
        echo 'mComment: ' . $this->mComment. '<br/>';
        echo 'mGameId: ' . $this->mGameId. '<br/>';
        echo 'mScore: ' . $this->mScore. '<br/>';
        echo 'mClient: ' . $this->mClient. '<br/>';
        echo 'mSpInfo: ' . $this->mSpInfo. '<br/>';
        echo 'mTerminalModel: ' . $this->mTerminalModel. '<br/>';
        echo 'mSdkVersion: ' . $this->mSdkVersion. '<br/>';
        echo 'mAndroidVersion: ' . $this->mAndroidVersion. '<br/>';
    }

    private function tryToGetReward() {
        if (!$this->mUUID) {
            return;
        }
        $isPass = Common::verifyClientEncryptData($this->mUUID, $this->mUserName, $this->mClientId);
        if(!$isPass){
            return false;
        }
        $configArr = array('uuid'=>$this->mUUID, 
                           'game_id'=>$this->mGameId,
                           'type'=>Util_Activity_Context::TASK_TYPE_DAILY_TASK,
                           'task_id'=>Util_Activity_Context::DAILY_TASK_COMMENT_TASK_ID,  
                           );
        $activity = new Util_Activity_Context(new Util_Activity_Comment($configArr));
        $activity->sendTictket();
    }

    private function addNewComment() {
        $nickName = $this->getNickName();
        $inBlackList = $this->isInBlackList();

        if($this->mUUID) {
            $userType = 1;
        } else {
            $userType = 2;
        }

        $item = array(
                'id'=>'',
                'title'=>$this->mComment,
                'badwords'=>'',
                'uuid'=>($this->mUUID ? $this->mUUID : ''),
                'uname'=>($this->mUserName ? $this->mUserName : ''),
                'nickname'=>$nickName,
                'imei'=>($this->mImei ? $this->mImei : ''),
                'imcrc'=>($this->mImei ? crc32($this->mImei) : ''),
                'game_id'=>$this->mGameId,
                'create_time'=>Common::getTime(),
                'check_time'=>'',
                'is_sensitive'=>0,
                'is_filter'=>0,
                'model'=>$this->mTerminalModel,
                'version'=>$this->mSdkVersion,
                'sys_version'=>$this->mAndroidVersion,
                'is_top'=>'',
                'top_time'=>'',
                'utype'=>$userType,
                'status'=>Client_Service_Comment::STATE_WAIT_FOR_REVIEW,
                'is_del'=>0,
                'is_blacklist'=>$inBlackList,
                'client_pkg'=>$this->mClient
                );

       return Client_Service_Comment::add($item);
    }

    private function getScoreSummary() {
        $stars = 0;
        $commenterCount = 0;
        $scoreItem = Resource_Service_Score::getByScore(array('game_id'=>$this->mGameId));

        if ($scoreItem) {
            $stars = $scoreItem['score']/2;
            $commenterCount = $scoreItem['number'];
        }

        $scoreLog = $this->getScoreByCurrentUser();
        $myStar = $scoreLog['score']/2;

        $this->mScoreSummary['stars'] = $stars;
        $this->mScoreSummary['commenterCount'] = $commenterCount;
        $this->mScoreSummary['myStar'] = $myStar;

        $this->mCommentsData['scoreSummary'] = $this->mScoreSummary;
    }

    private function getTopComment() {
        $topSearch = array();
        $topSearch['game_id'] = $this->mGameId;
        $topSearch['is_del'] = 0;
        $topSearch['status'] = Client_Service_Comment::STATE_REVIEW_PASS;
        $topSearch['is_top'] = 1;
        //top_time is end time in top period;
        $topSearch['top_time'] = array('>=', Common::getTime());

        $sortBy = array('create_time'=>'DESC');

        return Client_Service_Comment::getByComment($topSearch, $sortBy);
    }

    private function getComments() {
        $result = array();

        $search[Client_Dao_Comment::SQL_OPERATE_KEY] = 'AND';
        $search['game_id'] = $this->mGameId;
        $search['is_del'] = 0;
        $subSearch[Client_Dao_Comment::SQL_OPERATE_KEY] = 'OR';
        $subSearch['status'] = Client_Service_Comment::STATE_REVIEW_PASS;
        if($this->mUUID) {
            $subSearch['uuid'] = $this->mUUID;
        } else if($this->mImei) {
            $imeicrc = crc32($this->mImei);
            $subSearch['imcrc'] = $imeicrc;
        }
        $search[] = $subSearch;

        $sortBy = array();
        $topComment = $this->getTopComment();
        if ($topComment) {
            $sortBy['is_top'] = 'DESC';
        }
        $sortBy['create_time'] = 'DESC';

        return Client_Service_Comment::getListExt(
                                                $this->mPage, $this->perpage,
                                                $search, $sortBy);

    }

    private function fillAvatar($commentList) {
        $uuidList = array();
        foreach($commentList as $key => $value) {
            if ($value['uuid']) {
                $uuidList[] = $value['uuid'];
            }
        }

        if (empty($uuidList)) {
            return $commentList;
        }

        $search = array();
        $search['uuid'] = array('IN', $uuidList);

        list($total, $userInfoList) = Account_Service_User::getUserInfoList(1, $this->perpage, $search);
        $userInfoList = Common::resetKey($userInfoList, 'uuid');

        $count = count($commentList);

        $attachPath = Common::getAttachPath();
        for($i = 0; $i < $count; $i++) {
            $uuidOfCommenter = $commentList[$i]['uuid'];
            if ($userInfoList[$uuidOfCommenter]) {
                $avatarUrl = $attachPath . $userInfoList[$uuidOfCommenter]['avatar'];
                $commentList[$i]['avatar'] = $avatarUrl;

                if($commentList[$i]['utype'] == 1) {
                    $commentList[$i]['nickname']  = html_entity_decode(
                            $userInfoList[$uuidOfCommenter]['nickname'], ENT_QUOTES);
                }
            }
        }


        return $commentList;
    }

    private function constructOutputList($comments) {
        $result = array();
        foreach($comments as $key=>$comment) {

            if (!$comment) {
                continue;
            }

            $item['id'] = $comment['id'];
            $item['account'] = $comment['uname'];
            $item['imei'] = $comment['imei'];
            $item['avatarUrl'] = $comment['avatar'] ? $comment['avatar'] : '';
            $item['nickName'] = $comment['nickname'];
            if($comment['title']) {
                $item['comment'] = $this->_getfilterData($comment['badwords'], $comment['title']);
            } else {
                $item['comment'] = '';
            }
            $item['time'] = date("Y-m-d", $comment['create_time']);

            $result[] = $item;
        }

        return $result;
    }

    private function getItemList() {

        list($total, $commentList) = $this->getComments();

        if (!empty($commentList)) {
            $commentList = $this->fillAvatar($commentList);
            $commentList = $this->constructOutputList($commentList);
        }

        $countLoaded = $this->mPage * $this->perpage;
        $hasNext = ($countLoaded >= $total) ? false : true;

        $this->mCommentsData['list'] = $commentList;
        $this->mCommentsData['hasnext'] = $hasNext;
        $this->mCommentsData['curpage'] = $this->mPage;
        $this->mCommentsData['totalCount'] = $total;
    }

    private function getScoreByCurrentUser() {
        $search = array();
        $search['game_id'] = $this->mGameId;
        if($this->mUUID) {
            $search['uuid'] = $this->mUUID;
        } else {
            $search['imei'] = $this->mImei;
        }

        return Resource_Service_Score::getByLog($search);
    }

    private function updateAverageScore($oldScoreLog) {
        $totalScore = $this->mScore;
        $commenterCount = 1;
        $average = $this->mScore;
        $result = false;

        $scoreItem = Resource_Service_Score::getByScore(array('game_id'=>$this->mGameId));

        if ($scoreItem) {
            $isUpdate = $oldScoreLog ? 1:0;
            if ($isUpdate) {
                $totalScore += $scoreItem['total'] - $oldScoreLog['score'];
                $commenterCount = $scoreItem['number'];
            } else {
                $totalScore += $scoreItem['total'];
                $commenterCount += $scoreItem['number'];
            }
            $average = Resource_Service_Score::avgScore($totalScore, $commenterCount);

            $item = array(
                        'score'=>$average,
                        'total' => $totalScore,
                        'number'=>$commenterCount,
                        'update_time'=>Common::getTime());
            $whichGame = array('game_id'=>$this->mGameId);
            $result = Resource_Service_Score::updateGameScore($item, $whichGame);
        } else {
            $item = array(
                        'id'=>'',
                        'game_id'=>$this->mGameId,
                        'score'=>$average,
                        'total' => $totalScore,
                        'number'=>$commenterCount,
                        'update_time'=>Common::getTime());
            $result = Resource_Service_Score::add($item);
        }

        if ($result) {
            $this->mScoreSummary['stars'] = $average/2;
            $this->mScoreSummary['commenterCount'] = $commenterCount;
        }
        return $result;
    }

    private function updatePersonalScore($oldScoreLog) {

        if(!$oldScoreLog) {
            $nickName = $this->getNickName();
            $scoreLog = array(
                        'id'=>'',
                        'game_id'=>$this->mGameId,
                        'score'=>$this->mScore,
                        'user'=>$this->mUserName,
                        'uuid'=>$this->mUUID,
                        'imei'=>$this->mImei,
                        'nickname'=>$nickName,
                        'model'=>$this->mTerminalModel,
                        'stype'=>$this->mClient,
                        'version'=>$this->mSdkVersion,
                        'android'=>$this->mAndroidVersion,
                        'sp'=>$this->mSpInfo,
                        'create_time'=>Common::getTime(),
                    );
            return Resource_Service_Score::addLog($scoreLog);
        } else {
            $search = array();
            $search['game_id'] = $this->mGameId;
            if($this->mUUID) {
                $search['uuid'] = $this->mUUID;
            } else {
                $search['imei'] = $this->mImei;
            }
            return Resource_Service_Score::updateGameScoreLog(array('score'=>$this->mScore), $search);
        }
    }

    private function getNickName() {
        $nickName = '';

        if($this->mUUID) {
            $userInfo = Account_Service_User::getUserInfo(array('uuid'=>$this->mUUID));
            $nickName = $userInfo['nickname'];
            if($userInfo['nickname'] == $this->mUserName) {
                //如果昵称是手机号要打*替换
                $nickName = substr_replace($userInfo['nickname'], '*****', 3, 5);
            }
        } else {
            $nickName = $this->mTerminalModel.'用户';
        }

        return $nickName;
    }

    private function isInBlackList() {
        if($this->mUserName) {
            $blackListQuery = array('name'=>$this->mUserName, 'status'=>1);
        } else {
            $imeiCrc = crc32($this->mImei);
            $blackListQuery = array('imcrc'=>$imeiCrc, 'status'=>1);
        }
        $blackInfo = Client_Service_Blacklist::getByBlacklist($blackListQuery);

        return $blackInfo ? 1 : 0;
    }

    /**
     * 敏感词用*替换
     * @param unknown_type $badWords
     * @param unknown_type $title
     * @return string
     */
    private function _getfilterData($badWords, $title) {
        if($badWords) {
            $badWords = explode(',', $badWords);
            $badWords = $this->mySort($badWords);
            $title = Common::filter($badWords, $title, 2);
        }
        return html_entity_decode(preg_replace("/&#39/", "'", $title));
    }

    /**
     * 字符串按长度由大到小排序
     * @return array
     */
    public function mySort($badWords) {
        $temp = array();
        for($i = 1; $i < count($badWords); $i++){
            for($j = count($badWords) - 1; $j >= $i; $j--){
                if(strlen($badWords[$j]) > strlen($badWords[$j - 1])) {
                    $temp = $badWords[$j - 1];
                    $badWords[$j - 1] = $badWords[$j];
                    $badWords[$j] = $temp;
                }
            }
         }

        return $badWords;
    }
}
