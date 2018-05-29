<?php
function getStatus($status, $imageShow = true) {
	switch ($status) {
		case 0 :
			$showText = '已禁用';
			$showImg = '<IMG SRC="' . cdn("Public") . '/app/dwz/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="已禁用" ALT="已禁用">';
			break;
		case - 1 :
			$showText = '已删除';
			$showImg = '<IMG SRC="' . cdn("Public") . '/app/dwz/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" TITLE="已删除" ALT="已删除">';
			break;
		default :
			$showText = '正常';
			$showImg = '<IMG SRC="' . cdn("Public") . '/app/dwz/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">';

	}
	return ($imageShow === true) ?  $showImg  : $showText;
}
function showStatus($status, $id, $callback="") {
	switch ($status) {
		//当数据被禁用
		case 0 :
			$info = '<a href="__URL__/resume/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">启用</a>';
			break;
		case 2 :
			$info = '<a href="__URL__/pass/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">批准</a>';
			break;
		//正常
		case 1 :
			$info = '<a href="__URL__/forbid/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">禁用</a>';
			break;
		//当数据被删除
		case - 1 :
			$info = '<a href="__URL__/recycle/id/' . $id . '/navTabId/__MODULE__" target="ajaxTodo" callback="'.$callback.'">还原</a>';
			break;
	}
	return $info;
}

function getGroupName($id) {
	if ($id == 0) {
		return '无上级组';
	}
	if ($list = F ( 'groupName' )) {
		return $list [$id];
	}
	$dao = D ( "Role" );
	$list = $dao->select ( array ('field' => 'id,name' ) );
	foreach ( $list as $vo ) {
		$nameList [$vo ['id']] = $vo ['name'];
	}
	$name = $nameList [$id];
	F ( 'groupName', $nameList );
	return $name;
}

function pwdHash($password, $type = 'md5') {
	return hash ( $type, $password );
}

//获取当前登陆的管理员id
function admin_id()
{
	return session(C('USER_AUTH_KEY')) ? session(C('USER_AUTH_KEY')) : 0;
}
//获取当前登陆的管理员 account
function admin_account($field='account')
{
	$map['id'] = session(C('ADMIN_AUTH_KEY'));
	return D("Admin")->where( $map )->getField($field);
}

//身份证检测
function idNumCheck($num)
{
	import ('ORG.Util.idNumChecker');
	return idNumChecker::check_id ( $num ) ? TRUE : FALSE;
}

//隐藏字段
function hideIdNum($str, $num=4)
{
	return preg_replace('@[\d]{'.$num.'}$@is', str_repeat("*", $num), $str);
}

//快速获取表名
function T($model)
{
	return D($model)->getTableName();
}

//快速获取搜索字段
function MAP()
{
	Session::start();
	if(empty($_POST["_search"]) && empty($_POST["pageNum"])) return NULL;
	
	if(!empty($_POST["_search"]))
	{
		$map = array();
		foreach ($_POST["_search"] as $key=>$value)
		{
			$value = trim($value);
			if(strlen($value) > 0)
			{
				if(strpos($value, "*") !== false)
					$map[$key] = array("like", str_replace("*", "%", $value));
				else
					$map[$key] = $value;
			}
		}
	}

	//if(isset($map)) $_SESSION["_search"][md5(__SELF__)] = $map;
	//return (array)$_SESSION["_search"][md5(__SELF__)];
	return $map;
}

//生成列表查询条件
function orderField($field, $default='asc')
{
	if(isset($_POST['orderDirection']) && $_POST['orderField'] == $field)
		return $_POST['orderDirection'] == 'desc' ? 'desc' : 'asc';
	return $default;
}

//获取导出时间
function exdate($format, $time)
{
	if (empty($time))
		return '';
	return date($format, $time);
}

//加载扩展函数库
Load('extend');
