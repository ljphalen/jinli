<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
 * 
 * Feedback_Dao_Feedback
 * @author luojiapeng
 * 
 */
class Feedback_Dao_FeedbackReply extends Common_Dao_Base{
	protected $_name = 'game_client_feedback_reply';
	protected $_primary = 'feedback_id';
	
}