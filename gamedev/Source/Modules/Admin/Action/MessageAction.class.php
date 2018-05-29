<?php
/**
 * 后台消息管理
 * @author jiazhu
 */
class MessageAction extends SystemAction
{
	public $model = 'Message';
	
	public function _before_index() {
		
	}
	
	public function add()
	{
		$this->display();
	}
	
	public function addSave()
	{
		$id = $this->_post('id');
		$data = $this->_post();
		if ($data['receiver_account'] != MessageModel::RECEIVE_ALL)
		{
			$file_content = file_get_contents($_FILES['file_path']['tmp_name']);
			if (empty($file_content))
				$this->error('导入文件为空');

			if($_FILES['file_path']['type'] != "text/plain")
				$this->error('只能上传文本文件');
				
			$email_list = explode("\n", $file_content);
			$id_array= array();
			foreach ($email_list as $key => $val)
			{
				$val = trim($val);
				if (empty($val))
					continue;

				//与账号相关的（重置密码、审核注册账号）——注册邮箱
				//与应用相关的（审核应用，上下线收到邮件，发布消息）——联系人邮箱
				$user_info = D('Dev://Accounts')->getUserByEmail($val);
				if(!empty($user_info['id']))
					$id_array[] = $user_info['id'];
			}

			if (empty($id_array))
				$this->error('没有找到合法的邮箱地址');
			
			$id_str = implode(',', $id_array);
			$_POST['receiver_account'] = $id_str;
			
		}
		if ($data['send_type'] == '')
		{
			$_POST['send_type'] = MessageModel::SEND_MESSAGE;
		}
		
		$model = D ($this->model);
		$res =  $model->create();
		
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$model->content = nl2br($this->_post('content'));
		//保存当前数据对象
		if ($id)
		{
			$list = $model->save();
		}else 
		{
			$list=$model->add();
		}
		if ($list !== false)
		{
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('操作成功!', 'closeCurrent');
		} else {
			$this->log_error($model->getDbError());
			$this->error ('操作失败!');
		}
	}
}