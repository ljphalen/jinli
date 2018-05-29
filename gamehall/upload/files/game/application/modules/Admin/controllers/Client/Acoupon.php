<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 *
 * Enter description here ...
 * @author
 *
 */
class Client_AcouponController extends Admin_BaseController {

    public $actions = array(
        'listUrl' => '/Admin/Client_Acoupon/index',
        'addUrl' => '/Admin/Client_Acoupon/add',
        'addPostUrl' => '/Admin/Client_Acoupon/add_post',
        'editUrl' => '/Admin/Client_Acoupon/edit',
        'editPostUrl' => '/Admin/Client_Acoupon/edit_post',
        'detailUrl' => '/Admin/Client_Acoupon/detail',
        'detailPostUrl' => '/Admin/Client_Acoupon/detail_post',
        'gameVoucherDetailUrl' => '/Admin/Client_Acoupon/gameVoucherDetail',
        'gameVoucherDetailPostUrl' => '/Admin/Client_Acoupon/gameVoucherDetailPost',
        'deleteUrl' => '/Admin/Client_Acoupon/delete',
        'uploadUrl' => '/Admin/Client_Acoupon/upload',
        'uploadPostUrl' => '/Admin/Client_Acoupon/upload_post',
        'uploadImgUrl' => '/Admin/Client_Acoupon/uploadImg',
        'batchUpdateUrl'=>'/Admin/Client_Acoupon/batchUpdate',
        'sendTicketUrl'=>'/Admin/Client_Acoupon/sendTicket',
        'sendTicketPostUrl'=>'/Admin/Client_Acoupon/sendTicketPost',
        'activityDetailUrl' => '/Admin/Client_Acoupon/activityDetail',
        'ticketDetailUrl' => '/Admin/Client_Acoupon/ticketDetail',
        'moneyDetailUrl'=>'/Admin/Client_Acoupon/moneyDetail',
        'getTemplateUrl'=>'/Admin/Client_Acoupon/getTemplate'
    );

    const MAX_RETURN_MONEY_RATIO = 500;        // 最大返券百分比

    public $perpage = 20;
    const CLIENT_LOGIN_ACTIVITY_TYPE = 1;
    const GAME_LOGIN_ACTIVITY_TYPE = 2;
    const CONSUME_ACTIVITY_TYPE = 3;
    const PAYMENT_ACTIVITY_TYPE = 4;

    //赠送场景
    public $htype = array(
        self::CLIENT_LOGIN_ACTIVITY_TYPE => '登录客户端',
        self::GAME_LOGIN_ACTIVITY_TYPE => '登录游戏',
        self::CONSUME_ACTIVITY_TYPE => '消费返利',
        self::PAYMENT_ACTIVITY_TYPE=>'充值返利'
    );

    public $ticketType = array(
        1 => 'A券',
        2 => '游戏券',
    );

    private $mSendType = array(
        1 =>'福利任务',
        2 =>'日常任务',
        3 =>'活动任务',
        4 =>'手动发送',
        5 =>'积分抽奖',
        6 =>'商城兑换',
        7 =>'用户赠送',
        8 =>'节日活动',
        9 =>'VIP赠送',
    );

    public function indexAction()
    {
        $page = intval($this->getInput('page'));

        if ($page < 1) $page = 1;
        $s = $this->getInput(array('title','htype', 'ticket_type', 'hd_start_time','hd_end_time'));
        $params = array();

        if ($s['title']) $params['title'] = array('LIKE',$s['title']);
        if ($s['htype']) $params['htype'] = $s['htype'];
        if ($s['ticket_type']) $params['ticket_type'] = $s['ticket_type'];
        if ($s['hd_start_time']) $params['hd_start_time'] = array('>=',strtotime($s['hd_start_time']));
        if ($s['hd_end_time']) $params['hd_end_time'] = array('<=',strtotime($s['hd_end_time']));

        list($total, $list) = Client_Service_Acoupon::getACouponActivities($page, $this->perpage, $params);
        $this->assign('s', $s);
        $this->assign('list', $list);
        $this->assign('htype', $this->htype);
        $this->assign('ticketType', $this->ticketType);
        $url = $this->actions['listUrl'].'/?' . http_build_query($s) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
        $this->assign('total', $total);
    }

    public function addAction() {
        $this->assign('htype', $this->htype);
        $this->assign('ticketType', $this->ticketType);
    }

    public function editAction() {
        $id = intval($this->getInput('id'));
        $ret = Client_Service_Acoupon::getACouponInfoActivity($id);
        $this->assign('id', $id);
        $this->assign('info', $ret);
        $this->assign('htype', $this->htype);
        $this->assign('ticketType', $this->ticketType);
    }

    /**
     * 每个活动的明细
     */
    public function activityDetailAction() {
        $page = intval($this->getInput('page'));
        $s = $this->getInput(array('task_id','uname', 'uuid', 'start_time', 'end_time'));
        if ($page < 1) $page = 1;

        $params = array();
        if ($s['uuid']) {
            $search =  array();
            $search['uuid'] = array('LIKE',$s['uuid']);
            $users = Account_Service_User::getUsers($search);
            if($users){
                foreach($users as $key=>$value){
                    $uuidIds[] = $value['uuid'];
                }
            }
        }
        if ($s['uname']) {
            $search =  array();
            $search['uname'] = array('LIKE',$s['uname']);
            $users = Account_Service_User::getUsers($search);
            if($users){
                foreach($users as $key=>$value){
                    $unameIds[] = $value['uuid'];
                }
            }
        }

        if(count($uuidIds) && count($unameIds) ){
            $params['uuid'] = array('IN',array_intersect($uuidIds, $unameIds));
        }elseif(count($uuidIds)){
            $params['uuid'] = array('IN',$uuidIds);
        }elseif(count($unameIds)){
            $params['uuid'] = array('IN',$unameIds);
        }

        if($s['start_time']){
            $params['consume_time'][0] = array('>=', strtotime($s['start_time']));
        }
        if($s['end_time']){
            $params['consume_time'][1] = array('<=', strtotime($s['end_time']));
        }
        if($s['task_id']){
            $params['sub_send_type'] = $s['task_id'];
        }
        $params['status'] = 1;
        $params['send_type'] = 3;

        list($total, $result) = Client_Service_TicketTrade::getList($page, $this->perpage, $params, array('id'=>'ASC'));
        foreach($result as $key=>$value){
            $userInfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
            $tempUserInfo[$value['uuid']]['nickname'] = $userInfo['nickname'];
            $tempUserInfo[$value['uuid']]['uname'] =  $userInfo['uname'];
        }
        $this->assign('result', $result);
        $this->assign('s', $s);
        $this->assign('userInfo', $tempUserInfo);
        $this->assign('total', $total);
        $url = $this->actions['activityDetailUrl'].'/?' . http_build_query($s) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));
    }

    /**
     *
     * Enter description here ...
     */
    public function add_postAction() {
        $info = $this->_cookData();
        $info['create_time'] = time();
        $result = Client_Service_Acoupon::addActivity($info);
        if (!$result) $this->output(-1, '操作失败');
        $this->addAsyncAcouponData($info);
        $this->output(0, '操作成功');
    }

    public function edit_postAction() {
        $id = intval($this->getInput('id'));
        $info = $this->_cookData();
        $oldData = Client_Service_Acoupon::getACouponInfoActivity($id);
        $result = Client_Service_Acoupon::editACouponActivity($id, $info);
        if (!$result) $this->output(-1, '操作失败');
        $newData = $info;
        $this->asyncAcouponData($oldData, $newData);
        $this->output(0, '操作成功');
    }

    private function runAsyncTask(){
        Async_Task::execute('Async_Task_Adapter_ExtraUpdate', 'gameRewardAcoupon');
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updateRewardAcoupon');
    }

    /**
     *
     * Enter description here ...
     */
    public function deleteAction() {
        $id = $this->getInput('id');
        $info = Client_Service_Ad::getAd($id);
        if ($info && $info['id'] == 0) $this->output(-1, '无法删除');
        Util_File::del(Common::getConfig('siteConfig', 'attachPath') . $info['img']);
        $result = Client_Service_Acoupon::delete($id);
        if (!$result) $this->output(-1, '操作失败');
        $this->output(0, '操作成功');
    }

    /**
     *
     * Enter description here ...
     */
    private function _cookData() {
        $postField = array(
            'htype', 'ticket_type', 'ticket_object', 'title', 'hd_start_time', 'hd_end_time', 'status', 'hd_object',
            'condition_type', 'condition_value', 'rule_type', 'game_version', 'game_object',
            'denomination','deadline','percent','backAmount','ACoupon','subject_id','restoration',
            'hd_object_addition_info', 'game_object_addition_info'
        );
        $info = $this->getPost($postField);
        if(!$info['title']) $this->output(-1, '标题不能为空.');
        if(!$info['hd_start_time']) $this->output(-1, '开始时间不能为空.');
        if(!$info['hd_end_time']) $this->output(-1, '结束时间不能为空.');

        $info['hd_start_time'] = strtotime($info['hd_start_time']);
        $info['hd_end_time'] = strtotime($info['hd_end_time']);
        if($info['hd_end_time'] <= $info['hd_start_time']) $this->output(-1, '活动开始时间不能大于或等于结束时间.');

        //指定专题
        if(in_array($info['htype'], array(2, 3, 4)) && $info['game_object'] == 2 ){
            if(intval($info['subject_id']) < 1){
                $this->output(-1, '要填写专题ID'.$info['subject_id']);
            }
            if($info['status']) {
                $rs = Client_Service_Subject::getOnlineSubject($info['subject_id']);
                if ($rs == false) {
                    $this->output(-1, '专题不存在或未启用.');
                }
            }
        }

        $info['game_object_addition_info'] = $this->checkAndFormatGameObjAdditionInfo($info);
        $info['hd_object_addition_info'] = $this->checkAndFormatHdObjAdditionInfo($info);

        //全部游戏
        if(in_array($info['htype'], array(2,3,4)) && ($info['game_object'] == 1) ){
            $info['subject_id'] = 0;
        }
        //游戏版本判断
        if(in_array($info['htype'],array(1))) {
            if(!is_array($info['game_version'])) {
                $this->output(-1, '游戏大厅版本不能为空.');
            } else {
                unset($info['game_version'][0]);
                $arr = array();
                foreach ($info['game_version'] as $key => $value) {
                    $arr[(int)$key] = (int)$value;
                }
                $info['game_version'] = json_encode($arr);
            }
        }

        //规则有效性判断
        //登陆客户端
        if($info['htype'] == 1){
            if(!$info['denomination']) $this->output(-1, '面额不能为空.');
            if(!$info['deadline']) $this->output(-1, '代金券有效期不能为空.');
            $ruleContent = array(
                'denomination' => intval($info['denomination']),
                'deadline' => intval($info['deadline']),
            );
            $info['rule_content'] = json_encode($ruleContent);
        }

        //登陆游戏
        if($info['htype'] == 2){
            if(!$info['denomination']) $this->output(-1, '面额不能为空.');
            if(!$info['deadline']) $this->output(-1, 'A券有效期不能为空.');
            $ruleContent = array(
                'denomination' => intval($info['denomination']),
                'deadline' => intval($info['deadline']),
            );
            $info['rule_content'] = json_encode($ruleContent);
        }

        //消费返利
        if($info['htype']  == 3) {
            //单次消费-单区间-百分比
            if ($info['condition_type'] == 2) {
                if (!$info['deadline']) {
                    $this->output(-1, '代金券有效期不能为空.');
                }
                if (!$info['restoration']) {
                    $this->output(-1, '返还比例不能为空.');
                }
                if ($info['restoration'] > self::MAX_RETURN_MONEY_RATIO || $info['restoration'] < 1) {
                    $this->output(-1, '返还比例范围在1-' . self::MAX_RETURN_MONEY_RATIO . '之间.');
                }
                $ruleContent = array('denomination' => intval($info['denomination']), 'deadline' => intval($info['deadline']), 'restoration' => intval($info['restoration']));
                $info['rule_content'] = json_encode($ruleContent);
            }

            //消费返利-多区间-固定面额
            if (in_array($info['condition_type'], array(3, 8, 9))) {
                $ruleContent = $this->cookQuotaAcoupon($info);
                $info['rule_content'] = json_encode($ruleContent);
            }

            //消费返利-多区间百分比
            if (in_array($info['condition_type'], array(5, 6, 7, 10))) {
                $ruleContent= $this->cookPercentAcoupon($info);
                $info['rule_content'] = json_encode($ruleContent);
            }

        }
        //充值返利
        if($info['htype'] == 4){
            //单区间-百分比
            if ($info['condition_type'] == 1) {
                if(!$info['deadline']) $this->output(-1, '代金券有效期不能为空.');
                if(!$info['restoration']){
                    $this->output(-1, '返还比例不能为空.');
                }
                if($info['restoration']>self::MAX_RETURN_MONEY_RATIO || $info['restoration'] < 1){
                    $this->output(-1, '返还比例范围在1-'.self::MAX_RETURN_MONEY_RATIO.'之间.');
                }

                $ruleContent = array(
                    'denomination' => intval($info['denomination']),
                    'deadline' => intval($info['deadline']),
                    'restoration'=> intval($info['restoration'])
                );
                $info['rule_content'] = json_encode($ruleContent);
            }

            //充值返利-多区间-百分比
            if (in_array($info['condition_type'], array(2, 3, 4))) {
                $ruleContent= $this->cookPercentAcoupon($info);
                $info['rule_content'] = json_encode($ruleContent);
            }

            //充值返利-多区间-固定面额
            if (in_array($info['condition_type'], array(5, 6, 7))) {
                $ruleContent= $this->cookQuotaAcoupon($info);
                $info['rule_content'] = json_encode($ruleContent);
            }
        }

        unset($info['ACoupon']);
        unset($info['denomination']);
        unset($info['percent']);
        unset($info['backAmount']);
        unset($info['backPercent']);
        unset($info['deadline']);

        return $info;
    }

    /**
     * Enter description here ...
     */
    public function detailAction() {
        $configs['game_acoupon_desc'] = Game_Service_Config::getValue('game_acoupon_desc');
        $this->assign('configs', $configs);
    }

    /**
     * Enter description here ...
     */
    public function detail_postAction() {
        $config = $this->getPost('game_acoupon_desc');
        if(!$config) $this->output(-1, 'A券说明不能为空.');
        Game_Service_Config::setValue('game_acoupon_desc', $config);
        $this->output(0, '操作成功.');
    }

    /**
     * 游戏券说明
     */
    public function gameVoucherDetailAction() {
        $configs['game_voucher_detail'] = Game_Service_Config::getValue('game_voucher_detail');
        $this->assign('configs', $configs);
    }

    /**
     * 游戏券说明
     */
    public function gameVoucherDetailPostAction() {
        $config = $this->getPost('game_voucher_detail');
        if(!$config) $this->output(-1, '游戏券说明不能为空.');
        Game_Service_Config::setValue('game_voucher_detail', $config);
        $this->output(0, '操作成功.');
    }


    /**
     *
     * Enter description here ...
     */
    public function uploadAction() {
        $imgId = $this->getInput('imgId');
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     *
     * Enter description here ...
     */
    public function upload_postAction() {
        $ret = Common::upload('img', 'weal');
        $imgId = $this->getPost('imgId');
        $this->assign('code' , $ret['data']);
        $this->assign('msg' , $ret['msg']);
        $this->assign('data', $ret['data']);
        $this->assign('imgId', $imgId);
        $this->getView()->display('common/upload.phtml');
        exit;
    }

    /**
     * 编辑器中上传图片
     */
    public function uploadImgAction() {
        $ret = Common::upload('imgFile', 'weal');
        if ($ret['code'] != 0) die(json_encode(array('error' => 1, 'message' => '上传失败！')));
        exit(json_encode(array('error' => 0, 'url' => Common::getAttachPath() ."/". $ret['data'])));
    }
    /**
     * A券手动补单
     */
    public function sendTicketAction(){
        $this->assign('ticketType', $this->ticketType);
    }


    /**
     * A券手动补单post
     */
    public function sendTicketPostAction(){
        $filed = array(
            'ticketType',
            'ticketObject',
            'gameId',
            'userObject',
            'userType',
            'uname',
            'denomination',
            'start_time',
            'end_time',
            'reason'
        );
        $input = $this->getPost($filed);
        $data = $this->cookSendData($input);
        //组装赠送数据 导入赠送
        if($data['userObject'] == 1){
            $this->multiSendTicket($data);
        }elseif($data['userObject'] == 2){
            $this->sendUserTicket($data);
        }
    }

    /**
     * 获取CVS模板
     */
    public function getTemplateAction() {
        $type = $this->getInput("type");
        if (1 == $type) {
            $filename = "用户名_cvs模板";
            $title = array(array('uname', 'denomination'));
        } else if ( 2 == $type) {
            $filename = "uuid_cvs模板";
            $title = array(array('uuid', 'denomination'));
        } else {
            exit;
        }

        Util_Csv::putHead($filename);
        Util_Csv::putData($title);
        exit;
    }

    /**
     * 单个用户赠送
     */
    private function sendUserTicket($data){
        $aid = date('YmdHis').uniqid();
        if($data['userType'] == 1){
            $uuid = Api_Gionee_Account::getUuidByName($data['uname']);
            if(!$uuid){
                $this->output(-1, '账号"'.$data['uname'].'"不存在.');
            }
        }else{
            $uuid = $data['uname'];
        }

        $sendArr[] = array(
            'aid'=>$aid,
            'denomination'=>strval($data['denomination']),
            'startTime'=>(string)date('YmdHis', $data['start_time']),
            'endTime'=>(string)date('YmdHis', $data['end_time']),
            'uuid' => $uuid,
            'send_type' => 4,
            'sub_send_type' => 0,
            'ticket_type' => $data['ticketType'],
            'useApiKey' => $this->getUseApiKey($data),
            'game_id' => $data['gameId'],
            'desc'=> $data['reason']
        );
        $configArr = array(
            'type'=>4,
            'task_id'=>0,
            'reason'=>$data['reason'],
            'operator_name'=>$this->userInfo['username'],
            'prizeArr' =>$sendArr

        );
        $activity = new Util_Activity_Context(new Util_Activity_TicketSend($configArr));
        $rs = $activity->sendTictket();
        if(!$rs){
            $this->output(-1, '赠送失败.');
        }
        $this->output(0, '赠送成功.');
    }

    /**
     * 多用户批量赠送
     */
    private function multiSendTicket($data){
        $csv = new Util_Excel_Csv();
        $result = $csv->import($data['fileName']);
        if(!is_array($result)){
            $this->output(-1, '读取文件失败');
        }
        $acount = array();
        foreach ($result as $key=> $val){
            if($data['userType'] == 1 && (!$val['uname'] || strlen($val['uname']) > 11)){
                $this->output(-1, '用户的账号不能为空');
            }

            if($data['userType'] == 2 && (!$val['uuid'] || strlen($val['uuid']) != 32)){
                $this->output(-1, '用户的UUID不能为空或有误');
            }

            if($val['denomination'] <= 0){
                $this->output(-1, '用户的金额小于0，金额要大于等于0');
            }

            if($data['userType'] == 1){
                if(!array_key_exists('uname', $val)){
                    $this->output(-1, '没有uname栏目');
                }
                $acount[$val['uname']] = Api_Gionee_Account::getUuidByName($val['uname']);
                if(!$acount[$val['uname']]){
                    $this->output(-1, '账号"'.$val['uname'].'"不存在');
                }
            }
        }

        Common_Service_Base::beginTransaction();
        foreach ($result as $key=> $val){
            $aid = date('YmdHis').uniqid();
            $denomination = strval($val['denomination']);
            $sendArr[] = array(
                'aid'=>$aid,
                'denomination'=>$denomination,
                'startTime'=>(string)date('YmdHis', $data['start_time']),
                'endTime'=>(string)date('YmdHis', $data['end_time']),
                'uuid'=>($data['userType'] == 1)? $acount[$val['uname']] : $val['uuid'],
                'send_type' => 4,
                'sub_send_type' => 0,
                'ticket_type' => $data['ticketType'],
                'useApiKey' => $this->getUseApiKey($data),
                'game_id' => $data['gameId'],
                'desc'=> $data['reason']
            );
            //每次赠送100个
            if(($key+1) % 100 ==  0){
                $rs = $this->sendTicket($data, $sendArr);
                if(!$rs){
                    Common_Service_Base::rollBack();
                    $this->output(-1, '赠送失败.');
                }
                $sendArr = array();
            }
        }
        if(count($sendArr)){
            $rs = $this->sendTicket($data, $sendArr);
            if(!$rs){
                Common_Service_Base::rollBack();
                $this->output(-1, '赠送失败.');
            }
        }
        Common_Service_Base::commit();
        $this->output(0, '赠送成功.');
    }
    /**
     * @param $task
     * @return mixed
     */
    private function getUseApiKey($data){
        if($data['ticketType'] == Client_Service_Acoupon::TICKET_TYPE_GAMEVOUCHER){
            if($data['ticketObject'] == Client_Service_Acoupon::TICKET_OBJECT_GAMEHALL){
                return Client_Service_TicketTrade::getGameHallApikey();
            } else {
                return $data['apiKey'];
            }
        }
        return Client_Service_TicketTrade::getPaymentApiKey();
    }

    /**
     * A券明细
     */
    public function ticketDetailAction(){
        $page = intval($this->getInput('page'));
        $s = $this->getInput(array('task_id','uname', 'ticket_type', 'uuid', 'start_time', 'end_time', 'status','sendType'));
        if ($page < 1) $page = 1;
        $params = array();
        if ($s['uuid']) {
            $search =  array();
            $search['uuid'] = array('LIKE',$s['uuid']);
            $users = Account_Service_User::getUsers($search);
            if($users){
                foreach($users as $key=>$value){
                    if($value['uuid']){
                        $uuidIds[] = $value['uuid'];
                    }
                }
            }
        }

        if ($s['uname']) {
            $search =  array();
            $search['uname'] = array('LIKE',$s['uname']);
            $users = Account_Service_User::getUsers($search);
            if($users){
                foreach($users as $key=>$value){
                    if($value['uuid']){
                        $unameIds[] = $value['uuid'];
                    }

                }
            }
        }

        if(count($uuidIds) && count($unameIds) ){
            $params['uuid'] = array('IN',array_intersect($uuidIds, $unameIds));
        }elseif(count($uuidIds)){
            $params['uuid'] = array('IN',$uuidIds);
        }elseif(count($unameIds)){
            $params['uuid'] = array('IN',$unameIds);
        }elseif(count($unameIds) && !count($uuidIds) && $s['uuid']){
            $params['uuid'][0] = array('IN',$unameIds);
            $params['uuid'][1] = array('LIKE',$s['uuid']);
        }elseif(!count($unameIds) && !count($uuidIds) && $s['uuid']){
            $params['uuid'] = array('LIKE',$s['uuid']);
        }

        if($s['start_time']){
            $params['consume_time'][0] = array('>=', strtotime($s['start_time']));
        }
        if($s['end_time']){
            $params['consume_time'][1] = array('<=', strtotime($s['end_time']));
        }

        if ($s['status']) {
            $params['status'] = $s['status'] - 1 ;
        }

        if ($s['sendType']) {
            $params['send_type'] = $s['sendType'] ;
        }
        if ($s['ticket_type']) {
            $params['ticket_type'] = $s['ticket_type'] ;
        }

        if ($s['sendType']) {
            $params['send_type'] = $s['sendType'] ;
        }

        if ($s['outOrderId']) {
            $params['out_order_id'] = $s['outOrderId'] ;
        }

        list($total, $result) = Client_Service_TicketTrade::getList($page, $this->perpage, $params, array('id'=>'DESC'));

        $subSendTypeArr = $aids = $uuids = array();
        foreach($result as $key=>$value){
            $userInfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
            $tempUserInfo[$value['uuid']]['nickname'] = $userInfo['nickname'];
            $tempUserInfo[$value['uuid']]['uname'] =  $userInfo['uname'];

            if($value['send_type'] == 1){
                $ret = Client_Service_WealTaskConfig::get($value['sub_send_type']);
                $desc =$ret['task_name'];
            }elseif($value['send_type'] == 2){
                if($value['sub_send_type'] == 1){
                    $desc = '连续登录第'.$value['third_type'].'天';
                }else{
                    $ret = Client_Service_DailyTaskConfig::getByID(intval($value['sub_send_type']));
                    $desc = $desc =$ret['task_name'];
                }
            }elseif($value['send_type'] == 3){
                $ret = Client_Service_Acoupon::getACouponInfoActivity($value['sub_send_type']);
                $desc =$ret['title'];
            }elseif($value['send_type'] == 4){
                $desc ='手动赠送';
            }elseif($value['send_type'] == 5){
                $desc ='积分抽奖';
            }elseif($value['send_type'] == 6){
                $desc ='积分兑换';
            }
            $subSendTypeArr[$value['send_type']][$value['sub_send_type']] = $desc;

            if($value['send_type'] == 4){
                $aids[] = $value['aid'];
            }
        }

        $reasonArr = $this->getReasions($aids, $uuids);

        $this->assign('ticketType', $this->ticketType);
        $this->assign('subSendTypeArr', $subSendTypeArr);
        $this->assign('sendTypeArr', $this->mSendType);
        $this->assign('result', $result);
        $this->assign('s', $s);
        $this->assign('userInfo', $tempUserInfo);
        $this->assign('total', $total);
        $this->assign('reasonArr', $reasonArr);
        $url = $this->actions['ticketDetailUrl'].'/?' . http_build_query($s) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));

    }

    public function getReasions($aids, $uuids){
        $reasonArr = array();

        if(!$aids){
            return $reasonArr;
        }

        $search = array();
        $search['aid'] = array('IN',$aids);
        $reasons = Client_Service_SendTicketReason::getsBy($search);
        foreach($reasons as $k=>$v){
            $reasonArr[$v['aid']]['reason'] = $v['reason'];
            $reasonArr[$v['aid']]['name'] = $v['operator_name'];
        }
        return $reasonArr;
    }

    /**
     * A券明细
     */
    public function moneyDetailAction(){
        $page = intval($this->getInput('page'));
        $s = $this->getInput(array('uname', 'uuid', 'start_time','end_time','event'));
        if ($page < 1) $page = 1;

        $params = array();
        if ($s['uuid']) {
            $search =  array();
            $search['uuid'] = array('LIKE',$s['uuid']);
            $users = Account_Service_User::getUsers($search);
            if($users){
                foreach($users as $key=>$value){
                    if($value['uuid']){
                        $uuidIds[] = $value['uuid'];
                    }
                }
            }
        }

        if ($s['uname']) {
            $search =  array();
            $search['uname'] = array('LIKE',$s['uname']);
            $users = Account_Service_User::getUsers($search);
            if($users){
                foreach($users as $key=>$value){
                    if($value['uuid']){
                        $unameIds[] = $value['uuid'];
                    }

                }
            }
        }

        if(count($uuidIds) && count($unameIds) ){
            $params['uuid'] = array('IN',array_intersect($uuidIds, $unameIds));
        }elseif(count($uuidIds)){
            $params['uuid'] = array('IN',$uuidIds);
        }elseif(count($unameIds)){
            $params['uuid'] = array('IN',$unameIds);
        }

        if($s['start_time']){
            $params['trade_time'][0] = array('>=', strtotime($s['start_time']));
        }
        if($s['end_time']){
            $params['trade_time'][1] = array('<=', strtotime($s['end_time']));
        }

        if ($s['event']) {
            $params['event'] = $s['event'] ;
        }

        list($total, $result) = Client_Service_MoneyTrade::getList($page, $this->perpage, $params, array('id'=>'DESC'));

        foreach($result as $key=>$value){
            $userInfo = Account_Service_User::getUserInfo(array('uuid'=>$value['uuid']));
            $tempUserInfo[$value['uuid']]['nickname'] = $userInfo['nickname'];
            $tempUserInfo[$value['uuid']]['uname'] =  $userInfo['uname'];
        }

        $this->assign('result', $result);
        $this->assign('s', $s);
        $this->assign('userInfo', $tempUserInfo);
        $this->assign('total', $total);
        $url = $this->actions['moneyDetailUrl'].'/?' . http_build_query($s) . '&';
        $this->assign('pager', Common::getPages($total, $page, $this->perpage, $url));

    }

    private function strToIntArray($str, $delimiter=";") {
        $output = array();
        $array = explode($delimiter, $str);

        foreach ($array as $item) {
            if (ctype_alnum($item)) {
                $output[] = strval($item);
            }
            else if (strlen($item) != 0) {
                $this->output(-1, $item.'不合法');
            }
        }
        return $output;
    }

    private function checkAndFormatGameObjAdditionInfo($info) {
        if ((Client_Service_TaskHd::TARGET_GAME_GAMEID_LIST == $info['game_object'])
            || (Client_Service_TaskHd::TARGET_GAME_EXCLUDE_LIST == $info['game_object']) ) {
            $games = $this->strToIntArray($info['game_object_addition_info']);
            if ( count($games) == 0 ){
                $this->output(-1, '没有合法的游戏ID');
            }

            foreach ($games as $gameId) {
                $gameInfo = Resource_Service_Games::getBy(array('id' => $gameId));
                if (!$gameInfo) {
                    $this->output(-1, '游戏ID（'.$gameId.'）不存在');
                }
                if(!$gameInfo['api_key']){
                    $this->output(-1, '游戏ID（'.$gameId.'）不是联运游戏');
                }
            }

            $gameList = array('game_list' => $games);
            return json_encode($gameList);
        }else {
            return '';
        }
    }

    private function checkAndFormatHdObjAdditionInfo($info) {
        switch ( $info['hd_object'] ) {
            case Client_Service_TaskHd::TARGET_USER_USER_ALL:
            {
                return "";
            }
            case Client_Service_TaskHd::TARGET_USER_USER_BY_UUID:
            {
                $users = $this->strToIntArray($info['hd_object_addition_info']);
                if ( count($users) == 0 ){
                    $this->output(-1, '没有合法的参与帐号');
                }
                $userList = array('user_list' => $users);
                return json_encode($userList);
            }
            default:
            {
                $this->output(-1, '参与用户类型不正确.');
                break;
            }
        }
        return "";
    }

    /**
     * @param $info
     * @return array
     */
    private function cookQuotaAcoupon($info) {
        $ruleContent = array();
        foreach ($info['ACoupon'] as $key => $value) {
            if (!$value['section_start']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 开始区间不能为空.');
            }
            if (!$value['section_end']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 结束区间不能为空.');
            }
            if (!$value['backAmount']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 返还额度不能为空.');
            }
            if ($value['section_end'] <= $value['section_start']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 开始区间不能大于或等于结束区间.');
            }
            if ($info['ACoupon'][$key]['section_end'] != $info['ACoupon'][$key + 1]['section_start'] && $info['ACoupon'][$key + 1]['section_start']) {
                $this->output(-1, '规则 (' . ($key + 2) . ') 开始区间必须是连续区间.');
            }
            $ruleContent[$key]['section_start'] = (int)$value['section_start'];
            $ruleContent[$key]['section_end'] = (int)$value['section_end'];
            $ruleContent[$key]['backAmount'] = (int)$value['backAmount'];
            foreach ($value['denarr'] as $k => $v) {
                if (!$v['Step']) {
                    $this->output(-1, '规则 (' . ($key + 1) . ') 代金券 (' . ($k + 1) . ') 额度不能为空.');
                }
                if (!$v['effect_start_time']) {
                    $this->output(-1, '规则 (' . ($key + 1) . ') 代金券 (' . ($k + 1) . ') 开始生效时间不能为空.');
                }
                if (!$v['effect_end_time']) {
                    $this->output(-1, '规则 (' . ($key + 1) . ') 代金券 (' . ($k + 1) . ') 结束生效时间不能为空.');
                }
                if ($v['effect_end_time'] < $v['effect_start_time']) {
                    $this->output(-1, '规则 (' . ($key + 1) . ') 代金券 (' . ($k + 1) . ') 开始生效时间不能大于或等于结束时间.');
                }
                $ruleContent[$key]['denarr'][$k]['Step'] = $v['Step'];
                $ruleContent[$key]['denarr'][$k]['effect_start_time'] = $v['effect_start_time'];
                $ruleContent[$key]['denarr'][$k]['effect_end_time'] = $v['effect_end_time'];
            }
        }
        return $ruleContent;
    }

    /**
     * @param $info
     * @return array
     */
    private function cookPercentAcoupon($info) {
        $ruleContent = array();
        $lastStartTime = $lastEndTime = 0;
        foreach ($info['ACoupon'] as $key => $value) {
            if (!$value['section_start']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 开始区间不能为空.');
            }
            if (!$value['section_end']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 结束区间不能为空.');
            }
            if (!$value['backPercent']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 区间返还比例不能为空.');
            }
            if ($value['section_end'] <= $value['section_start']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 开始区间不能大于或等于结束消费区间.');
            }
            if ($info['ACoupon'][$key]['section_end'] != $info['ACoupon'][$key + 1]['section_start'] && $info['ACoupon'][$key + 1]['section_start']) {
                $this->output(-1, '规则 (' . ($key + 2) . ') 开始区间必须是连续消费区间.');
            }
            if ($value['backPercent'] > self::MAX_RETURN_MONEY_RATIO || $value['backPercent'] < 1) {
                $this->output(-1, '返还比例范围在1-' . self::MAX_RETURN_MONEY_RATIO . '之间.');
            }
            if (!$value['effect_start_time']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 代金券 开始生效时间不能为空.');
            }
            if (!$value['effect_end_time']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 代金券 结束生效时间不能为空.');
            }
            if ($value['effect_end_time'] < $value['effect_start_time']) {
                $this->output(-1, '规则 (' . ($key + 1) . ') 代金券  开始生效时间不能大于或等于结束时间.');
            }

            if (($info['htype'] == 3 && $info['condition_type'] == 7) || ($info['htype'] == 4 && $info['condition_type'] == 4)) {
                if ($key != 0) {
                    if ($value['effect_start_time'] != $lastStartTime) {
                        $this->output(-1, '规则 (' . ($key + 1) . ') 代金券  开始生效时间不一致.');
                    }
                    if ($value['effect_end_time'] != $lastEndTime) {
                        $this->output(-1, '规则 (' . ($key + 1) . ') 代金券  结束生效时间不一致.');
                    }
                }
            }

            $lastStartTime = $value['effect_start_time'];
            $lastEndTime = $value['effect_end_time'];

            $ruleContent[$key]['section_start'] = (int)$value['section_start'];
            $ruleContent[$key]['section_end'] = (int)$value['section_end'];
            $ruleContent[$key]['backPercent'] = (int)$value['backPercent'];
            $ruleContent[$key]['effect_start_time'] = $value['effect_start_time'];
            $ruleContent[$key]['effect_end_time'] = $value['effect_end_time'];
        }
        return $ruleContent;
    }

    /**
     * @param $input
     * @return array
     */
    private function cookSendData($input) {
        $ext = strtolower(end(explode(".", $_FILES['csv']['name'])));
        $input['userObject'] = intval($input['userObject']);        
        $fileName = $_FILES['csv']['tmp_name'];
        switch($input['userObject']){
            case 1:
                if($ext != "csv") {
                    $this->output(-1, '导入文件格式非法，必须是csv.');
                }
                break;
            case 2:
                $input['uname'] = trim($input['uname']);
                if($input['uname'] == ''){
                    $this->output(-1, '用户名或UUID不能为空');
                }
                $input['denomination'] = intval($input['denomination']);
                if($input['denomination'] <= 0){
                    $this->output(-1, '代金券的面额要大于0，请输入大于0的整数');
                }
                $input['denomination'] = sprintf("%.2f", $input['denomination']);
                $input['userType'] = intval($input['userType']);
                if($input['userType'] == 1 && strlen($input['uname']) > 11){
                    $this->output(-1, '用户名输入有误');
                }
                if($input['userType']==2 && strlen($input['uname']) != 32){
                    $this->output(-1, 'UUID输入有误');
                }
                break;
        }
        $input['fileName']= $fileName;
        if (!$input['start_time']) {
            $this->output(-1, '开始时间不能为空.');
        }
        if (!$input['end_time']) {
            $this->output(-1, '结束时间不能为空.');
        }
        $input['start_time'] = strtotime($input['start_time']);
        $input['end_time'] = strtotime($input['end_time']);
        if ($input['start_time'] >= $input['end_time']) {
            $this->output(-1, '结束时间不能小于等于开始时间.');
        }

        if($input['ticketType'] == 2 && $input['ticketObject'] == 2){
            if (!$input['gameId']) {
                $this->output(-1, '游戏Id不能为空.');
            }
            $gameInfo = Resource_Service_Games::getBy(array('id'=>$input['gameId'], 'status'=>Resource_Service_Games::STATE_ONLINE));
            if(!$gameInfo){
                $this->output(-1, '游戏不存在.');
            }
            if(empty($gameInfo['api_key'])){
                $this->output(-1, '游戏不是联运游戏.');
            }
            $input['apiKey'] = $gameInfo['api_key'];
        }

        if($input['ticketType'] == 2 && $input['ticketObject'] == 1){
            $input['gameId'] = Resource_Service_Games::getGameHallId();
        }

        if (!$input['reason']) {
            $this->output(-1, '赠送原因不能为空.');
        }
        return $input;
    }

    /**
     * @param $data
     * @param $sendArr
     * @return array
     */
    private function sendTicket($data, $sendArr) {
        $configArr = array(
            'type' => 4,
            'task_id' => 0,
            'prizeArr' => $sendArr,
            'reason' => $data['reason'],
            'operator_name' => $this->userInfo['username'],
            'optName' => $this->userInfo['username']
        );
        $activity = new Util_Activity_Context(new Util_Activity_TicketSend($configArr));
        $rs = $activity->sendTictket();
        return array($configArr, $activity, $rs);
    }

    private function addAsyncAcouponData($info){
        //非登陆游戏的不做处理
        if($info['htype'] != 2) return;
        $gameObject = $info['game_object'];
        $allGameIds = array();
        switch($gameObject){
            case 1:
            case 4:
                $allGameIds = $this->getAllUnionGameIds();
                break;
            case 2:
                $newSubjectId = $info['subject_id'];
                $allGameIds = $this->getSubjectGameIds($newSubjectId);
                break;
            case 3:
                $newAdditionInfo = json_decode($info['game_object_addition_info'], true);
                $allGameIds = $newAdditionInfo['game_list'];
                break;
        }
        if($allGameIds){
            $this->asyncGames($allGameIds);
        }
    }

    /**
     * 编辑操作中游戏有奖状态代码更新
     * @param $oldData
     * @param $newData
     */
    private function asyncAcouponData($oldData, $newData){
        //非登陆游戏的不做处理
        if($newData['htype'] != 2) return;
        $gameObject = $newData['game_object'];
        $allGameIds = array();
        switch($gameObject){
            case 1:

            case 4:
                $allGameIds = $this->getAllUnionGameIds();
                break;
            case 2:
                $oldSubjectId = $oldData['subject_id'];
                $oldGameIds = $this->getSubjectGameIds($oldSubjectId);
                $newSubjectId = $newData['subject_id'];
                $newGameIds = $this->getSubjectGameIds($newSubjectId);
                $allGameIds = $this->getOptionGameIds($oldGameIds, $newGameIds);
                break;
            case 3:
                $oldAdditionInfo = json_decode($oldData['game_object_addition_info'], true);
                $oldGameIds = $oldAdditionInfo['game_list'];
                $newAdditionInfo = json_decode($newData['game_object_addition_info'], true);
                $newGameIds = $newAdditionInfo['game_list'];
                $allGameIds = $this->getOptionGameIds($oldGameIds, $newGameIds);
                break;
        }
        if($allGameIds){
            $this->asyncGames($allGameIds);
        }
    }

    private function getAllUnionGameIds(){
        $params =array(
            'status'=>1,
            'cooperate' => 1,
            'api_key'=>array('<>', '')
        );
        $unionGames = Resource_Service_Games::getsBy($params);
        $unionGames = Common::resetKey($unionGames, 'id');
        $gameIds = array_unique(array_keys($unionGames));
        return $gameIds;
    }

    private function getOptionGameIds($oldGameIds, $newGameIds){
        $gameIds =array_unique(array_merge($oldGameIds, $newGameIds));
        return $gameIds;
    }

    /**
     * 批量更新游戏有奖状态
     * @param $gameIds
     */
    private function asyncGames($gameIds){
        Async_Task::execute('Async_Task_Adapter_ExtraUpdate', 'gameRewardAcoupon');
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updteGamesAcoupon', $gameIds);
        Async_Task::execute('Async_Task_Adapter_GameListData', 'updateRewardAcoupon');
    }

    /**
     * 获取专题的游戏id
     * @param $subjectId
     * @return array
     */
    private function getSubjectGameIds($subjectId){
        $games = Client_Service_SubjectGames::getSubjectAllItemsGames($subjectId);
        $games = Common::resetKey($games, 'game_id');
        $gameIds = array_keys($games);
        return $gameIds;
    }
}
