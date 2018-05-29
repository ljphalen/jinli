<?php
class OptlogModel extends RelationModel
{
	protected $trueTableName = 'think_optlog';
	protected $tablePrefix = 'think_';

	public $_auto		=	array(
			array('admin_id','admin_id',self::MODEL_INSERT,'function'),
			array('created_at','time',self::MODEL_INSERT,'function'),
	);
	
}