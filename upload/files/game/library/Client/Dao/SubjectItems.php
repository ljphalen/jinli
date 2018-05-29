<?php
if (!defined('BASE_PATH')) exit('Access Denied!');

/**
 * 专题子项
 * Client_Dao_SubjectItems
 * @author wupeng
 */
class Client_Dao_SubjectItems extends Common_Dao_Base {
	protected $_name = 'game_client_subject_items';
	protected $_primary = 'item_id';
}