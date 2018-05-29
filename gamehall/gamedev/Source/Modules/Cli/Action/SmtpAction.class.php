<?php
/**
 * 定时发送系统
 * @author shuhai
 */
class SmtpAction extends CliBaseAction
{
	function index()
	{
		$this->printf("php cli.php Smtp everyDayReport 每日工作报告");
	}

    function everyDayReport()
    {
        $this->setExpire();
        $this->appReport();
        $this->contractReport();
        $this->checkExpire();
    }

    function contractReport()
    {
        $m = D('Contract');
        $emails = M('Contract_contact')->field('email,area')->where(array('status'=>1))->select();
        if(empty($emails))
            $emails = array('yxdt@gionee.com');

        $start_time = strtotime(date("Y-m-d 17:00:00", strtotime("-1 day")));
        $end_time = strtotime(date("Y-m-d 17:00:00"));
        $subject = sprintf("%s 开发者平台合同申请情况", date("Y-m-d"));

        foreach($emails as $email){
            $map['area'] = $email['area'];
            $map['ctime'] = array(array("egt",$start_time),array("lt",$end_time));
            $this->printf("TodayInfo’s WHERE : ".json_encode($map));
            $todayInfo = $m->where($map)->select();
            $this->assign('todayInfo',$todayInfo);

            $where['ctime'] = array('lt',$end_time);
            $this->printf("HistoryInfo’s WHERE : ".json_encode($where));
            $historyInfo = $m->where($where)->select();
            $this->assign('historyInfo',$historyInfo);

            $body = $this->fetch('contract_list');
           // $this->printf("Email {$email} send result : {$body} ");

            $res = smtp_mail($email['email'], $subject, $body);
            $this->printf("Email {$email['email']} send result : {$res} ");
        }

    }

	function appReport()
	{
		$email = C('DAY_REPORT_EMAIL');
		$this->printf("Emails found : ".join(", ", $email));

		if(empty($email))
			$email = array('yxdt@gionee.com');
		if(is_string($email))
			$email = array($email);
		
		$start_time = strtotime(date("Y-m-d 18:00:00", strtotime("-1 day")));
		$end_time = strtotime(date("Y-m-d 18:00:00"));
		
		$where = array("created_at"=>array(array("egt", $start_time), array("lt", $end_time)));
		$this->printf("WHERE : ".json_encode($where));
		
		//联运应用审核状态
		$status = range(-4, 3);
		$union_apps = M("union_apps")->where($where)->group('status')->getField('status,count(*) as ct', true);
		foreach ($status as $_st)
			$union_apps[$_st] = $union_apps[$_st] ? $union_apps[$_st] : 0;
		
		$union_apps_all = M("union_apps")->group('status')->getField('status,count(*) as ct', true);
		foreach ($status as $_st)
			$union_apps_all[$_st] = $union_apps_all[$_st] ? $union_apps_all[$_st] : 0;
		
		//key已审核=审核通过+审核不通过
		$union_apps["2"] = $union_apps["-1"] + $union_apps["1"];
		$union_apps_all["2"] = $union_apps_all["-1"] + $union_apps_all["1"];
		
		$union_apps["total"] = M("union_apps")->where($where)->count();
		$union_apps_all["total"] = M("union_apps")->count();
		
		$this->is_union = $is_union = array(2=>"普通应用", 1=>"联运应用");
		foreach ($is_union as $union=>$union_name) {
			
			$apks = $apps = $apps_all = $apks_all = array();
			$unionWhere = array("is_join" => $union);
			$where = array_merge($where, $unionWhere);
			
			//应用总数
			$apps["total"] = M("apks")->where($where)->count('distinct app_id');
			$apps_all["total"] = M("apks")->where($unionWhere)->count('distinct app_id');
			
			//版本状态
			$status = range(-4, 3);
			$apks = M("apks")->where($where)->group('status,is_join')->getField('status,count(*) as ct', true);
			$apks_all = M("apks")->where($unionWhere)->group('status,is_join')->getField('status,count(*) as ct', true);
	
			foreach ($status as $_st)
			{
				$apks[$_st] = $apks[$_st] ? $apks[$_st] : 0;
				$apks_all[$_st] = $apks_all[$_st] ? $apks_all[$_st] : 0;
			}
			
			//版本总数
			$apks["total"] = M("apks")->where($where)->count();
			$apks_all["total"] = M("apks")->where($unionWhere)->count();
			
			//app审核通过=审核通过+已上线，已审核=审核通过+审核不通过
			$apks["2"] += $apks["3"];
			$apks_all["2"] += $apks_all["3"];
			$apks["-2"] = $apks["-2"] + $apks["-3"] + $apks["-4"];
			$apks_all["-2"] = $apks_all["-2"] + $apks_all["-3"] + $apks_all["-4"];
			
			$apks["4"] = $apks["2"] + $apks["-1"];
			$apks_all["4"] = $apks_all["2"] + $apks_all["-1"];
			
			//统计账号审核通过，且应用还未审核的数量
			$apks["1"] = $apks_all["1"] = 0;
			$author_ids = D("AccountInfos")->where(array("status"=>2))->getField('account_id', true);
			if(!empty($author_ids)) {
				$map = array("author_id"=>array("in", $author_ids), "status"=>1);
				$apks_all["1"] = M("apks")->where($map)->count();
					
				$map = array_merge($where, $map);
				$apks["1"] = M("apks")->where($map)->count();
			}
			
			$_apks[$union] = array_map('intval', $apks);
			$_apks_all[$union] = array_map('intval', $apks_all);
			$_apps[$union] = array_map('intval', $apps);
			$_apps_all[$union] = array_map('intval', $apps_all);
		}

		$this->assign("_apks", $_apks);
		$this->assign("_apks_all", $_apks_all);
		$this->assign("_apps", $_apps);
		$this->assign("_apps_all", $_apps_all);

		//数字统计
		$this->union_apps = array_map('intval', $union_apps);
		$this->union_apps_all = array_map('intval', $union_apps_all);

		$body = $this->fetch("sumary");
		
		//审核通过、未通过、已上线
		$chkapps = $where = array();
		$where["status"] = -1;
		$where["checked_at"] = array(array("egt", $start_time), array("lt", $end_time));
		$chkapps["-1"] = M("apks")->where($where)->select();
		foreach ($chkapps["-1"] as $k=>$v) {
			$optlog = D("Admin://Optlog")->where(array("apk_id"=>$v["id"]))->order(array("id"=>"desc"))->find();
			if($optlog["reason_id"])
				$optlog["remarks"] .= D("Reason")->where(array("reason_id"=>$optlog['reason_id']))->getField('reason_content');

			$chkapps["-1"][$k]["remarks"] = $optlog["remarks"];
		}

		$where["status"] = 2;
		$chkapps["2"] = M("apks")->where($where)->select();

		$where = array();
		$where["status"] = array("gt", "2");
		$where["onlined_at"] = array(array("egt", $start_time), array("lt", $end_time));
		$chkapps["3"] = M("apks")->where($where)->select();
		
		$this->chkapps_type = array("-1"=>"审核未通过", "2"=>"审核通过", "3"=>"已上线");
		$this->chkapps = $chkapps;
		$body = $subject = sprintf("%s 开发者平台应用审核情况", date("Y-m-d"));
		$body = $this->fetch("sumary");
		
		foreach ($email as $e)
		{
			$res = smtp_mail($e, $subject, $body);
			$this->printf("Email {$e} send result : {$res} ");
		}
		
		if(!empty($chkapps["-1"]) || !empty($chkapps["2"]) || !empty($chkapps["3"])) {
			$body = $subject = sprintf("%s 游戏测试结果汇总", date("Y-m-d"));
			$body = $this->fetch("detail_list");
			
			foreach ($email as $e)
			{
				$res = smtp_mail($e, $subject, $body);
				$this->printf("Email {$e} send result : {$res} ");
			}
		}else{
			$this->printf("%s : detail list is null", date("Y-m-h H:i:s"));
		}
		
		$log = sprintf("everyDayReport %s send finish", date("Y-m-h H:i:s"));
		$this->printf($log);
	}
	
	function test()
	{
		$email = $this->_get("email","trim", "admin@4wei.cn");
		$result = smtp_mail("admin@4wei.cn", "test", "test");
		var_dump($result);
	}

    function checkExpire()
    {
        $m = D('Contract');
        $now = time();
        $year = date('Y',$now);
        $month = date('m',$now);
        $day = date('d',$now);
        $contracts = $m->where(array('status'=>5))->field('app_name,contact_email')->select();
        foreach($contracts as $v){
            $email = $v['contact_email'];
            $app_name = $v['app_name'];
            $subject = sprintf("《%s》即将到期!",$app_name);
            $body = sprintf("尊敬的开发者您好，您在金立游戏开发者平台的应用《%s》所签署的合同即将到期，请及时续签合同，否则影响正常的结算！“

金立游戏开发者平台

%s年%s月%s日",$app_name,$year,$month,$day);
            $res = smtp_mail($email, $subject, $body);
            $this->printf("Email {$email} send result : {$res} ");
        }
    }

    function setExpire()
    {
        $now = time();
        $m = D('Dev://Contract');
        $contracts = $m->where(array('status'=>'4','hide' => 0))->field('id,etime')->select();
        foreach($contracts as $v){
            $delta = $v['etime'] - $now;
            if($delta > 0 && $delta < 2592000){
                $status = 5;  // 即将到期
            }elseif ($delta <= 0){
                $status = -3;  // 已过期
            }else{
                continue;
            }
            $m->where(array('id'=>$v['id']))->setField('status',$status);
        }
    }
}