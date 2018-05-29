<?php
class SmtpAction extends CliBaseAction
{
	function test()
	{
		$email = $this->_get("email","trim", "admin@4wei.cn");
		$result = smtp_mail("admin@4wei.cn", "test", "test");
		var_dump($result);
	}
}