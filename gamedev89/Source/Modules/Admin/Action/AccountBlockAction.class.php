<?php
class AccountBlockAction extends SystemAction 
{
	public $model='accounts';
	public function _after_index(& $voList)
	{
		if (is_array($voList))
		{
			//完善封号数据
			foreach ($voList as $key=> $val)
			{
				if ($val['status'] == AccountsModel::STATUS_CLOSE)
				{
					$block_log = D('Blocklog')->where(' account_id='.$val['id'].' and status='.BlocklogModel::STATUS_BLOCK)->order('id desc')->find();
					if ($block_log)
					{
						$admin_info = D('Admin')->find($block_log['admin_id']);
						$block_log['admin_name'] = $admin_info['nickname'];
					}
					$voList[$key]['block_info'] = $block_log;
					
				}
			}
		}
	}
	
	/**
	 *  封号
	 */
	public function doBlock()
	{
		$id = $this->_get('id');
		$account = D('Dev://Accounts')->find($id);
		$this->assign('account',$account);
		$this->display();
	}
	
	public function deBlock()
	{
		$id = $this->_get('id');
		$account = D('Dev://Accounts')->find($id);
		
		//获得帐号封号日志
		$block_log = D('Blocklog')->where(' account_id='.$id.' and status='.BlocklogModel::STATUS_BLOCK)->order('id desc')->find();
		
		$this->assign('account',$account);
		$this->assign('block_log',$block_log);
		$this->display();
	}
	
	/**
	 *  封号保存
	 */
	public function doBlockSave()
	{
		$id = $this->_post('id');
		$remarks = $this->_post('remarks');
		$status = $this->_post('status');
		
		$data = array(
			'account_id' => $id,
			'status' => $status,
			'admin_id' =>  $_SESSION['authId'],
			'add_time' => time(),
			'deblock_time' => (int) $this->_post('deblock_time'), 
			'remarks' => $status == BlocklogModel::STATUS_BLOCK?BlocklogModel::getReason($remarks):$remarks,
		);
		if ($remarks == 100)
		{
			$data['remarks'] = $this->_post('reason_content');
		}
		//var_dump($data);exit;
		//解封时 或 解封时间为2020年（永久），状态标记为已处理
		if ($status == BlocklogModel::STATUS_DEBLOCK || ($status == BlocklogModel::STATUS_BLOCK && $deblock_time==1577808000) )
		{
			$data['deblock_status'] = 1;
		}
		
		D('Dev://Accounts')->doBlock($id,$status,$data);
		$this->success('操作成功!');
		
	}
}