<?php

$db = db::factory(get_db_config());
$user = new user($db);
$company = new company($db);
$channel = get('channeltype');
$tmpret = $company->getClientList($channel);
//var_dump($tmpret);
if(!empty($tmpret)){
    $status = 0;
}else{
    $status = 1;
}
//{status:0,stautsnote:"",data:[]}

$str = '{status:'.$status.',stautsnote:"",data:[';
foreach ($tmpret as $key => $var){
	if($var['clientid']==0)continue;
    //$str .='{'.$var['clientid'].':'.$var['clientid'].'-'.$var['name'].'},';
    $str .= '{title:"'.$var['clientid'].'-'.$var['name'].'",clientid:"'.$var['clientid'].'"},';
}   
$str = rtrim($str,',');
$str .= ']}';

echo $str;