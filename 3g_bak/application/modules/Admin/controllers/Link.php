<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class LinkController extends Admin_BaseController {

    public $actions = array(
        'configUrl' => '/Admin/Link/config',
        'indexUrl'  => '/Admin/Link/index',
        'logUrl'    => '/Admin/Link/log',
        'deleteUrl' => '/Admin/Link/delete',
    );

    public $prizeList = array(
        '1' => '一等奖',
        '2' => '二等奖',
        '3' => '三等奖',
        '4' => '四等奖',
        '5' => '五等奖',
    );

    public $prizeStatus = array(
        '1'  => '已领取',
        '0'  => '未领取',
        '-1' => '已放弃',
        '-2' => '已过期',
    );

    public function configAction() {
        $keys     = array(
            'user_link_per_scores',
            'user_link_status',
            'user_link_sdate',
            'user_link_edate',
            'user_link_prize_ratio',
            'user_link_prize_level',
            'user_link_prize_position',
            'user_link_init_value',
            'user_link_step',
            'user_link_takepart_scores',
            'user_link_expire_minus'
        );
        $postData = $this->getInput($keys);
        if (!empty($postData['user_link_prize_level'])) {
            $postData['user_link_prize_level'] = json_encode($postData['user_link_prize_level']);
            foreach ($postData as $k => $v) {
                Gionee_Service_Config::setValue($k, $v);
            }
        }
        $data  = Gionee_Service_Config::getsBy(array('3g_key' => array("IN", $keys)));
        $goods = User_Service_Commodities::getsBy(array('event_flag' => 1), array('id' => 'DESC'));
        $ret   = array();
        foreach ($data as $m) {
            $ret[$m['3g_key']] = $m['3g_value'];
        }

        $this->assign('data', $ret);
        $this->assign('prizeLevels', json_decode($ret['user_link_prize_level'], true));
        $this->assign('prizeRatios', json_decode($ret['user_link_prize_ratio'], true));
        $this->assign('goods', $goods);
        $this->assign('prizeList', $this->prizeList);
        $this->assign('rankData', Event_Service_Link::getRankData());
    }

    public function indexAction() {
        $postData = $this->getInput(array('page', 'uname'));
        $page     = max($postData['page'], 1);
        $where    = array();
        if (!empty($postData['uname'])) {
            $where['uname'] = $postData['uname'];
        }
        list($total, $dataList) = Event_Service_Link::getUserList($page, $this->pageSize, $where, array('id' => 'DESC'));
        foreach ($dataList as $k => $v) {
            $username = '';
            if (!empty($v['uid'])) {
                $userInfo = Gionee_Service_User::getUser($v['uid']);
                $username = $userInfo['username'];
            }

            $dataList[$k]['username'] = $username;
        }
        $this->assign('dataList', $dataList);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?uname={$postData['uname']}&"));
        $this->assign('uname', $postData['uname']);
    }

    public function logAction() {
        $postData = $this->getInput(array('page', 'uname', 'prize_status','prize_level'));
        $page     = max($postData['page'], 1);
        $where    = array();
        if (!empty($postData['uname'])) {
        	$user = Gionee_Service_User::getBy(array('username'=>$postData['uname']));
        	if(!empty($user)){
        		$where['uid'] = $user['id'];
        	}
        }
        if (in_array($postData['prize_status'],array_keys($this->prizeStatus))) {
            $where['prize_status'] = $postData['prize_status'];
        }
        
        if(!empty($postData['prize_level'])){
        	$where['prize_level'] = $postData['prize_level'];
        	if($postData['prize_level'] == 7){
        		$where['prize_level'] = 0;
        	}
        }

        list($total, $dataList) = Event_Service_Link::getPrizeList($page, $this->pageSize, $where, array('id' => 'DESC'));
        foreach ($dataList as $k => $v) {
            $username = '';
            if (!empty($v['uid'])) {
                $userInfo = Gionee_Service_User::getUser($v['uid']);
                $username = $userInfo['username'];
            }

            $dataList[$k]['username'] = $username;
        }
        $this->assign('dataList', $dataList);
        unset($postData['page']);
        $requestParams = http_build_query($postData);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['logUrl'] . "?".$requestParams."&"));
        $this->assign('params', $postData);
        $prizeList    = $this->prizeList;
        $prizeList[6] = '六等奖';
        $prizeList[7]='幸运奖';
        $this->assign('prizeLevel', $prizeList);
        $this->assign('prizeStatus', $this->prizeStatus);
    }

    public function logexportAction() {
        $where = array();
        $list  = Event_Service_Link::getPrizeDao()->getsBy($where);
        $prizeList    = $this->prizeList;
        $prizeList[6] = '六等奖';
        $prizeList[0] = '幸运奖';
        foreach ($list as $k => $v) {
            $username = '';
            if (!empty($v['uid'])) {
                $userInfo = Gionee_Service_User::getUser($v['uid']);
                $username = $userInfo['username'];
            }

            $list[$k]['username'] = $username;
            $list[$k]['prize_level'] = $prizeList[$v['prize_level']];
            $list[$k]['prize_status'] = $this->prizeStatus[$v['prize_status']];
            $list[$k]['add_time'] = date('y/m/d H:i:s', $v['add_time']);
            $list[$k]['update_time'] = date('y/m/d H:i:s', $v['update_time']);
            $list[$k]['status']     = $v['status'] == 1 ? '已领取' : '未领取';
            $area                   = Vendor_IP::find($v['user_ip']);
            $list[$k]['user_ip']    = implode(',', array($area[1], $area[2]));
            $list[$k]['user_ip1']    = $v['user_ip'];
        }
        Common::export($list, '', '', "七夕活动奖励列表");
    }
}

?>