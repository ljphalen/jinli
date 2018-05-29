<?php
/**
 * 用户消息表
 * @author jiazhu
 *
 */
class AccountMessageModel extends RelationModel 
{
	protected $trueTableName = 'account_message';
	
	 //读取状态 0:未读 1：已读 
	 CONST READ_STATE_INIT = 0;
	 CONST READ_STATE_SUC = 1;
	 
	  protected $_auto = array ( 
        array('add_time','time',1,'function'), 		
    );
}