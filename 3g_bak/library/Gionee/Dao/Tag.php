<?php
if (!defined('BASE_PATH')) exit('Access Denied!');
/**
category	必选	String	要监控的目标的类型名称
action	必选	String	用户跟网页进行交互的动作名称
opt_label	可选	String	事件的一些额外信息
opt_value	可选	Number	跟事件相关的数值
 */
class Gionee_Dao_Tag extends Common_Dao_Base {
	protected $_name    = 'tj_tag';
	protected $_primary = 'id';
}