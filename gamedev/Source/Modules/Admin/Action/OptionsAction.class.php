<?php
class OptionsAction extends SystemAction
{
	public function _filter(&$map)
	{
		$_search = MAP();
		$map = !empty($_search) ? array_merge($_search, $map) : $map;
		$map['upid'] = $this->_request("upid", "intval", 0);
	}
	
	function _before()
	{
		$upid = $this->_request("upid", "intval", 0);
		
		$this->assign("upid", $upid);
	}
}