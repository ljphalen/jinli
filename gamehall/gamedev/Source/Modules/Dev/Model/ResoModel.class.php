<?php
/**
 * 应用model类
 *
 * @name ResoModel.class.php
 * @author gaochao
 * @datetime 2013-12-18
 */
class ResoModel extends Model{
	protected $trueTableName = '`reso`';
	
	public function getAllReso(){
		$res = $this->order("reso_id desc")->find();
		return $res;
	}
	
}
