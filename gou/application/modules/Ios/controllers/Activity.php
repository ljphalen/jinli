<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class ActivityController extends Ios_BaseController {


    /**
     * 推荐抽奖
     */
    public function recommendAction() {
        $uid = Common::getIosUid();
        $phone = '';

        if($uid){
            $record = Activity_Service_Recommend::getBy(array('uid'=>$uid));
            if($record) $phone = $record['phone'];
        }

        $this->assign('phone', $phone);
        $this->assign('recommendUrl', '/api/ios_activity/recommend');
        $this->assign('title', '推荐有理');
    }

}