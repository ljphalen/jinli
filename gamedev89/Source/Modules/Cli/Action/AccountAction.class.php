<?php
class AccountAction extends CliBaseAction
{
	function index()
	{
		$this->createUser();
	}
	
	function createUser()
	{
		$index = range(1, 20);
		
		foreach ($index as $no)
		{
			$email = sprintf("youxi%03d@126.com", $no);
			if(D("Accounts")->where(array("email"=>$email))->find())
				continue;
			
			$data = array('email'=>$email, "nickname"=>$email, "crypted_password"=>"87319cc959199cd40e06a83441508b8469ee90ae", "salt"=>"a3e1c41deb29c8f140bf28cd6a7cf9f78f87fb14", "status"=>1);
			$account_id = D("Accounts")->data($data)->add();
			
			if(empty($account_id))
				continue;
			
			D("AccountTax")->data(array("account_id"=>$account_id))->add();
			
			$data = array("status"=>2, "account_id"=>$account_id, "company"=>"{$no}.com", "phone"=>13100000000, "contact_email"=>$email);
			D("AccountInfos")->data($data)->add();
			
			echo "{$email} done\n";
		}
	}
}