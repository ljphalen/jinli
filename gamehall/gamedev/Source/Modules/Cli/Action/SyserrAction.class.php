<?php
/**
 * 系统异常通过邮箱发送通知
 * 根据需要执行 php cli.php Sync get
 */
class SyserrAction extends CliBaseAction
{
	function index()
	{
		$model = D("think_syserror");
		$logs = $model->where(array("email"=>"0", "status"=>"0"))->select();
		
		$email = C('SYSERR_REPORT_TO');
		if(empty($email))
			$email = array('kfzpt@gionee.com', 'admin@4wei.cn');
		if(is_string($email))
			$email = explode(',', $email);

		$email = (array)$email;
		foreach ($logs as $log) {
			$title = sprintf("[%s]%s, %s", $log["level"] > 1 ? "严重错误" : "错误", $log["title"], date("Y-m-d H:i", $log["created_at"]));
			$body = $log["body"];
			if(!empty($log["app_id"]))
				$body .= sprintf("<br />涉及应用：%s", D("Apps")->where(array("id"=>$log["app_id"]))->getField('app_name'));
			if(!empty($log["fix"]))
				$body .= sprintf("<br />解决办法：%s", $log["fix"]);
			
			foreach ($email as $mail)
				smtp_mail($mail, $title, $body);
			
			$model->save(array("id"=>$log["id"], "email"=>1));
			$this->printf("error id:%d send", $log["id"]);
		}
		
		$this->printf("email send done");
	}
}