<?php
class SyserrorModel extends Model
{
	protected $trueTableName = 'think_syserror';
	
	protected $_auto = array (
			array('status','0'),
			array('created_at', 'time', Model::MODEL_INSERT, 'function'),
			array('updated_at', 'time', Model::MODEL_UPDATE, 'function'),
	);
	
	/**
	 * 系统异常记录，并在后台展示
	 * @param string $title
	 * @param string $body
	 * @param string $level
	 * @param string $fix
	 */
	function error($title, $body, $fix="", $app_id=0, $level="1")
	{
		$data = array("title"=>$title, "body"=>$body, "level"=>$level, "fix"=>$fix, "app_id"=>$app_id);
		$data = $this->create($data);
		$this->add($data);
	}
}