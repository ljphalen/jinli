<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Terry
 * Class Apk_ScoreController
 */
class Apk_ScoreController extends Api_BaseController {

    private $perpage=10;
    public $actions = array(
        'editUserUrl'   => '/apk/user_uid/edit',
        'modifyUserUrl' => '/apk/user_uid/modify',
        'ruleUrl'       => '/apk/score/rule',
        'rankUrl'       => '/apk/score/rank',
        'taskUrl'       => '/apk/score/task',
    );

    /**
     * 个人积分流水
     */
    public function flowAction(){
        $uid = Common::getAndroidtUid();

        if(!$uid) $this->output(-1, '非法请求.');

        $page = $this->getInput('page');
        if ($page < 1) $page = 1;

        $perpage = $this->getInput('perpage');
        if ($perpage < 1) $perpage = $this->perpage;

        $score_sdate = strtotime(Gou_Service_Config::getValue('gou_score_sdate'));
        $score_edate = strtotime(Gou_Service_Config::getValue('gou_score_edate'));

        $last_day = strtotime(date('Y-m-d', strtotime('-5 days')));
        if($last_day < $score_sdate) $last_day = $score_sdate;

        $now_day = Common::getTime();
        if($now_day > $score_edate) $now_day = $score_edate;

        list($total, $result) = Gou_Service_ScoreLog::getList($page, $perpage, array('uid'=>$uid, 'create_time'=>array(array('>=', $last_day), array('<=', $now_day))), array('create_time'=>'DESC'));

        $flow = array();
        $score_type = Gou_Service_ScoreLog::scoreType();

        foreach($result as &$item){
            if(array_key_exists($item['type_id'], $score_type)) $item['type_title'] = $score_type[$item['type_id']]['title'];
            $flow[date('m/d', $item['create_time'])]['list'][] = array('title'=>$item['type_title'], 'score'=>'+'.$item['score']);
            $flow[date('m/d', $item['create_time'])]['date'] = date('m/d', $item['create_time']);
        }

        $hasnext = (ceil((int)$total / $this->perpage) - ($page)) > 0 ? true : false;
        $this->output(0, '', array(
            'list' => $flow,
            'hasnext' => $hasnext,
            'curpage' => $page));

    }

    /**
     * 积分处理
     * @param int $id   id类型可以详细查看scoreConfig配置文件
     */
    public function scoreAction(){
        $uid = Common::getAndroidtUid();
        $id = intval($this->getInput('id'));
        $sign = $this->getInput('sign');

        //给用户增加scoreID

        if(!$uid) $this->output(-1, '', array('type_id'=>$id));
//        if($id == 1) User_Service_Uid::checkUserUid();

        $site_config = Common::getConfig('siteConfig');
        if(md5($id.$site_config['secretKey']) !== $sign) $this->output(-1, '', array('type_id'=>$id));

        $score_type = Gou_Service_ScoreLog::scoreType();

        if(Gou_Service_ScoreLog::score($id, $uid)) $this->output(0, '', array('type_id'=>$id, 'score'=>$score_type[$id]['score']));


        $this->output(-1, '', array('type_id'=>$id));

    }


    /**
     * 模拟测试
     */
//    public function testAction(){
//        $id = intval($this->getInput('id'));
//        $uid = $this->getInput('uid');
//        $now_time = $this->getInput('date');
//
//        $score_type = Gou_Service_ScoreLog::scoreType();
//
//        if(Gou_Service_ScoreLog::score($id, $uid, $now_time)) $this->output(0, '成功.', array('type_id'=>$id, 'score'=>$score_type[$id]['score']));
//
//        $this->output(-1, '参数错误.', array('type_id'=>$id));
//
//    }

}
