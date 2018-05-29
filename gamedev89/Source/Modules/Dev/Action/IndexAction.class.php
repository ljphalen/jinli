<?php

class IndexAction extends BaseAction
{
	function index()
	{
		header('Location: ' . U("Apps/index"));
		exit;
	}
}