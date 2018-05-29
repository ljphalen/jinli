<?php

class IndexAction extends BaseAction
{
	function index()
	{
		$this->userinfo = $this->uid > 0 ? D('Accountinfo')->getAccInfo($this->uid) : array();
		A("Login")->login();
	}
}