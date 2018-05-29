<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CommentController extends Api_BaseController {

    public $perpage = 10;

    /**
     * 获取某用户评论
     */
    public function indexAction() {
        $page = intval($this->getInput('page'));
        if ($page < 1) $page = 1;
        $imei = $this->getInput('imei');
        $uname = $this->getInput('uname');
        $game_id = $this->getInput('gameId');
        $isLogin = strtolower($this->getInput('isLogin'));
        if(!$game_id) $this->clientOutput(array());

        if($imei) $imeicrc = crc32($imei);
        $allItems = $params = $search = array();

        $params['game_id'] = $game_id;
        $params['is_del'] = 0;
        $search['game_id'] = $game_id;
        if($isLogin == 'true') {
            $params['uname'] = $uname;
            $search['user'] = $uname;
        } else {
            $params['imcrc'] = $imeicrc;
            $search['imei'] = $imei;
        }

        //当前用户的第一条
        $currUserNewComment = Client_Service_Comment::getByComment($params, array('is_top'=>'DESC','create_time'=>'DESC'));

        $isCurrentUserTop = ($currUserNewComment && $currUserNewComment['is_top'] == 1) ? true : false;
        if(!$isCurrentUserTop){
            //其他用户置顶的评论
            $params = array();
            $params['game_id'] = $game_id;
            $params['is_del'] = 0;
            $params['status'] = Client_Service_Comment::STATE_REVIEW_PASS;
            $params['is_top'] = 1;
            $params['top_time'] = array('>=', Common::getTime());
            $otherUserTopComment = Client_Service_Comment::getByComment($params, array('is_top'=>'DESC','create_time'=>'DESC'));
        }

        if($page <= 1) {
            if($currUserNewComment){
                $score = Resource_Service_Score::getByLog($search);
                $allItems[] =  array(
                    'id'=>$currUserNewComment['id'],
                    'account'=>$isLogin == 'true' ? $currUserNewComment['uname'] : '',
                    'imei'=>$isLogin == 'true' ? '' : $currUserNewComment['imei'],
                    'nickName'=>html_entity_decode($currUserNewComment['nickname'], ENT_QUOTES),
                    'comment'=> $currUserNewComment['title'] ? $this->_getfilterData($currUserNewComment['badwords'], $currUserNewComment['title']) : '',
                    'time'=>$currUserNewComment['title'] ? date("Y-m-d",$currUserNewComment['create_time']) : '',
                    'score'=> $score ? Resource_Service_Score::avgScore($score['score'], 1)/2 : 0,//转换星星数量
                );
            }

            if($otherUserTopComment){
                $otherParams = array();
                $otherParams['game_id'] = $game_id;
                if($otherUserTopComment['uname']){
                    $otherParams['user'] =  $otherUserTopComment['uname'];
                } else if($otherUserTopComment['imei']){
                    $otherParams['imei'] =  $otherUserTopComment['imei'];
                }

                $otherScore = Resource_Service_Score::getByLog($otherParams);
                $allItems[] =  array(
                    'id'=>$otherUserTopComment['id'],
                    'account'=>$isLogin == 'true' ? $otherUserTopComment['uname'] : '',
                    'imei'=>$isLogin == 'true' ? '' : $otherUserTopComment['imei'],
                    'nickName'=>html_entity_decode($otherUserTopComment['nickname'], ENT_QUOTES),
                    'comment'=> $otherUserTopComment['title'],
                    'time'=>$otherUserTopComment['title'] ? date("Y-m-d",$otherUserTopComment['create_time']) : '',
                    'score'=> $otherScore ? Resource_Service_Score::avgScore($otherScore['score'], 1)/2 : 0,//转换星星数量
                );
            }
        }

        $params = array();
        $params['game_id'] = $game_id;
        $params['is_del'] = 0;
        $params['status'] = Client_Service_Comment::STATE_REVIEW_PASS;

        $search['uname'] = $search['user'];
        unset($search['user']);
        $currUserComments = Client_Service_Comment::getsByComment($search);
        foreach($currUserComments as $k=>$v){
            $currTmp[] = $v['id'];
        }

        if($currTmp) {
            $params['ids'] = $currTmp;
        }

        list($total,$comments) = Client_Service_Comment::getSearchList($page, $this->perpage, $params, array('create_time'=>'DESC'));

        if($comments){
            foreach($comments as $key=>$value){
                $params = $score = array();
                $params['game_id'] = $game_id;
                if($value['uname']){
                    $params['user'] =  $value['uname'];
                } else if($value['imei']){
                    $params['imei'] =  $value['imei'];
                }

                $score = Resource_Service_Score::getByLog($params);

                //有置顶的话，在有效期内显示
                if($value['id'] != $otherUserTopComment['id'] && $value['id'] != $currUserNewComment['id']){
                    $allItems[] = array(
                        'id'=>$value['id'],
                        'account'=>$value['uname'],
                        'imei'=>$value['imei'],
                        'nickName'=>html_entity_decode($value['nickname'], ENT_QUOTES),
                        'comment'=>$value['title'] ? $this->_getfilterData($value['badwords'],$value['title']) : '',
                        'time'=>date("Y-m-d",$value['create_time']),
                        'score'=>$score ? Resource_Service_Score::avgScore($score['score'], 1)/2 : 0,//转换星星数量
                    );
                }
            }
        }

        $hasnext = (ceil((int) $total / $this->perpage) - ($page)) > 0 ? true : false;
        $data = array('list'=>$allItems, 'hasnext'=>$hasnext, 'curpage'=>$page,'total'=>$total);

        $this->localOutput('','',$data);
    }

    /**
     * 添加评论
     */
    public function addCommentAction() {
        $sp = $this->getInput('sp');
        $imei = $this->getInput('imei');
        $uname = $this->getInput('uname');
        $title = $this->getInput('comment');
        $star = $this->getInput('star');
        $game_id = $this->getInput('gameId');
        $isLogin = strtolower($this->getInput('isLogin'));
        $client_pkg = $this->getInput('client_pkg');
        if($imei) $imeicrc = crc32($imei);

        $arr_sp = explode("_", $sp);
        //获取机型
        $mode = $arr_sp[0];
        //获取sdk版本
        $version = $arr_sp[1];
        //获取android版本
        $sys_version = substr($arr_sp[3],7);

        if(!$game_id) $this->clientOutput(array());
        if(!$star) $this->clientOutput(array());
        $score = $star * 2;

        if($uname && $isLogin == 'true') {
            $userInfo = Account_Service_User::getUserInfo(array('uname'=>$uname));
            $blackinfo = Client_Service_Blacklist::getByBlacklist(array('name'=>$uname,'status'=>1));
            $nickname = $userInfo['nickname'];
            if($userInfo['nickname'] == $uname){ //如果昵称是手机号要打*替换
                $nickname = substr_replace($userInfo['nickname'],'*****',3,5);
            }
            $utype = 1;
        } else {
            $blackinfo = Client_Service_Blacklist::getByBlacklist(array('imcrc'=>$imeicrc,'status'=>1));
            $nickname = $mode.'用户';
            $utype = 2;
        }
        $is_blacklist = ($blackinfo ? 1 : 0);
        if($client_pkg == "com.android.amigame") {
            $client_pkg = 1;
        } else {
            $client_pkg = 2;
        }

        $info = array();
        if($title) {
            //评论信息
            $info = array(
                'id'=>'',
                'title'=>$title,
                'badwords'=>'',
                'uname'=>$isLogin == 'true' ? $uname : '',
                'uuid'=>$userInfo['uuid'],
                'nickname'=>$nickname,
                'imei'=>$isLogin == 'true' ? '' : $imei,
                'imcrc'=>$isLogin == 'true' ? '' : $imeicrc,
                'game_id'=>$game_id,
                'create_time'=>Common::getTime(),
                'check_time'=>'',
                'is_sensitive'=>0,
                'is_filter'=>0,
                'model'=>$mode,
                'version'=>$version,
                'sys_version'=>$sys_version,
                'is_top'=>'',
                'top_time'=>'',
                'utype'=>$utype,
                'status'=>1,
                'is_del'=>0,
                'is_blacklist'=>$is_blacklist,
                'client_pkg'=>$client_pkg,
            );
        }
        //评分日志
        $scorelog = array(
            'id'=>'',
            'game_id'=>$game_id,
            'score'=>$score,
            'user'=>$isLogin == 'true' ? $uname : '',
            'uuid'=>$userInfo['uuid'],
            'imei'=>$isLogin == 'true' ? '' : $imei,
            'nickname'=>$nickname,
            'model'=>$mode,
            'stype'=>$client_pkg,
            'version'=>$version,
            'android'=>$sys_version,
            'sp'=>$sp,
            'create_time'=>Common::getTime(),
        );
        $ret = Client_Service_Comment::addComment($info, $score, $game_id, $uname, $imei ,$imeicrc, $isLogin, $scorelog);
        if(!$ret) $this->clientOutput(array());
        //刷新游戏评分附加属性
        $this->refreshCache($game_id);
        $this->localOutput('','',true);
    }

    /**
     * 敏感词用*替换
     * @param unknown_type $badwords
     * @param unknown_type $title
     * @return string
     */
    private function _getfilterData($badwords,$title) {
        if($badwords) {
            $badwords = explode(',',$badwords);
            $badwords = $this->mySort($badwords);
            $title = Common::filter($badwords, $title, 2);
        }
        return html_entity_decode(preg_replace("/&#39/", "'", $title));
    }

    /**
     * 字符串按长度由大到小排序
     * @return array
     */
    public function mySort($badwords) {
        $temp = array();
        for($i=1;$i<count($badwords);$i++){
            for($j=count($badwords)-1;$j>=$i;$j--){
                if(strlen($badwords[$j]) > strlen($badwords[$j-1])) {
                    $temp = $badwords[$j-1];
                    $badwords[$j-1] = $badwords[$j];
                    $badwords[$j] = $temp;
                }
            }
        }
        return $badwords;
    }

    private function refreshCache($gameId){
        Resource_Service_GameExtraCache::refreshGameScore($gameId);
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updteListItem', $gameId);
    }
}