<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * @author Terry
 * Class Ios_activityController
 */
class Ios_ActivityController extends Api_BaseController {

    public $actions = array(
    );

    /**
     * 推荐抽奖
     */
    public function recommendAction(){
        $mobile = $this->getInput('mobile');
        if(!Common::checkMobile($mobile)) $this->output(-1, '手机号码格式不正确.');

        if(!$uid = Common::getIosUid()) $this->output(-1, '抱歉, 此次活动仅针对苹果手机用户.');

        if(Activity_Service_Recommend::getCount(array('uid'=>$uid))) $this->output(-1, '您已经推荐过.');

        $data = array(
            'phone'         => $mobile,
            'uid'           => Common::getIosUid(),
            'create_time'   => Common::getTime()
        );

        $ret = Activity_Service_Recommend::addRecommend($data);
        if (!$ret) $this->output(-1, '操作失败.');

        $this->output(0, '操作成功.');
    }

}