<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author Terry
 *
 */
class ScoreController extends Apk_BaseController {

    private $perpage = 10;

    private $rankNum = 10;

    public $actions = array(
        'flowUrl'       =>'/ios/score/flow',
        'summaryUrl'    =>'/ios/score/summary',
        'editUserUrl'   => '/ios/user_uid/edit',
        'modifyUserUrl' => '/ios/user_uid/modify',
        'ruleUrl'       => '/ios/score/rule',
        'rankUrl'       => '/ios/score/rank',
        'taskUrl'       => '/ios/score/task',
    );


    /**
     * 积分排行榜
     */
    public function rankAction(){
        list($uid, $userUID) = User_Service_Uid::getUserInfo('ios');
        if(!$uid) $this->output('-1', '非法请求.');

        $my_sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));

        $need_edit = false;
        if(empty($userUID['nickname'])||empty($userUID['mobile'])) $need_edit = true;

        $rank = $rank_temp = array();

        //获取积分前10条
        list(, $rs_top) = Gou_Service_ScoreSummary::getList(0, $this->rankNum, array(), array('sum_score'=>'DESC'));

        //设置排名
        if(count($rs_top) == 0) $this->output('-1', '暂无排名.');

        $score = 0;
        $score_pos = 1;
        $score_step = 0;
        foreach($rs_top as $item){
            if($item['sum_score'] < $score){
                $score_pos += ++$score_step;
                $score_step = 0;
            }elseif($item['sum_score'] == $score){
                $score_step++;
            }

            $item['pos'] = $item['sum_score']==0?0:$score_pos;
            $rank_temp[] = $item;
            $score = $item['sum_score'];
        }

        $rank_temp = Common::resetKey($rank_temp, 'uid');
        $rank = $rank_temp;
        $is_include = true;

        //获取当前uid的积分的前后排名记录
        if(!array_key_exists($uid, $rank_temp)){
            $is_include = false;

            if(empty($my_sum)) $my_sum = array('uid' => $uid, 'sum_score' => 0);

            list($rk, ) = Gou_Service_ScoreSummary::myRank($my_sum['sum_score']);
            $my_sum['pos'] = $rk;

            list($my_above, $my_below) = Gou_Service_ScoreSummary::myRankBoth($my_sum['sum_score'], $uid);
            $rank_temp = array_filter(array($my_above, $my_sum, $my_below));

            if($my_above && array_key_exists($my_above['uid'], $rank)){
                $rank_temp = array_merge($rank, Common::resetKey($rank_temp, 'uid'));
            }else{
                $rank[] = array();
                $rank_temp = array_merge($rank, Common::resetKey($rank_temp, 'uid'));
            }
        }

        //获取uid用户信息
        $uids = array_keys(Common::resetKey(array_filter($rank_temp), 'uid'));
        $rank = $users = array();
        if(!empty($uids)){
            $users = User_Service_Uid::getsBy(array('uid'=>array('IN', $uids)));
            $users = Common::resetKey($users, 'uid');
        }

        //格式化积分排名信息, 特别处理省略号的问题
        foreach($rank_temp as $item){
            if(empty($item)){
                $rank[] = array();
            }else {
                $rank[] = array(
                    'pos'       => $item['pos'],
                    'sum_score' => $item['sum_score'],
                    'scoreid'   => isset($users[$item['uid']]) ? $users[$item['uid']]['scoreid'] : '',
                    'nickname'  => isset($users[$item['uid']]) ? ($users[$item['uid']]['nickname'] ? $users[$item['uid']]['nickname'] : $users[$item['uid']]['scoreid']) : '',
                    'is_me'     => $uid == $item['uid'] ? true : false,
                    'uid'       => $item['uid']
                );

                if ($uid == $item['uid']) $my_key = count($rank) - 1;
            }
        }

        unset($rank_temp);

        //处理要显示的积分排名的记录数
        if($num = count($rank)-$this->rankNum){
            $my_key--;
            if(!empty($rank[--$my_key])){
                $rank[$my_key] = array();
            }
            for($i=0; $i<$num; $i++){
                if(!empty($rank[--$my_key])) unset($rank[$my_key]);
            }
        }

        if(empty($userUID['nickname'])||empty($userUID['mobile'])){
            $edit_url = $this->actions['editUserUrl'];
        }else{
            $edit_url = $this->actions['modifyUserUrl'];
        }

        $this->assign('rank', $rank);
        $this->assign('need_edit', $need_edit);
        $this->assign('edit_url', $edit_url);
        $this->assign('title', '分数排行榜');
    }

    /**
     * 个人积分流水
     */
    public function flowAction(){
        list($uid, $userUID) = User_Service_Uid::getUserInfo('ios');
        if(!$uid) $this->output('-1', '非法请求.');

        $my_sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));

        $name = $userUID['nickname']?sprintf('%s(%s)', $userUID['nickname'], $userUID['scoreid']):$userUID['scoreid'];

        $this->assign('sum_score', isset($my_sum['sum_score'])?$my_sum['sum_score']:0);
        $this->assign('name', $name);
        $this->assign('title', '分数获取记录');
    }

    /**
     * 个人积分任务
     */
    public function taskAction(){
        list($uid, $userUID) = User_Service_Uid::getUserInfo('ios');
        if(!$uid) $this->output('-1', '非法请求.');

        $need_edit = false;
        if(empty($userUID['nickname'])||empty($userUID['mobile'])) $need_edit = true;

        //获取当日个人积分任务
        $today = date('Y-m-d', Common::getTime());
        $task = Common::resetKey(Gou_Service_ScoreLog::getTasks(array('uid'=>$uid, 'date'=>$today)), 'type_id');

        $score_type = Common::resetKey(Common::getConfig('scoreConfig'), 'id');

        $my_task = array();
        $today_score = 0;

        foreach($score_type as $key => $item){
            if($item['score'] > 0){ //增加积分
                if(array_key_exists($key, $task)){
                    $my_task[$item['id']] = array(
                        'id'    => $item['id'],
                        'title' => $item['title'],
                        'subtitle'  => $item['subtitle'],
                        'score' => $item['score'],
                        'count' => $task[$key]['count'],
                        'limit' => $item['limit'],
                        'is_finish'=> $task[$key]['count'] == $item['limit']?1:0,
                    );
                }else{
                    $my_task[$item['id']] = array(
                        'id'    => $item['id'],
                        'title' => $item['title'],
                        'subtitle'  => $item['subtitle'],
                        'score' => $item['score'],
                        'count' => 0,
                        'limit' => $item['limit'],
                        'is_finish'=> 0,
                    );
                }
            }
        }

        $score_type = Gou_Service_ScoreLog::scoreType();
        foreach($task as $key=>$item){
            if(array_key_exists($key, $score_type)) $today_score += $item['count']*$score_type[$key]['score'];
        }

        $name = $userUID['nickname']?sprintf('%s(%s)', $userUID['nickname'], $userUID['scoreid']):$userUID['scoreid'];
        $my_sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));

        //更新任务列表已经被用户打开过
        Gou_Service_ScoreSummary::updateBy(array('look_task_date'=>$today), array('uid'=>$uid));

        if(empty($userUID['nickname'])||empty($userUID['mobile'])){
            $edit_url = $this->actions['editUserUrl'];
        }else{
            $edit_url = $this->actions['modifyUserUrl'];
        }

        $this->assign('sum_score', isset($my_sum['sum_score'])?$my_sum['sum_score']:0);
        $this->assign('name', $name);
        $this->assign('task', $my_task);
        $this->assign('today_score', $today_score);
        $this->assign('uid_encrypt', urlencode(Common::encrypt($uid)));
        $this->assign('edit_url', $edit_url);
        $this->assign('need_edit', $need_edit);
        $this->assign('title', '每日任务');
    }

    /**
     * 个人积分累计/成就
     */
    public function summaryAction(){
        list($uid, $userUID) = User_Service_Uid::getUserInfo('ios');

        //来之分享
        $is_from_share = false;
        if(!$uid){
            $uid = Common::encrypt($this->getInput('euid'), 'DECODE');
            $is_from_share = true;
        }

        if(!$uid) $this->output('-1', '非法请求.');

        if(!$uid) $this->output('-1', '非法请求.');

        $score_type = Common::resetKey(Common::getConfig('scoreConfig'), 'id');
        $summary = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));

        $my_sums = array();
        if($summary){
            $my_sum['sum_score'] = $summary['sum_score'];

            //累计签到
            foreach($score_type[1]['upto'] as $item){
                $my_sums['sum_sign']['sum'] = $summary['sum_sign']%$item['total'];
                $my_sums['sum_sign']['per'] = round($summary['sum_sign']%$item['total']/$item['total']*100) . '%';
                $my_sums['sum_sign']['tip'] = sprintf('累计签到 %s 天获 %s 分', $item['total'], $item['score']);
            }

            //累计砍价
            foreach($score_type[2]['upto'] as $item){
                $my_sums['sum_cut']['sum'] = $summary['sum_cut']%$item['total'];
                $my_sums['sum_cut']['per'] = round($summary['sum_cut']%$item['total']/$item['total']*100) . '%';
                $my_sums['sum_cut']['tip'] = sprintf('累计砍价 %s 次获 %s 分', $item['total'], $item['score']);
            }

            //累计邀请好友砍价
            foreach($score_type[4]['upto'] as $item){
                $my_sums['sum_fcut']['sum'] = $summary['sum_fcut']%$item['total'];
                $my_sums['sum_fcut']['per'] = round($summary['sum_fcut']%$item['total']/$item['total']*100) . '%';
                $my_sums['sum_fcut']['tip'] = sprintf('累计邀请好友砍价 %s 次获 %s 分', $item['total'], $item['score']);
            }

            //累计分享砍价信息
            foreach($score_type[3]['upto'] as $item){
                $my_sums['sum_scut']['sum'] = $summary['sum_scut']%$item['total'];
                $my_sums['sum_scut']['per'] = round($summary['sum_scut']%$item['total']/$item['total']*100) . '%';
                $my_sums['sum_scut']['tip'] = sprintf('累计分享砍价信息 %s 次获 %s 分', $item['total'], $item['score']);
            }

        }else{
            $my_sum['sum_score'] = 0;
            //累计签到
            $my_sums['sum_sign']['sum'] = 0;
            $my_sums['sum_sign']['per'] = '0%';
            foreach($score_type[1]['upto'] as $item){
                $my_sums['sum_sign']['tip'] = sprintf('累计签到 %s 天获 %s 分', $item['total'], $item['score']);
            }

            //累计砍价
            $my_sums['sum_cut']['sum'] = 0;
            $my_sums['sum_cut']['per'] = '0%';
            foreach($score_type[2]['upto'] as $item){
                $my_sums['sum_cut']['tip'] = sprintf('累计砍价 %s 次获 %s 分', $item['total'], $item['score']);
            }

            //累计邀请好友砍价
            $my_sums['sum_fcut']['sum'] = 0;
            $my_sums['sum_fcut']['per'] = '0%';
            foreach($score_type[4]['upto'] as $item){
                $my_sums['sum_fcut']['tip'] = sprintf('累计邀请好友砍价 %s 次获 %s 分', $item['total'], $item['score']);
            }

            //累计分享砍价信息
            $my_sums['sum_scut']['sum'] = 0;
            $my_sums['sum_scut']['per'] = '0%';
            foreach($score_type[3]['upto'] as $item){
                $my_sums['sum_scut']['tip'] = sprintf('累计分享砍价信息 %s 次获 %s 分', $item['total'], $item['score']);
            }
        }

        if($is_from_share){
            $user = User_Service_Uid::getBy(array('uid'=>$uid));
            if($user){
                $name = $user['nickname']?sprintf('%s(%s)', $user['nickname'], $user['scoreid']):$user['scoreid'];
            }
        }else{
            $name = $userUID['nickname']?sprintf('%s(%s)', $userUID['nickname'], $userUID['scoreid']):$userUID['scoreid'];
            $my_sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
        }

        $this->assign('name', $name);
        $this->assign('sum_score', isset($my_sum['sum_score'])?$my_sum['sum_score']:0);
        $this->assign('my_sums', $my_sums);
        $this->assign('euid', Common::encrypt($uid));
        $this->assign('is_from_share', $is_from_share);
        $this->assign('title', '我的累计奖励');
    }


    /**
     * 个人积分概要
     */
    public function profileAction(){
        list($uid, $userUID) = User_Service_Uid::getUserInfo('ios');
        if(!$uid) $this->output('-1', '非法请求.');

        $my_sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));

        //判断账户是编辑还是修改
        $edit_url = '';
        $edit_label = '';
        $name = $userUID['nickname']?sprintf('%s(%s)', $userUID['nickname'], $userUID['scoreid']):$userUID['scoreid'];
        if(empty($userUID['nickname'])||empty($userUID['mobile'])){
            $edit_url = $this->actions['editUserUrl'];
            $edit_label = '编辑信息';
        }else{
            $edit_url = $this->actions['modifyUserUrl'];
            $edit_label = '更新信息';
        }

        //判断当天积分任务是否需要提醒
        $task_tip = Gou_Service_ScoreSummary::scoreTaskTip($uid);

        //获取积分超过百分数
        $my_sum_score = isset($my_sum['sum_score'])?$my_sum['sum_score']:0;
        list(, $rank_percent) = Gou_Service_ScoreSummary::myRank($my_sum_score);

        list(, $score_date) = Gou_Service_ScoreLog::scoreAvailable();

        $score_sdate = date('m.d', strtotime(Gou_Service_Config::getValue('gou_score_sdate')));
        $score_edate = date('m.d', strtotime(Gou_Service_Config::getValue('gou_score_edate')));

        $this->assign('name', $name);
        $this->assign('sum_score', $my_sum_score);
        $this->assign('edit_url', $edit_url);
        $this->assign('edit_label', $edit_label);
        $this->assign('over', $rank_percent);
        $this->assign('task_tip', $task_tip);
        $this->assign('ruleUrl', $this->actions['ruleUrl']);
        $this->assign('rankUrl', $this->actions['rankUrl']);
        $this->assign('taskUrl', $this->actions['taskUrl']);
        $this->assign('score_sdate', $score_sdate);
        $this->assign('score_edate', $score_edate);
        $this->assign('available', $score_date);
    }

    /**
     * 积分规则说明
     */
    public function ruleAction(){
        $this->assign('title', '分数用来干什么');
    }
}