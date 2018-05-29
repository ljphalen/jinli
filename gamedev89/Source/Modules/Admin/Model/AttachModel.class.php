<?php
class AttachModel extends RelationModel
{
	protected $trueTableName = 'think_attach';
	
	public $_auto		=	array(
			array('dateline','time',self::MODEL_BOTH,'function'),
	);
}