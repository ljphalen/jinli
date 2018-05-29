<?php
class LoginModel extends RelationModel
{
	protected $trueTableName = 'think_login';
	protected $tablePrefix = 'think_';
	
	public $_auto		=	array(
		array('dateline','time',self::MODEL_BOTH,'function'),
		array('user_id','admin_id',self::MODEL_BOTH,'function'),
	);
	
	protected $_link = array(
		'Admin'	=>	array(
			'mapping_type'	=>	BELONGS_TO,
			'foreign_key'	=>	'user_id',
		),
	);
}