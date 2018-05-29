<?php

/**
 * 合同模块
 * @author noprom
 */
Class ContractAction extends SystemAction
{
    public $model = "Contract";

    const CONTRACT_NOT_APPLY = 0; // 未申请
    const CONTRACT_APPLY_ING = 1; // 申请中
    const CONTRACT_APPLY_NOT_PASS = -1; // 申请不通过
    const CONTRACT_APPLY_SUCCESS = 2; // 扫描件未回传
    const CONTRACT_CHECK_ING = 3; // 审核中
    const CONTRACT_CHECK_FAILED = -2; // 审核不通过
    const CONTRACT_CHECK_PASSED = 4; // 审核通过
    const CONTRACT_TIME_PASSED = -3; // 已过期
    const CONTRACT_TIME_PASSED_ISCOMING = 5; // 即将到期

    //应用申请导出配置
    //应用名称 	包名 	税费率 	分成比例 	公司名称 	签署日期 	商务对接人 	申请状态 	审核人
    protected $_export_config = array(
        'app_name' => array('应用名称'),
        'package' => array('包名'),
        'receipt_id' => array('税费率', 'getShuiStateById', 'callback'),
        'share' => array('分成比例'),
        'company_name' => array('公司名称'),
        'ctime' => array('签署日期', 'date', 'callback', '"Y-m-d",{{field_val}}', 'ctime'),
        'join_id' => array('商务对接人', 'getJoinerById', 'callback'),
        'status' => array('申请状态', 'getContractStatus', 'callback'),
        'checker' => array('审核人'),
    );

    private $receipt = array(
        'A .   乙方结算时提供的为普通发票，税费率为：6%；', 'B.    乙方结算时提供税率为3%的增值税专用发票，税费率为：3%；', 'C.    乙方结算时提供税率为6%的增值税专用发票，税费率为：0%；'
    );

    public function _filter(&$map)
    {
        $_search = MAP();
        $map = !empty($_search) ? array_merge($_search, $map) : $map;

        //按时间搜索
        if (!empty($_search["startDay"]) || !empty($_search["endDay"])) {
            $s = empty($_search["startDay"]) ? 0 : strtotime($_search["startDay"]);
            $e = empty($_search["endDay"]) ? 0 : strtotime($_search["endDay"]) + 86399;

            if (!empty($s))
                $map["ctime"] = array('EGT', $s);
            if (!empty($e))
                $map["ctime"] = array('ELT', $e);
            if (!empty($s) && !empty($e)) {
                if ($e < $s)
                    $this->error("结束时间不能小于等于开始时间");
                $map["ctime"] = array("between", array($s, $e));
            }
        }

        //如果是审核合同，只显示已经申请通过的
        if(!isset($map["status"]))
        {
        	if(ACTION_NAME == 'index')
        		$map["status"] = array("lt", 3);
        	if(ACTION_NAME == 'check')
        		$map["status"] = array("gt", 2);
        }
    }

    // 合同审核
    public function check()
    {
        $name = $this->model;
        if (!empty($this->ViewModel)) $name = $this->ViewModel;
        //列表过滤器，生成查询Map对象
        $map = $this->_search($name);
        //$map['status'] = array('notlike', array('0', '1', '-1'), 'AND');
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }

        $model = D($name);
        if (!empty ($model)) {
            $this->_list($model, $map);
        }

        $this->assign('map', $map);
        $this->display();
    }

    // 合同审核页面的导出设置
    public function check_export()
    {
        //合同编号 	应用名称 	包名 	合同名称 	税费率 	分成比例 	公司名称 	签署日期 	回传日期 合同有效期 合同状态
        $this->_export_config = array(
            'number' => array('合同编号'),
            'app_name' => array('应用名称'),
            'package' => array('包名'),
            'name' => array('合同名称'),
            'receipt_id' => array('税费率', 'getShuiStateById', 'callback'),
            'share' => array('分成比例'),
            'company_name' => array('公司名称'),
            'ctime' => array('签署日期', 'date', 'callback', '"Y-m-d",{{field_val}}', 'ctime'),
            'rtime' => array('回传日期', 'date', 'callback', '"Y-m-d",{{field_val}}', 'rtime'),
            'etime' => array('合同有效期', 'date', 'callback', '"Y-m-d",{{field_val}}', 'etime'),
            'status' => array('合同状态', 'getContractStatus', 'callback'),
        );
        $this->export();
    }

    // 查看合同资料
    public function  details()
    {
        $m = D($this->model);
        $id = $this->_get('id', 'intval,trim', 0);
        $check = $this->_get('check', 'intval,trim', 0);
        if (empty($id) || !$vo = $m->find($id))
            exit("记录不存在");

        // 发票类型
        $receipt = $this->receipt[intval($vo['receipt_id']) - 1];
        $vo['receipt'] = $receipt;
        $vo['status'] = $this->getContractStatus($vo['status']);

        // 金立对接人
        $joiner = M('contract_contact')->where(array('id' => $vo['join_id']))->field('name,email')->find();

        $this->assign('vo', $vo);
        $this->assign('check', $check);
        $this->assign("joiner", $joiner);
        $this->display();
    }

    // 查看同一个包名的合同
    public function detail_pkg()
    {
        $m = D($this->model);
        $id = $this->_get('id', 'intval,trim', 0);
        $pkg = $this->_get('pkg', 'trim', 0);

        if (empty($id) || !$vo = $m->find($id))
            exit("记录不存在");

        $name = $this->model;
        if (!empty($this->ViewModel)) $name = $this->ViewModel;
        //列表过滤器，生成查询Map对象
        $map['package'] = $pkg;
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }

        $model = D($name);
        if (!empty ($model)) {
            $this->_list($model, $map);
        }
        $this->assign('map', $map);
        $this->assign('vo', $vo);
        $this->display();
    }

    // 修改合同细节
    public function details_edit()
    {
        $id = $this->_post('id', 'trim,strip_tags', 0);
        $app_name = $this->_post('app_name', 'trim,strip_tags', "");
        $receipt_id = $this->_post('receipt_id', 'trim,strip_tags', 0);
        $share = $this->_post('share', 'trim,strip_tags', '5:5');
        $account_name = $this->_post('account_name', 'trim,strip_tags', "");
        $account_bank = $this->_post('account_bank', 'trim,strip_tags', "");
        $account_key = $this->_post('account_key', 'trim,strip_tags', "");
        $company_name = $this->_post('company_name', 'trim,strip_tags', "");
        $area = $this->_post('area', 'trim,strip_tags', "");
        $province = $this->_post('province', 'trim,strip_tags', "");
        $city = $this->_post('city', 'trim,strip_tags', "");
        $address_detail = $this->_post('address_detail', 'trim,strip_tags', "");
        $contact = $this->_post('contact', 'trim,strip_tags', "");
        $contact_email = $this->_post('contact_email', 'trim,strip_tags', "");
        $checker = $this->_post('checker', 'trim,strip_tags', "");


        // 首先判空处理
        empty($app_name) && $this->error('游戏名称不能为空!');
        empty($receipt_id) && $this->error('发票比例不能为空!');
        empty($share) && $this->error('分成不能为空!');
        empty($account_name) && $this->error('开户名不能为空!');
        empty($account_bank) && $this->error('开户银行不能为空!');
        empty($account_key) && $this->error('银行账号不能为空!');
        empty($company_name) && $this->error('公司名称不能为空!');
        empty($area) && $this->error('所处地区不能为空!');
        empty($province) && $this->error('所处省份不能为空!');
        empty($city) && $this->error('所处城市不能为空!');
        empty($address_detail) && $this->error('联系地址不能为空!');
        empty($contact) && $this->error('联系人不能为空!');
        empty($contact_email) && $this->error('联系人邮箱不能为空!');

        $data = array(
            'app_name' => $app_name,
            'receipt_id' => intval($receipt_id),
            'share' => $share,
            'account_name' => $account_name,
            'account_key' => $account_key,
            'account_bank' => $account_bank,
            'company_name' => $company_name,
            'area' => $area,
            'province' => $province,
            'city' => $city,
            'address_detail' => $address_detail,
            'contact' => $contact,
            'contact_email' => $contact_email,
            'checker' => $checker
        );

        // 如果分成比例有修改，则写入think_config表以便以后查询使用
        $config = M('think_config');
        $config_data = array('name' => 'SHARE_RATIO', 'value' => $share);
        $exist = $config->where($config_data)->find();
        if (!$exist) {
            $config->add($config_data);
        }

        $m = D($this->model);
        if ($m->where(array('id' => $id))->save($data)) {
            return $this->success('修改成功!', "closeCurrent");
        } else {
            D("Syserror")->error("修改合同信息出错", $m->getDbError(), $m->_sql(), 0, "2");
            return $this->error('修改失败');
        }
    }

    // 审核页面
    public function authorize()
    {
        $m = D($this->model);

        $id = $this->_get('id', 'intval,trim', 0);
        $check = $this->_get('check', 'intval,trim', 0);
        if (empty($id) || !$vo = $m->find($id))
            exit("记录不存在");

        //没有审核通过的注册账号申请的key不可以进行审核的操作
        if (1 != D("Dev://Accountinfo")->where(array("account_id" => $vo["author_id"], "status" => AccountinfoModel::STATUS_SUC))->count()) {
            $this->error("注册账号未审核通过，不能审核");
            return;
        }

        // 发票类型
        $receipt = $this->receipt[intval($vo['receipt_id']) - 1];
        // 金立对接人
        $joiner = M('contract_contact')->where(array('id' => $vo['join_id']))->field('name,email')->find();

        $this->assign("vo", $vo);
        $this->assign("receipt", $receipt);
        $this->assign("joiner", $joiner);
        $this->assign("check", $check);
        $this->display("authorize");
    }

    // 执行合同审核操作
    function save_authorize()
    {
        $m = D($this->model);
        $id = $this->_post("id", "intval", 0);
        $status = $this->_post("status", "trim,intval", -1);
        $notpass_reason = $this->_post('notpass_reason', 'trim', '');
        $note = $this->_post('note', 'trim', '');
        $notice = $this->_post('notice', 'trim', 0);

        if (empty($id) || !$vo = $m->find($id))
            $this->error("记录不存在");

        //没有审核通过的注册账号申请的key不可以进行审核的操作
        if (1 != D("Dev://Accountinfo")->where(array("account_id" => $vo["author_id"], "status" => AccountinfoModel::STATUS_SUC))->count())
            $this->error("注册账号未审核通过，不能审核");

        //准备审核记录的参数
        $log = array(
            "id" => $id,
            "status" => $status,
            'notpass_reason' => $notpass_reason,
            'note' => $note,
            'checker' => $_SESSION['loginUserName']
        );

        // 如果用户在到期之前续签了合同，这个合同审核通过之后，之前的合同状态变更为“审核通过”
        if(intval($status) == self::CONTRACT_CHECK_PASSED){
            if(intval($vo['type']) == 1){ // 续签合同
                $in = array('in',array(self::CONTRACT_TIME_PASSED,self::CONTRACT_TIME_PASSED_ISCOMING));
                $xu = $m->where(array('status'=>$in,'number'=>$vo['number'],'package'=>$vo['package']))->find();

                $data = array(
                    'status' => self::CONTRACT_CHECK_PASSED,
                    'hide' => 1, // 防止自动检测时将状态变为 即将到期
                    'etime' => $vo['vtime'] // 之前的合同结束时间变为续签合同开始时间
                );
                if($m->where(array('id'=>$xu['id']))->save($data)){
                }else{
                    D("Syserror")->error("如果用户在到期之前续签了合同，这个合同审核通过之后，之前的合同状态变更为“审核通过”,保存合同信息出错", $m->getDbError(), $m->_sql(), 0, "2");
                }
            }
        }

        // 向审核记录表插入数据
        $data_log = array(
            'admin' => $_SESSION['loginUserName'],
            'contract_id' => $id,
            'status' => $status,
            'time' => time(),
            'notpass_reason' => $notpass_reason,
            'note' => $note
        );
        M('Contract_log')->add($data_log);

        // 发送邮件
        if ($notice) {
            if($status == self::CONTRACT_APPLY_NOT_PASS ||$status == self::CONTRACT_APPLY_SUCCESS)
                $type = '申请';
            else if($status == self::CONTRACT_CHECK_FAILED ||$status == self::CONTRACT_CHECK_PASSED){
                $type = '审核';
            }

            if ($status == self::CONTRACT_APPLY_NOT_PASS || $status == self::CONTRACT_CHECK_FAILED) {  // 申请|审核 不通过
                self::send_mail_fail($vo, $notpass_reason,$type);
            } elseif ($status == self::CONTRACT_APPLY_SUCCESS || $status == self::CONTRACT_CHECK_PASSED) { // 申请|审核 通过
                self::send_mail_success($vo,$type);
            }
        }

        $m->where(array('id' => $id))->save($log);
        $this->success("审核成功", "closeCurrent");
    }

    protected function send_mail_success($key,$type)
    {
        $app_name = $key['app_name'];
        $sendemail = $key['contact_email'];

        $subject = sprintf("【%s】%s %s合同通过", C("SMTP.SMTP_NAME"), $app_name, $type);
        $body = <<<EEE
        亲爱的开发者：<br>
<p>您的应用《%s》的合同%s通过，请及时登录开发者平台下载电子合同进行签字盖章并回传。</p>

<p>*如有问题，还请邮件至dev.game@gionee.com，或拨打我们的开发者客服电话：0755-83211672<br>

金立游戏开发者平台</p>
EEE;
        $body = sprintf($body, $app_name,$type);
        smtp_mail($sendemail, $subject, $body);
    }

    protected function send_mail_fail($key, $notpass_reason,$type)
    {
        $app_name = $key['app_name'];
        $sendemail = $key['contact_email'];

        $subject = sprintf("【%s】%s %s合同不通过", C("SMTP.SMTP_NAME"), $app_name, $type);
        $body = <<<EEE
        亲爱的开发者：<br>
<p>您的应用《%s》的合同%s不通过，请重新修改后再申请。</p>
<p>%s不通过原因：%s</p>
<p>*如有问题，还请邮件至dev.game@gionee.com，或拨打我们的开发者客服电话：0755-83211672<br>

金立游戏开发者平台</p>
EEE;

        $body = sprintf($body, $app_name, $type, $type, $notpass_reason);
        smtp_mail($sendemail, $subject, $body);
    }

    // 主合同展示页面
    public function show()
    {
        if (!isset($_GET['id'])) $this->error('此页面不存在！');

        $id = $this->_get('id', 'trim', 0);
        $contract = M('contract')->where(array('id' => $id))->find();
        if ($contract == false) $this->error('不存在此合同!');

        D('Dev://Contract')->show($contract);
    }

    // 合同续签展示页面
    public function reshow()
    {
        if (!isset($_GET['id'])) $this->error('此页面不存在！');

        $m = M('contract');
        $id = $this->_get('id', 'trim', 0);
        if (empty($id) || !$contract = $m->where(array('id' => $id))->find())
            return $this->error('不存在该合同!');

        D('Dev://Contract')->reshow($contract);
    }

    // 合同审核
    public function manage()
    {
        $name = $this->model;

        //列表过滤器，生成查询Map对象
        $map = $this->_search($name);

        //按应用加唯一合同展示列表
        $model = D($this->model);
        $this->_filter($map);

        $map["id"] = array("in", $model->group("package")->getField("id", true));

        $this->_list($model, $map);
        $this->assign('map', $map);
        $this->display();
    }

    // 合同查看页面的导出设置
    public function manage_export()
    {
        //APPID	合同编号	应用名称	包名	应用状态	合同名称	税费率	开发者	回传日期	合同有效期	合同数	合同状态
        $this->_export_config = array(
            'app_id'		=> array('APPID'),
            'number'		=> array('合同编号'),
            'app_name'		=> array('应用名称'),
            'package'		=> array('包名'),
            'name'			=> array('合同名称'),
            'receipt_id'	=> array('税费率', 'getShuiStateById', 'callback'),
            'company_name'	=> array('开发者'),
            'rtime'			=> array('回传日期', 'date', 'callback', '"Y-m-d",{{field_val}}', 'rtime'),
        	'vtime'			=> array('合同生效日期', 'date', 'callback', '"Y-m-d",{{field_val}}', 'vtime'),
            'etime'			=> array('合同终止日期', 'date', 'callback', '"Y-m-d",{{field_val}}', 'etime'),
        	'package2'		=> array('合同数量', 'getContractCount', 'callback', '{{field_val}}', 'package'),
            'status'		=> array('合同状态', 'getContractStatus', 'callback'),
        );
        $this->export();
    }

    // 联系人展示+新增+编辑
    public function contact()
    {
        $m = M('contract_contact');
        if (IS_POST && isset($_POST["id"])) {
            $adder = $_SESSION['loginUserName'];

            $id = $this->_post('id', 'trim');
            $name = $this->_post('name', 'trim', '');
            $phone = $this->_post('phone', 'trim', '');
            $email = $this->_post('email', 'trim', '');
            $area = $this->_post('area', 'trim', '');

            empty($name) && $this->error('联系人名称不能为空');
            empty($phone) && $this->error('联系电话不能为空');
            empty($email) && $this->error('联系邮箱不能为空');
            empty($area) && $this->error('负责区域不能为空');

            // 写入数据
            $data = array(
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'area' => $area,
                'status' => $this->_post('status'),
                'ctime' => time(),
                'adder' => $adder
            );

            if (empty($id)) { // 新增
                if ($m->where(array('name' => $name,'status' => 1))->select()) {
                    return $this->error('该联系人已经存在!');
                }else if($m->where(array('area' => $area,'status' => 1))->select()){
                    return $this->error('一个地区只能添加一个联系人!');
                }

                if ($m->add($data))
                    return $this->success('操作成功', "closeCurrent");
                else
                    return $this->error('操作失败');
            } else { // 编辑
                if ($m->where(array('id' => $id))->save($data))
                    return $this->success('操作成功', "closeCurrent");
                else return $this->error('操作失败');
            }

        } else {
            // 联系人较少，暂不做分页
            $contactHead = array('编号', '联系人名称', '联系电话', '联系邮箱', '负责区域', '状态', '创建日期', '添加人', '操作');
            $contacts = $m->select();

            $this->assign('contactHead', $contactHead);
            $this->assign('contacts', $contacts);
            $this->display();
        }
    }

    // 联系人编辑
    public function editcontact()
    {
        $id = $this->_get('id', 'trim', 0);

        $m = M('contract_contact');
        $contact = $m->where(array('id' => $id))->find();
        if (!$contact) $this->error('不存在此联系人');

        $this->assign('contact', $contact);
        $this->display('addcontact');
    }

    // 根据ID获得税费率
    public function getShuiStateById($id)
    {
        $id = intval($id);
        switch ($id) {
            case 1:
                return '6%';
            case 2:
                return '3%';
            case 3:
                return '0%';
        }
    }

    // 根据ID获得商务对接人
    public function getJoinerById($id)
    {
        return D($this->model)->getJoiner($id);
    }

    // 获得当前合同状态
    public function getContractStatus($status)
    {
        return D($this->model)->getStatus($status);
    }

    // 根据ID获取合同状态
    public function getContractType($type)
    {
        return D($this->model)->getContractType($type);
    }
    
    // 根据包名获取合同数量
    public function getContractCount($package)
    {
    	return D($this->model)->where(array("package"=>$package))->count() + 0;
    }
}