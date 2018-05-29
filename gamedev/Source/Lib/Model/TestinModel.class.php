<?php
/**
 * 云测模型
 * @author shuhai
 *
 */
class TestinModel extends Model
{
	protected $trueTableName = "testin";
	public $_status = array("3"=>"等待测试", "4"=>"正在测试", "5"=>"部分完成", "6"=>"测试完成");
	public $_run_status = array("1"=>"测试通过", "2"=>"待优化","3"=>"未执行","5"=>"安装失败", "6"=>"运行中失败");
	public $_chk_status = array("0"=>"待审核", "1"=>"检测通过", "-1"=>"检测不通过");

	function getResult($map)
	{
		if(!is_array($map))
		{
			if(strlen($map) == 32)
				$map = array("apk_md5"=>$map);
			elseif(is_numeric($map))
				$map = array("apk_id"=>$map);
		}
		$find = $this->where($map)->find();
		if(empty($find))
			return array();

		$find["report"] = !empty($find["report"]) ? json_decode($find["report"], true) : array();
		$find["detail_report"] = !empty($find["detail_report"]) ? json_decode($find["detail_report"], true) : array();
		return $find;
	}
	
	function getStatusByMd5($md5)
	{
		$find = $this->where(array("apk_md5"=>$md5))->field('chk_status,status,fail')->find();
		list($chk_status, $status, $fail) = array_values($find);
		
		if(empty($status) || !isset($this->_status[$status]))
			return '';

		if(-1 == $chk_status)
			return '任务异常';
		
		//已经有测试结果
		if($status == 6)
			return $fail > 0 ? '<font color="red">未通过('.$fail.')</font>' : '通过';
		return $this->_status[$status];
	}
	
	function getStatusByApkId($apkId)
	{
		$md5 = $this->where(array("apk_id"=>$apkId))->getField('apk_md5');
		if(empty($md5)) return '';
		
		return $this->getStatusByMd5($md5);
	}
}