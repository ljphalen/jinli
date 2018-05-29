<?php
/**
 * 用户消息
 * @author jiazhu
 *
 */
class MessageAction extends BaseAction
{
	/*
	 * 消息列表
	 */
	public function index()
	{
		import ( 'ORG.Util.Page' ); // 导入分页类
		$state = $this->_get('state');
		
		$message_m = D('Dev://Message');
		$account_message = D('Dev://AccountMessage');
		
		$map = array('account_id' => $this->uid);
		if ($state != null) {
			$map['read_state'] = $state;
		}
		$count = $account_message->where($map)->count();
		
		$Page = new Page ( $count, $this->pageSize ); // 实例化分页类 传入总记录数
		$Page->setConfig ( "prev", "«" );
		$Page->setConfig ( "next", "»" );
		$Page->setConfig ( "theme", "<ul class=\"pagination\">%upPage% %linkPage% %downPage%</ul>" );
		$show = $Page->show (); // 分页显示输出
		$list = $account_message->where($map)->order('id desc')->limit ( $Page->firstRow . ',' . $Page->listRows )->select ();
		if (is_array($list))
		{
			foreach ($list as $key => $val)
			{
				//追加信息内容
				$list[$key]['message_info'] = $message_m->find($val['message_id']);
			}
		}
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign ( 'page', $show ); // 赋值分页输出
		$this->display();
	}
	
	/*
	 * 设为已读
	 */
	public function read()
	{
		$id = $this->_post('id');
		$account_message = D('Dev://AccountMessage');
		$map = array('id' => $id, 'account_id' => $this->uid);
		$res = $account_message->where($map)->save(array('read_state' => AccountMessageModel::READ_STATE_SUC,'read_time' =>  time()));
		echo (int) $res;
		exit();
	}
	
	/*
	 * 删除
	 */
	public function del()
	{
		
		$id = $this->_post('id');
		$id_arr  = explode(',', $id);
		$account_message = D('Dev://AccountMessage');
		$map = array('id' => array('in',$id_arr), 'account_id' => $this->uid);
		$res = $account_message->where($map)->delete();
		echo (int) $res;
		exit();
	}
}