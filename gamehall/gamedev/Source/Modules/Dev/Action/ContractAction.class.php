<?php

/**
 * 开发者合同模块
 * @author noprom
 *
 */
class ContractAction extends BaseAction
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

    const ONEYEAR = 31536000; // 一年

    // 我的合同
    function mindex()
    {
        import("Extend.General.Page", LIB_PATH); //导入分页类

        $status = $this->_get('status', 'trim', null);
        if (IS_POST) {
            $status = $this->_post('status', 'trim', null);
            $name = $this->_post('name', 'trim', '');
            $map["_string"] = sprintf("concat(package, app_name, number) like '%%%s%%'", $name);
        }
        if (is_numeric($status) && in_array($status, array_keys(ContractModel::$status))) {
            $map["status"] = $status;
            $this->assign('status', $status);
        }
        $map['author_id'] = $this->uid;

        $m = M('contract');
        $count = $m->where($map)->count();

        $Page = new Page ($count, $this->pageSize);
        $Page->setConfig("first", "首页");
        $Page->setConfig("prev", "« 上一页");
        $Page->setConfig("next", "下一页 »");
        $Page->setConfig("last", "尾页");
        $Page->setConfig("theme", "<ul class=\"pagination viciao\">%first% %upPage% %linkPage% %downPage% %end%</ul>");
        $show = $Page->show();

        $contracts = $m->where($map)->field('id,app_id,app_name,package,ctime,number,status')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $con = array();
        foreach ($contracts as $v) {
            switch ($v['status']) {
                case self::CONTRACT_NOT_APPLY:
                    $url = U('Contract/edit',array('id'=>$v['id'],'add_edit' => 1));
                    $text = '立即申请';
                    break;
                case self::CONTRACT_APPLY_ING:
                case self::CONTRACT_APPLY_NOT_PASS:
                    $url = U('Contract/edit',array('id'=>$v['id'],'add_edit' => 1,'re_edit' => 1));
                    $text = '修改申请';
                    break;
                case self::CONTRACT_APPLY_SUCCESS:
                    $url = U('Contract/reapply', array('id' => $v['id']));
                    $text = '合同回传';
                    break;
                case self::CONTRACT_CHECK_PASSED:
                case self::CONTRACT_CHECK_ING:
                case self::CONTRACT_TIME_PASSED:
                case self::CONTRACT_CHECK_FAILED:
                    $url = U('Contract/manage', array('id' => $v['id'], 'pkg' => $v['package']), '');
                    $text = '查看合同';
                    break;
                case self::CONTRACT_TIME_PASSED_ISCOMING:
                    $url = U('Contract/reconfirm', array('id' => $v['id']));
                    $text = '续签合同';
                    break;
            }

            $status = ContractModel::getStatus($v['status']);
            $v['status'] = $status;
            $v['url'] = $url;
            $v['text'] = $text;
            
            $con[] = $v;
        }

        unset($contracts);

        $this->assign("name", $name);
        $this->assign("contracts", $con);
        $this->assign("count", $count);
        $this->assign("page", $show);
        $this->display();
    }

    // 合同回传
    public function reapply()
    {
        $m = D($this->model);
        $id = $this->_get('id', 'intval', 0);
        $edit = $this->_get('edit', 'intval', 0);           // 合同修改
        $reassign = $this->_get('reassign','intval',0);     // 合同续签
        $map = array("author_id" => $this->uid, "id" => $id);
        if (empty($id) || !$vo = $m->where($map)->find())
            return $this->error('不存在该合同!');

        $this->assign('reassign', $reassign);
        $this->assign('edit', $edit);
        $this->assign('vo', $vo);
        $this->display();
    }

    public function reapply_post()
    {
        if (!IS_POST) $this->error('页面不存在');
        $m = D($this->model);

        $id = $this->_post('id', 'trim,intval', 0);
        $edit_id = $this->_post('edit_id', 'trim,intval', 0);
        if (empty($id) || !$vo = $m->where(array("author_id" => $this->uid, "id" => $id))->find())
            return $this->error('不存在该合同!');

        // 上传合同文件
        $uploadSetting = array(
            'maxSize' => 20 * 1024 * 1024,   //文件大小限制
            'allowExts' => array('pdf')     //文件类型设置
        );
        $uploadList = helper("Upload")->_upload("user", $uploadSetting);
        if (!is_array($uploadList[0]))
            return $this->error($uploadList);

        $data = array(
            'rtime' => time(),
            'filename' => $uploadList[0]["name"],
            'filepath' => $uploadList[0]["filepath"],
            'status' => self::CONTRACT_CHECK_ING
        );

        $jumpUrl = U('Contract/manage', array('id' => $id,'pkg'=>$vo['package']));
        if ($m->where(array('id' => $id))->save($data)) {
            if ($edit_id == 1) {
                $this->assign('edit', $edit_id);
                $this->assign('vo', $vo);
                $this->display('reapply_online');
                return;
            }
            return $this->success('您的回传合同已经提交成功，请等待审核', $jumpUrl);
        } else {
            return $this->error('您的回传合同提交失败，请重试', $jumpUrl);
        }
    }

    // 合同管理
    public function manage()
    {
        $id = $this->_get('id', 'intval,trim', 0);
        $pkg = $this->_get('pkg', 'trim', '', '');
        if (empty($id) || !$vo = D($this->model)->where(array("author_id" => $this->uid, "id" => $id, 'package' => $pkg))->find())
            return $this->error('不存在该合同!');

        $like = array(self::CONTRACT_CHECK_FAILED, self::CONTRACT_CHECK_ING,
                      self::CONTRACT_CHECK_PASSED, self::CONTRACT_TIME_PASSED_ISCOMING,
                      self::CONTRACT_TIME_PASSED);
        $map['author_id'] = $this->uid;
        $map['package'] = $pkg;
        $map['status'] = array('like', $like, 'OR');
        $contracts = D($this->model)->where($map)->select();

        $this->assign('v', $vo);
        $this->assign('contracts', $contracts);
        $this->display();
    }

    // 合同修改
    public function edit()
    {
        $m = D($this->model);
        $id = $this->_get('id', 'intval,trim', 0);
        $add = $this->_get('add','intval,trim',0);
        $add_edit = $this->_get('add_edit','intval,trim',0);
        $re_edit = $this->_get('re_edit','intval,trim',0);
        if (empty($id) || !$contract = $m->where(array("id" => $id, "author_id" => $this->uid))->find())
            return $this->error('不存在该合同!');

        $this->assign('vo', $contract);
        $this->assign('add', $add);
        $this->assign('add_edit', $add_edit);
        $this->assign('re_edit', $re_edit);
        $this->display();
    }

    // 新增合同
    public function add()
    {
        $this->display();
    }

    // 确认续签合同
    public function reconfirm(){
        $m = D($this->model);
        $id = $this->_get('id','trim,intval',0);
        if (empty($id) || !$contract = $m->where(array("id" => $id, "author_id" => $this->uid))->find())
            return $this->error('不存在该合同!');

        $this->assign('id',$id);
        $this->display('confirm_reapply');
    }

    public function reconfirm_post(){
        $m = D($this->model);
        $id = $this->_post('id','trim,intval',0);
        $map = array("id" => $id, "author_id" => $this->uid);
        if (empty($id) || !$vo = $m->where($map)->find())
            return $this->error('此合同不能续签');

        // 合同续签
        $now = time();
        $m->where($map)->setField('xutime',$now); // 设置续签时间
        $start = $now > $vo['etime'] ? $now:$vo['etime'];
        $data = array(
            'pid' => $id,
            'name' => '《'.$vo['app_name'].'》续签合同',
            'type' => 1,
            'status' => self::CONTRACT_APPLY_SUCCESS,
            'app_id' => $vo['app_id'],
            'author_id' => $this->uid,
            'receipt_id' => $vo['receipt_id'],
            'join_id' => $vo['join_id'],
            'app_name' => $vo['app_name'],
            'package' => $vo['package'],
            'account_name' => $vo['account_name'],
            'account_key' => $vo['account_key'],
            'account_bank' => $vo['account_bank'],
            'number' => $vo['number'],
            'ctime' => $now,
            'vtime' => $now,
            'etime' => $start + self::ONEYEAR,
            'share' => $vo['share'],
            'company_name' => $vo['company_name'],
            'area' => $vo['area'],
            'province' => $vo['province'],
            'city' => $vo['city'],
            'address_detail' => $vo['address_detail'],
            'contact' => $vo['contact'],
            'contact_email' => $vo['contact_email'],
            'checker' => $vo['checker']
        );
        if($rid = $m->add($data)){
            return $this->success('提交续签合同申请成功!',U('contract/reapply',array('id'=>$rid,'reassign'=>1)));
        }else{
            D("Syserror")->error("生成续签合同出错", M('contract')->getDbError(), M('contract')->_sql(), 0, "2");
            return $this->error('申请失败，请稍后重试!');
        }
    }

    // 保存合同信息
    public function save()
    {
        $m = D($this->model);
        $edit_id = $this->_post('id', 'trim,intval', 0);        // 修改的合同的编号
        $add = $this->_post('add', 'trim,intval', 0);           // 新增一条合同
        $add_edit = $this->_post('add_edit', 'trim,intval', 0); // 未确认合同 | 合同申请还未通过时用户修改了合同
        $re_edit = $this->_post('re_edit', 'trim,intval', 0);   // 此处为合同申请还未通过时用户修改了合同
        $vo = $m->where(array("id"=>$edit_id, "author_id"=>$this->uid))->find();
        if($edit_id > 0 && $edit_id != $vo["id"])
        	return $this->error('不存在该合同!');

        $post = $this->_post();
        $post = array_map('trim', $post);

        $app_name = $this->_post('app_name', 'trim,strip_tags', "");
        $package = $this->_post('package', 'trim,strip_tags', "");
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
        $receipt_id = $this->_post('receipt_id', 'trim,strip_tags', "");
        $app_type = $this->_post('app_type', 'intval', "0");

        // 首先判空处理
        empty($app_name) && $this->error('游戏名称不能为空!');
        empty($package) && $this->error('包名不能为空!');
        empty($account_name) && $this->error('开户名不能为空!');
        empty($account_bank) && $this->error('开户银行不能为空!');
        empty($account_key) && $this->error('银行账号不能为空!');
        empty($company_name) && $this->error('公司名称不能为空!');
        empty($area) && $this->error('所处地区不能为空!');
        empty($province) && $this->error('所在省份不能为空!');
        empty($city) && $this->error('所在城市不能为空!');
        empty($address_detail) && $this->error('详细地址不能为空!');
        empty($contact) && $this->error('联系人不能为空!');
        empty($contact_email) && $this->error('联系人邮箱不能为空!');
        empty($receipt_id) && $this->error('发票比例不能为空!');

        // 联运网游需要进行包名正则判断
        if ($app_type == 0 && !preg_match("@^[a-zA-Z0-9\-\.]+\.am$@is", $post["package"]))
            $this->error("应用包名不正确，包名必须以 .am 结尾");
        if (strlen($post["package"]) < 4 || strlen($post["package"]) > 200)
            $this->error("应用包名长度不正确， 只支持3-200字符");
        
        //重复包名检查
        $package_ids = $m->where(array("package"=>$package, "author_id"=>$this->uid))->getField('id,package', true);
        unset($package_ids[$edit_id]);

        if(count($package_ids) > 0)
        	return $this->error("应用包名已存在");

        // 生成插入数据
        $nowtime = time();
        $joiner = M('contract_contact')->where(array('area' => $area, 'status'=>1))->field('id,name')->find();
        if (!$joiner) $this->error('暂时不提供您所在地区的服务');
        $join_id = $joiner['id'];
        $checker = $joiner['name'];

        $data = array(
            'name' => '《' . $app_name . '》电子合同',
            'app_id' => 0,
        	'app_type' => $app_type,
            'author_id' => intval($this->uid),
            'receipt_id' => intval($receipt_id),
            'join_id' => intval($join_id),
            'join_name' => $checker,
            'app_name' => $app_name,
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

        // 更新联系人表的地址
        $userData = array('address' => $province . $city . $address_detail);
        M('account_infos')->where(array('account_id' => $this->uid))->save($userData);

        if($add == 1){ // 合同状态为 审核通过且点击修改,此时保存原合同状态
            $data['status'] = self::CONTRACT_APPLY_SUCCESS;
            $data['number'] = $vo['number'];
            $data['ctime'] = $nowtime;
            $data['vtime'] = $nowtime;
            $data['etime'] = $vo['etime']; // 合同修改通过后，有效期不变
            $data['package'] = $package;
            $msg = '合同修改成功,请确认并尽快回传合同!';
            if($cid = $m->add($data)){
                return $this->success($msg, U('contract/reapply', array('id' => $cid, 'edit' => 1)));
            }else {
                D("Syserror")->error("合同状态为:审核通过,且点击修改,此时保存原合同状态出错", M('contract')->getDbError(), M('contract')->_sql(), 0, "2");
                return $this->error('修改失败，请稍后重试!');
            }
        }
        elseif ($edit_id > 0) { // 编辑状态
            // 此处解决：当合同状态为审核通过的时候，不能通过地址栏访问，直接就修改合同
            if($vo['status'] == self::CONTRACT_CHECK_PASSED){
                return $this->error('您的合同已经审核通过，不能再编辑了！');
            }

            $data['status'] = self::CONTRACT_APPLY_ING;
            $data['number'] = $vo['number'];
            $data['ctime'] = $vo['ctime'];
            $data['vtime'] = $vo['vtime'];
            $data['etime'] = $vo['etime'];
            $data['package'] = $package;
            $msg = '合同修改成功,请尽快回传合同!';
            if($res = $m->where(array('id'=>$edit_id))->save($data)){
                if($add_edit == 1){
                    // 此种情况为用户未保存合同时，再点立即申请
                    $data['status'] = self::CONTRACT_NOT_APPLY;
                    $m->where(array('id'=>$edit_id))->save($data);
                    if($re_edit == 1){ // 此种情况为合同状态为申请中，用户点了 重新修改,跳转后不生成新的合同编号
                        return $this->success('合同修改成功，请确认合同信息!', U('contract/confirm', array('id' => $edit_id,'re_edit'=>1)));
                    }else{
                        return $this->success('合同修改成功，请确认合同信息!', U('contract/confirm', array('id' => $edit_id)));
                    }
                }
                else
                    return $this->success($msg, U('contract/reapply', array('id' => $edit_id, 'edit' => 1)));
            }else {
            	
            	if($res == 0)
            	{
            		return $this->error('合同没有任何变化，请重新编辑!');
            	}else{
	                D("Syserror")->error("修改合同时出错", M('contract')->getDbError(), M('contract')->_sql(), 0, "2");
	                return $this->error('合同修改失败，请稍后重试!');
            	}
            }
        } else { // 新增合同
            $data['status'] = self::CONTRACT_NOT_APPLY;
            $data['number'] = '';
            $data['ctime'] = $nowtime;
            $data['vtime'] = $nowtime;
            $data['etime'] = $nowtime + self::ONEYEAR;
            $data['package'] = $package;
            $msg = '合同提交成功,请确认合同信息!';
            // 插入数据库
            if ($cid = $m->add($data)) {
                return $this->success($msg, U('contract/confirm', array('id' => $cid)));
            } else {
                D("Syserror")->error("新增合同时出错", M('contract')->getDbError(), M('contract')->_sql(), 0, "2");
                return $this->error('合同提交失败，请稍后重试!');
            }
        }
    }

    // 确认合同信息
    public function confirm()
    {
        // 获取参数并过滤
        $id = $this->_get('id', 'trim', 0);
        $re_edit = $this->_get('re_edit','intval,trim',0); // 合同为申请中时修改了合同，不生成合同编号

        if (!isset($id)) $this->error('请先填写合同申请!');
        $contractInfo = M('contract')->where(array('id' => $id, 'author_id' => $this->uid))->find();
        if (!$contractInfo) $this->error('不存在此合同信息!');

        // 签署日期
        $ctime = $contractInfo['ctime'];
        $ctime2 = date('Y/m/d', $contractInfo['ctime']);
        $y = date('Y', $ctime);
        $m = date('m', $ctime);
        $d = date('d', $ctime);
        $ctime = $y . ' 年 ' . $m . ' 月 ' . $d . ' 日';

        // 金立对接人
        $jinliInfo = M('contract_contact')->where(array('id' => $contractInfo['join_id']))->field('status,adder,ctime', true)->find();
        $jinliName = $jinliInfo['name'];
        $jinliEmail = $jinliInfo['email'];

        // 签约公司
        $company = $contractInfo['company_name'];
        $address = $contractInfo['province'].$contractInfo['city'].$contractInfo['address_detail'];
        $contact = $contractInfo['contact'];
        $contact_email = $contractInfo['contact_email'];

        // 游戏信息
        $app_name = $contractInfo['app_name'];
        $package = $contractInfo['package'];

        // 分成比例
        $share = $contractInfo['share'];
        // 发票类型
        $receipt_id = intval($contractInfo['receipt_id']);
        switch ($receipt_id) {
            case 1:
                $receiptType = 'A';
                break;
            case 2:
                $receiptType = 'B';
                break;
            case 3:
                $receiptType = 'C';
                break;
        }

        // 银行账户信息
        $account_name = $contractInfo['account_name'];
        $account_key = $contractInfo['account_key'];
        $account_bank = $contractInfo['account_bank'];


        $this->assign('id', $id);
        $this->assign('re_edit',$re_edit);
        $this->assign('jinliName', $jinliName);
        $this->assign('jinliEmail', $jinliEmail);
        $this->assign('company', $company);
        $this->assign('address', $address);
        $this->assign('contact', $contact);
        $this->assign('contact_email', $contact_email);
        $this->assign('app_name', $app_name);
        $this->assign('package', $package);
        $this->assign('share', $share);
        $this->assign('receiptType', $receiptType);
        $this->assign('account_name', $account_name);
        $this->assign('account_bank', $account_bank);
        $this->assign('account_key', $account_key);
        $this->display();
    }

    /**
     * 1、点击“提交并继续”后，自动生成加盖电子章的PDF文档；生成合同编号；
     * 2、合同申请后，平台自动发邮件通知金立对接商务。
     */
    // 确认提交合同,只有第一次新增合同才会到这一步确认
    public function subconfirm()
    {
        if (!IS_POST) $this->error('页面不存在');
        if (!isset($_POST['agree'])) {
            $this->error('您必须阅读并同意签署以上合同内容');
        }

        $id = $this->_post('id', 'trim,intval', 0);
        $re_edit = $this->_post('re_edit', 'trim,intval', 0);


        $m = M('contract');
        $contract = $m->where(array('id' => $id, 'author_id' => $this->uid))->find();
        if (!$contract) $this->error('不存在此合同!');

        // 检查重复提交
        if(intval($contract['status']) != 0){
            return $this->error('不能重复提交合同!');
        }

        // 新增的信息
        if($re_edit == 1) // 合同状态为申请中且点击了重新修改，此时不生成新的合同编号
            $number = $contract['number'];
        else
            $number = $this->getIndex();
        $now = time();
        $data = array(
            'ctime' => $now,
            'vtime' => $now,
            'etime' => $now + self::ONEYEAR,
            'number' => $number,
            'status' => self::CONTRACT_APPLY_ING // 申请中
        );

        // 发邮件
        $account = M('accounts')->where(array('id' => $contract['account_id']))->field('email')->find();
        $nowtime = date('Y-m-d H:i:s', time());
        $joiner = M('contract_contact')->where(array('id' => $contract['join_id']))->field('email')->find();
        $sendemail = $joiner['email'];
        $subject = '《'.$contract['app_name'].'》合同申请！';
        $body = '账号为' . $contract['contact_email'] . '的用户于' . $nowtime . '向您提交了合同申请，具体申请信息：<br><br>

        合同编号：' . $number . '<br>
        游戏名称：' . $contract['app_name'] . '<br>
        游戏包名：' . $contract['package'] . '<br>
        账号：' . $account['contact_email'] . '<br>
        公司名称：' . $contract['company_name'] . '<br>
        联系人：' . $contract['contact'] . '<br>
        联系人邮箱：' . $contract['contact_email'] . '<br>

        请及时登录审核后台进行审核，地址：http://admin.dev.game.gionee.com/

        时间：' . $nowtime . '<br>
        金立游戏开发者平台';

        try {
            $sendStatus = smtp_mail($sendemail, $subject, $body);
        } catch (Exception $e) {

        }

        // 保存合同
        $save = $m->where(array('id' => $contract['id']))->save($data);
        if ($save) {
            $this->assign('number', $number);
            $this->display('success');
        } else {
            D("Syserror")->error("申请合同出错", M('contract')->getDbError(), M('contract')->_sql(), 0, "2");
            return $this->error('提交失败,请重试');
        }
    }

    // 合同展示页面
    public function show()
    {
        if (!isset($_GET['id'])) $this->error('此页面不存在！');

        $id = $this->_get('id', 'trim', 0);
        $contract = M('contract')->where(array('id' => $id, 'author_id' => $this->uid))->find();
        if ($contract == false) $this->error('不存在此合同!');

        D($this->model)->show($contract);
    }

    // 合同续签展示页面
    public function reshow()
    {
        if (!isset($_GET['id'])) $this->error('此页面不存在！');
        $m = M('contract');
        $id = $this->_get('id', 'trim', 0);
        if (empty($id) || !$contract = $m->where(array('id' => $id, 'author_id' => $this->uid))->find())
            return $this->error('不存在该合同!');

        D($this->model)->reshow($contract);
    }

    // 获取当天合同的index
    public function getIndex()
    {
        $m = M('think_config');

        $today = date('Ymd', time());
        $name = 'GN' . $today . '-';
        $map = array('name' => $name);

        $m->startTrans();
        $todayIndex = $m->where($map)->lock(true)->find();
        if (!$todayIndex) {
            $r = $name . '01';
            $res = $m->add(array('name' => $name, 'value' => 2));
        } else {
            $index = intval($todayIndex['value']);
            $index = sprintf('%02d', $index);
            $m->where($map)->setInc('value', 1);
            $r = $name . $index;
        }
        $m->commit();
        return $r;
    }
}