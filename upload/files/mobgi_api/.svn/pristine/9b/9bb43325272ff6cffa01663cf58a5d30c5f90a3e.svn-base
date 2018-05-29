<?php

/**
 * @Encoding      :   UTF-8
 * @Author       :   hunter.fang
 * @Email         :   782802112@qq.com
 * @Time          :   2015-4-16 16:04:02
 * $Id: lz.php 62100 2015-4-16 16:04:02Z hunter.fang $
 */

//http://dev.www.mobgi.com/lz.php?ct=checkUidExists&uid=8889&request_secret=KW49IghKy3su-Tnk1Ev33M
//http://hunter.backend.mobgi.com/lz.php?ct=insertRecord&udid=8891569&nick_name=hexllename&open_id=99x9&request_secret=KW49IghKy3su-Tnk1Ev33M

function connectDb($cfg){
	$dsn	= "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']}";
	try {
		$dbh	= new PDO($dsn, $cfg['user'], $cfg['pass']);
		$dbh->query("SET NAMES 'utf8'");
		return $dbh;
	}catch (PDOException $e) {
		file_put_contents ( './db_error_'.date("Y-m-d").'.log', $dsn."[Connection failed: " . $e->getMessage()."]\r\n", FILE_APPEND );
		exit;
	}
}

/**
 * 自动转义
 *
 * @param array $array
 * @return array
 */
function auto_addslashes(&$array)
{
    if ($array)
    {
        foreach ($array as $key => $value)
        {
            if (!is_array($value))
            {
                /* key值处理 */
                $tmp_key         = addslashes($key);
                $array[$tmp_key] = addslashes($value);
                if ($tmp_key != $key)
                {
                    /* 删除原生元素 */
                    unset($array[$key]);
                }
            }
            else
            {
                auto_addslashes($array[$key]);
            }
        }
    }
}

/* 自动转义 */
if (!get_magic_quotes_gpc())
{
    auto_addslashes($_POST);
    auto_addslashes($_GET);
    auto_addslashes($_COOKIE);
    auto_addslashes($_FILES);
    auto_addslashes($_REQUEST);
}

$env['dev'] = array(
    'mysql'=>array(
        'host' => '192.168.0.14', 
        'port' => 3306, 
        'dbname' => 'lotto', 
        'user' => 'stephen', 
        'pass' => '123456',
        'table' => 'upload_portrait',
        ),
);

$env['test'] = array(
    'mysql'=>array(
        'host' => '10.50.10.12', 
        'port' => 3306, 
        'dbname' => 'lotto', 
        'user' => 'eric', 
        'pass' => 'XqfX29pXso',
        'table' => 'upload_portrait',
        ),
);

$env['prod'] = array(
    'mysql'=>array(
        'host' => '10.50.10.12', 
        'port' => 3306, 
        'dbname' => 'lotto', 
        'user' => 'eric', 
        'pass' => 'XqfX29pXso',
        'table' => 'upload_portrait',
        ),
);


$ENV_MODE=empty($_ENV["PHP_ENV_MODE"])?$_SERVER["PHP_ENV_MODE"]:$_ENV["PHP_ENV_MODE"];
if(empty($ENV_MODE)){//未设置环境变量这取线上配置
    $ENV_MODE="prod";
}

$config = $env[$ENV_MODE];

$mysql_config = $config['mysql'];


$ct_arr = array('checkUidExists', 'insertRecord');
$ct = $_POST['ct'];
if(!in_array($ct, $ct_arr)){
    echo 'param error!';
    exit;
}

$request_secret = $_POST['request_secret'];
if($request_secret != 'KW49IghKy3su-Tnk1Ev33M'){
    echo 'secret key error!';
    exit;
}

if($ct == 'checkUidExists'){
    $uid = intval($_POST['uid']);
    if(empty($uid)){
        echo 'pamram error! uid is empty!';
        exit;
    }
    
    $dbh =connectDb($mysql_config);
    $getSql	="select count(1) as num FROM upload_portrait WHERE employeeid =". $uid;;
    $rs1	= $dbh->query($getSql, PDO::FETCH_ASSOC);
    $rows2	= $rs1->fetchALL();
    $return = array('num'=>$rows2[0]['num']);
    echo json_encode($return);
    exit;
}


if($ct == 'insertRecord'){
    $employeeid =  intval($_POST['udid']);
    $employeename = $_POST['nick_name'];
    $udid = $_POST['open_id'];
    
    if(empty($employeeid) || empty($employeename) || empty($udid)){
        echo 'param error!';
        exit;
    }
    
    $uploadtime = date("Y-m-d H:i:s");
    
    $dbh =connectDb($mysql_config);
    $insertSql = 'insert into upload_portrait(`employeeid`,`employeename`,`udid`,`uploadtime`) values'."('".$employeeid."', '". $employeename . "', '". $udid. "', '". $uploadtime. "')";
    $rs_hunter 	= $dbh->query($insertSql,PDO::FETCH_ASSOC);
    $return = array();
    if($rs_hunter){
        $return['code'] = '0';
        $return['msg'] = '添加成功';
        
        /* 写入文件备份数据用*/  
        $fh = fopen("lz_add_user_sql.txt", 'aw') ;  
        fwrite($fh, $insertSql. ";\r\n") ;  
        fclose($fh) ;   
        
    }else{
//        $errorInfo = $dbh->errorInfo();
//        $errmsg = $errorInfo[2];
//        var_dump($errmsg);
        $return['code'] = '1';
        $return['msg'] = '添加失败';
    }
    echo json_encode($return);
    exit;
}