<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

class CpController extends User_BaseController {

    public $pageSize = 20;

    public $actions = array(
        'monthUrl' => '/user/cp/month',
        'indexUrl' => '/user/cp/index',
    );

    public $confirmStatus = array(
        '0' => '确认',
        '1' => '已确认',
    );

    public $modelTypes = array(
        '1' => 'CPC',
        '2' => 'CPA',
        '3' => 'CPS',
        '4' => 'CPT'
    );

    //日点击报表
    public function indexAction() {
        $res = $this->_checkLogin();
        if (!$res) {
            Common::redirect('/User/Cp/Login');
        }
        $postData = $this->getInput(array('page', 'date', 'bid'));
        !$postData['date'] && $postData['date'] = date('m/d/Y', time());
        $page         = max($postData['page'], 1);
        $bids         = $where = $uids = array();
        $userInfo     = $this->userInfo;
        $where['pid'] = $userInfo['id'];
        if (intval($postData['bid'])) $bids[] = $postData['bid'];
        if (!empty($bids)) {
            $where['bid'] = array("IN", $bids);
        }
        list($total, $data) = Gionee_Service_ParterUrl::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
        foreach ($data as $m => $n) {
            $bussiness                 = Gionee_Service_Business::get($n['bid']);
            $data[$m]['business_name'] = $bussiness['name'];
            $uids[]                    = $n['id'];
        }
        $pvs = array();
        if (!empty($uids)) {
            $pvs = $this->_getPvData(1, $uids, $postData['date'], $userId = 0);
        }
        $businessList = Gionee_Service_Business::getsBy(array('parter_id' => $this->userInfo['id']), array('id' => 'DESC'));
        $this->assign('list', $businessList);
        $this->assign('data', $data);
        $this->assign('pvs', $pvs);
        $this->assign('params', $postData);
        $this->assign('total', $total);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['indexUrl'] . "?" . html_entity_decode($postData) . "&"));
    }

    //月对账表
    public function monthAction() {
        $res = $this->_checkLogin();
        if (!$res) {
            Common::redirect('/User/Cp/Login');
        }
        $postData = $this->getInput(array('page', 'date', 'bid'));
        !$postData['date'] && $postData['date'] = Date('m/d/Y', strtotime('-1 month'));
        $arr           = explode('/', $postData['date']);
        $date          = $arr[2] . '-' . $arr[0];
        $page          = max($postData['page'], 1);
        $where         = array();
        $where['date'] = $date;
        if (intval($postData['bid'])) {
            $where['bid'] = $postData['bid'];
        }
        $userInfo     = $this->userInfo;
        $where['pid'] = $userInfo['id'];
        //$where['check_status'] = 1;//已审核的才显示
        list($total, $dataList) = Gionee_Service_Receipt::getList($page, $this->pageSize, $where, array('id' => 'DESC'));
        $unconfrimNum = 0;
        foreach ($dataList as $k => $v) {
            $bus                            = Gionee_Service_Business::get($v['bid']);
            $dataList[$k]['bussiness_name'] = $bus['name'];
            if ($v['check_status'] == 0) {
                $unconfrimNum++;
            }
        }
        $businessList = Gionee_Service_Business::getsBy(array('parter_id' => $this->userInfo['id']), array('id' => 'DESC'));
        $this->assign('list', $businessList);
        $this->assign('dataList', $dataList);
        $this->assign('total', $total);
        $this->assign('params', $postData);
        $this->assign('confirmStatus', $this->confirmStatus);
        $this->assign('date', $date);
        $this->assign('userInfo', $this->userInfo);
        $this->assign('modelTypes', $this->modelTypes);
        $this->assign('unconfirmNum', $unconfrimNum);
        $this->assign('pager', Common::getPages($total, $page, $this->pageSize, $this->actions['monthUrl'] . "?" . html_entity_decode($postData) . "&"));
    }


    public function ajaxConfirmAction() {
        $id = $this->getInput('id');
        if (!intval($id)) $this->output('-1', '参数有错！');
        $ret = Gionee_Service_Receipt::edit(array('confirm_status' => 1), $id);
        if ($ret) {
            $this->output('0', '修改成功');
        } else {
            $this->output('-1', '修改失败！');
        }
    }

    public function ajaxAllconfirmAction() {
        $pid = $this->getInput('pid');
        if (!intval($pid)) $this->output('-1', '参数有错!');
        $ret = Gionee_Service_Receipt::editBy(array('confirm_status' => 1), array('pid'            => $pid,
                                                                                  'confirm_status' => 0,
                                                                                  'check_status'   => 1
        ));
        if ($ret) {
            $this->output('0', '一键确认成功');
        } else {
            $this->output('-1', '确认失败！');
        }
    }

    public function loginPostAction() {
        $postData = $this->getInput(array('account', 'password', 'remember_me'));
        if (!$postData['account'] || !$postData['password']) $this->output('-1', '账号或密码不能为空！');
        $pwd = $this->_RSADecode($postData['password']);
        $res = Gionee_Service_Parter::login($postData['account'], $pwd);
        if ($res['code'] == -1) $this->output('-1', $res['msg']);
        if ($postData['remember_me']) {
            Util_Cookie::set("USER_PASSWORD", $pwd, true, Common::getTime() + (5 * 3600), '/');
        }
        $redirectUrl = '/User/Cp/Index';
        $this->output('0', '登陆成功', array('redirectUrl' => $redirectUrl));
    }

    public function loginAction() {
        $ret = $this->_checkLogin();
        if ($ret) {
            Common::redirect('/User/Cp/Index');
        }
        $pwd = Util_Cookie::get('USER_PASSWORD');
        $this->assign('password', $pwd);
    }

    public function logoutAction() {
        Gionee_Service_Parter::logout(2);
        Common::redirect('/User/Cp/Login');
    }

    //检测登陆
    private function _checkLogin() {
        $this->userInfo = Gionee_Service_Parter::isLogin();
        if (empty($this->userInfo)) {
            return false;
        }
        return true;
    }

    /**
     * RSA 解密
     */

    private function _RSADecode($crypttext, $fromjs = true) {
        $dataPath  = Common::getConfig('siteConfig', 'dataPath');
        $fileName  = $dataPath . 'private_key.pem';
        $crypttext = base64_encode(pack("H*", $crypttext));
        return $this->_privateKey_decode($crypttext, $fileName, $fromjs);
    }

    private function _privateKey_decode($crypttext, $fileName, $fromjs = false) {
        $key_content = file_get_contents($fileName);
        $prikeyid    = openssl_get_privatekey($key_content);
        $crypttext   = base64_decode($crypttext);
        $padding     = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
        if (openssl_private_decrypt($crypttext, $sourcestr, $prikeyid, $padding)) {
            return $fromjs ? rtrim(strrev($sourcestr), "\0") : "" . $sourcestr;
        }
        return;
    }

    /**
     * 获取PV值
     *
     * @param unknown $type
     * @param unknown $ids
     * @param unknown $date
     */
    private function _getPvData($type, $ids, $date, $userId = 0) {
        $pvs  = array();
        $rkey = "USER:CP_DAY:{$userId}:{$date}";
        $nids = Common::getCache()->get($rkey);
        if (empty($nids)) {
            $nids = Gionee_Service_Log::getDataIdsByType($ids, 0);
            if (!empty($nids)) {
                $params['type']  = 'nav';
                $params['sdate'] = $params['edate'] = strtotime($date);
                foreach ($nids as $key => $val) {
                    $params['key'] = array('IN', array_values($val));
                    $logData       = Gionee_Service_Log::getSumByChannel($params, 'nav', array('date'), array('date'));
                    $totalPv       = 0;
                    foreach ($logData as $m => $n) {
                        $totalPv += $n['total'];
                    }
                    $pvs[$key] = $totalPv;
                }
            }
            Common::getCache()->set($rkey, $nids, 3600);
        }
        return $pvs;
    }
}