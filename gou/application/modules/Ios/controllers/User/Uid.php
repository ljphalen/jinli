<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * @author Terry
 *
 */
class User_UidController extends Apk_BaseController {

    private $perpage=10;

    public $actions = array(
    );


    /**
     * 编辑个人信息
     */
    public function editAction(){
        list($uid, $userUID) = User_Service_Uid::getUserInfo('ios');
        $my_sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
        list($rank, ) = Gou_Service_ScoreSummary::myRank($my_sum['sum_score']);

        $this->assign('nickname', $userUID['nickname']);
        $this->assign('mobile', $userUID['mobile']);
        $this->assign('title', '完善个人信息');
        $this->assign('rank', $rank);
    }

    /**
     * 修改个人信息
     */
    public function modifyAction(){
        list($uid, $userUID) = User_Service_Uid::getUserInfo('ios');
        $my_sum = Gou_Service_ScoreSummary::getBy(array('uid'=>$uid));
        list($rank, ) = Gou_Service_ScoreSummary::myRank($my_sum['sum_score']);

        $this->assign('nickname', $userUID['nickname']);
        $this->assign('mobile', $userUID['mobile']);
        $this->assign('title', '修改个人信息');
        $this->assign('rank', $rank);
    }

}