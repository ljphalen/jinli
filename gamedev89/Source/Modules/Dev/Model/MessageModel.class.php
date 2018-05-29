<?php
class MessageModel extends Model
{
	protected $trueTableName = 'message';
	
	 //消息类型 1.系统通知 
	 CONST TYPE_SYS = 1;
	 
	 //消息发送模式 1：仅发站内心 2.站内信，邮件 3邮件
	 CONST SEND_MESSAGE = 1;
	 CONST SEND_ALL = 2;
	 CONST SEND_EMAIL = 3;
	 
	 //接收用户 all：所有用户；多个用户id以逗号分割 
	 CONST RECEIVE_ALL = 'all';
	 
	 //消息状态 0：未发送 1：发送中 2 ：发送完成  -1：删除
	 CONST STATE_INIT = 0;
	 CONST STATE_LOAD = 1;
	 CONST STATE_COMPLATE = 2;
	 CONST STATE_DEL = -1;
	 
	 
	 protected $_auto = array ( 
        array('add_time','time',1,'function'), 		
    );
    
    /**
     * 获得消息状态
     * @param int $state
     */
    public static function getState($state=null)
    {
    	$arr = array(
    		self::STATE_INIT => '未发送',
    		self::STATE_LOAD => '发送中',
    		self::STATE_COMPLATE => '发送完成',
    		self::STATE_DEL => '删除',
    	);
    	if ($state ===NULL)
    	{
    		return $arr;
    	}else 
    	{
    		return $arr[$state];
    	}
    }
    
    /**
     * 获取发送模式
     * @param unknown_type $type
     */
    public function getType($type=null)
    {
    	$arr = array(
    		self::SEND_MESSAGE => '仅站内信',
    		self::SEND_ALL => '站内信，邮件',
    		self::SEND_EMAIL => '仅邮件',
    	);
    	
    	if ($type ===NULL)
    	{
    		return $arr;
    	}else 
    	{
    		return $arr[$type];
    	}
    }
    
    /**
     * 发送站内消息
     * @param  string|array $user 接收用户
     * @param string title '标题', 
     * @param string 'content => '内容')
     * @param int $state  	消息发送模式 1：仅发站内心 2.站内信，邮件 3邮件 
     */
    public static function send($account,$title,$content, $send_type=self::SEND_MESSAGE )
    {
    	if (empty($account)) return false;
    	if (is_array($account)) $account = implode(',', $account);
    	$data = array(
    		'title' => $title,
    		'content' => $content,
    		'send_type' => $send_type ,
    		'receiver_account' => $account,
    		'add_time' => time(),
    	);
    	$res = D('Message')->data($data)->add();
    	return $res;
    }
}