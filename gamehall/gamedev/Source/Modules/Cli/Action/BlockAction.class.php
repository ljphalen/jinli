<?php
/**
 * 用户解封
 * @author jiazhu
 * @version 2014-01-04
 *
 */
class BlockAction extends CliBaseAction
{
	
	/*
	 * 解封用户
	 */
	public function deblock()
	{
		
		$accounts_m = D('Dev://Accounts');
		$blocklog_m = D('Dev://Blocklog');
		
		$this->printf("--- block user start ---");
		
		while (1)
		{
			$map = array(
							'status' => AccountsModel::STATUS_CLOSE,  
							'deblock_time' => array('elt',time()),
			 );
			 $user_list = $accounts_m->where($map)->order('id asc')->limit(0,100)->select();
			 if (!empty($user_list))
			 {
			 	foreach ($user_list as $key => $val)
			 	{
			 		$data = array(
						'account_id' => $val['id'],
						'status' => BlocklogModel::STATUS_DEBLOCK,
						'admin_id' =>  0,
						'add_time' => time(),
						'remarks' => '系统解封',
					);
			 		$accounts_m->doBlock($val['id'],BlocklogModel::STATUS_DEBLOCK,$data);
			 		$this->printf("--{$val['id']} done--");
			 	}
			 	
			 }else 
			 {
			 	break;
			 }
		}
		$this->printf("--- block user done ---");
		exit(0);
	}
}